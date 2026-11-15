<?php use App\Models\Photo; use App\Services\Csrf; ?>
<h1>Galerías</h1>
<p class="ad-sub">Crea galerías y gestiona sus fotos.</p>

<form class="ad-card" method="post" action="/admin/galerias">
    <h2>Nueva galería</h2>
    <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
    <div class="ad-row">
        <div><label>Nombre</label><input type="text" name="title" required placeholder="Ej.: Otoño en León"></div>
        <div><label>Diseño</label>
            <select name="style"><?php foreach ($styles as $id => $m): ?><option value="<?= e($id) ?>"><?= e($m['label']) ?></option><?php endforeach; ?></select></div>
    </div>
    <div style="margin-bottom:1rem"><label>Descripción (opcional)</label><textarea name="description" rows="2"></textarea></div>
    <label class="ad-check" style="margin-bottom:1.2rem"><input type="checkbox" name="published" checked> Publicada en la web</label>
    <button class="ad-btn">Crear y subir fotos →</button>
</form>

<div class="adg">
    <?php foreach ($galleries as $g): ?>
        <a class="adg-card" href="/admin/galeria/<?= (int) $g['id'] ?>">
            <div class="adg-img"><?php if ($g['cover']): ?><img src="<?= e(Photo::thumbUrl($g['cover'])) ?>" alt="" loading="lazy"><?php endif; ?></div>
            <div class="adg-info">
                <strong><?= e($g['title']) ?></strong>
                <span><?= (int) $g['photo_count'] ?> fotos · <?= e($styles[$g['style']]['label'] ?? $g['style']) ?></span>
                <span class="ad-badge <?= $g['is_published'] ? '' : 'is-off' ?>"><?= $g['is_published'] ? 'Publicada' : 'Oculta' ?></span>
            </div>
        </a>
    <?php endforeach; ?>
</div>
