<?php
declare(strict_types=1);
namespace App\Services;

/** Mensajes de una sola visualización (tras redirecciones). */
final class Flash
{
    public static function set(string $message, string $type = 'ok'): void
    {
        Auth::start();
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
    }

    public static function pull(): ?array
    {
        Auth::start();
        $f = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $f;
    }
}
