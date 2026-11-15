<?php
declare(strict_types=1);

require __DIR__ . '/../app/bootstrap.php';

use App\Controllers\{HomeController, GalleryController};
use App\Core\Router;

$router = new Router();
$router->get('/', [new HomeController(), 'index']);
$router->get('/galeria/{slug}', [new GalleryController(), 'show']);

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
