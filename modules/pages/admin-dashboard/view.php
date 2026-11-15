<?php use App\Core\Module; use App\Models\Photo; use App\Services\Csrf; Module::use('components/sortable'); ?>
<h1>Galerías</h1>
<p class="ad-sub">Crea galerías y gestiona sus fotos.</p>

<form class="ad-card" method="post" action="/admin/galerias">
    <h2>Nueva galería</h2>
    <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
    <div class="ad-row">
        <div><label>Nombre</label><input type="text" name="title" required placeholder="Ej.: Otoño en León"></div>
        <div><label>Descripción (opcional)</label><textarea name="description" rows="1"></textarea></div>
    </div>
    <label>Diseño</label>
    <?= Module::render('components/style-picker', ['styles' => $styles, 'name' => 'style', 'selected' => 'masonry']) ?>
    <label class="ad-check" style="margin-bottom:1.2rem"><input type="checkbox" name="published" checked> Publicada en la web</label>
    <button class="ad-btn">Crear y subir fotos →</button>
</form>

<?php if (count($galleries) > 1): ?><p class="ad-hint" style="margin-bottom:.8rem">Arrastra el icono ⠿ para cambiar el orden de las galerías en la web. Se guarda solo.</p><?php endif; ?>
<div class="adg" data-bulk data-sortable data-reorder-url="/admin/galerias/orden">
    <?php foreach ($galleries as $g): ?>
        <a class="adg-card" data-id="<?= (int) $g['id'] ?>" href="/admin/galeria/<?= (int) $g['id'] ?>">
            <span class="bk-box" role="checkbox" aria-checked="false" tabindex="0" title="Seleccionar">✓</span>
            <span class="srt-handle" title="Arrastra para reordenar" aria-label="Reordenar">⠿</span>
            <div class="adg-img"><?php if ($g['cover']): ?><img src="<?= e(Photo::thumbUrl($g['cover'])) ?>" alt="" loading="lazy"><?php endif; ?></div>
            <div class="adg-info">
                <strong><?= e($g['title']) ?></strong>
                <span><?= (int) $g['photo_count'] ?> fotos · <?= e($styles[$g['style']]['label'] ?? $g['style']) ?></span>
                <span class="ad-badge <?= $g['is_published'] ? '' : 'is-off' ?>"><?= $g['is_published'] ? 'Publicada' : 'Oculta' ?></span>
            </div>
        </a>
    <?php endforeach; ?>
</div>
<?php if ($galleries) echo Module::render('components/bulk', ['kind' => 'galleries', 'listSel' => '.adg']); ?>
