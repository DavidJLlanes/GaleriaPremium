<?php
declare(strict_types=1);
namespace App\Core;

final class Router
{
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        // /galeria/{slug} -> regex con grupos con nombre
        $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern) . '/?$#';
        $this->routes['GET'][] = [$regex, $handler];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = rawurldecode(parse_url($uri, PHP_URL_PATH) ?: '/');
        foreach ($this->routes[$method] ?? [] as [$regex, $handler]) {
            if (preg_match($regex, $path, $m)) {
                $handler(array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY));
                return;
            }
        }
        Page::notFound();
    }
}
