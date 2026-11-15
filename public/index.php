<?php
declare(strict_types=1);

require __DIR__ . '/../app/bootstrap.php';

use App\Controllers\{HomeController, GalleryController};
use App\Controllers\Admin\{AuthController, GalleryController as AdminGallery, PhotoController, SettingsController};
use App\Core\Router;

$router = new Router();
$router->get('/', [new HomeController(), 'index']);
$router->get('/galeria/{slug}', [new GalleryController(), 'show']);

// ---- Panel de administración ----
$auth = new AuthController(); $ag = new AdminGallery(); $ph = new PhotoController(); $st = new SettingsController();
$router->get('/admin',                       [$ag, 'index']);
$router->get('/admin/login',                 [$auth, 'form']);
$router->post('/admin/login',                [$auth, 'login']);
$router->post('/admin/logout',               [$auth, 'logout']);
$router->post('/admin/galerias',             [$ag, 'create']);
$router->get('/admin/galeria/{id}',          [$ag, 'show']);
$router->post('/admin/galeria/{id}/editar',  [$ag, 'update']);
$router->post('/admin/galeria/{id}/borrar',  [$ag, 'delete']);
$router->post('/admin/galeria/{id}/subir',   [$ag, 'upload']);
$router->post('/admin/foto/{id}/editar',     [$ph, 'update']);
$router->post('/admin/foto/{id}/ia',         [$ph, 'ai']);
$router->post('/admin/foto/{id}/portada',    [$ph, 'cover']);
$router->post('/admin/foto/{id}/borrar',     [$ph, 'delete']);
$router->get('/admin/ajustes',               [$st, 'show']);
$router->post('/admin/ajustes',              [$st, 'save']);
$router->post('/admin/ajustes/probar',       [$st, 'test']);

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
