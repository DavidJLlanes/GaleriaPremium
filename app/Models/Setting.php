<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Database;

final class Setting
{
    public static function get(string $key, string $default = ''): string
    {
        $row = Database::one('SELECT value FROM settings WHERE key = ?', [$key]);
        return $row['value'] ?? $default;
    }

    public static function set(string $key, string $value): void
    {
        Database::pdo()->prepare(
            'INSERT INTO settings (key, value) VALUES (?, ?) ON CONFLICT(key) DO UPDATE SET value = excluded.value'
        )->execute([$key, $value]);
    }
}
