<?php

require_once 'config/database.php';
require_once 'services/CartService.php';

class OrderService {

    public static function create($userId) {
        $db = Database::connect();

        $cart = CartService::get($userId);

        if (!$cart) return false;

        $stmt = $db->prepare("INSERT INTO orders (user_id) VALUES (?)");
        $stmt->execute([$userId]);

        $orderId = $db->lastInsertId();

        foreach ($cart as $item) {
            $stmt = $db->prepare("
                INSERT INTO order_items (order_id,product_id,qty,price)
                VALUES (?,?,?,?)
            ");

            $stmt->execute([
                $orderId,
                $item['product_id'],
                $item['qty'],
                $item['price']
            ]);
        }

        CartService::clear($userId);

        return $orderId;
    }
}
