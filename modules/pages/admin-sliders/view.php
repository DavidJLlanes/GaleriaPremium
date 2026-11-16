<?php use App\Core\Module; use App\Models\Photo; use App\Services\Csrf; ?>
<h1>Sliders</h1>
<p class="ad-sub">Pases de fotos que puedes colocar entre las galerías de la portada. Su posición, y si van fijados o destacados, se decide en <a class="ad-link" href="/admin">Galerías</a>, que es donde se ordena todo.</p>

<h2 class="ad-listh">Tus sliders <span class="ad-badge"><?= count($sliders) ?></span></h2>
<?php if (!$sliders): ?><p class="ad-hint">Todavía no hay ningún slider. Crea el primero abajo.</p><?php endif; ?>
<div class="adg">
    <?php foreach ($sliders as $g): ?>
        <a class="adg-card<?= $g['pinned'] ? ' is-pinned' : '' ?><?= $g['featured'] ? ' is-featured' : '' ?>" href="/admin/slider/<?= (int) $g['id'] ?>">
            <div class="adg-img"><?php if ($g['cover']): ?><img src="<?= e(Photo::thumbUrl($g['cover'])) ?>" alt="" loading="lazy"><?php endif; ?></div>
            <div class="adg-info">
                <strong><?= e($g['title']) ?></strong>
                <span><?= (int) $g['photo_count'] ?> fotos · <?= e($designs[\App\Services\Slider::config($g)['design']]['label'] ?? '') ?></span>
                <span class="ad-badge <?= $g['is_published'] ? '' : 'is-off' ?>"><?= $g['is_published'] ? 'Publicado' : 'Oculto' ?></span>
                <?php if ($g['pinned']): ?><span class="ad-badge">Fijado</span><?php endif; ?><?php if ($g['featured']): ?><span class="ad-badge">Destacado</span><?php endif; ?>
            </div>
        </a>
    <?php endforeach; ?>
</div>

<details class="ad-card ad-new" <?= $sliders ? '' : 'open' ?>>
    <summary>+ Nuevo slider</summary>
    <form method="post" action="/admin/sliders" class="ad-newbody">
        <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
        <label>Nombre</label><input type="text" name="title" required placeholder="Ej.: Lo mejor del otoño">
        <button class="ad-btn" style="margin-top:1rem">Crear y subir fotos →</button>
    </form>
</details>
