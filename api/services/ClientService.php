<?php

require_once 'api/config/database.php';
require_once 'api/config/jwt.php';

class ClientService
{
    // 🔹 CREATE
    public static function register($data)
    {
        $db = Database::connect();

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

        return JWT::encode([
            'id' => $id,
            'email' => $data['email'],
            'type' => 'client'
        ]);
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

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
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

            $userId = $pdo->lastInsertId();

            $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return $user;*/
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
}
