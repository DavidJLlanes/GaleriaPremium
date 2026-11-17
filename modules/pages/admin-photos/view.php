<?php use App\Core\Module; use App\Models\Photo;
$qs = static fn (array $o): string => '?' . http_build_query(array_filter(['galeria' => $gallery, 'q' => $q, 'p' => $o['p'] ?? null] , fn ($v) => $v !== null && $v !== '' && $v !== 0));
?>
<h1>Fotos</h1>
<p class="pnl-sub">Todas las fotos de todas tus galerías. Marca varias con ✓ para moverlas a otra galería o borrarlas; pulsa una foto para editarla.</p>

<form class="pnl-card ph-filter" method="get" action="/admin/fotos">
    <div><label>Galería</label>
        <select name="galeria" onchange="this.form.submit()">
            <option value="">Todas (<?= count($galleries) ?>)</option>
            <?php foreach ($galleries as $g): ?><option value="<?= (int) $g['id'] ?>" <?= (int) $gallery === (int) $g['id'] ? 'selected' : '' ?>><?= e($g['title']) ?> · <?= (int) $g['photo_count'] ?></option><?php endforeach; ?>
        </select></div>
    <div><label>Buscar en nombre, descripción y categorías</label><input type="text" name="q" value="<?= e($q) ?>" placeholder="Escribe y pulsa Intro"></div>
    <button class="pnl-btn is-ghost">Buscar</button>
    <?php if ($gallery || $q !== ''): ?><a class="pnl-link" href="/admin/fotos">Quitar filtros</a><?php endif; ?>
</form>

<p class="pnl-hint" style="margin-bottom:.8rem"><?= $total ?> <?= $total === 1 ? 'foto' : 'fotos' ?><?= $pages > 1 ? " · página $page de $pages" : '' ?></p>
<?php if (!$photos): ?>
    <p class="pnl-hint">No hay fotos<?= ($gallery || $q !== '') ? ' con ese filtro' : ' todavía. Crea una galería y sube las primeras' ?>.</p>
<?php else: ?>
<div class="ph-grid" data-bulk>
    <?php foreach ($photos as $p): ?>
        <div class="ph-card" data-id="<?= (int) $p['id'] ?>">
            <span class="bk-box" role="checkbox" aria-checked="false" tabindex="0" title="Seleccionar">✓</span>
            <a class="ph-link" href="/admin/galeria/<?= (int) $p['gallery_id'] ?>#foto-<?= (int) $p['id'] ?>">
                <img src="<?= e(Photo::thumbUrl($p['filename'])) ?>" alt="" loading="lazy">
                <span class="ph-info"><strong><?= e($p['title'] !== '' ? $p['title'] : 'Sin nombre') ?></strong><small><?= e($p['gallery_title']) ?> · <?= (int) $p['width'] ?>×<?= (int) $p['height'] ?></small><?php if (!empty($p['cats'])): ?><small class="ph-cats"><?= e(implode(' · ', $p['cats'])) ?></small><?php endif; ?>></span>
            </a>
        </div>
    <?php endforeach; ?>
</div>
<?php if ($pages > 1): ?>
<nav class="ph-pager" aria-label="Páginas">
    <?php if ($page > 1): ?><a class="pnl-btn is-ghost is-small" href="<?= e($qs(['p' => $page - 1])) ?>">← Anterior</a><?php endif; ?>
    <span>Página <?= $page ?> de <?= $pages ?></span>
    <?php if ($page < $pages): ?><a class="pnl-btn is-ghost is-small" href="<?= e($qs(['p' => $page + 1])) ?>">Siguiente →</a><?php endif; ?>
</nav>
<?php endif; ?>
<?= Module::render('components/bulk', ['kind' => 'photos', 'listSel' => '.ph-grid', 'galleries' => $galleries, 'extra' => Module::render('components/bulk-cats', ['all' => \App\Services\Categories::all()])]) ?>
<?php endif; ?>
