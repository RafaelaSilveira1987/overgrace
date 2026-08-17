-- Tabela de tokens de recuperacao para clientes e administradores.
-- Para instalacoes novas.
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    client_id BIGINT UNSIGNED NULL DEFAULT NULL,
    user_id BIGINT UNSIGNED NULL DEFAULT NULL,
    token_hash CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_password_reset_token_hash (token_hash),
    KEY idx_password_reset_client_id (client_id),
    KEY idx_password_reset_user_id (user_id),
    KEY idx_password_reset_expires_at (expires_at),
    KEY idx_password_reset_used_at (used_at)
);
