<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\{Config, Response};
use App\Models\{Gallery, Photo};
use App\Services\{Auth, Csrf};
use App\Services\Ai\{AiException, AiService};

final class PhotoController
{
    private function load(array $p): array
    {
        Auth::guard(true); Csrf::verify();
        $photo = Photo::find((int) $p['id']);
        if (!$photo) Response::json(['ok' => false, 'error' => 'La foto no existe'], 404);
        return $photo;
    }

    public function update(array $p): void
    {
        $photo = $this->load($p);
        $title = trim((string) ($_POST['title'] ?? ''));
        if ($title === '') Response::json(['ok' => false, 'error' => 'El nombre no puede estar vacío'], 422);
        Photo::update((int) $photo['id'], $title, trim((string) ($_POST['description'] ?? '')));
        Response::json(['ok' => true]);
    }

    /** Genera con IA título y/o descripción. NO guarda: devuelve los textos para que el usuario los revise. */
    public function ai(array $p): void
    {
        $photo = $this->load($p);
        $path = rtrim(Config::get('uploads.dir'), '/') . '/originals/' . $photo['filename'];
        try {
            $r = AiService::describe($path, trim((string) ($_POST['title'] ?? '')));
            Response::json(['ok' => true] + $r);
        } catch (AiException $e) {
            Response::json(['ok' => false, 'error' => $e->getMessage()], 502);
        }
    }

    public function cover(array $p): void
    {
        $photo = $this->load($p);
        Gallery::setCover((int) $photo['gallery_id'], (int) $photo['id']);
        Response::json(['ok' => true]);
    }

    public function delete(array $p): void
    {
        $photo = $this->load($p);
        Photo::delete((int) $photo['id']);
        Response::json(['ok' => true]);
    }
}
