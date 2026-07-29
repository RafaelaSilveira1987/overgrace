<?php

require_once 'api/core/Response.php';
require_once 'api/services/payment/PaymentService.php';

class WebhookController
{

    public function mercadoPago()
    {
        try {

            $raw = file_get_contents('php://input');

            $payload = json_decode($raw, true);


            if (!$payload) {

                http_response_code(400);

                echo json_encode([
                    "error" => "Payload inválido"
                ]);

                return;
            }

 
            PaymentService::processWebhook($payload);


            http_response_code(200);

            echo json_encode([
                "success" => true
            ]);
        } catch (Exception $e) {


            http_response_code(500);

            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
}
