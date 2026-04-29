<?php

require_once 'api/config/database.php';
require_once 'api/config/jwt.php';
 
class AuthService {

    public static function login($email, $password) {
        $db = Database::connect();

        $stmt = $db->prepare("SELECT * FROM users WHERE email=?");
        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        //var_dump(password_hash('123', PASSWORD_DEFAULT));

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        return JWT::encode([
            'id' => $user['id'],
            'email' => $user['email']
        ]);
    }

    public static function register($email, $password) {
        $db = Database::connect();

        $stmt = $db->prepare("INSERT INTO users (email,password) VALUES (?,?)");
        $stmt->execute([
            $email,
            password_hash($password, PASSWORD_DEFAULT)
        ]);

        return $db->lastInsertId();
    }
}
