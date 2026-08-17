-- OverGrace - reserva temporária e baixa idempotente de estoque
-- Execute UMA VEZ no banco overgrace.

CREATE TABLE stock_reservations (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    size VARCHAR(60) NOT NULL DEFAULT '',
    quantity INT UNSIGNED NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'reserved',
    expires_at DATETIME NOT NULL,
    consumed_at DATETIME NULL,
    released_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_stock_reservation_order_sku (order_id, product_id, size),
    KEY idx_stock_reservation_sku (product_id, size, status, expires_at),
    KEY idx_stock_reservation_order (order_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE products_stock_movements
    ADD COLUMN order_id BIGINT UNSIGNED NULL AFTER id;

CREATE INDEX idx_stock_movements_order
    ON products_stock_movements(order_id);
