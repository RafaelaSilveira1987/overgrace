<?php

require_once 'api/core/Response.php';
require_once 'api/middleware/AuthMiddleware.php';
require_once 'api/services/CouponService.php';

class CouponController
{
    // 🔹 CREATE
    public function create()
    {
        AuthMiddleware::handle();

        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['cupom']) || empty($data['tipo'])) {
            Response::json(['error' => 'Campos obrigatórios não preenchidos'], 400);
        }
 
        $id = CouponService::create($data);

        Response::json([
            'success' => true,
            'id' => $id
        ]);
    }

    // 🔹 UPDATE
    public function update($id)
    {
        AuthMiddleware::handle();

        $data = json_decode(file_get_contents("php://input"), true);

        CouponService::update($id, $data);

        Response::json(['success' => true]);
    }

    // 🔹 LIST (COM PAGINAÇÃO)
    public function get()
    {
        $filters = [
            'cupom'     => $_GET['cupom'] ?? null,
            'tipo'      => $_GET['tipo'] ?? null,
            'status'    => $_GET['status'] ?? null,
            'validade'  => $_GET['validade'] ?? null,
            'order_by'  => $_GET['order_by'] ?? null,
            'order_dir' => $_GET['order_dir'] ?? null,
        ];

        $page  = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;

        if ($page < 1) $page = 1;
        if ($limit < 1) $limit = 10;

        $offset = ($page - 1) * $limit;

        $data   = CouponService::list($filters, $limit, $offset);
        $total  = CouponService::count($filters);
        $totals = CouponService::totals();

        Response::json([
            'data' => $data,
            'totals' => $totals,
            'pagination' => [
                'total' => (int)$total,
                'page'  => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }

    // 🔹 DETAIL
    public function getById($id)
    {
        $item = CouponService::findById($id);

        if (!$item) {
            Response::json(['error' => 'Cupom não encontrado'], 404);
        }

        Response::json($item);
    }

    // 🔹 DELETE (soft)
    public function delete($id)
    {
        CouponService::delete($id);

        Response::json(['success' => true]);
    }
}
