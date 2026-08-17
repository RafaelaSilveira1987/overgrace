-- OverGrace - fechamento do fluxo carrinho -> pedido -> pagamento
-- MySQL 8.x. Migration defensiva para o estado atual do projeto.

USE overgrace;

-- 1) Snapshot da modalidade de entrega no próprio pedido.
SET @has_shipping_method := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'shipping_method'
);
SET @sql := IF(@has_shipping_method = 0,
    'ALTER TABLE orders ADD COLUMN shipping_method VARCHAR(30) NULL AFTER shipping',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_shipping_label := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'shipping_label'
);
SET @sql := IF(@has_shipping_label = 0,
    'ALTER TABLE orders ADD COLUMN shipping_label VARCHAR(100) NULL AFTER shipping_method',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2) Snapshot do endereço usado no pedido.
CREATE TABLE IF NOT EXISTS order_addresses (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    order_id BIGINT UNSIGNED NOT NULL,
    cep VARCHAR(10) NULL,
    endereco VARCHAR(255) NULL,
    numero VARCHAR(20) NULL,
    bairro VARCHAR(120) NULL,
    complemento VARCHAR(150) NULL,
    cidade VARCHAR(120) NULL,
    estado VARCHAR(2) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_order_addresses_order (order_id),
    CONSTRAINT fk_order_addresses_order
        FOREIGN KEY (order_id) REFERENCES orders(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3) Garante campos usados pelo PaymentModel atual.
SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payments' AND COLUMN_NAME='uuid');
SET @sql := IF(@col=0, 'ALTER TABLE payments ADD COLUMN uuid CHAR(36) NULL AFTER id', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payments' AND COLUMN_NAME='gateway');
SET @sql := IF(@col=0, "ALTER TABLE payments ADD COLUMN gateway VARCHAR(30) NOT NULL DEFAULT 'mercadopago' AFTER order_id", 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payments' AND COLUMN_NAME='gateway_payment_id');
SET @sql := IF(@col=0, 'ALTER TABLE payments ADD COLUMN gateway_payment_id VARCHAR(255) NULL AFTER gateway', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payments' AND COLUMN_NAME='gateway_customer_id');
SET @sql := IF(@col=0, 'ALTER TABLE payments ADD COLUMN gateway_customer_id VARCHAR(255) NULL AFTER gateway_payment_id', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payments' AND COLUMN_NAME='fee');
SET @sql := IF(@col=0, 'ALTER TABLE payments ADD COLUMN fee DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER amount', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payments' AND COLUMN_NAME='net_amount');
SET @sql := IF(@col=0, 'ALTER TABLE payments ADD COLUMN net_amount DECIMAL(10,2) NULL AFTER fee', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payments' AND COLUMN_NAME='currency');
SET @sql := IF(@col=0, "ALTER TABLE payments ADD COLUMN currency VARCHAR(10) NOT NULL DEFAULT 'BRL' AFTER net_amount", 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payments' AND COLUMN_NAME='installments');
SET @sql := IF(@col=0, 'ALTER TABLE payments ADD COLUMN installments INT NOT NULL DEFAULT 1 AFTER currency', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payments' AND COLUMN_NAME='qr_code');
SET @sql := IF(@col=0, 'ALTER TABLE payments ADD COLUMN qr_code LONGTEXT NULL AFTER installments', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payments' AND COLUMN_NAME='qr_code_base64');
SET @sql := IF(@col=0, 'ALTER TABLE payments ADD COLUMN qr_code_base64 LONGTEXT NULL AFTER qr_code', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payments' AND COLUMN_NAME='pix_copy_paste');
SET @sql := IF(@col=0, 'ALTER TABLE payments ADD COLUMN pix_copy_paste LONGTEXT NULL AFTER qr_code_base64', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payments' AND COLUMN_NAME='boleto_url');
SET @sql := IF(@col=0, 'ALTER TABLE payments ADD COLUMN boleto_url TEXT NULL AFTER pix_copy_paste', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payments' AND COLUMN_NAME='boleto_barcode');
SET @sql := IF(@col=0, 'ALTER TABLE payments ADD COLUMN boleto_barcode VARCHAR(255) NULL AFTER boleto_url', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payments' AND COLUMN_NAME='authorization_code');
SET @sql := IF(@col=0, 'ALTER TABLE payments ADD COLUMN authorization_code VARCHAR(255) NULL AFTER boleto_barcode', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payments' AND COLUMN_NAME='expires_at');
SET @sql := IF(@col=0, 'ALTER TABLE payments ADD COLUMN expires_at DATETIME NULL AFTER authorization_code', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payments' AND COLUMN_NAME='gateway_response');
SET @sql := IF(@col=0, 'ALTER TABLE payments ADD COLUMN gateway_response LONGTEXT NULL AFTER expires_at', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payments' AND COLUMN_NAME='paid_at');
SET @sql := IF(@col=0, 'ALTER TABLE payments ADD COLUMN paid_at DATETIME NULL AFTER gateway_response', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE payments SET uuid = UUID() WHERE uuid IS NULL OR uuid='';
ALTER TABLE payments MODIFY COLUMN uuid CHAR(36) NOT NULL;
ALTER TABLE payments MODIFY COLUMN status VARCHAR(30) NOT NULL DEFAULT 'pending';

-- Índices defensivos.
SET @idx := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payments' AND INDEX_NAME='uq_payments_uuid');
SET @sql := IF(@idx=0, 'CREATE UNIQUE INDEX uq_payments_uuid ON payments(uuid)', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payments' AND INDEX_NAME='idx_payments_gateway_payment');
SET @sql := IF(@idx=0, 'CREATE INDEX idx_payments_gateway_payment ON payments(gateway,gateway_payment_id)', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 4) Webhook idempotente por gateway/evento.
DELETE w1 FROM payment_webhooks w1
INNER JOIN payment_webhooks w2
    ON w1.gateway <=> w2.gateway
   AND w1.event_id = w2.event_id
   AND w1.id > w2.id
WHERE w1.event_id IS NOT NULL;

SET @idx := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payment_webhooks' AND INDEX_NAME='uq_payment_webhook_event');
SET @sql := IF(@idx=0, 'CREATE UNIQUE INDEX uq_payment_webhook_event ON payment_webhooks(gateway,event_id)', 'SELECT 1'); PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 5) Histórico de status precisa de PK/autoincrement para auditoria confiável.
SET @has_pk := (
    SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
    WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='order_status_history' AND CONSTRAINT_TYPE='PRIMARY KEY'
);
SET @sql := IF(@has_pk=0,
    'ALTER TABLE order_status_history MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY(id)',
    'ALTER TABLE order_status_history MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Conferência:
-- DESCRIBE orders;
-- DESCRIBE order_addresses;
-- DESCRIBE payments;
-- SHOW INDEX FROM payment_webhooks;
