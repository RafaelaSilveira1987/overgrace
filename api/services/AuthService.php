<?php

require_once 'api/config/database.php';
require_once 'api/config/jwt.php';

class AuthService
{

    public static function loginClient($email, $password)
    {
        return self::loginByTable('clients', $email, $password, 'client');
    }

    public static function loginAdmin($email, $password)
    {
        return self::loginByTable('users', $email, $password, 'admin');
    }

    public static function login($email, $password)
    {
        $client = self::loginClient($email, $password);

        if ($client) {
            return $client;
        }

        return self::loginAdmin($email, $password);
    }

    public static function me($payload)
    {
        $role = $payload['role'] ?? null;
        $table = $role === 'admin' ? 'users' : 'clients';

        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM {$table} WHERE id=?");
        $stmt->execute([$payload['id']]);

        $userService = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userService) {
            return false;
        }

        return [
            'id' => $userService['id'],
            'nome' => $userService['nome'] ?? null,
            'email' => $userService['email'],
            'cpf' => $userService['cpf'] ?? null,
            'telefone' => $userService['telefone'] ?? null,
            'role' => $role
        ];
    }

    private static function loginByTable($table, $email, $password, $role)
    {
        $db = Database::connect();

        $stmt = $db->prepare("SELECT * FROM {$table} WHERE email=?");
        $stmt->execute([$email]);

        $userService = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userService || !password_verify($password, $userService['password'])) {
            return false;
        }

        /*
        $token = JWT::encode([
            'id' => $userService['id'],
            'email' => $userService['email'],
            'role' => $role
        ]);*/

        $accessToken = JWT::generateAccessToken([
            'id' => $userService['id'],
            'email' => $userService['email'],
            'role' => $role
        ]);

        $refreshToken = JWT::generateRefreshToken([
            'id' => $userService['id'],
            'email' => $userService['email'],
            'role' => $role
        ]);

        return [
            'token' => $accessToken,
            'refresh_token' => $refreshToken,
            'role' => $role
        ];
    }


    public static function register($data)
    {
        $db = Database::connect();

        $email = trim($data['email']);

        $stmt = $db->prepare("SELECT id FROM users WHERE email=?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            throw new InvalidArgumentException('E-mail ja cadastrado');
        }

        $stmt = $db->prepare("SELECT id FROM clients WHERE email=?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            throw new InvalidArgumentException('E-mail ja cadastrado');
        }

        $name = trim($data['nome'] . ' ' . $data['sobrenome']);

        $columns = ['name', 'email', 'password'];
        $values = [
            $name,
            $email,
            password_hash($data['password'], PASSWORD_DEFAULT)
        ];

        $availableColumns = self::getClientColumns($db);

        if (in_array('cpf', $availableColumns)) {
            $columns[] = 'cpf';
            $values[] = preg_replace('/\D/', '', $data['cpf']);
        }

        if (in_array('telefone', $availableColumns)) {
            $columns[] = 'telefone';
            $values[] = preg_replace('/\D/', '', $data['telefone']);
        }

        $placeholders = implode(',', array_fill(0, count($columns), '?'));
        $sqlColumns = implode(',', $columns);

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("INSERT INTO clients ({$sqlColumns}) VALUES ({$placeholders})");
            $stmt->execute($values);

            $id = $db->lastInsertId();
            $db->commit();

            return $id;
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            throw $e;
        }
    }

    private static function getClientColumns($db)
    {
        $stmt = $db->query("SHOW COLUMNS FROM clients");
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field');
    }
}
