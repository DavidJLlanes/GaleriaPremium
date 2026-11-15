<?php
use App\Models\Gallery; use App\Services\{Auth, Csrf}; use App\Services\Ai\AiService;
if (!Auth::check()) return;
$galleries = array_map(fn ($g) => ['id' => (int) $g['id'], 'title' => $g['title']], Gallery::all());
$ai = AiService::isConfigured();
?>
<div class="du" id="dropUpload" hidden data-csrf="<?= e(Csrf::token()) ?>" data-galleries="<?= e(json_encode($galleries, JSON_UNESCAPED_UNICODE)) ?>" data-ai="<?= $ai ? '1' : '0' ?>" data-ai-auto="<?= $ai && AiService::settings()['auto'] ? '1' : '0' ?>">
    <div class="du-veil"><div><b>Suelta para subir</b><span>Se convertirá a WebP 75%</span></div></div>
    <div class="du-modal" role="dialog" aria-modal="true" aria-label="Subir fotos" hidden>
        <div class="du-head"><b>Subir fotos</b><button type="button" class="du-x" aria-label="Cerrar">×</button></div>
        <?php if (!$galleries): ?>
            <p class="du-empty">Aún no hay galerías. <a href="/admin">Crea una primero</a>.</p>
        <?php else: ?>
        <label class="du-row">Galería
            <select class="du-gal"><?php foreach ($galleries as $g): ?><option value="<?= $g['id'] ?>"><?= e($g['title']) ?></option><?php endforeach; ?></select>
        </label>
        <div class="du-list"></div>
        <label class="du-ai"><input type="checkbox" class="du-aichk" <?= $ai ? '' : 'disabled' ?>> Generar con IA el nombre y la descripción que deje vacíos<?= $ai ? '' : ' (IA sin configurar)' ?></label>
        <div class="du-foot">
            <span class="du-msg"></span>
            <button type="button" class="du-btn du-cancel">Cancelar</button>
            <button type="button" class="du-btn is-main du-go">Subir</button>
        </div>
        <?php endif; ?>
    </div>
</div>
