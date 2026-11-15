<?php
declare(strict_types=1);
/**
 * Crea o actualiza un administrador.
 *   php bin/admin.php <usuario> [contraseña]
 * Si omites la contraseña se pide por teclado (no queda en el historial del shell).
 */
require __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;
use App\Models\User;

if (empty($argv[1])) { fwrite(STDERR, "Uso: php bin/admin.php <usuario> [contraseña]\n"); exit(1); }
Database::pdo()->exec(file_get_contents(ROOT . '/database/schema.sql'));

$user = $argv[1];
$pass = $argv[2] ?? null;
if ($pass === null) {
    fwrite(STDOUT, 'Contraseña: ');
    @system('stty -echo 2>/dev/null'); $pass = trim((string) fgets(STDIN)); @system('stty echo 2>/dev/null');
    fwrite(STDOUT, "\n");
}
if (strlen($pass) < 8) { fwrite(STDERR, "La contraseña debe tener al menos 8 caracteres.\n"); exit(1); }

User::save($user, $pass);
echo "✔ Administrador '$user' listo → /admin\n";
