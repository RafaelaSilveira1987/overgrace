<?php

require_once 'api/core/Response.php';
require_once 'api/payment/PaymentService.php';

class WebhookController
{

    public function mercadoPago()
    {
        try {

            $payload = json_decode(
                file_get_contents('php://input'),
                true
            );

            PaymentService::processWebhook($payload);

            http_response_code(200);
            echo 'OK';

        } catch (Exception $e) {

            http_response_code(500);

            echo $e->getMessage();

        }
    }

}