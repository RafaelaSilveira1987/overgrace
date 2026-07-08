<?php

require_once 'api/config/database.php';

require_once 'api/services/OrderService.php';
require_once 'api/services/ClientService.php';

require_once 'api/config/paymentClients/MercadoPagoClient.php';

require_once 'api/services/payment/PaymentModel.php'; // <- seu arquivo atual de INSERT/UPDATE


class PaymentService
{

    /**
     * Cria pagamento PIX via Mercado Pago
     */
    public static function createPix(int $orderId, int $userId)
    {
        $db = Database::connect();

        try {

            $db->beginTransaction();

            // 1. Buscar pedido
            $order = OrderService::get($orderId, $userId);

            if (!$order) {
                throw new Exception("Pedido não encontrado");
            }

            if ((float)$order['total_amount'] <= 0) {
                throw new Exception("Pedido inválido");
            }

            // 2. Buscar cliente
            $stmt = $db->prepare("
                SELECT *
                FROM clients
                WHERE id = ?
                LIMIT 1
            ");

            $stmt->execute([$userId]);
            $client = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$client) {
                throw new Exception("Cliente não encontrado");
            }

            // 3. Instanciar Mercado Pago
            $mp = new MercadoPagoClient();

            // 4. Criar pagamento no gateway
            $mpResponse = $mp->createPix([
                "transaction_amount" => (float)$order['total_amount'],
                "description" => "Pedido #{$order['id']}",
                "payment_method_id" => "pix",
                "payer" => [
                    "email" => $client['email'],
                    "first_name" => $client['nome']
                ]
            ]);

            if (!$mpResponse || empty($mpResponse['id'])) {
                throw new Exception("Erro ao criar pagamento no Mercado Pago");
            }

            $txData = $mpResponse['point_of_interaction']['transaction_data'] ?? [];

            // 5. Persistir pagamento
            $paymentId = PaymentModel::create([
                'order_id' => $order['id'],

                'gateway' => 'mercadopago',
                'gateway_payment_id' => $mpResponse['id'],

                'gateway_customer_id' => null,

                'method' => 'pix',
                'status' => $mpResponse['status'] ?? 'pending',

                'amount' => (float)$order['total_amount'],
                'net_amount' => (float)$order['total_amount'],

                'currency' => 'BRL',
                'installments' => 1,

                'qr_code' => $txData['qr_code'] ?? null,
                'qr_code_base64' => $txData['qr_code_base64'] ?? null,
                'pix_copy_paste' => $txData['qr_code'] ?? null,

                'gateway_response' => $mpResponse
            ]);

            $db->commit();

            // 6. resposta pro frontend
            return [
                'payment_id' => $paymentId,
                'order_id' => $order['id'],
                'status' => $mpResponse['status'],
                'qr_code' => $txData['qr_code'] ?? null,
                'qr_code_base64' => $txData['qr_code_base64'] ?? null
            ];
        } catch (Exception $e) {

            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Busca pagamento
     */
    public static function get(int $id, int $userId)
    {
        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT p.*
            FROM payments p
            JOIN orders o ON o.id = p.order_id
            WHERE p.id = ?
            AND o.client_id = ?
            LIMIT 1
        ");

        $stmt->execute([$id, $userId]);

        $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$payment) {
            throw new Exception("Pagamento não encontrado");
        }

        return $payment;
    }

    /**
     * Atualiza status (webhook ou refresh manual)
     */
    public static function updateStatus(int $paymentId, int $userId)
    {
        $db = Database::connect();

        // 1. buscar pagamento
        $stmt = $db->prepare("
            SELECT *
            FROM payments
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([$paymentId]);

        $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$payment) {
            throw new Exception("Pagamento não encontrado");
        }

        // 2. consultar Mercado Pago
        $mp = new MercadoPagoClient();

        $mpResponse = $mp->getPayment($payment['gateway_payment_id']);

        if (!$mpResponse) {
            throw new Exception("Erro ao consultar gateway");
        }

        // 3. atualizar status local
        $stmt = $db->prepare("
            UPDATE payments
            SET
                status = ?,
                gateway_response = ?,
                updated_at = NOW()
            WHERE id = ?
        ");

        $stmt->execute([
            $mpResponse['status'],
            json_encode($mpResponse),
            $paymentId
        ]);

        return [
            'id' => $paymentId,
            'status' => $mpResponse['status']
        ];
    }

    /**
     * Cancela pagamento (local apenas por enquanto)
     */
    public static function cancel(int $paymentId, int $userId)
    {
        $db = Database::connect();

        $stmt = $db->prepare("
            UPDATE payments
            SET status = 'cancelled',
                updated_at = NOW()
            WHERE id = ?
        ");

        $stmt->execute([$paymentId]);

        return true;
    }

    public static function processWebhook(array $payload)
    {
        // Aceita apenas notificações de pagamento
        if (
            empty($payload['type']) ||
            $payload['type'] !== 'payment'
        ) {
            return;
        }

        $gatewayPaymentId = $payload['data']['id'] ?? null;

        if (!$gatewayPaymentId) {
            return;
        }

        // Consulta o pagamento diretamente no Mercado Pago
        $mp = new MercadoPagoClient();

        $gatewayPayment = $mp->getPayment($gatewayPaymentId);

        if (!$gatewayPayment) {
            throw new Exception("Pagamento não localizado no Mercado Pago.");
        }

        // Procura o pagamento local
        $payment = PaymentModel::findByGatewayPaymentId(
            (string)$gatewayPayment['id']
        );

        if (!$payment) {
            throw new Exception("Pagamento local não encontrado.");
        }

        $transactionData =
            $gatewayPayment['point_of_interaction']['transaction_data'] ?? [];

        // Atualiza todas as informações recebidas do gateway
        PaymentModel::updateGateway(
            $payment['id'],
            [

                'gateway_payment_id' => $gatewayPayment['id'],

                'status' => $gatewayPayment['status'],

                'qr_code' =>
                $transactionData['qr_code'] ?? null,

                'qr_code_base64' =>
                $transactionData['qr_code_base64'] ?? null,

                'pix_copy_paste' =>
                $transactionData['qr_code'] ?? null,

                'authorization_code' =>
                $gatewayPayment['authorization_code'] ?? null,

                'expires_at' =>
                $gatewayPayment['date_of_expiration'] ?? null,

                'gateway_response' => $gatewayPayment

            ]
        );

        $db = Database::connect();

        switch ($gatewayPayment['status']) {

            case 'approved':

                $stmt = $db->prepare("
                UPDATE orders
                SET
                    payment_status = 'paid',
                    status = 'paid',
                    updated_at = NOW()
                WHERE id = ?
                ");

                $stmt->execute([
                    $payment['order_id']
                ]);

                break;

            case 'pending':

                $stmt = $db->prepare("
                UPDATE orders
                SET
                    payment_status = 'pending',
                    updated_at = NOW()
                WHERE id = ?
                ");

                $stmt->execute([
                    $payment['order_id']
                ]);

                break;

            case 'cancelled':

                $stmt = $db->prepare("
                UPDATE orders
                SET
                    payment_status = 'cancelled',
                    updated_at = NOW()
                WHERE id = ?
                ");

                $stmt->execute([
                    $payment['order_id']
                ]);

                break;

            case 'rejected':

                $stmt = $db->prepare("
                UPDATE orders
                SET
                    payment_status = 'rejected',
                    updated_at = NOW()
                WHERE id = ?
                ");

                $stmt->execute([
                    $payment['order_id']
                ]);

                break;

            case 'refunded':

                $stmt = $db->prepare("
                UPDATE orders
                SET
                    payment_status = 'refunded',
                    updated_at = NOW()
                WHERE id = ?
                ");

                $stmt->execute([
                    $payment['order_id']
                ]);

                break;

            case 'charged_back':

                $stmt = $db->prepare("
                UPDATE orders
                SET
                    payment_status = 'chargeback',
                    updated_at = NOW()
                WHERE id = ?
                ");

                $stmt->execute([
                    $payment['order_id']
                ]);

                break;
        }
    }
}
