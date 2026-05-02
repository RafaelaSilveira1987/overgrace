<?php

require_once 'api/core/Response.php';
require_once 'api/middleware/AuthMiddleware.php';
require_once 'api/services/ProductService.php';

class ProductController
{

    // 🔹 CRIAR PRODUTO
    public function create()
    {
        AuthMiddleware::handle();

        // 🔥 suporta JSON OU multipart
        $isMultipart = !empty($_FILES);

        if ($isMultipart) {
            $data = $_POST;

            // arrays vêm como string
            $data['tamanhos'] = json_decode($data['tamanhos'] ?? '[]', true);
            $data['tags'] = json_decode($data['tags'] ?? '[]', true);
        } else {
            $data = json_decode(file_get_contents("php://input"), true);
        }

        // 🔹 validação
        if (
            empty($data['nome']) ||
            empty($data['slug']) ||
            empty($data['categoria']) ||
            empty($data['preco']) ||
            empty($data['data_inicio'])
        ) {
            Response::json(['error' => 'Campos obrigatórios não preenchidos'], 400);
        }

        if (empty($data['tamanhos'])) {
            Response::json(['error' => 'Informe pelo menos um tamanho'], 400);
        }

        // 🔥 processa upload
        $data['imagens'] = $this->processUpload();

        $produtoId = ProductService::create($data);

        Response::json([
            'success' => true,
            'produto_id' => $produtoId
        ]);
    }

    // 🔹 UPDATE
    public function update($id)
    {
        AuthMiddleware::handle();

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (str_contains($contentType, 'multipart/form-data')) {

            $data = $_POST;

            $data['tamanhos'] = json_decode($data['tamanhos'] ?? '[]', true);
            $data['tags'] = json_decode($data['tags'] ?? '[]', true);
            $data['imagens_existentes'] = $data['imagens_existentes'] ?? '[]';
        } else {

            $raw = file_get_contents("php://input");
            $data = json_decode($raw, true) ?? [];
        }


        if (
            empty($data['nome']) ||
            empty($data['slug']) ||
            empty($data['categoria']) ||
            empty($data['preco'])
        ) {
            Response::json(['error' => 'Campos obrigatórios não preenchidos'], 400);
        }

        // 🔥 se vier imagem nova, processa
        $novasImagens = $this->processUpload();

        if (!empty($novasImagens)) {
            $data['imagens'] = $novasImagens;
        }

        ProductService::update($id, $data);

        Response::json(['success' => true]);
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

        $produtos      = ProductService::list($filters, $limit, $offset);
        $total         = ProductService::count($filters);
        $totalGeral    = ProductService::totals();

        $ids = array_column($produtos, 'id');

        $sizesMap = ProductService::getSizesByProducts($ids);

        foreach ($produtos as &$produto) {
            $produto['tamanhos'] = $sizesMap[$produto['id']] ?? [];
        }

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
        $produto = ProductService::findById($id);

        if (!$produto) {
            Response::json(['error' => 'Produto não encontrado'], 404);
        }

        Response::json($produto);
    }

    // 🔹 DETALHE
    public function getByUuid($id)
    {
        $produto = ProductService::findByUuid($id);

        if (!$produto) {
            Response::json(['error' => 'Produto não encontrado'], 404);
        }

        Response::json($produto);
    }

    // 🔥 UPLOAD CENTRALIZADO
    private function processUpload()
    {
        $imagens = [];

        if (empty($_FILES['imagens'])) {
            return $imagens;
        }

        $uploadDir = __DIR__ . '/../../../frontend/uploads/products/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        foreach ($_FILES['imagens']['tmp_name'] as $key => $tmpName) {

            $originalName = $_FILES['imagens']['name'][$key];
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            // 🔒 valida extensão
            if (!in_array($ext, $allowed)) {
                continue;
            }

            // 🔥 UUID
            $uuid = bin2hex(random_bytes(16));
            $fileName = $uuid . '.' . $ext;

            $destino = $uploadDir . $fileName;

            if (move_uploaded_file($tmpName, $destino)) {
                $imagens[] = $fileName;
            }
        }

        return $imagens;
    }

    // 🔹 exclusao
    public function delete($id)
    {
        $produto = ProductService::delete($id);

        if (!$produto) {
            Response::json(['error' => 'Produto não encontrado'], 404);
        }

        Response::json($produto);
    }
}
