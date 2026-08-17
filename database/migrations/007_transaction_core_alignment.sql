-- OverGrace - alinhamento final do núcleo pedido/pagamento/estoque
-- MySQL 8.x. Execute uma vez no banco overgrace.
-- É defensiva: normaliza status e adiciona apenas estruturas auxiliares ausentes.

USE overgrace;

-- 1) Status financeiros internos passam a usar apenas:
-- pending | paid | failed | refunded | expired
-- VARCHAR evita novo erro de ENUM quando o gateway evoluir.
ALTER TABLE orders
    MODIFY COLUMN payment_status VARCHAR(30) NOT NULL DEFAULT 'pending';

UPDATE orders SET payment_status = 'paid'     WHERE payment_status = 'approved';
UPDATE orders SET payment_status = 'failed'   WHERE payment_status IN ('rejected','cancelled','canceled','chargeback','charged_back');
UPDATE orders SET payment_status = 'pending'  WHERE payment_status IS NULL OR payment_status = '';

ALTER TABLE payments
    MODIFY COLUMN status VARCHAR(30) NOT NULL DEFAULT 'pending';

UPDATE payments SET status = 'pending'  WHERE status IN ('processing','in_process','authorized','in_mediation');
UPDATE payments SET status = 'failed'   WHERE status IN ('rejected','cancelled','canceled','chargeback','charged_back');
UPDATE payments SET status = 'refunded' WHERE status = 'partially_refunded';

-- 2) paid_at é útil para auditoria/relatórios, mas não existia em bancos antigos.
SET @has_paid_at := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'payments'
      AND COLUMN_NAME = 'paid_at'
);
SET @sql := IF(
    @has_paid_at = 0,
    'ALTER TABLE payments ADD COLUMN paid_at DATETIME NULL',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Preenche paid_at para pagamentos antigos já marcados como pagos.
UPDATE payments
SET paid_at = COALESCE(paid_at, updated_at, created_at)
WHERE status = 'paid' AND paid_at IS NULL;

-- 3) Índices úteis para webhook/consulta do gateway.
SET @has_gateway_idx := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'payments'
      AND INDEX_NAME = 'idx_payments_gateway_payment'
);
SET @sql := IF(
    @has_gateway_idx = 0,
    'CREATE INDEX idx_payments_gateway_payment ON payments(gateway, gateway_payment_id)',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_webhook_idx := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'payment_webhooks'
      AND INDEX_NAME = 'idx_payment_webhooks_event'
);
SET @sql := IF(
    @has_webhook_idx = 0,
    'CREATE INDEX idx_payment_webhooks_event ON payment_webhooks(gateway, event_id)',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 4) A tabela criada pela reserva deve usar a mesma collation do banco legado.
-- Evita "Illegal mix of collations" nas comparações de tamanho/SKU.
ALTER TABLE stock_reservations
    CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE stock_reservations
    MODIFY COLUMN status VARCHAR(20) NOT NULL DEFAULT 'reserved';

-- 5) Recria o evento de expiração com os status internos normalizados.
SET GLOBAL event_scheduler = ON;
DROP EVENT IF EXISTS ev_expire_stock_reservations;

DELIMITER $$
CREATE EVENT ev_expire_stock_reservations
ON SCHEDULE EVERY 1 MINUTE
STARTS CURRENT_TIMESTAMP + INTERVAL 1 MINUTE
DO
BEGIN
    UPDATE stock_reservations
    SET
        status = 'expired',
        released_at = COALESCE(released_at, NOW()),
        updated_at = NOW()
    WHERE status = 'reserved'
      AND expires_at <= NOW();

    UPDATE orders o
    INNER JOIN (
        SELECT DISTINCT order_id
        FROM stock_reservations
        WHERE status = 'expired'
    ) r ON r.order_id = o.id
    SET
        o.payment_status = 'expired',
        o.status = CASE WHEN o.status = 'pending' THEN 'expired' ELSE o.status END,
        o.updated_at = NOW()
    WHERE o.payment_status = 'pending';

    UPDATE payments p
    INNER JOIN orders o ON o.id = p.order_id
    SET
        p.status = 'expired',
        p.updated_at = NOW()
    WHERE o.payment_status = 'expired'
      AND p.status = 'pending';
END$$
DELIMITER ;

-- Conferência sugerida após executar:
-- SHOW VARIABLES LIKE 'event_scheduler';
-- SHOW EVENTS FROM overgrace;
-- DESCRIBE payments;
-- SELECT DISTINCT payment_status FROM orders;
-- SELECT DISTINCT status FROM payments;
