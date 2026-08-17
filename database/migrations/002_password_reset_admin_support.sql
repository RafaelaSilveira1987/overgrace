-- Execute SOMENTE se password_reset_tokens ja foi criada pela migracao anterior
-- que possuia client_id como NOT NULL e ainda nao possuia user_id.

ALTER TABLE password_reset_tokens
    MODIFY client_id BIGINT UNSIGNED NULL DEFAULT NULL;

ALTER TABLE password_reset_tokens
    ADD COLUMN user_id BIGINT UNSIGNED NULL DEFAULT NULL AFTER client_id,
    ADD INDEX idx_password_reset_user_id (user_id);
