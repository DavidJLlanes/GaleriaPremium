<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Database;

final class User
{
    public static function findByUsername(string $username): ?array
    {
        return Database::one('SELECT * FROM users WHERE username = ?', [$username]);
    }

    public static function save(string $username, string $password): void
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        Database::pdo()->prepare(
            'INSERT INTO users (username, password_hash) VALUES (?, ?)
             ON CONFLICT(username) DO UPDATE SET password_hash = excluded.password_hash'
        )->execute([$username, $hash]);
    }
}
