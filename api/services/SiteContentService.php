<?php

require_once __DIR__ . '/../config/database.php';

class SiteContentService
{
    private static function db(): PDO
    {
        return Database::connect();
    }

    public static function getAll(bool $activeOnly = false): array
    {
        $where = $activeOnly ? 'WHERE active = 1' : '';

        $stmt = self::db()->prepare("\n            SELECT\n                id,\n                section_key,\n                field_key,\n                field_type,\n                label,\n                value,\n                sort_order,\n                active\n            FROM site_content\n            {$where}\n            ORDER BY section_key ASC, sort_order ASC, id ASC\n        ");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function grouped(bool $activeOnly = true): array
    {
        $rows = self::getAll($activeOnly);
        $grouped = [];

        foreach ($rows as $row) {
            $grouped[$row['section_key']][$row['field_key']] = [
                'id' => (int) $row['id'],
                'type' => $row['field_type'],
                'label' => $row['label'],
                'value' => $row['value'],
                'active' => (int) $row['active'],
            ];
        }

        return $grouped;
    }

    public static function updateValue(string $sectionKey, string $fieldKey, ?string $value): bool
    {
        $stmt = self::db()->prepare("\n            UPDATE site_content\n            SET value = :value\n            WHERE section_key = :section_key\n              AND field_key = :field_key\n            LIMIT 1\n        ");

        return $stmt->execute([
            ':value' => $value,
            ':section_key' => $sectionKey,
            ':field_key' => $fieldKey,
        ]);
    }
}
