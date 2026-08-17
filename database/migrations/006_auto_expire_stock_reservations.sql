-- OverGrace - expiração automática de reservas de estoque
-- Execute uma vez no banco overgrace.
-- Em ambiente local com root/WAMP, o SET GLOBAL normalmente funciona.
-- Em produção, se o usuário MySQL não tiver permissão para habilitar eventos,
-- habilite event_scheduler pelo painel/servidor e execute apenas o CREATE EVENT.

USE overgrace;

SET GLOBAL event_scheduler = ON;

DROP EVENT IF EXISTS ev_expire_stock_reservations;

DELIMITER $$
CREATE EVENT ev_expire_stock_reservations
ON SCHEDULE EVERY 1 MINUTE
STARTS CURRENT_TIMESTAMP + INTERVAL 1 MINUTE
DO
BEGIN
    -- Expira a reserva física/lógica.
    UPDATE stock_reservations
    SET
        status = 'expired',
        released_at = COALESCE(released_at, NOW()),
        updated_at = NOW()
    WHERE status = 'reserved'
      AND expires_at <= NOW();

    -- Pedido ainda pendente deixa de permanecer pagável depois da janela.
    UPDATE orders o
    INNER JOIN (
        SELECT DISTINCT order_id
        FROM stock_reservations
        WHERE status = 'expired'
          AND released_at IS NOT NULL
    ) r ON r.order_id = o.id
    SET
        o.payment_status = 'expired',
        o.status = CASE
            WHEN o.status = 'pending' THEN 'expired'
            ELSE o.status
        END,
        o.updated_at = NOW()
    WHERE o.payment_status = 'pending';

    -- A tela do checkout consulta payments durante o polling.
    -- Portanto o pagamento local também deve refletir a expiração.
    UPDATE payments p
    INNER JOIN orders o ON o.id = p.order_id
    SET
        p.status = 'expired',
        p.updated_at = NOW()
    WHERE o.payment_status = 'expired'
      AND p.status IN ('pending', 'in_process');
END$$
DELIMITER ;

-- Conferências úteis:
-- SHOW VARIABLES LIKE 'event_scheduler';
-- SHOW EVENTS FROM overgrace;
