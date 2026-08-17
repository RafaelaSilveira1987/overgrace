<?php

class Database
{

    private static $conn;

    public static function connect()
    {

        if (self::$conn) {
            return self::$conn;
        }

        $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST');
        $db   = $_ENV['DB_NAME'] ?? getenv('DB_NAME');
        $userService = $_ENV['DB_USER'] ?? getenv('DB_USER');
        $pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS');

        if (!$host || !$db || !$userService) {
            throw new Exception('Configuração do banco não encontrada no .env');
        }

        self::$conn = new PDO(
            "mysql:host={$host};dbname={$db};charset=utf8mb4",
            $userService,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );

        return self::$conn;
    }
}
