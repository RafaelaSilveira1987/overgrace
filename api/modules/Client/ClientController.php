<?php

require_once 'api/core/Response.php';
require_once 'api/services/ClientService.php';


class ClientController
{
    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        try {

            $token = ClientService::login(
                $data['email'],
                $data['password']
            );

            if (!$token) {
                Response::json(['message' => 'Credenciais inválidas'], 401);
            }

            Response::json(['token' => $token]);
        } catch (Exception $e) {
            Response::json(['error' => $e->getMessage()], 400);
        }
    }

    public function register()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        try {

            $token = ClientService::register($data);

            Response::json([
                'success' => true,
                'token' => $token
            ]);
        } catch (Exception $e) {
            Response::json(['error' => $e->getMessage()], 400);
        }
    }

    public function updateAddress()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        //$userService = Auth::getUser(); // via JWT

        try {
            //ClientService::updateAddress($userService->id, $data);
            Response::json(['success' => true]);
        } catch (Exception $e) {
            Response::json(['error' => $e->getMessage()], 400);
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

        $orders        = ClientService::list($filters, $limit, $offset);
        $total         = ClientService::count($filters);
        $totalGeral    = ClientService::totals();

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
}
