<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../integrations/IntegrationSettingsService.php';
require_once __DIR__ . '/../integrations/SmtpMailer.php';
require_once __DIR__ . '/../../helpers/logger.php';

class NotificationService
{
    public static function event(string $eventType, array $context = []): void
    {
        try {
            $template = self::template($eventType, $context);
            if (!$template) return;

            $db = Database::connect();
            self::createPanelNotification($db, $eventType, $template, $context);
            self::queueAdminEmails($db, $eventType, $template, $context);

            $sync = strtolower((string)($_ENV['NOTIFICATION_SYNC_SEND'] ?? getenv('NOTIFICATION_SYNC_SEND') ?: '1'));
            if (in_array($sync, ['1', 'true', 'yes', 'on'], true)) {
                self::processPending(5);
            }
        } catch (Throwable $e) {
            Logger::error('[NOTIFICATION EVENT] ' . $e->getMessage());
        }
    }

    public static function processPending(int $limit = 20): array
    {
        $db = Database::connect();
        $config = IntegrationSettingsService::getGroup('smtp', true);
        $enabled = (string)($config['smtp_enabled'] ?? '0') === '1';

        if (!$enabled) {
            return ['processed' => 0, 'sent' => 0, 'failed' => 0, 'message' => 'SMTP desativado'];
        }

        $limit = max(1, min(100, $limit));
        $stmt = $db->query("SELECT * FROM notification_queue WHERE status IN ('pending','retry') AND scheduled_at <= NOW() ORDER BY id ASC LIMIT {$limit}");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $sent = 0;
        $failed = 0;

        foreach ($rows as $row) {
            $id = (int)$row['id'];
            try {
                $db->prepare("UPDATE notification_queue SET status='processing', attempts=attempts+1 WHERE id=?")
                   ->execute([$id]);
                SmtpMailer::send(
                    $config,
                    (string)$row['recipient'],
                    (string)($row['subject'] ?: 'Notificação OverGrace'),
                    (string)$row['message']
                );
                $db->prepare("UPDATE notification_queue SET status='sent', sent_at=NOW(), last_error=NULL WHERE id=?")
                   ->execute([$id]);
                $sent++;
            } catch (Throwable $e) {
                $failed++;
                $nextStatus = ((int)$row['attempts'] + 1) >= 5 ? 'failed' : 'retry';
                $db->prepare("UPDATE notification_queue SET status=?, last_error=?, scheduled_at=DATE_ADD(NOW(), INTERVAL 5 MINUTE) WHERE id=?")
                   ->execute([$nextStatus, substr($e->getMessage(), 0, 2000), $id]);
                Logger::error('[NOTIFICATION SEND #' . $id . '] ' . $e->getMessage());
            }
        }

        return ['processed' => count($rows), 'sent' => $sent, 'failed' => $failed];
    }

    public static function listPanel(int $limit = 50, bool $unreadOnly = false): array
    {
        $db = Database::connect();
        $where = $unreadOnly ? 'WHERE read_at IS NULL' : '';
        $limit = max(1, min(100, $limit));
        $rows = $db->query("SELECT * FROM admin_notifications {$where} ORDER BY created_at DESC LIMIT {$limit}")
                   ->fetchAll(PDO::FETCH_ASSOC);
        $unread = (int)$db->query("SELECT COUNT(*) FROM admin_notifications WHERE read_at IS NULL")->fetchColumn();
        return ['data' => $rows, 'unread' => $unread];
    }

    public static function markRead(?int $id = null): void
    {
        $db = Database::connect();
        if ($id) {
            $db->prepare('UPDATE admin_notifications SET read_at=COALESCE(read_at,NOW()) WHERE id=?')->execute([$id]);
        } else {
            $db->exec('UPDATE admin_notifications SET read_at=COALESCE(read_at,NOW()) WHERE read_at IS NULL');
        }
    }

    private static function createPanelNotification(PDO $db, string $eventType, array $template, array $context): void
    {
        if (!self::tableExists($db, 'admin_notifications')) return;
        $stmt = $db->prepare('INSERT INTO admin_notifications (event_type,title,message,entity_type,entity_id,action_url,metadata) VALUES (?,?,?,?,?,?,?)');
        $stmt->execute([
            $eventType,
            $template['title'],
            $template['text'],
            $context['entity_type'] ?? 'order',
            $context['order_id'] ?? $context['entity_id'] ?? null,
            $context['action_url'] ?? (!empty($context['order_id']) ? '/orders?order=' . (int)$context['order_id'] : null),
            json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    }

    private static function queueAdminEmails(PDO $db, string $eventType, array $template, array $context): void
    {
        if (!self::tableExists($db, 'notification_queue')) return;
        $config = IntegrationSettingsService::getGroup('smtp', true);
        if ((string)($config['smtp_enabled'] ?? '0') !== '1') return;

        $recipients = preg_split('/[,;\s]+/', (string)($config['smtp_admin_recipients'] ?? ''), -1, PREG_SPLIT_NO_EMPTY);
        $recipients = array_values(array_unique(array_filter(array_map('trim', $recipients), fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL))));
        if (!$recipients) return;

        $payload = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $stmt = $db->prepare('INSERT INTO notification_queue (event_type,channel,recipient,subject,message,payload,status) VALUES (?,\'email\',?,?,?,?,\'pending\')');
        foreach ($recipients as $recipient) {
            $stmt->execute([$eventType, $recipient, $template['subject'], $template['html'], $payload]);
        }
    }

    private static function template(string $eventType, array $c): ?array
    {
        $orderId = (int)($c['order_id'] ?? 0);
        $orderCode = $orderId ? '#OVG-' . str_pad((string)$orderId, 5, '0', STR_PAD_LEFT) : 'OverGrace';
        $customer = htmlspecialchars((string)($c['customer_name'] ?? 'Cliente'), ENT_QUOTES, 'UTF-8');
        $amount = isset($c['amount']) ? 'R$ ' . number_format((float)$c['amount'], 2, ',', '.') : null;
        $method = strtoupper((string)($c['method'] ?? ''));

        $map = [
            'order.created' => ['Novo pedido recebido', "O pedido {$orderCode} foi criado por {$customer}.", 'Novo pedido — ' . $orderCode],
            'payment.paid' => ['Pagamento aprovado', "O pagamento do pedido {$orderCode} foi aprovado" . ($method ? " via {$method}" : '') . '.', 'Pagamento aprovado — ' . $orderCode],
            'payment.review' => ['Pagamento exige revisão', "O pedido {$orderCode} foi pago após a reserva expirar. Verifique o estoque antes de atender.", 'Atenção: pagamento em revisão — ' . $orderCode],
            'payment.failed' => ['Pagamento não aprovado', "O pagamento do pedido {$orderCode} foi recusado ou cancelado.", 'Pagamento não aprovado — ' . $orderCode],
            'order.expired' => ['Pedido expirado', "O prazo de pagamento do pedido {$orderCode} terminou e a reserva foi liberada.", 'Pedido expirado — ' . $orderCode],
            'order.canceled' => ['Pedido cancelado', "O pedido {$orderCode} foi cancelado.", 'Pedido cancelado — ' . $orderCode],
            'payment.refunded' => ['Pagamento reembolsado', "O pagamento do pedido {$orderCode} foi reembolsado.", 'Reembolso — ' . $orderCode],
            'stock.low' => ['Estoque baixo', (string)($c['message'] ?? 'Um produto atingiu o estoque mínimo.'), 'Alerta de estoque baixo'],
        ];
        if (!isset($map[$eventType])) return null;
        [$title, $text, $subject] = $map[$eventType];
        $safeText = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $amountRow = $amount ? '<tr><td style="padding:8px 0;color:#776f67">Valor</td><td style="padding:8px 0;text-align:right;font-weight:600">' . $amount . '</td></tr>' : '';
        $html = '<!doctype html><html><body style="margin:0;background:#f2ede6;color:#1d1a17;font-family:Arial,sans-serif"><div style="max-width:620px;margin:32px auto;background:#fff;border:1px solid #ded6cd"><div style="padding:26px 32px;background:#1d1a17;color:#f6efe7;letter-spacing:4px;text-align:center;font-family:Georgia,serif;font-size:21px">OVERGRACE</div><div style="padding:34px 32px"><div style="font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#8b8178">Notificação administrativa</div><h1 style="font-family:Georgia,serif;font-size:28px;margin:12px 0 16px">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1><p style="line-height:1.7;color:#514b46">' . $safeText . '</p><table style="width:100%;margin-top:22px;border-top:1px solid #e7e0d8">' . ($orderId ? '<tr><td style="padding:14px 0;color:#776f67">Pedido</td><td style="padding:14px 0;text-align:right;font-weight:600">' . $orderCode . '</td></tr>' : '') . $amountRow . '</table></div><div style="padding:18px 32px;background:#f7f3ee;color:#8b8178;font-size:12px">Mensagem automática da plataforma OverGrace.</div></div></body></html>';
        return compact('title', 'text', 'subject', 'html');
    }

    private static function tableExists(PDO $db, string $table): bool
    {
        try {
            $stmt = $db->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=DATABASE() AND table_name=?');
            $stmt->execute([$table]);
            return (bool)$stmt->fetchColumn();
        } catch (Throwable $e) {
            return false;
        }
    }
}
