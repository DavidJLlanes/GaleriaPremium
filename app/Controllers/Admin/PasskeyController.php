<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\{Page, Response};
use App\Services\{Auth, Csrf, Flash, Passkeys};

/** Acceso con huella / Face ID (passkeys) y gestión de los dispositivos registrados. */
final class PasskeyController
{
    private function body(): array
    {
        $d = json_decode((string) file_get_contents('php://input'), true);
        return is_array($d) ? $d : [];
    }

    public function page(array $p = []): void
    {
        Auth::guard();
        Page::render('pages/admin-access', ['keys' => Passkeys::all(Auth::userId())], ['title' => 'Acceso', 'nav' => 'acceso'], 'layouts/admin');
    }

    /** Público: reto para entrar. */
    public function loginOptions(array $p = []): void
    {
        Auth::start(); Csrf::verify();
        if (Auth::blocked()) Response::json(['ok' => false, 'error' => 'Demasiados intentos. Espera 10 minutos.'], 429);
        Response::json(['ok' => true, 'publicKey' => Passkeys::loginOptions()]);
    }

    /** Público: comprueba la firma e inicia sesión. */
    public function login(array $p = []): void
    {
        Auth::start(); Csrf::verify();
        $err = Passkeys::login($this->body());
        if ($err) { usleep(300000); Response::json(['ok' => false, 'error' => $err], 401); }
        Response::json(['ok' => true, 'redirect' => '/admin']);
    }

    public function registerOptions(array $p = []): void
    {
        Auth::guard(true); Csrf::verify();
        Response::json(['ok' => true, 'publicKey' => Passkeys::registerOptions(Auth::userId(), Auth::username())]);
    }

    public function register(array $p = []): void
    {
        Auth::guard(true); Csrf::verify();
        $err = Passkeys::register(Auth::userId(), $this->body());
        if ($err) Response::json(['ok' => false, 'error' => $err], 422);
        Flash::set('Dispositivo añadido. Ya puedes entrar con huella o Face ID.');
        Response::json(['ok' => true]);
    }

    public function delete(array $p): void
    {
        Auth::guard(); Csrf::verify();
        Passkeys::delete(Auth::userId(), (int) $p['id']);
        Flash::set('Dispositivo eliminado.');
        Response::redirect('/admin/acceso');
    }
}
