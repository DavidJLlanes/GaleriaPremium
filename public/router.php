<?php
// Solo desarrollo: php -S localhost:8000 -t public public/router.php
$f = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (is_file($f)) return false;
require __DIR__ . '/index.php';
