<?php use App\Core\Module; use App\Services\Csrf; $gid = (int) $gallery['id']; ?>
<a class="ad-sub" href="/admin" style="display:inline-block;margin-bottom:.8rem">← Galerías</a>
<h1><?= e($gallery['title']) ?></h1>
<p class="ad-sub"><?= count($photos) ?> fotos · <a href="/galeria/<?= e($gallery['slug']) ?>" target="_blank" rel="noopener" class="ad-link">ver en la web ↗</a></p>

<form class="ad-card" method="post" action="/admin/galeria/<?= $gid ?>/editar">
    <h2>Ajustes de la galería</h2>
    <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
    <div class="ad-row">
        <div><label>Nombre</label><input type="text" name="title" value="<?= e($gallery['title']) ?>" required></div>
        <div><label>Descripción</label><textarea name="description" rows="1"><?= e($gallery['description']) ?></textarea></div>
    </div>
    <label>Diseño de esta galería</label>
    <?= Module::render('components/style-picker', ['styles' => $styles, 'name' => 'style', 'selected' => $gallery['style']]) ?>
    <?= Module::render('components/style-options', ['styles' => $styles, 'gallery' => $gallery]) ?>
    <label style="margin-top:1.4rem">Efecto al ampliar una foto (lightbox)</label>
    <?= Module::render('components/style-picker', ['styles' => $lightboxes, 'name' => 'lightbox', 'selected' => $gallery['lightbox'] ?? 'fade']) ?>
    <label class="ad-check" style="margin:1.2rem 0"><input type="checkbox" name="published" <?= $gallery['is_published'] ? 'checked' : '' ?>><span>Publicada en la web</span></label>
    <button class="ad-btn">Guardar</button>
</form>

<?= Module::render('components/uploader', ['galleryId' => $gid, 'aiConfigured' => $aiConfigured, 'aiAuto' => $aiAuto]) ?>

<?php Module::use('components/sortable'); Module::use('components/style-options'); if ($canCrop) echo Module::render('components/cropper'); ?>
<section class="ad-card">
    <h2>Fotos</h2>
    <?php if ($canCrop): ?><p class="ad-hint" style="margin:-.6rem 0 1rem">✂ <b>La primera foto fija la proporción de la galería</b> (<?= $gallery['crop_ratio'] ? e(rtrim(rtrim(number_format((float) $gallery['crop_ratio'], 2), '0'), '.')) . ':1' : 'automática' ?>). Pulsa «Recortar» en cada foto para elegir qué zona se ve.</p><?php endif; ?>
    <?php if (count($photos) > 1): ?><p class="ad-hint" style="margin:-.6rem 0 1rem">Arrastra el icono ⠿ para cambiar el orden (se guarda solo). Marca la casilla ✓ de varias fotos para <b>moverlas a otra galería</b> o borrarlas a la vez.</p><?php endif; ?>
    <?php if (!$photos): ?><p class="ad-hint">Todavía no hay fotos. Sube la primera arriba.</p><?php endif; ?>
    <div class="pr-list" data-bulk data-ratio="<?= e((string) ($gallery['crop_ratio'] ?? '')) ?>" data-sortable data-reorder-url="/admin/galeria/<?= $gid ?>/orden">
        <?php foreach ($photos as $i => $p): ?>
            <?= Module::render('components/photo-row', ['p' => $p, 'isCover' => (int) $gallery['cover_photo_id'] === (int) $p['id'], 'aiConfigured' => $aiConfigured, 'canCrop' => $canCrop, 'isFirst' => $i === 0]) ?>
        <?php endforeach; ?>
    </div>
</section>
<?php if ($photos) echo Module::render('components/bulk', ['kind' => 'photos', 'listSel' => '.pr-list', 'galleries' => array_values(array_filter(\App\Models\Gallery::all(), fn ($g) => (int) $g['id'] !== $gid))]); ?>

<form method="post" action="/admin/galeria/<?= $gid ?>/borrar" onsubmit="return confirm('¿Eliminar la galería y todas sus fotos? No se puede deshacer.')">
    <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
    <button class="ad-btn is-danger">Eliminar galería</button>
</form>
