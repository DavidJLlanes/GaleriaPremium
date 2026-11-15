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

    /** Guarda el recorte de una foto. La primera de la galería define la proporción; las demás se fuerzan a ella. */
    public function crop(array $p): void
    {
        $photo = $this->load($p);
        $gid = (int) $photo['gallery_id']; $iw = max(1, (int) $photo['width']); $ih = max(1, (int) $photo['height']);
        $c = \App\Services\Crop::sanitize($_POST);
        if (!$c) Response::json(['ok' => false, 'error' => 'Recorte no válido'], 422);

        $first = Photo::forGallery($gid)[0] ?? null;
        $isFirst = $first && (int) $first['id'] === (int) $photo['id'];
        if (!$isFirst) {
            $ratio = (float) (Gallery::find($gid)['crop_ratio'] ?? 0);
            if ($ratio > 0) $c = \App\Services\Crop::lock($c, $ratio, $iw, $ih);
        }
        Photo::setCrop((int) $photo['id'], $c);
        Gallery::syncCrops($gid);
        Response::json(['ok' => true, 'first' => $isFirst, 'ratio' => (float) (Gallery::find($gid)['crop_ratio'] ?? 0), 'crop' => $c]);
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
        Gallery::syncCrops((int) $photo['gallery_id']);
        Response::json(['ok' => true]);
    }
}
