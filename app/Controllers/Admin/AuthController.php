<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\{Page, Response};
use App\Services\{Auth, Csrf, Flash};

final class AuthController
{
    public function form(array $p = []): void
    {
        if (Auth::check()) Response::redirect('/admin');
        header('Cache-Control: private, no-cache, must-revalidate');   // sin «no-store»: el navegador puede ofrecer guardar la contraseña
        Page::render('pages/admin-login', [], ['title' => 'Acceso', 'bare' => true], 'layouts/admin');
    }

    public function login(array $p = []): void
    {
        Auth::start(); Csrf::verify();
        if (Auth::blocked()) {
            Flash::set('Demasiados intentos. Espera 10 minutos.', 'error');
            Response::redirect('/admin/login');
        }
        if (Auth::attempt((string) ($_POST['username'] ?? ''), (string) ($_POST['password'] ?? ''))) {
            Response::redirect('/admin');
        }
        usleep(400000);
        Flash::set('Usuario o contraseña incorrectos.', 'error');
        Response::redirect('/admin/login');
    }

    public function logout(array $p = []): void
    {
        Auth::start(); Csrf::verify();
        Auth::logout();
        Response::redirect('/admin/login');
    }
}
