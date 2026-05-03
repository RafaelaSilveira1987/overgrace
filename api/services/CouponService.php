<?php

require_once 'api/config/database.php';

class CouponService
{
    // 🔹 CREATE
    public static function create($data)
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare("
            INSERT INTO coupons
            (uuid, cupom, tipo, valor, minimo, limite, validade, status)
            VALUES
            (UUID(), ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['cupom'],
            $data['tipo'],
            $data['valor'] ?? null,
            $data['minimo'] ?? 0,
            $data['limite'] ?? null,
            $data['validade'] ?? null,
            $data['status'] ?? 'ativo'
        ]);

        return $pdo->lastInsertId();
    }

    // 🔹 LIST
    public static function list($filters, $limit, $offset)
    {
        $pdo = Database::connect();

        $where = [];
        $params = [];

        if (!empty($filters['cupom'])) {
            $where[] = "cupom LIKE :cupom";
            $params[':cupom'] = "%" . $filters['cupom'] . "%";
        }

        if (!empty($filters['tipo'])) {
            $where[] = "tipo = :tipo";
            $params[':tipo'] = $filters['tipo'];
        }

        if (!empty($filters['status'])) {
            $where[] = "status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['validade'])) {
            $where[] = "DATE(validade) = :validade";
            $params[':validade'] = $filters['validade'];
        }

        $whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

        // ordenação
        $orderBy = 'id';
        $orderDir = 'DESC';

        $allowed = ['id', 'cupom', 'validade', 'status'];

        if (!empty($filters['order_by']) && in_array($filters['order_by'], $allowed)) {
            $orderBy = $filters['order_by'];
        }

        if (!empty($filters['order_dir']) && in_array(strtoupper($filters['order_dir']), ['ASC', 'DESC'])) {
            $orderDir = strtoupper($filters['order_dir']);
        }

        $sql = "
            SELECT *
            FROM coupons
            $whereSql
            ORDER BY $orderBy $orderDir
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $pdo->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 COUNT
    public static function count($filters)
    {
        $pdo = Database::connect();

        $where = [];
        $params = [];

        if (!empty($filters['cupom'])) {
            $where[] = "cupom LIKE :cupom";
            $params[':cupom'] = "%" . $filters['cupom'] . "%";
        }

        if (!empty($filters['status'])) {
            $where[] = "status = :status";
            $params[':status'] = $filters['status'];
        }

        $whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM coupons $whereSql");

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->execute();

        return $stmt->fetchColumn();
    }

    // 🔹 TOTALS (dashboard)
    public static function totals()
    {
        $pdo = Database::connect();

        $stmt = $pdo->query("
            SELECT 
                COUNT(*) as total,
                SUM(status = 'ativo') as ativos,
                SUM(status = 'inativo') as inativos,
                SUM(validade < NOW()) as expirados
            FROM coupons
        ");

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 FIND
    public static function findById($id)
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM coupons WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 UPDATE
    public static function update($id, $data)
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare("
            UPDATE coupons SET
                cupom = ?,
                tipo = ?,
                valor = ?,
                minimo = ?,
                limite = ?,
                validade = ?,
                status = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $data['cupom'],
            $data['tipo'],
            $data['valor'] ?? null,
            $data['minimo'] ?? 0,
            $data['limite'] ?? null,
            $data['validade'] ?? null,
            $data['status'] ?? 'ativo',
            $id
        ]);
    }

    // 🔹 DELETE (soft)
    public static function delete($id)
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare("
            UPDATE coupons 
            SET status = 'inativo'
            WHERE id = ?
        ");

        return $stmt->execute([$id]);
    }
}
