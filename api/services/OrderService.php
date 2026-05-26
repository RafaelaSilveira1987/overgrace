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
}
