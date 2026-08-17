<?php

require_once __DIR__ . '/../../config/database.php';

class IntegrationSettingsService
{
    private const SENSITIVE_KEYS = [
        'smtp_password',
        'evolution_api_key',
    ];

    public static function getGroup(string $group, bool $revealSecrets = false): array
    {
        $db = Database::connect();
        $stmt = $db->prepare('SELECT setting_key, setting_value, is_encrypted FROM integration_settings WHERE setting_group = ?');
        $stmt->execute([$group]);

        $result = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $key = (string) $row['setting_key'];
            $value = (string) ($row['setting_value'] ?? '');
            if ((int) $row['is_encrypted'] === 1 && $value !== '') {
                $value = self::decrypt($value);
            }
            if (!$revealSecrets && in_array($key, self::SENSITIVE_KEYS, true) && $value !== '') {
                $value = '********';
            }
            $result[$key] = $value;
        }
        return $result;
    }

    public static function get(string $group, string $key, ?string $default = null): ?string
    {
        $values = self::getGroup($group, true);
        return array_key_exists($key, $values) ? (string) $values[$key] : $default;
    }

    public static function saveGroup(string $group, array $values, ?int $adminId = null): void
    {
        $db = Database::connect();
        $sql = 'INSERT INTO integration_settings
            (setting_group, setting_key, setting_value, is_encrypted, updated_by)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
              setting_value = VALUES(setting_value),
              is_encrypted = VALUES(is_encrypted),
              updated_by = VALUES(updated_by),
              updated_at = CURRENT_TIMESTAMP';
        $stmt = $db->prepare($sql);

        foreach ($values as $key => $value) {
            $key = preg_replace('/[^a-z0-9_\-]/i', '', (string) $key);
            if ($key === '') continue;
            $value = is_bool($value) ? ($value ? '1' : '0') : trim((string) $value);
            $encrypted = in_array($key, self::SENSITIVE_KEYS, true);

            // Asteriscos significam manter o segredo já salvo.
            if ($encrypted && $value === '********') continue;

            $stored = $encrypted && $value !== '' ? self::encrypt($value) : $value;
            $stmt->execute([$group, $key, $stored, $encrypted ? 1 : 0, $adminId]);
        }
    }

    private static function encryptionKey(): string
    {
        $raw = $_ENV['APP_ENCRYPTION_KEY'] ?? $_ENV['JWT_SECRET'] ?? getenv('APP_ENCRYPTION_KEY') ?: getenv('JWT_SECRET');
        if (!$raw) throw new RuntimeException('APP_ENCRYPTION_KEY ou JWT_SECRET não configurado.');
        return hash('sha256', $raw, true);
    }

    private static function encrypt(string $plain): string
    {
        $iv = random_bytes(12);
        $tag = '';
        $cipher = openssl_encrypt($plain, 'aes-256-gcm', self::encryptionKey(), OPENSSL_RAW_DATA, $iv, $tag);
        if ($cipher === false) throw new RuntimeException('Falha ao criptografar configuração.');
        return base64_encode(json_encode([
            'iv' => base64_encode($iv),
            'tag' => base64_encode($tag),
            'data' => base64_encode($cipher),
        ], JSON_UNESCAPED_SLASHES));
    }

    private static function decrypt(string $encoded): string
    {
        $payload = json_decode(base64_decode($encoded), true);
        if (!is_array($payload)) return '';
        $plain = openssl_decrypt(
            base64_decode($payload['data'] ?? ''),
            'aes-256-gcm',
            self::encryptionKey(),
            OPENSSL_RAW_DATA,
            base64_decode($payload['iv'] ?? ''),
            base64_decode($payload['tag'] ?? '')
        );
        return $plain === false ? '' : $plain;
    }
}
