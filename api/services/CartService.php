<?php

require_once 'config/database.php';

class CartService {

    public static function add($userId, $productId, $qty) {
        $db = Database::connect();

        $stmt = $db->prepare("
            INSERT INTO cart (user_id, product_id, qty)
            VALUES (?,?,?)
            ON DUPLICATE KEY UPDATE qty = qty + ?
        ");

        $stmt->execute([$userId,$productId,$qty,$qty]);
    }

    public static function get($userId) {
        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT c.*, p.name, p.price
            FROM cart c
            JOIN products p ON p.id = c.product_id
            WHERE c.user_id=?
        ");

        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function clear($userId) {
        $db = Database::connect();
        $db->prepare("DELETE FROM cart WHERE user_id=?")->execute([$userId]);
    }
}
