-- OverGrace - alinhamento dos métodos de pagamento
-- MySQL 8.x. Pode ser executada uma vez no banco overgrace.

USE overgrace;

-- O schema legado aceitava apenas pix e credit_card.
-- O fluxo atual também usa boleto e pode receber novos meios do gateway.
ALTER TABLE payments
    MODIFY COLUMN method VARCHAR(30)
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci
    NOT NULL DEFAULT 'pix';

-- Normaliza aliases antigos, caso existam.
UPDATE payments SET method = 'credit_card' WHERE method IN ('card', 'cartao');
UPDATE payments SET method = 'boleto' WHERE method = 'ticket';

CREATE INDEX idx_payments_order_method_status
    ON payments(order_id, method, status);
