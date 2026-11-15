<?php
declare(strict_types=1);

define('ROOT', dirname(__DIR__));

spl_autoload_register(static function (string $class): void {
    if (!str_starts_with($class, 'App\\')) return;
    $file = ROOT . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    if (is_file($file)) require $file;
});

require __DIR__ . '/helpers.php';

\App\Core\Config::load(require ROOT . '/config/config.php');

if (\App\Core\Config::get('app.debug')) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}
