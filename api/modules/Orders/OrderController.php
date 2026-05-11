<?php

require_once 'api/core/Response.php';
require_once 'api/middleware/AuthMiddleware.php';
require_once 'api/services/OrderService.php';

class OrderController
{

    public function create()
    {
        $user = AuthMiddleware::handle();

        $cartToken = $this->getCartToken();

        $orderId = OrderService::createOrder($user['id'], $cartToken, date('Y-m-d'));

        if (!$orderId) {
            Response::json(['error' => 'empty cart'], 400);
        }

        Response::json(['order_id' => $orderId]);
    }

    public function get($id)
    {
        try {


            $user = AuthMiddleware::handle();

            $order = OrderService::get(
                $id,
                $user['id']
            );

            Response::json($order);
        } catch (Exception $e) {

            Response::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    private function getCartToken()
    {
        if (empty($_COOKIE['cart_token'])) {
            $token = bin2hex(random_bytes(16));

            setcookie(
                'cart_token',
                $token,
                time() + (60 * 60 * 24 * 30),
                '/',
                '',
                false,
                true
            );

            $_COOKIE['cart_token'] = $token; // importante para uso imediato
        }

        return $_COOKIE['cart_token'];
    }
}
