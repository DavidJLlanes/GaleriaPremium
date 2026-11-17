<?php use App\Core\Module; use App\Models\Photo; use App\Services\Csrf; Module::use('components/sortable'); ?>
<h1>Galerías</h1>
<p class="pnl-sub">Tus galerías, en el orden en que salen en la web. Pulsa una para gestionar sus fotos y su diseño.</p>



<h2 class="pnl-listh">Tus galerías <span class="pnl-badge"><?= count($galleries) ?></span></h2>
<?php if (count($galleries) > 1): ?><p class="pnl-hint" style="margin-bottom:.8rem">Arrastra el icono ⠿ para cambiar el orden (se guarda solo). El chincheta <b>fija</b> una galería como la primera (solo una) y la estrella la marca como <b>destacada</b>.</p><?php endif; ?>
<div class="adg" data-bulk data-sortable data-reorder-url="/admin/galerias/orden">
    <?php foreach ($galleries as $g): ?>
        <a class="adg-card<?= $g['pinned'] ? ' is-pinned' : '' ?><?= $g['featured'] ? ' is-featured' : '' ?>" data-id="<?= (int) $g['id'] ?>"<?= $g['pinned'] ? ' data-fixed="1"' : '' ?> href="/admin/<?= $g['kind'] === 'slider' ? 'slider' : 'galeria' ?>/<?= (int) $g['id'] ?>">
            <span class="bk-box" role="checkbox" aria-checked="false" tabindex="0" title="Seleccionar">✓</span>
            <?php if (!$g['pinned']): ?><span class="srt-handle" title="Arrastra para reordenar" aria-label="Reordenar">⠿</span><?php endif; ?>
            <span class="adg-flags">
                <button type="button" class="adg-flag<?= $g['pinned'] ? ' is-on' : '' ?>" data-flag="pin" data-url="/admin/galeria/<?= (int) $g['id'] ?>/marca" aria-pressed="<?= $g['pinned'] ? 'true' : 'false' ?>" title="<?= $g['pinned'] ? 'Quitar de «primera»' : 'Fijar como la primera (solo una)' ?>" aria-label="Fijar como primera"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 3h6l-1 6 3 3v2H7v-2l3-3zM12 14v7" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                <button type="button" class="adg-flag<?= $g['featured'] ? ' is-on' : '' ?>" data-flag="featured" data-url="/admin/galeria/<?= (int) $g['id'] ?>/marca" aria-pressed="<?= $g['featured'] ? 'true' : 'false' ?>" title="<?= $g['featured'] ? 'Quitar destacada' : 'Marcar como destacada' ?>" aria-label="Destacar"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg></button>
            </span>
            <div class="adg-img"><?php if ($g['cover']): ?><img src="<?= e(Photo::thumbUrl($g['cover'])) ?>" alt="" loading="lazy"><?php endif; ?></div>
            <div class="adg-info">
                <strong><?= e($g['title']) ?></strong>
                <span><?= (int) $g['photo_count'] ?> fotos · <?= $g['kind'] === 'slider' ? 'Slider' : e($styles[$g['style']]['label'] ?? $g['style']) ?></span>
                <span class="pnl-badge <?= $g['is_published'] ? '' : 'is-off' ?>"><?= $g['is_published'] ? 'Publicada' : 'Oculta' ?></span>
                <?php if ($g['pinned']): ?><span class="pnl-badge">Fijada</span><?php endif; ?><?php if ($g['featured']): ?><span class="pnl-badge">Destacada</span><?php endif; ?>
            </div>
        </a>
    <?php endforeach; ?>
</div>
<?php if ($galleries) echo Module::render('components/bulk', ['kind' => 'galleries', 'listSel' => '.adg']); ?>

<details class="pnl-card pnl-new" <?= $galleries ? '' : 'open' ?>>
    <summary>+ Nueva galería</summary>
    <form method="post" action="/admin/galerias" class="pnl-newbody">
        <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
        <div class="pnl-row">
            <div><label>Nombre</label><input type="text" name="title" required placeholder="Ej.: Otoño en León"></div>
            <div><label>Descripción (opcional)</label><textarea name="description" rows="1"></textarea></div>
        </div>
        <label>Diseño</label>
        <?= Module::render('components/style-picker', ['styles' => $styles, 'name' => 'style', 'selected' => 'masonry']) ?>
        <label class="pnl-check" style="margin:1.2rem 0"><input type="checkbox" name="published" checked><span>Publicada en la web</span></label>
        <button class="pnl-btn">Crear y subir fotos →</button>
    </form>
</details>
