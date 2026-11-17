<?php use App\Core\Module; use App\Models\Photo; $id = (int) $cat['id'];
Module::use('pages/admin-photos'); ?>
<span data-cat-current="<?= $id ?>" hidden></span>
<a class="ad-sub" href="/admin/categorias" style="display:inline-block;margin-bottom:.8rem">← Categorías</a>
<div class="cd-head">
    <div><h1><?= e($cat['name']) ?></h1>
        <p class="ad-sub" style="margin:0"><?= (int) $cat['n'] ?> <?= (int) $cat['n'] === 1 ? 'foto' : 'fotos' ?><?= $id === 0 ? ' sin ninguna categoría: selecciónalas y añádeles una desde la barra de abajo' : '' ?></p></div>
    <?php if ($id > 0): ?><div class="cd-acts" data-cat-id="<?= $id ?>" data-cat-name="<?= e($cat['name']) ?>" data-cat-n="<?= (int) $cat['n'] ?>">
        <button type="button" class="ad-btn is-ghost is-small" data-cd="rename">Renombrar</button>
        <button type="button" class="ad-btn is-ghost is-small" data-cd="del">Borrar categoría (conserva las fotos)</button>
        <?php if ((int) $cat['n'] > 0): ?><button type="button" class="ad-btn is-danger is-small" data-cd="delall">Borrar categoría y sus <?= (int) $cat['n'] ?> fotos</button><?php endif; ?>
    </div><?php endif; ?>
</div>
<?php if (!$photos): ?><p class="cd-empty">No hay fotos <?= $id === 0 ? 'sin categoría: todas tienen alguna.' : 'en esta categoría todavía. Añádela a fotos desde Fotos o al editar una foto.' ?></p>
<?php else: ?>
<div class="ph-grid" data-bulk>
    <?php foreach ($photos as $p): ?>
        <div class="ph-card" data-id="<?= (int) $p['id'] ?>">
            <span class="bk-box" role="checkbox" aria-checked="false" tabindex="0" title="Seleccionar">✓</span>
            <a class="ph-link" href="/admin/galeria/<?= (int) $p['gallery_id'] ?>#foto-<?= (int) $p['id'] ?>">
                <img src="<?= e(Photo::thumbUrl($p['filename'])) ?>" alt="" loading="lazy">
                <span class="ph-info"><strong><?= e($p['title'] !== '' ? $p['title'] : 'Sin nombre') ?></strong><small><?= e($p['gallery_title']) ?></small><?php if ($p['cats']): ?><small class="ph-cats"><?= e(implode(' · ', $p['cats'])) ?></small><?php endif; ?></span>
            </a>
        </div>
    <?php endforeach; ?>
</div>
<?= Module::render('components/bulk', ['kind' => 'photos', 'listSel' => '.ph-grid', 'galleries' => $galleries, 'extra' => Module::render('components/bulk-cats', ['all' => $all, 'current' => $id])]) ?>
<?php endif; ?>
