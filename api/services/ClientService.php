<?php

require_once 'api/config/database.php';
require_once 'api/config/jwt.php';

class ClientService
{
    // 🔹 CREATE
    public static function register($data)
    {
        $db = Database::connect();

        try {
            $db->beginTransaction();

            // verifica email
            $stmt = $db->prepare("SELECT id FROM clients WHERE email=?");
            $stmt->execute([$data['email']]);

            if ($stmt->fetch()) {
                throw new Exception("Email já cadastrado");
            }

            $stmt = $db->prepare("
            INSERT INTO clients 
            (uuid, email, password, nome, sobrenome, cpf, telefone)
            VALUES 
            (UUID(), ?, ?, ?, ?, ?, ?)
        ");

            $stmt->execute([
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                $data['nome'],
                $data['sobrenome'] ?? '',
                $data['cpf'] ?? null,
                $data['telefone'] ?? null
            ]);

            $id = $db->lastInsertId();

            $stmt = $db->prepare("
            INSERT INTO clients_address 
            (client_id, tipo, cep, endereco, numero, bairro, complemento, cidade, estado)
            VALUES 
            (?, 'entrega', ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $id,
                $data['cep'],
                $data['endereco'],
                $data['numero'],
                $data['bairro'],
                $data['complemento'] ?? null,
                $data['cidade'],
                $data['estado'],
            ]);

            $db->commit();

            return JWT::encode([
                'id' => $id,
                'email' => $data['email'],
                'type' => 'client'
            ]);
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    // 🔹 UPDATE
    public static function updateAddress($clientId, $data)
    {

        $db = Database::connect();

        $stmt = $db->prepare("
        UPDATE clients SET
            cep = ?,
            endereco = ?,
            numero = ?,
            complemento = ?,
            bairro = ?,
            cidade = ?,
            estado = ?
        WHERE id = ?
        ");

        $stmt->execute([
            $data['cep'],
            $data['endereco'],
            $data['numero'],
            $data['complemento'],
            $data['bairro'],
            $data['cidade'],
            $data['estado'],
            $clientId
        ]);

        return true;
    }

    // 🔹 LOGIN
    public static function login($email, $senha)
    {
        $db = Database::connect();

        $stmt = $db->prepare("SELECT * FROM clients WHERE email=? AND status='ativo'");
        $stmt->execute([$email]);

        $client = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$client) {
            return false;
        }

        // 🔐 cliente Google não tem senha
        if (!$client['senha']) {
            throw new Exception("Use login com Google");
        }

        if (!password_verify($senha, $client['password'])) {
            return false;
        }

        return JWT::encode([
            'id' => $client['id'],
            'email' => $client['email'],
            'type' => 'client' // 👈 importante se você usa múltiplos perfis
        ]);
    }

    public static function loginWithGoogle($token)
    {
        //$client = new Google_Client(['client_id' => 'SEU_CLIENT_ID']);
        /*$payload = $client->verifyIdToken($token);

        if (!$payload) {
            throw new Exception('Token inválido');
        }

        $email = $payload['email'];
        $googleId = $payload['sub'];

        $pdo = Database::connect();

        // verifica se já existe
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = ?");
        $stmt->execute([$email]);

        $userService = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userService) {
            // cria usuário automaticamente
            $stmt = $pdo->prepare("
            INSERT INTO customers (uuid, email, nome, google_id)
            VALUES (UUID(), ?, ?, ?)
        ");

            $stmt->execute([
                $email,
                $payload['name'],
                $googleId
            ]);

            $userServiceId = $pdo->lastInsertId();

            $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
            $stmt->execute([$userServiceId]);
            $userService = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return $userService;*/
    }


    // 🔹 VALIDA SENHA
    private static function validarSenha($senha)
    {
        //if (strlen($senha) < 8) return false;
        //if (!preg_match('/[A-Z]/', $senha)) return false;
        //if (!preg_match('/[a-z]/', $senha)) return false;
        //if (!preg_match('/[0-9]/', $senha)) return false;
        return true;
    }

    public static function list($filters = [], $limit = 10, $offset = 0)
    {
        $pdo = Database::connect();

        $where = [];
        $params = [];

        // 🔎 Filtros dinâmicos
        if (!empty($filters['descricao'])) {
            $where[] = "(
                            p.cpf LIKE :descricao
                            OR p.nome LIKE :descricao
                            OR p.email LIKE :descricao
                        )";
            $params[':descricao'] = "%" . $filters['descricao'] . "%";
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
            'id'    => 'p.id',
            'nome'  => 'p.nome',
            'cpf'   => 'p.cpf',
            'email' => 'p.email',
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
                p.*,
                COUNT(o.id) AS pedidos,
                COALESCE(SUM(o.subtotal), 0) AS valor_gasto,
                MAX(o.created_at) as ultimo_pedido
            FROM clients p
            LEFT JOIN orders o
                ON o.client_id = p.id
            $whereSql
            GROUP BY p.id
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

        return $orders;
    }

    public static function totals()
    {
        $pdo = Database::connect();

        $sql = "
        SELECT 
            COUNT(*) as qt_clients
        FROM clients p";

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
            $where[] = "(
                            p.cpf LIKE :descricao
                            OR p.nome LIKE :descricao
                            OR p.email LIKE :descricao
                        )";
            $params[':descricao'] = "%" . $filters['descricao'] . "%";
        }

        $whereSql = "";
        if (!empty($where)) {
            $whereSql = "WHERE " . implode(" AND ", $where);
        }

        $sql = "SELECT COUNT(*) as total FROM clients p $whereSql";

        $stmt = $pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
