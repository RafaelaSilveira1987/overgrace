<?php

require_once 'api/config/database.php';


class CartService
{

    public static function add($cartToken, $productId, $size, $qty)
    {
        $db = Database::connect();

        // 1. Buscar ou criar carrinho
        $stmt = $db->prepare("SELECT id FROM carts WHERE session_token = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$cartToken]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cart) {
            $stmt = $db->prepare("
            INSERT INTO carts (session_token, status, created_at)
            VALUES (?, 'active', NOW())
        ");
            $stmt->execute([$cartToken]);

            $cartId = $db->lastInsertId();
        } else {
            $cartId = $cart['id'];
        }

        // 2. Buscar item existente
        $stmt = $db->prepare("
        SELECT id, quantity 
        FROM cart_items 
        WHERE cart_id = ? AND product_id = ? AND size = ?
        LIMIT 1
    ");
        $stmt->execute([$cartId, $productId, $size]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            // 3. Atualiza quantidade
            $stmt = $db->prepare("
            UPDATE cart_items
            SET quantity = quantity + ?, updated_at = NOW()
            WHERE id = ?
        ");
            $stmt->execute([$qty, $item['id']]);
        } else {
            // ⚠️ aqui você deve pegar o preço do produto
            $stmt = $db->prepare("SELECT preco_atual FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            $price = $product['preco_atual'] ?? 0;

            // 4. Inserir novo item
            $stmt = $db->prepare("
            INSERT INTO cart_items 
            (cart_id, product_id, size, quantity, price, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
            $stmt->execute([$cartId, $productId, $size, $qty, $price]);
        }
    }

    public static function getCart($cartToken)
    {
        $db = Database::connect();

        // 1. Buscar carrinho
        $stmt = $db->prepare("
        SELECT id
        FROM carts
        WHERE session_token = ?
        AND status = 'active'
        LIMIT 1
    ");
        $stmt->execute([$cartToken]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cart) {
            return [
                'items' => [],
                'total' => 0
            ];
        }

        $cartId = $cart['id'];

        // 2. Buscar itens + imagem principal
        $stmt = $db->prepare("
        SELECT 
            ci.id,
            ci.product_id,
            ci.size,
            ci.quantity,
            ci.price,
            (ci.quantity * ci.price) as subtotal,

            p.descricao,

            (
                SELECT pi.imagem
                FROM products_img pi
                WHERE pi.produto_id = p.id
                ORDER BY pi.destaque DESC
                LIMIT 1
            ) as imagem

        FROM cart_items ci
        JOIN products p ON p.id = ci.product_id
        WHERE ci.cart_id = ?
    ");

        $stmt->execute([$cartId]);

        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Calcular total
        $total = 0;

        foreach ($items as &$item) {
            $item['subtotal'] = (float) $item['subtotal'];
            $item['price'] = (float) $item['price'];
            $item['quantity'] = (int) $item['quantity'];

            $total += $item['subtotal'];
        }

        return [
            'items' => $items,
            'total' => $total
        ];
    }

    public static function countItems($cartToken)
    {
        $db = Database::connect();

        $stmt = $db->prepare("
        SELECT SUM(ci.quantity) as total
        FROM carts c
        JOIN cart_items ci ON ci.cart_id = c.id
        WHERE c.session_token = ?
        AND c.status = 'active'
    ");

        $stmt->execute([$cartToken]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) ($result['total'] ?? 0);
    }

    public static function delete($itemId)
    {
        $db = Database::connect();
        $db->prepare("DELETE FROM cart_items WHERE id=?")->execute([$itemId]);
    }

    public static function clear($userId)
    {
        $db = Database::connect();
        $db->prepare("DELETE FROM cart WHERE user_id=?")->execute([$userId]);
    }

    public static function update($cartToken, $itemId, $quantity)
    {
        $db = Database::connect();

        // 1. Buscar carrinho ativo
        $stmt = $db->prepare("
        SELECT id 
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

        $cartId = $cart['id'];

        // 2. Validar se o item pertence ao carrinho
        $stmt = $db->prepare("
        SELECT id 
        FROM cart_items 
        WHERE id = ? 
        AND cart_id = ?
        LIMIT 1
        ");
        $stmt->execute([$itemId, $cartId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            throw new Exception('Item não pertence ao carrinho');
        }

        // 3. Validar quantidade
        if ($quantity < 1) {
            // opção: remover automaticamente
            $stmt = $db->prepare("DELETE FROM cart_items WHERE id = ?");
            $stmt->execute([$itemId]);
            return;
        }

        // 4. Atualizar quantidade
        $stmt = $db->prepare("
        UPDATE cart_items
        SET quantity = ?, updated_at = NOW()
        WHERE id = ?
        ");
        $stmt->execute([$quantity, $itemId]);
    }
}
