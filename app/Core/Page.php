<?php
declare(strict_types=1);
namespace App\Core;

final class Page
{
    /**
     * Renderiza un módulo "pages/*" dentro de un layout (por defecto layouts/main).
     * $meta: title, bodyClass...
     */
    public static function render(string $page, array $data = [], array $meta = [], string $layout = 'layouts/main'): void
    {
        Module::use('layouts/base'); // tokens + reset: siempre primero
        Module::use($layout);        // el layout antes que la página (el JS del admin define window.Admin)
        $content = Module::render($page, $data);
        $html = Assets::inject(Module::render($layout, $meta + ['content' => $content]));
        echo $layout === 'layouts/main' ? \App\Services\Lqip::inject($html) : $html;
    }

    public static function notFound(): void
    {
        http_response_code(404);
        self::render('pages/not-found', [], ['title' => 'No encontrada']);
    }
}
