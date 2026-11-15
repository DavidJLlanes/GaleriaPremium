<?php
// SOLO DESARROLLO:  php -S 127.0.0.1:8000 -t public public/router.php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Assets de módulos servidos directamente desde /modules (sin necesidad de publicar)
if (preg_match('#^/assets/m/([a-z0-9-]+/[a-z0-9-]+)/(style\.css|script\.js)$#', $path, $m)) {
    $f = dirname(__DIR__) . "/modules/{$m[1]}/{$m[2]}";
    if (is_file($f)) {
        header('Content-Type: ' . ($m[2] === 'style.css' ? 'text/css' : 'application/javascript'));
        readfile($f); return true;
    }
}
if (is_file(__DIR__ . $path)) return false;
require __DIR__ . '/index.php';
