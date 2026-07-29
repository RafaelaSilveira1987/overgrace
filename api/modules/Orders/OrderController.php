<?php

require_once 'api/core/Response.php';
require_once 'api/middleware/AuthMiddleware.php';
require_once 'api/services/OrderService.php'; 
require_once 'api/services/payment/PaymentService.php';


class OrderController
{
    public function create()
    {
        $userService = AuthMiddleware::handle();

        $cartToken = $this->getCartToken();

        $orderId = OrderService::createOrder($userService['id'], $cartToken, date('Y-m-d'));

        if (!$orderId) {
            Response::json(['error' => 'empty cart'], 400);
        }

        Response::json(['order_id' => $orderId]);
    }

    public function get($id)
    {
        try {


            $userService = AuthMiddleware::handle();

            $order = OrderService::get(
                $id,
                $userService['id']
            );

            Response::json($order);
        } catch (Exception $e) {

            Response::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    // 🔹 LISTAR PRODUTOS (mantido)
    public function list()
    {
        $filters = [
            'descricao'   => $_GET['descricao']   ?? null,
            'status'      => $_GET['status']   ?? null,
            'order_by'    => $_GET['order_by']    ?? null,
            'order_dir'   => $_GET['order_dir']   ?? null,
        ];

        $page  = isset($_GET['page'])  ? (int) $_GET['page']  : 1;
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;

        if ($page < 1) $page = 1;
        if ($limit < 1) $limit = 10;

        $offset = ($page - 1) * $limit;

        $orders        = OrderService::list($filters, $limit, $offset);
        $total         = OrderService::count($filters);
        $totalGeral    = OrderService::totals();

        Response::json([
            'data' => $orders,
            'totals' => $totalGeral,
            'pagination' => [
                'total' => (int) $total,
                'page'  => $page,
                'limit' => $limit,
                'pages' => $limit > 0 ? ceil($total / $limit) : 1
            ]
        ]);
    }

    public function listClient()
    {

        $filters = [
            'client_id'   => $_GET['client_id']   ?? null,
        ];

        $page  = isset($_GET['page'])  ? (int) $_GET['page']  : 1;
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;

        if ($page < 1) $page = 1;
        if ($limit < 1) $limit = 10;

        $offset = ($page - 1) * $limit;

        $orders        = OrderService::list($filters, $limit, $offset);
        $total         = OrderService::count($filters);
        $totalGeral    = OrderService::totals();

        Response::json([
            'data' => $orders,
            'totals' => $totalGeral,
            'pagination' => [
                'total' => (int) $total,
                'page'  => $page,
                'limit' => $limit,
                'pages' => $limit > 0 ? ceil($total / $limit) : 1
            ]
        ]);
    }

    public function getPaymentOrder($order_id)
    {

        $client_id = $_GET['client_id'] ?? null;

        $payment = PaymentService::getbyOrder(
            (int)$order_id,
            (int)$client_id
        );

        Response::json([
            'data' => $payment,
        ]);
    }


    public function dash()
    {
        $competencia = trim($_GET['competencia'] ?? '');

        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $competencia)) {
            $competencia = date('Y-m');
        }

        $orders = OrderService::dashboardList($competencia);
        $items = OrderService::dashboardItemList($competencia);
        $totals = OrderService::dashboardTotals($competencia);

        Response::json([
            'data'   => $orders,
            'items' => $items,
            'totals' => $totals,
        ]);
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
