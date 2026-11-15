<?php
declare(strict_types=1);
namespace App\Core;

final class View
{
    /** Renderiza una vista dentro del layout. */
    public static function render(string $view, array $data = []): void
    {
        $content = self::partial($view, $data);
        echo self::partial('layout', $data + ['content' => $content]);
    }

    public static function partial(string $view, array $data = []): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require ROOT . '/app/Views/' . $view . '.php';
        return (string) ob_get_clean();
    }

    public static function e(?string $s): string
    {
        return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
