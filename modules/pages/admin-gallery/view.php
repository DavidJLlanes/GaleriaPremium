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
    <label class="ad-check" style="margin:1.2rem 0"><input type="checkbox" name="published" <?= $gallery['is_published'] ? 'checked' : '' ?>> Publicada en la web</label>
    <button class="ad-btn">Guardar</button>
</form>

<?= Module::render('components/uploader', ['galleryId' => $gid, 'aiConfigured' => $aiConfigured, 'aiAuto' => $aiAuto]) ?>

<?php Module::use('components/sortable'); ?>
<section class="ad-card">
    <h2>Fotos</h2>
    <?php if (count($photos) > 1): ?><p class="ad-hint" style="margin:-.6rem 0 1rem">Arrastra el icono ⠿ para cambiar el orden. Se guarda solo.</p><?php endif; ?>
    <?php if (!$photos): ?><p class="ad-hint">Todavía no hay fotos. Sube la primera arriba.</p><?php endif; ?>
    <div class="pr-list" data-sortable data-reorder-url="/admin/galeria/<?= $gid ?>/orden">
        <?php foreach ($photos as $p): ?>
            <?= Module::render('components/photo-row', ['p' => $p, 'isCover' => (int) $gallery['cover_photo_id'] === (int) $p['id'], 'aiConfigured' => $aiConfigured]) ?>
        <?php endforeach; ?>
    </div>
</section>

<form method="post" action="/admin/galeria/<?= $gid ?>/borrar" onsubmit="return confirm('¿Eliminar la galería y todas sus fotos? No se puede deshacer.')">
    <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
    <button class="ad-btn is-danger">Eliminar galería</button>
</form>
