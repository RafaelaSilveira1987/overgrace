-- OverGrace - suporte seguro aos status administrativos de pedido
-- Mantém os valores existentes e remove limitação de ENUM que pode gerar
-- "Data truncated for column status" ao selecionar novos status no painel.

ALTER TABLE orders
    MODIFY COLUMN status VARCHAR(30) NOT NULL DEFAULT 'pending';
