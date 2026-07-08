<?php

require_once 'api/config/database.php';

class PaymentModel
{
    /**
     * Cria um pagamento
     */
    public static function create(array $data)
    {
        $db = Database::connect();

        // Valores padrão
        $defaults = [

            'gateway' => 'mercadopago',

            'gateway_payment_id' => null,

            'gateway_customer_id' => null,

            'method' => 'pix',

            'status' => 'pending',

            'fee' => 0,

            'net_amount' => $data['amount'] ?? 0,

            'currency' => 'BRL',

            'installments' => 1,

            'qr_code' => null,

            'qr_code_base64' => null,

            'pix_copy_paste' => null,

            'boleto_url' => null,

            'boleto_barcode' => null,

            'authorization_code' => null,

            'expires_at' => null,

            'gateway_response' => null

        ];

        $data = array_merge($defaults, $data);

        $sql = "

            INSERT INTO payments (

                uuid,

                order_id,

                gateway,

                gateway_payment_id,

                gateway_customer_id,

                method,

                status,

                amount,

                fee,

                net_amount,

                currency,

                installments,

                qr_code,

                qr_code_base64,

                pix_copy_paste,

                boleto_url,

                boleto_barcode,

                authorization_code,

                expires_at,

                gateway_response,

                created_at,

                updated_at

            )

            VALUES (

                UUID(),

                :order_id,

                :gateway,

                :gateway_payment_id,

                :gateway_customer_id,

                :method,

                :status,

                :amount,

                :fee,

                :net_amount,

                :currency,

                :installments,

                :qr_code,

                :qr_code_base64,

                :pix_copy_paste,

                :boleto_url,

                :boleto_barcode,

                :authorization_code,

                :expires_at,

                :gateway_response,

                NOW(),

                NOW()

            )

        ";

        $stmt = $db->prepare($sql);

        $stmt->execute([

            ':order_id' => $data['order_id'],

            ':gateway' => $data['gateway'],

            ':gateway_payment_id' => $data['gateway_payment_id'],

            ':gateway_customer_id' => $data['gateway_customer_id'],

            ':method' => $data['method'],

            ':status' => $data['status'],

            ':amount' => $data['amount'],

            ':fee' => $data['fee'],

            ':net_amount' => $data['net_amount'],

            ':currency' => $data['currency'],

            ':installments' => $data['installments'],

            ':qr_code' => $data['qr_code'],

            ':qr_code_base64' => $data['qr_code_base64'],

            ':pix_copy_paste' => $data['pix_copy_paste'],

            ':boleto_url' => $data['boleto_url'],

            ':boleto_barcode' => $data['boleto_barcode'],

            ':authorization_code' => $data['authorization_code'],

            ':expires_at' => $data['expires_at'],

            ':gateway_response' => $data['gateway_response']
                ? json_encode($data['gateway_response'], JSON_UNESCAPED_UNICODE)
                : null

        ]);

        return (int)$db->lastInsertId();
    }

    /**
     * Busca por ID
     */
    public static function get(int $id)
    {
        $db = Database::connect();

        $stmt = $db->prepare("SELECT * FROM payments WHERE id = ?");

        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    /**
     * Busca pelo ID do gateway
     */
    public static function findByGatewayPaymentId(string $gatewayPaymentId)
    {
        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT *
            FROM payments
            WHERE gateway_payment_id = ?
            LIMIT 1
        ");

        $stmt->execute([$gatewayPaymentId]);

        return $stmt->fetch();
    }

    /**
     * Atualiza dados do gateway
     */
    public static function updateGateway(int $id, array $data)
    {
        $db = Database::connect();

        $stmt = $db->prepare("

            UPDATE payments SET

                gateway_payment_id = :gateway_payment_id,

                status = :status,

                qr_code = :qr_code,

                qr_code_base64 = :qr_code_base64,

                pix_copy_paste = :pix_copy_paste,

                authorization_code = :authorization_code,

                expires_at = :expires_at,

                gateway_response = :gateway_response,

                updated_at = NOW()

            WHERE id = :id

        ");

        return $stmt->execute([

            ':gateway_payment_id' => $data['gateway_payment_id'],

            ':status' => $data['status'],

            ':qr_code' => $data['qr_code'],

            ':qr_code_base64' => $data['qr_code_base64'],

            ':pix_copy_paste' => $data['pix_copy_paste'],

            ':authorization_code' => $data['authorization_code'],

            ':expires_at' => $data['expires_at'],

            ':gateway_response' => json_encode(
                $data['gateway_response'],
                JSON_UNESCAPED_UNICODE
            ),

            ':id' => $id

        ]);
    }

    /**
     * Atualiza somente o status
     */
    public static function updateStatus(
        int $id,
        string $status
    ) {

        $db = Database::connect();

        $stmt = $db->prepare("

            UPDATE payments

            SET

                status = ?,

                paid_at = IF(?='paid', NOW(), paid_at),

                updated_at = NOW()

            WHERE id = ?

        ");

        return $stmt->execute([

            $status,

            $status,

            $id

        ]);
    }
}