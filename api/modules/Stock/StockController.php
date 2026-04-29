<?php

require_once 'api/core/Response.php';
require_once 'api/middleware/AuthMiddleware.php';
require_once 'api/services/StockService.php';

class StockController
{ 

    // 🔹 CRIAR PRODUTO
    public function create()
    {
        AuthMiddleware::handle();

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        // 🔹 Detecta corretamente o tipo de requisição
        if (str_contains($contentType, 'multipart/form-data')) {

            $data = $_POST;

            // 🔥 tamanhos (string → array)
            if (isset($data['tamanhos']) && is_string($data['tamanhos'])) {
                $raw = stripslashes($data['tamanhos']);

                $decoded = json_decode($raw, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    Response::json([
                        'error' => 'JSON de tamanhos inválido',
                        'debug' => json_last_error_msg()
                    ], 400);
                }

                $data['tamanhos'] = $decoded;
            }

            // 🔹 tags (opcional)
            if (isset($data['tags']) && is_string($data['tags'])) {
                $data['tags'] = json_decode($data['tags'], true) ?? [];
            }
        } elseif (str_contains($contentType, 'application/json')) {

            $data = json_decode(file_get_contents("php://input"), true);

            if (!$data) {
                Response::json(['error' => 'JSON inválido'], 400);
            }
        } else {
            // 🔥 fallback (evita quebra)
            $data = $_POST;
        }

        // 🔥 garante estrutura
        if (empty($data['tamanhos']) || !is_array($data['tamanhos'])) {
            Response::json(['error' => 'Tamanhos inválidos'], 400);
        }

        // 🔥 valida pelo menos um tamanho com quantidade > 0
        $temQuantidade = false;

        foreach ($data['tamanhos'] as $item) {
            if (!empty($item['quantidade']) && $item['quantidade'] > 0) {
                $temQuantidade = true;
                break;
            }
        }

        if (!$temQuantidade) {
            Response::json([
                'error' => 'Informe quantidade em pelo menos um tamanho'
            ], 400);
        }

        // 🔥 normalização (evita undefined no service)
        $data['produto_id']     = $data['produto_id'] ?? null;
        $data['tipo_movimento'] = $data['tipo_movimento'] ?? 'entrada';
        $data['fornecedor']     = $data['fornecedor'] ?? null;
        $data['lote']           = $data['lote'] ?? null;
        $data['custo_unitario'] = $data['custo'] ?? null;
        $data['observacao']     = $data['obs'] ?? null;
        $data['data_movimento'] = $data['data'] ?? date('Y-m-d');

        // 🔥 valida produto
        if (empty($data['produto_id'])) {
            Response::json(['error' => 'Produto não informado'], 400);
        }

        // 🔥 chama service
        $ok = StockService::create($data);

        Response::json([
            'success' => true
        ]);
    }

    // 🔹 LISTAR PRODUTOS (mantido)
    public function get()
    {
        $filters = [
            'descricao'   => $_GET['descricao']   ?? null,
            'preco_min'   => $_GET['preco_min']   ?? null,
            'preco_max'   => $_GET['preco_max']   ?? null,
            'data_inicio' => $_GET['data_inicio'] ?? null,
            'data_fim'    => $_GET['data_fim']    ?? null,
            'ativo'       => $_GET['ativo']       ?? null,
            'categoria'   => $_GET['categoria']   ?? null,
            'order_by'    => $_GET['order_by']    ?? null,
            'order_dir'   => $_GET['order_dir']   ?? null,
        ];

        $page  = isset($_GET['page'])  ? (int) $_GET['page']  : 1;
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;

        if ($page < 1) $page = 1;
        if ($limit < 1) $limit = 10;

        $offset = ($page - 1) * $limit;

        $produtos      = StockService::list($filters, $limit, $offset);
        $total         = StockService::count($filters);
        $totalGeral    = StockService::totals();

        $ids = array_column($produtos, 'id');

        Response::json([
            'data' => $produtos,
            'totals' => $totalGeral,
            'pagination' => [
                'total' => (int) $total,
                'page'  => $page,
                'limit' => $limit,
                'pages' => $limit > 0 ? ceil($total / $limit) : 1
            ]
        ]);
    }

    // 🔹 DETALHE
    public function getById($id)
    {
        $produto = StockService::findById($id);

        if (!$produto) {
            Response::json(['error' => 'Produto não encontrado'], 404);
        }

        Response::json($produto);
    }

    // 🔹 exclusao
    public function delete($id)
    {
        $produto = StockService::delete($id);

        if (!$produto) {
            Response::json(['error' => 'Produto não encontrado'], 404);
        }

        Response::json($produto);
    }
}
