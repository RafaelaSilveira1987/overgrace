<?php

require_once 'api/config/database.php';

class OrderService
{

    public static function createOrder($userServiceId, $cartToken, $checkoutData)
    {
        $db = Database::connect();

        $db->beginTransaction();

        try {

            // 1. buscar carrinho
            $stmt = $db->prepare("
            SELECT *
            FROM carts
            WHERE session_token = ?
            AND status = 'active'
            LIMIT 1
            ");

            $stmt->execute([$cartToken]);

            $cart = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cart) {
                throw new Exception('Carrinho não encontrado');
            }

            // 2. itens
            $stmt = $db->prepare("
            SELECT 
                ci.*,
                p.descricao
            FROM cart_items ci
            JOIN products p ON p.id = ci.product_id
            WHERE ci.cart_id = ?
            ");

            $stmt->execute([$cart['id']]);

            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$items) {
                throw new Exception('Carrinho vazio');
            }

            // 3. calcular total
            $subtotal = 0;

            foreach ($items as $item) {
                $subtotal += ($item['price'] * $item['quantity']);
            }

            $discount = (float) ($cart['coupon_valor'] ?? 0);

            $shipping = 0;

            $total = $subtotal - $discount + $shipping;

            // 4. criar pedido
            $stmt = $db->prepare("
            INSERT INTO orders
            (
                client_id,
                cart_id,
                status,
                subtotal,
                discount,
                shipping,
                total_amount,
                coupon_id,
                payment_status,
                created_at
            )
            VALUES
            (?, ?, 'pending', ?, ?, ?, ?, ?, 'pending', NOW())
            ");

            $stmt->execute([
                $userServiceId,
                $cart['id'],
                $subtotal,
                $discount,
                $shipping,
                $total,
                $cart['coupon_id']
            ]);

            $orderId = $db->lastInsertId();

            // 5. copiar itens
            foreach ($items as $item) {

                $stmt = $db->prepare("
                INSERT INTO order_items
                (
                    order_id,
                    product_id,
                    product_name,
                    size,
                    quantity,
                    price,
                    subtotal,
                    created_at
                )
                VALUES
                (?, ?, ?, ?, ?, ?, ?, NOW())
            ");

                $stmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['descricao'],
                    $item['size'],
                    $item['quantity'],
                    $item['price'],
                    $item['price'] * $item['quantity']
                ]);
            }

            // 6. converter carrinho
            $stmt = $db->prepare("
            UPDATE carts
            SET status = 'converted'
            WHERE id = ?
            ");

            $stmt->execute([$cart['id']]);

            $db->commit();

            return [
                'success' => true,
                'order_id' => $orderId
            ];
        } catch (Exception $e) {

            $db->rollBack();

            throw $e;
        }
    }


    public static function get($orderId, $userServiceId)
    {
        $db = Database::connect();

        $stmt = $db->prepare("
        SELECT *
        FROM orders
        WHERE id = ?
        AND client_id = ?
        LIMIT 1
        ");

        $stmt->execute([
            $orderId,
            $userServiceId
        ]);

        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            throw new Exception('Pedido não encontrado');
        }

        return $order;
    }

    public static function list($filters = [], $limit = 10, $offset = 0)
    {
        $pdo = Database::connect();

        $where = [];
        $params = [];

        // 🔎 Filtros dinâmicos
        if (!empty($filters['descricao'])) {
            $where[] = "p.descricao LIKE :descricao";
            $params[':descricao'] = "%" . $filters['descricao'] . "%";
        }

        if (!empty($filters['status'])) {
            $where[] = "p.status = :status";
            $params[':status'] = $filters['status'];
        }

        // Monta WHERE
        $whereSql = "";
        if (!empty($where)) {
            $whereSql = "WHERE " . implode(" AND ", $where);
        }

        // 🔃 Ordenação dinâmica (segura)
        $orderBy = 'p.id'; // padrão
        $orderDir = 'DESC'; // padrão

        $allowedFields = [
            'id' => 'p.id',
            'descricao' => 'p.descricao',
            'status' => 'p.status',
        ];

        $allowedDirections = ['ASC', 'DESC'];

        if (!empty($filters['order_by']) && isset($allowedFields[$filters['order_by']])) {
            $orderBy = $allowedFields[$filters['order_by']];
        }

        if (!empty($filters['order_dir']) && in_array(strtoupper($filters['order_dir']), $allowedDirections)) {
            $orderDir = strtoupper($filters['order_dir']);
        }


        // Query final
        $sql = "
            SELECT 
                c.nome as client_name,
                p.*
            FROM orders p
            inner join clients c on c.id = p.client_id
            $whereSql
            ORDER BY $orderBy $orderDir
            LIMIT :limit OFFSET :offset
        ";


        $stmt = $pdo->prepare($sql);

        // Bind dos filtros
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        // Bind paginação
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();

        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($orders as &$order) {
            $order['items'] = self::list_items($order['id']);
        }

        return $orders;
    }

    public static function list_items($orderId)
    {
        $db = Database::connect();

        $stmt = $db->prepare("
        SELECT *
        FROM order_items
        WHERE order_id = ?
        ");

        $stmt->execute([
            $orderId
        ]);

        $order = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$order) {
            throw new Exception('Pedido não encontrado');
        }

        return $order;
    }

    public static function totals()
    {
        $pdo = Database::connect();

        $sql = "
        SELECT 
            COUNT(*) as qt_products
        FROM orders p";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function count($filters = [])
    {
        $pdo = Database::connect();

        $where = [];
        $params = [];

        if (!empty($filters['descricao'])) {
            $where[] = "descricao LIKE :descricao";
            $params[':descricao'] = "%" . $filters['descricao'] . "%";
        }

        if (!empty($filters['preco_min'])) {
            $where[] = "preco_atual >= :preco_min";
            $params[':preco_min'] = $filters['preco_min'];
        }

        if (!empty($filters['preco_max'])) {
            $where[] = "preco_atual <= :preco_max";
            $params[':preco_max'] = $filters['preco_max'];
        }

        if (!empty($filters['data_inicio'])) {
            $where[] = "inicio_exibicao >= :data_inicio";
            $params[':data_inicio'] = $filters['data_inicio'];
        }

        if (!empty($filters['data_fim'])) {
            $where[] = "inicio_exibicao <= :data_fim";
            $params[':data_fim'] = $filters['data_fim'];
        }

        $whereSql = "";
        if (!empty($where)) {
            $whereSql = "WHERE " . implode(" AND ", $where);
        }

        $sql = "SELECT COUNT(*) as total FROM orders $whereSql";

        $stmt = $pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public static function dashboardList($competencia = null)
    {
        $pdo = Database::connect();

        // Competência atual como padrão
        if (empty($competencia)) {
            $competencia = date('Y-m');
        }

        $inicio = $competencia . '-01';
        $fim = date('Y-m-t', strtotime($inicio));

        $sql = "
        SELECT
            c.nome AS client_name,
            p.*
        FROM orders p
        INNER JOIN clients c ON c.id = p.client_id
        WHERE DATE(p.created_at) BETWEEN :inicio AND :fim
        ORDER BY p.created_at DESC
        LIMIT 20
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':inicio' => $inicio,
            ':fim'    => $fim
        ]);

        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($orders as &$order) {
            $order['items'] = self::list_items($order['id']);
        }

        return $orders;
    }

    public static function dashboardItemList($competencia = null)
    {
        $pdo = Database::connect();

        // Competência atual como padrão
        if (empty($competencia)) {
            $competencia = date('Y-m');
        }

        $inicio = $competencia . '-01';
        $fim = date('Y-m-t', strtotime($inicio));

        $sql = "
        SELECT
            p.id,
            p.product_name,
            sum(p.quantity) as qty_sum,
            sum(p.subtotal) as total_sum,
            (
                SELECT imagem 
                FROM products_img pi 
                WHERE pi.produto_id = p.product_id 
                ORDER BY destaque DESC 
                LIMIT 1
            ) as imagem_principal
        FROM order_items p
        inner join orders o on o.id = p.order_id
        WHERE DATE(o.created_at) BETWEEN :inicio AND :fim
        group by 1,2
        ORDER BY p.created_at DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':inicio' => $inicio,
            ':fim'    => $fim
        ]);

        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $orders;
    }

    public static function dashboardTotals($competencia = null)
    {
        $pdo = Database::connect();

        if (empty($competencia)) {
            $competencia = date('Y-m');
        }

        $inicio = $competencia . '-01';
        $fim = date('Y-m-t', strtotime($inicio));

        $sql = "
        SELECT
            COUNT(*) AS total_orders,
            COALESCE(SUM(subtotal), 0) AS total_value,
            COALESCE(SUM(CASE WHEN status = 'pending' THEN subtotal ELSE 0 END), 0) AS pending_value,
            COALESCE(SUM(CASE WHEN status = 'approved' THEN subtotal ELSE 0 END), 0) AS finished_value,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_orders,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS finished_orders
        FROM orders p
        WHERE DATE(p.created_at) BETWEEN :inicio AND :fim
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':inicio' => $inicio,
            ':fim'    => $fim
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
