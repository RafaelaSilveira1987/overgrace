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
            $mpResponse = $mp->createPayment([
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
     * Cria pagamento Cartão de crédito via Mercado Pago
     */
    public static function createCreditCard(
        int $orderId,
        int $userId,
        array $data
    ) {
        $db = Database::connect();

        try {

            $db->beginTransaction();

            // 1. Validar dados do cartão

            if (empty($data['token'])) {
                throw new Exception("Token do cartão não informado.");
            }

            if (empty($data['payment_method_id'])) {
                throw new Exception("Bandeira do cartão não informada.");
            }

            $installments = (int) ($data['installments'] ?? 1);

            if ($installments < 1) {
                throw new Exception("Quantidade de parcelas inválida.");
            }

            // 2. Buscar pedido

            $order = OrderService::get($orderId, $userId);

            if (!$order) {
                throw new Exception("Pedido não encontrado");
            }

            if ((float)$order['total_amount'] <= 0) {
                throw new Exception("Pedido inválido");
            }

            // 3. Buscar cliente

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

            if (empty($client['email'])) {
                throw new Exception("Cliente não possui e-mail.");
            }

            // 4. Mercado Pago

            $mp = new MercadoPagoClient();

            $payload = [

                "transaction_amount" =>
                (float)$order['total_amount'],

                "description" =>
                "Pedido #{$order['id']}",

                "token" =>
                $data['token'],

                "installments" =>
                $installments,

                "payment_method_id" =>
                $data['payment_method_id'],

                "payer" => [

                    "email" =>
                    $client['email'],

                    "identification" => [

                        "type" => "CPF",

                        "number" =>
                        preg_replace(
                            '/\D/',
                            '',
                            $client['cpf']
                        )
                    ]
                ]
            ];

            if (!empty($data['issuer_id'])) {
                $payload['issuer_id'] = (int)$data['issuer_id'];
            }

            $mpResponse = $mp->createPayment($payload);

            if (!$mpResponse || empty($mpResponse['id'])) {
                throw new Exception(
                    "Erro ao criar pagamento no Mercado Pago"
                );
            }

            // 5. Persistir pagamento

            $paymentId = PaymentModel::create([

                'order_id' => $order['id'],

                'gateway' => 'mercadopago',

                'gateway_payment_id' => $mpResponse['id'],

                'gateway_customer_id' => null,

                'method' => 'credit_card',

                'status' => $mpResponse['status'] ?? 'pending',

                'amount' => (float)$order['total_amount'],

                'net_amount' => (float)(
                    $mpResponse['transaction_amount'] ??
                    $order['total_amount']
                ),

                'currency' => 'BRL',

                'installments' => $installments,

                'qr_code' => null,

                'qr_code_base64' => null,

                'pix_copy_paste' => null,

                'gateway_response' => $mpResponse
            ]);

            $db->commit();

            // 6. Resposta

            return [

                'payment_id' => $paymentId,

                'order_id' => $order['id'],

                'status' => $mpResponse['status'] ?? null,

                'status_detail' => $mpResponse['status_detail'] ?? null,

                'amount' => (float)$order['total_amount'],

                'installments' => $installments,

                'payment_method_id' =>
                $mpResponse['payment_method_id'] ??
                    $data['payment_method_id']

            ];
        } catch (Exception $e) {

            if ($db->inTransaction()) {
                $db->rollBack();
            }

            throw $e;
        }
    }

    /** 
     * Cria pagamento boleto via Mercado Pago
     */
    public static function createBoleto(
        int $orderId,
        int $userId
    ) {
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
            FROM clients c
            inner join clients_address ca on ca.client_id = c.id
            WHERE c.id = ?
            LIMIT 1
            ");

            $stmt->execute([$userId]);

            $client = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$client) {
                throw new Exception("Cliente não encontrado");
            }

            // 3. Validar dados necessários

            $required = [
                'email',
                'cpf',
                'nome',
                'sobrenome',
                'endereco',
                'numero',
                'cep',
                'bairro',
                'cidade',
                'estado'
            ];

            foreach ($required as $field) {

                if (empty($client[$field])) {
                    throw new Exception(
                        "Cliente não possui o campo obrigatório: {$field}"
                    );
                }
            }

            // 4. Mercado Pago 

            $mp = new MercadoPagoClient();

            $mpResponse = $mp->createPayment([

                "transaction_amount" =>
                (float)$order['total_amount'],

                "description" =>
                "Pedido #{$order['id']}",

                "payment_method_id" =>
                "bolbradesco",

                "payer" => [

                    "email" =>
                    $client['email'],

                    "first_name" =>
                    $client['nome'],

                    "last_name" =>
                    $client['sobrenome'],

                    "identification" => [

                        "type" => "CPF",

                        "number" =>
                        preg_replace(
                            '/\D/',
                            '',
                            $client['cpf']
                        )
                    ],

                    "address" => [

                        "zip_code" =>
                        preg_replace(
                            '/\D/',
                            '',
                            $client['cep']
                        ),

                        "street_name" =>
                        $client['endereco'],

                        "street_number" =>
                        $client['numero'],

                        "neighborhood" =>
                        $client['bairro'],

                        "city" =>
                        $client['cidade'],

                        "federal_unit" =>
                        strtoupper($client['estado'])
                    ]
                ]
            ]);

            if (!$mpResponse || empty($mpResponse['id'])) {
                throw new Exception(
                    "Erro ao criar boleto no Mercado Pago"
                );
            }

            // 5. Persistir

            $paymentId = PaymentModel::create([

                'order_id' => $order['id'],

                'gateway' => 'mercadopago',

                'gateway_payment_id' =>
                $mpResponse['id'],

                'gateway_customer_id' => null,

                'method' => 'boleto',

                'status' =>
                $mpResponse['status'] ?? 'pending',

                'amount' =>
                (float)$order['total_amount'],

                'net_amount' =>
                (float)$order['total_amount'],

                'currency' => 'BRL',

                'installments' => 1,

                'qr_code' => null,

                'qr_code_base64' => null,

                'pix_copy_paste' => null,

                'gateway_response' => $mpResponse
            ]);

            $db->commit();

            return [

                'payment_id' => $paymentId,

                'order_id' => $order['id'],

                'status' =>
                $mpResponse['status'] ?? null,

                'status_detail' =>
                $mpResponse['status_detail'] ?? null,

                'amount' =>
                (float)$order['total_amount'],

                'boleto_url' =>
                $mpResponse['transaction_details']['external_resource_url'] ?? null,

                'barcode' =>
                $mpResponse['barcode']
                    ?? null,

                'expiration_date' =>
                $mpResponse['date_of_expiration']
                    ?? null
            ];
        } catch (Exception $e) {

            if ($db->inTransaction()) {
                $db->rollBack();
            }

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
     * Busca pagamento pelo pedido
     */
    public static function getbyOrder(int $order_id, int $client_id)
    {
        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT p.*
            FROM payments p
            JOIN orders o ON o.id = p.order_id
            WHERE p.order_id = ?
            AND o.client_id = ?
            LIMIT 1
        ");

        $stmt->execute([$order_id, $client_id]);

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
        //cria o registro do webhook para validações futuras
        $webhookId = PaymentWebhookModel::create($payload);

        try {

            // Aceita apenas eventos de pagamento
            if (
                empty($payload['type']) ||
                $payload['type'] !== 'payment'
            ) {

                PaymentWebhookModel::processed($webhookId);

                return;
            }


            $gatewayPaymentId =
                $payload['data']['id'] ?? null;


            if (!$gatewayPaymentId) {

                PaymentWebhookModel::processed($webhookId);

                return;
            }


            // Consulta pagamento oficial no Mercado Pago
            $mp = new MercadoPagoClient();

            $gatewayPayment = $mp->getPayment(
                $gatewayPaymentId
            );

            if (!$gatewayPayment) {

                throw new Exception(
                    "Pagamento não localizado no Mercado Pago."
                );
            }


            // Busca pagamento local
            $payment = PaymentModel::findByGatewayPaymentId(
                (string)$gatewayPayment['id']
            );


            if (!$payment) {

                throw new Exception(
                    "Pagamento local não encontrado."
                );
            }


            /*
            * Atualiza status do pagamento
            *
            * Mercado Pago:
            * approved
            * pending
            * rejected
            * cancelled
            * refunded
            * charged_back
            */

            $internalStatus = PaymentStatus::fromMercadoPago($gatewayPayment['status']);

            PaymentModel::updateStatus(
                $payment['id'],
                $internalStatus
            );



            /*
         * Atualiza informações extras do gateway
         */
            $transactionData =
                $gatewayPayment['point_of_interaction']['transaction_data']
                ?? [];


            PaymentModel::updateGateway(
                $payment['id'],
                [

                    'gateway_payment_id' =>
                    $gatewayPayment['id'],

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


                    'gateway_response' =>
                    $gatewayPayment

                ]
            );



            /*
         * Sincroniza pedido
         */
            OrderService::syncPaymentStatus(
                $payment['order_id'],
                $gatewayPayment['status']
            );



            // Marca webhook como processado
            PaymentWebhookModel::processed(
                $webhookId
            );
        } catch (Exception $e) {


            // opcional:
            // salvar erro no webhook futuramente

            throw $e;
        }
    }
}
