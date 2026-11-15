<?php
declare(strict_types=1);
namespace App\Core;

final class Page
{
    /**
     * Renderiza un módulo de tipo "pages/*" dentro del layout principal.
     * $meta: title, bodyClass...
     */
    public static function render(string $page, array $data = [], array $meta = []): void
    {
        Module::use('layouts/main'); // el CSS base va siempre primero
        $content = Module::render($page, $data);
        $html = Module::render('layouts/main', $meta + ['content' => $content]);
        echo Assets::inject($html);
    }

    public static function notFound(): void
    {
        http_response_code(404);
        self::render('pages/not-found', [], ['title' => 'No encontrada']);
    }
}
