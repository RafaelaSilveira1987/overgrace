<?php

require_once 'api/config/database.php';

class StockReservationService
{
    private static array $schemaCache = [];

    public static function isAvailable(?PDO $db = null): bool
    {
        $db = $db ?: Database::connect();
        $key = 'reservations';
        if (array_key_exists($key, self::$schemaCache)) {
            return self::$schemaCache[$key];
        }

        try {
            $stmt = $db->query("SHOW TABLES LIKE 'stock_reservations'");
            if (!$stmt->fetchColumn()) {
                return self::$schemaCache[$key] = false;
            }

            $required = [
                'id', 'order_id', 'product_id', 'size', 'quantity',
                'status', 'expires_at', 'consumed_at', 'released_at',
                'created_at', 'updated_at'
            ];
            $cols = $db->query("SHOW COLUMNS FROM stock_reservations")
                       ->fetchAll(PDO::FETCH_COLUMN);
            $missing = array_diff($required, $cols ?: []);

            return self::$schemaCache[$key] = empty($missing);
        } catch (Throwable $e) {
            return self::$schemaCache[$key] = false;
        }
    }

    public static function movementsHaveOrderId(?PDO $db = null): bool
    {
        $db = $db ?: Database::connect();
        $key = 'movement_order_id';
        if (array_key_exists($key, self::$schemaCache)) {
            return self::$schemaCache[$key];
        }

        try {
            $stmt = $db->query("SHOW COLUMNS FROM products_stock_movements LIKE 'order_id'");
            return self::$schemaCache[$key] = (bool)$stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            return self::$schemaCache[$key] = false;
        }
    }

    private static function requireReservationSchema(PDO $db): void
    {
        if (!self::isAvailable($db)) {
            throw new RuntimeException(
                'A reserva de estoque ainda não foi instalada. Execute database/migrations/004_stock_reservations.sql.'
            );
        }
    }

    public static function reservationMinutes(): int
    {
        $value = (int)($_ENV['STOCK_RESERVATION_MINUTES'] ?? getenv('STOCK_RESERVATION_MINUTES') ?: 30);
        return $value > 0 ? $value : 30;
    }

    /**
     * Expira reservas vencidas e sincroniza o pedido pendente.
     *
     * A disponibilidade de estoque nunca depende apenas deste UPDATE: todas
     * as consultas de reserva também exigem expires_at > NOW(). Este método
     * mantém o banco consistente e evita que o painel continue exibindo uma
     * reserva vencida como "reserved".
     */
    public static function cleanupExpired(?PDO $db = null): int
    {
        $db = $db ?: Database::connect();
        if (!self::isAvailable($db)) return 0;

        // Captura os pedidos antes da atualização para sincronizar somente
        // aqueles cuja reserva realmente venceu nesta execução.
        $find = $db->query("
            SELECT DISTINCT order_id
            FROM stock_reservations
            WHERE status = 'reserved'
              AND expires_at <= NOW()
        ");
        $orderIds = array_map('intval', $find->fetchAll(PDO::FETCH_COLUMN) ?: []);

        if (!$orderIds) return 0;

        $stmt = $db->prepare("
            UPDATE stock_reservations
            SET
                status = 'expired',
                released_at = COALESCE(released_at, NOW()),
                updated_at = NOW()
            WHERE status = 'reserved'
              AND expires_at <= NOW()
        ");
        $stmt->execute();
        $affected = $stmt->rowCount();

        // Um pedido ainda pendente não deve permanecer pagável depois que a
        // janela que protegia o estoque expirou. Não altera pedidos já pagos,
        // enviados, cancelados etc.
        $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
        $orderStmt = $db->prepare("
            UPDATE orders
            SET
                payment_status = 'expired',
                status = CASE
                    WHEN status = 'pending' THEN 'expired'
                    ELSE status
                END,
                updated_at = NOW()
            WHERE id IN ($placeholders)
              AND payment_status = 'pending'
        ");
        $orderStmt->execute($orderIds);

        // Mantém a tabela de pagamentos alinhada para que a tela de checkout
        // consiga perceber a expiração durante o polling local.
        try {
            $paymentStmt = $db->prepare("
                UPDATE payments
                SET status = 'expired', updated_at = NOW()
                WHERE order_id IN ($placeholders)
                  AND status IN ('pending', 'in_process')
            ");
            $paymentStmt->execute($orderIds);
        } catch (Throwable $e) {
            // Compatibilidade com instalações antigas de payments.
        }

        return $affected;
    }


    public static function hasExpiredForOrder(int $orderId, ?PDO $db = null): bool
    {
        $db = $db ?: Database::connect();
        if (!self::isAvailable($db)) return false;

        $stmt = $db->prepare("
            SELECT 1
            FROM stock_reservations
            WHERE order_id = ?
              AND status = 'expired'
            LIMIT 1
        ");
        $stmt->execute([$orderId]);
        return (bool)$stmt->fetchColumn();
    }

    public static function hasActiveForOrder(int $orderId, ?PDO $db = null): bool
    {
        $db = $db ?: Database::connect();
        if (!self::isAvailable($db)) return false;

        $stmt = $db->prepare("
            SELECT 1
            FROM stock_reservations
            WHERE order_id = ?
              AND status = 'reserved'
              AND expires_at > NOW()
            LIMIT 1
        ");
        $stmt->execute([$orderId]);
        return (bool)$stmt->fetchColumn();
    }

    public static function reserveForOrder(int $orderId, PDO $db): void
    {
        self::requireReservationSchema($db);
        self::cleanupExpired($db);

        $stmt = $db->prepare("\n            SELECT product_id, COALESCE(size, '') AS size, SUM(quantity) AS quantity\n            FROM order_items\n            WHERE order_id = ?\n            GROUP BY product_id, COALESCE(size, '')\n        ");
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$items) {
            throw new Exception('Pedido sem itens para reserva de estoque');
        }

        $expiresAt = date('Y-m-d H:i:s', time() + self::reservationMinutes() * 60);

        foreach ($items as $item) {
            $productId = (int)$item['product_id'];
            $size = (string)$item['size'];
            $qty = (int)$item['quantity'];

            $stockStmt = $db->prepare("\n                SELECT estoque\n                FROM products_stock\n                WHERE produto_id = ? AND COALESCE(tamanho, '') = ?\n                LIMIT 1\n                FOR UPDATE\n            ");
            $stockStmt->execute([$productId, $size]);
            $stock = $stockStmt->fetchColumn();

            if ($stock === false) {
                throw new Exception("Estoque não configurado para o produto {$productId}" . ($size !== '' ? " / tamanho {$size}" : ''));
            }

            $reservedStmt = $db->prepare("\n                SELECT COALESCE(SUM(quantity), 0)\n                FROM stock_reservations\n                WHERE product_id = ?\n                  AND COALESCE(size, '') = ?\n                  AND status = 'reserved'\n                  AND expires_at > NOW()\n                  AND order_id <> ?\n            ");
            $reservedStmt->execute([$productId, $size, $orderId]);
            $reserved = (int)$reservedStmt->fetchColumn();
            $available = (int)$stock - $reserved;

            if ($available < $qty) {
                throw new Exception("Estoque insuficiente para concluir o pedido" . ($size !== '' ? " (tamanho {$size})" : '') . ". Disponível: {$available}");
            }

            $upsert = $db->prepare("\n                INSERT INTO stock_reservations\n                    (order_id, product_id, size, quantity, status, expires_at, created_at, updated_at)\n                VALUES (?, ?, ?, ?, 'reserved', ?, NOW(), NOW())\n                ON DUPLICATE KEY UPDATE\n                    quantity = VALUES(quantity),\n                    status = 'reserved',\n                    expires_at = VALUES(expires_at),\n                    consumed_at = NULL,\n                    released_at = NULL,\n                    updated_at = NOW()\n            ");
            $upsert->execute([$orderId, $productId, $size, $qty, $expiresAt]);
        }
    }

    public static function extendForOrder(int $orderId, ?string $expiresAt = null, ?PDO $db = null): void
    {
        $db = $db ?: Database::connect();
        self::requireReservationSchema($db);
        if (!$expiresAt) {
            $expiresAt = date('Y-m-d H:i:s', time() + self::reservationMinutes() * 60);
        } else {
            $ts = strtotime($expiresAt);
            if ($ts !== false) $expiresAt = date('Y-m-d H:i:s', $ts);
        }

        $stmt = $db->prepare("\n            UPDATE stock_reservations\n            SET expires_at = ?, updated_at = NOW()\n            WHERE order_id = ? AND status = 'reserved'\n        ");
        $stmt->execute([$expiresAt, $orderId]);
    }

    public static function releaseForOrder(int $orderId, ?PDO $db = null, string $status = 'released'): void
    {
        $db = $db ?: Database::connect();
        if (!self::isAvailable($db)) return;
        $allowed = ['released', 'expired'];
        if (!in_array($status, $allowed, true)) $status = 'released';

        $stmt = $db->prepare("\n            UPDATE stock_reservations\n            SET status = ?, released_at = NOW(), updated_at = NOW()\n            WHERE order_id = ? AND status = 'reserved'\n        ");
        $stmt->execute([$status, $orderId]);
    }

    public static function consumeForOrder(int $orderId, PDO $db): void
    {
        self::requireReservationSchema($db);
        self::cleanupExpired($db);

        // Idempotência: um webhook/refresh repetido nunca pode baixar o mesmo pedido novamente.
        $done = $db->prepare("SELECT COUNT(*) FROM stock_reservations WHERE order_id = ? AND status = 'consumed'");
        $done->execute([$orderId]);
        if ((int)$done->fetchColumn() > 0) {
            return;
        }
        $legacyDone = $db->prepare("SELECT COUNT(*) FROM products_stock_movements WHERE order_id = ? AND tipo_movimento = 'saida'");
        $legacyDone->execute([$orderId]);
        if ((int)$legacyDone->fetchColumn() > 0) {
            return;
        }

        // Se o pedido foi criado antes da migration ou a reserva expirou, tenta recriar
        // a reserva de forma transacional antes de consumir.
        $check = $db->prepare("SELECT COUNT(*) FROM stock_reservations WHERE order_id = ? AND status = 'reserved' AND expires_at > NOW()");
        $check->execute([$orderId]);
        if ((int)$check->fetchColumn() === 0) {
            self::reserveForOrder($orderId, $db);
        }

        $stmt = $db->prepare("\n            SELECT id, product_id, COALESCE(size, '') AS size, quantity\n            FROM stock_reservations\n            WHERE order_id = ? AND status = 'reserved' AND expires_at > NOW()\n            ORDER BY id\n            FOR UPDATE\n        ");
        $stmt->execute([$orderId]);
        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$reservations) {
            throw new Exception('Reserva de estoque indisponível para o pedido');
        }

        foreach ($reservations as $reservation) {
            $productId = (int)$reservation['product_id'];
            $size = (string)$reservation['size'];
            $qty = (int)$reservation['quantity'];

            $stockStmt = $db->prepare("\n                SELECT estoque\n                FROM products_stock\n                WHERE produto_id = ? AND COALESCE(tamanho, '') = ?\n                LIMIT 1\n                FOR UPDATE\n            ");
            $stockStmt->execute([$productId, $size]);
            $stock = $stockStmt->fetchColumn();
            if ($stock === false || (int)$stock < $qty) {
                throw new Exception("Estoque físico insuficiente ao confirmar pagamento do pedido #{$orderId}");
            }

            $movementCheck = $db->prepare("\n                SELECT id FROM products_stock_movements\n                WHERE order_id = ? AND produto_id = ? AND COALESCE(tamanho, '') = ? AND tipo_movimento = 'saida'\n                LIMIT 1\n            ");
            $movementCheck->execute([$orderId, $productId, $size]);
            if ($movementCheck->fetchColumn()) {
                $db->prepare("UPDATE stock_reservations SET status='consumed', consumed_at=COALESCE(consumed_at,NOW()), updated_at=NOW() WHERE id=?")
                   ->execute([(int)$reservation['id']]);
                continue;
            }

            $db->prepare("\n                UPDATE products_stock\n                SET estoque = estoque - ?\n                WHERE produto_id = ? AND COALESCE(tamanho, '') = ? AND estoque >= ?\n            ")->execute([$qty, $productId, $size, $qty]);

            $db->prepare("\n                INSERT INTO products_stock_movements\n                    (order_id, produto_id, tamanho, tipo_movimento, quantidade, data_movimento, observacao, created_at)\n                VALUES (?, ?, ?, 'saida', ?, CURDATE(), ?, NOW())\n            ")->execute([$orderId, $productId, $size, $qty, "Venda confirmada - Pedido #{$orderId}"]);

            $db->prepare("\n                UPDATE stock_reservations\n                SET status='consumed', consumed_at=NOW(), updated_at=NOW()\n                WHERE id=?\n            ")->execute([(int)$reservation['id']]);
        }
    }
}
