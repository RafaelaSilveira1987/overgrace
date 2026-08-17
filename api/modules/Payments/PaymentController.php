<?php

require_once 'api/core/Response.php';
require_once 'api/middleware/AuthMiddleware.php';

require_once 'api/services/payment/PaymentService.php';

class PaymentController
{

    /**
     * Cria um pagamento PIX
     */
    /*
    public function create()
    { 
        try {

            $user = AuthMiddleware::handle();

            $body = json_decode(file_get_contents('php://input'), true);

            if (empty($body['order_id'])) {
                Response::json([
                    'success' => false,
                    'message' => 'Pedido não informado.'
                ], 400);
            }

            $payment = PaymentService::createPix(
                (int) $body['order_id'], (int) $body['client_id']
            );

            Response::json([
                'success' => true,
                'data' => $payment
            ]);
        } catch (Exception $e) {

            Response::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }*/

    public function create()
    {
        try {

            $user = AuthMiddleware::handle();

            $body = json_decode(file_get_contents('php://input'), true);

            if (empty($body['order_id'])) {
                Response::json([
                    'success' => false,
                    'message' => 'Pedido não informado.'
                ], 400);

                return;
            }

            $method = strtolower($body['method'] ?? 'pix');

            $payment = match ($method) {

                'pix' => PaymentService::createPix(
                    (int) $body['order_id'],
                    (int) $body['client_id']
                ),

                'credit_card' => PaymentService::createCreditCard(
                    (int) $body['order_id'],
                    (int) $body['client_id'],
                    $body
                ),

                'boleto' => PaymentService::createBoleto(
                    (int) $body['order_id'],
                    (int) $body['client_id']
                ),

                default => throw new Exception(
                    "Método de pagamento inválido."
                )
            };

            Response::json([
                'success' => true,
                'data' => $payment
            ]);
        } catch (Exception $e) {

            Response::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Consulta um pagamento
     */
    public function get($id)
    {
        try {

            $user = AuthMiddleware::handle();

            $payment = PaymentService::get(
                (int)$id,
                (int)$user['id']
            );

            Response::json([
                'success' => true,
                'data' => $payment
            ]);
        } catch (Exception $e) {

            Response::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Atualiza o status consultando o Mercado Pago
     */
    public function refresh($id)
    {
        try {

            $user = AuthMiddleware::handle();

            $payment = PaymentService::updateStatus(
                (int)$id,
                (int)$user['id']
            );

            Response::json([
                'success' => true,
                'data' => $payment
            ]);
        } catch (Exception $e) {

            Response::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Cancela um pagamento
     */
    public function cancel($id)
    {
        try {

            $user = AuthMiddleware::handle();

            PaymentService::updateStatus(
                (int)$id,
                (int)$user['id']
            );

            Response::json([
                'success' => true,
                'message' => 'Pagamento cancelado.'
            ]);
        } catch (Exception $e) {

            Response::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
