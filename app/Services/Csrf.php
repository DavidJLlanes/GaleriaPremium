<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\Response;

final class Csrf
{
    public static function token(): string
    {
        Auth::start();
        return $_SESSION['csrf'] ??= bin2hex(random_bytes(24));
    }

    /** Verifica el token (campo _csrf del formulario o cabecera X-CSRF-Token). Aborta con 419 si falla. */
    public static function verify(): void
    {
        $sent = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!is_string($sent) || !hash_equals(self::token(), $sent)) {
            if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'json') || isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                Response::json(['ok' => false, 'error' => 'Token de seguridad caducado. Recarga la página.'], 419);
            }
            http_response_code(419);
            exit('Token de seguridad caducado. Vuelve atrás y recarga la página.');
        }
    }
}
