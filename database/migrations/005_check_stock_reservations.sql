-- Diagnóstico do schema de reserva de estoque.
-- Este arquivo NÃO altera dados. Execute para conferir a estrutura atual.

USE overgrace;

SELECT TABLE_NAME
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'stock_reservations';

SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'stock_reservations'
ORDER BY ORDINAL_POSITION;

SELECT INDEX_NAME, COLUMN_NAME, SEQ_IN_INDEX, NON_UNIQUE
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'stock_reservations'
ORDER BY INDEX_NAME, SEQ_IN_INDEX;

-- Estrutura esperada pelo código atual:
-- id
-- order_id
-- product_id
-- size
-- quantity
-- status
-- expires_at
-- consumed_at
-- released_at
-- created_at
-- updated_at
