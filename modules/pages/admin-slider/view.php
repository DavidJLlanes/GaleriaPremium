<?php use App\Core\Module; use App\Services\{Csrf, Slider}; $gid = (int) $gallery['id'];
$refId = (int) (\App\Models\Gallery::referencePhoto($gid)['id'] ?? 0); Module::use('components/sortable'); echo Module::render('components/cropper'); ?>
<a class="pnl-sub" href="/admin/sliders" style="display:inline-block;margin-bottom:.8rem">← Sliders</a>
<h1><?= e($gallery['title']) ?></h1>
<p class="pnl-sub"><?= count($photos) ?> fotos · aparece en la portada si está publicado y tiene fotos</p>

<form class="pnl-card" method="post" action="/admin/slider/<?= $gid ?>/editar">
    <h2>Ajustes del slider</h2>
    <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
    <label>Nombre</label><input type="text" name="title" value="<?= e($gallery['title']) ?>" required>
    <?php foreach (['design', 'fx', 'arrows', 'dots'] as $k): $s = Slider::SPEC[$k]; ?>
        <h3 class="sls-h"><?= e($s['label']) ?></h3>
        <?= Module::render('components/style-picker', ['styles' => $pick[$k], 'name' => 'sl_' . $k, 'selected' => $cfg[$k]]) ?>
    <?php endforeach; ?>
    <h3 class="sls-h">Pase y detalles</h3>
    <div class="sls-grid">
        <?php foreach (['autoplay', 'loop', 'hover', 'captions', 'title'] as $k): ?>
            <label class="pnl-check"><input type="checkbox" name="sl_<?= $k ?>" value="1" <?= $cfg[$k] === '1' ? 'checked' : '' ?>><span><?= e(Slider::SPEC[$k]['label']) ?></span></label>
        <?php endforeach; ?>
        <div><label>Segundos entre fotos: <b id="slInt"><?= e($cfg['interval']) ?></b></label>
            <input type="range" name="sl_interval" min="2" max="15" step="0.5" value="<?= e($cfg['interval']) ?>" oninput="document.getElementById('slInt').textContent=this.value"></div>
    </div>
    <h3 class="sls-h">Efecto al ampliar una foto (lightbox)</h3>
    <?= Module::render('components/style-picker', ['styles' => $lightboxes, 'name' => 'lightbox', 'selected' => $gallery['lightbox'] ?? 'fade']) ?>
    <label class="pnl-check" style="margin:1.2rem 0 0"><input type="checkbox" name="show_cats" <?= ($gallery['show_cats'] ?? 1) ? 'checked' : '' ?>><span>Mostrar las categorías de las fotos</span></label>
    <label class="pnl-check" style="margin:.6rem 0 0"><input type="checkbox" name="show_exif" <?= \\App\\Models\\Gallery::showExif($gallery) ? 'checked' : '' ?>><span>Mostrar datos EXIF en el lightbox</span></label>
    <label class="pnl-check" style="margin:.6rem 0 1.2rem"><input type="checkbox" name="published" <?= $gallery['is_published'] ? 'checked' : '' ?>><span>Publicado en la web</span></label>
    <button class="pnl-btn">Guardar</button>
</form>

<?= Module::render('components/uploader', ['galleryId' => $gid, 'aiConfigured' => $aiConfigured, 'aiAuto' => $aiAuto]) ?>

<section class="pnl-card">
    <h2>Fotos</h2>
    <p class="pnl-hint" style="margin:-.6rem 0 1rem">✂ <b>La primera foto fija la proporción del slider</b> (<?= $gallery['crop_ratio'] ? e(rtrim(rtrim(number_format((float) $gallery['crop_ratio'], 2), '0'), '.')) . ':1' : 'se fijará al subirla' ?>) y todas las demás la respetan: pulsa «Recortar» para elegir qué zona se ve.<?= count($photos) > 1 ? ' Arrastra ⠿ para ordenar (se guarda solo).' : '' ?></p>
    <?php if (!$photos): ?><p class="pnl-hint">Todavía no hay fotos. Sube la primera arriba.</p><?php endif; ?>
    <div class="pr-list" data-bulk data-ratio="<?= e((string) ($gallery['crop_ratio'] ?? '')) ?>" data-sortable data-reorder-url="/admin/galeria/<?= $gid ?>/orden">
        <?php foreach ($photos as $i => $p): ?>
            <?= Module::render('components/photo-row', ['p' => $p, 'isCover' => false, 'aiConfigured' => $aiConfigured, 'canCrop' => true, 'isFirst' => (int) $p['id'] === $refId]) ?>
        <?php endforeach; ?>
    </div>
</section>
<?php if ($photos) echo Module::render('components/bulk', ['kind' => 'photos', 'listSel' => '.pr-list', 'galleries' => array_values(array_filter(\App\Models\Gallery::allMixed(), fn ($g) => (int) $g['id'] !== $gid))]); ?>

<form method="post" action="/admin/slider/<?= $gid ?>/borrar" onsubmit="return confirm('¿Eliminar el slider y todas sus fotos? No se puede deshacer.')">
    <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
    <button class="pnl-btn is-danger">Eliminar slider</button>
</form>
