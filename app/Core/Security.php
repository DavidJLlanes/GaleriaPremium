<?php
declare(strict_types=1);
namespace App\Core;

/**
 * Endurecimiento común de todas las respuestas dinámicas (HTML, JSON, redirecciones):
 * cabeceras de seguridad, errores sin detalles en producción y registro en el log.
 */
final class Security
{
    public static function boot(bool $debug): void
    {
        if (!$debug) { ini_set('display_errors', '0'); ini_set('display_startup_errors', '0'); }
        ini_set('log_errors', '1');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_httponly', '1');

        if (PHP_SAPI === 'cli') return;

        // Cualquier error no controlado: página genérica (nunca rutas ni trazas) + log para el administrador
        set_exception_handler(static function (\Throwable $e) use ($debug): void {
            error_log(sprintf('[galeria] %s: %s en %s:%d', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()));
            if (!headers_sent()) { http_response_code(500); header('Content-Type: text/html; charset=utf-8'); }
            echo $debug ? '<pre>' . htmlspecialchars((string) $e) . '</pre>'
                : '<!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title>Error</title><body style="font:16px system-ui;background:#0b0b0c;color:#ddd;display:grid;place-items:center;min-height:100vh;margin:0"><p>Algo ha fallado. Inténtalo de nuevo en unos segundos.</p>';
        });

        if (headers_sent()) return;
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=(), interest-cohort=()');
        header('Cross-Origin-Opener-Policy: same-origin');
        header('X-Permitted-Cross-Domain-Policies: none');
        // Todo se sirve desde el propio dominio (tipografías y fotos incluidas). 'unsafe-inline' solo por los <style>/<script> JSON
        // y los pocos manejadores onsubmit/onchange del panel; no se carga nada de terceros ni se permite <object>/<base> ajenos.
        header("Content-Security-Policy: default-src 'self'; img-src 'self' data: blob:; media-src 'self' blob:; font-src 'self' data:; "
            . "style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline'; connect-src 'self'; object-src 'none'; "
            . "base-uri 'self'; form-action 'self'; frame-ancestors 'self'; manifest-src 'self'");
        if ($https) header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        // El panel y las respuestas con sesión nunca se guardan en cachés compartidas
        if (str_starts_with((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/admin')) {
            header('Cache-Control: no-store');
            header('X-Robots-Tag: noindex, nofollow');
        }
    }
}
