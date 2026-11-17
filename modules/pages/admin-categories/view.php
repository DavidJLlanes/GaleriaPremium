<?php use App\Core\Module; use App\Services\{Categories, Csrf, HeroButton}; Module::use('components/photo-cats'); $untagged = Categories::untaggedCount(); ?>
<h1>Categorías</h1>
<p class="ad-sub">Las categorías se crean al subir o editar una foto (máximo 3 por foto) y quedan guardadas para reutilizarlas. Aquí las gestionas, eliges cómo se ve el indicador y cada galería o slider decide si las muestra.</p>

<section class="ad-card" id="catMgr">
    <h2>Gestión <span class="ad-badge"><?= count($cats) ?></span></h2>
    <div class="cat-tools">
        <form class="cat-new" id="catNew"><input type="text" placeholder="Nueva categoría (sin fotos)" maxlength="<?= Categories::LEN ?>" aria-label="Nueva categoría"><button class="ad-btn is-small">+ Crear</button></form>
        <input type="text" id="catFind" placeholder="Buscar…" aria-label="Buscar categoría">
        <select id="catSort" aria-label="Ordenar"><option value="az">A → Z</option><option value="n">Más fotos primero</option><option value="n0">Menos fotos primero</option></select>
        <button type="button" class="ad-btn is-ghost is-small" id="catClean" title="Borra las categorías que no usa ninguna foto">Limpiar las vacías</button>
    </div>

    <a class="cat-un" href="/admin/categoria/0"><b>Fotos sin categoría</b><small><?= $untagged ?> <?= $untagged === 1 ? 'foto' : 'fotos' ?> · asígnalas en bloque</small><span>Ver →</span></a>

    <?php if (!$cats): ?><p class="ad-hint">Todavía no hay categorías. Crea una arriba o escríbela al subir una foto.</p><?php endif; ?>
    <ul class="cat-list" id="catList">
        <?php foreach ($cats as $k): ?>
            <li data-id="<?= (int) $k['id'] ?>" data-name="<?= e(mb_strtolower($k['name'])) ?>" data-n="<?= (int) $k['n'] ?>">
                <span class="bk-box cat-chk" role="checkbox" aria-checked="false" tabindex="0" title="Marcar para unir">✓</span>
                <input type="text" value="<?= e($k['name']) ?>" maxlength="<?= Categories::LEN ?>" aria-label="Nombre de la categoría">
                <a class="cat-n" href="/admin/categoria/<?= (int) $k['id'] ?>" title="Ver sus fotos"><?= (int) $k['n'] ?> <?= (int) $k['n'] === 1 ? 'foto' : 'fotos' ?></a>
                <button type="button" class="ad-btn is-ghost is-small" data-cat="save">Renombrar</button>
                <select class="cat-mv" data-cat="move" aria-label="Pasar todas sus fotos a otra categoría"><option value="">Pasar fotos a…</option><?php foreach ($cats as $o): if ($o['id'] === $k['id']) continue; ?><option value="<?= (int) $o['id'] ?>"><?= e($o['name']) ?></option><?php endforeach; ?></select>
                <button type="button" class="ad-btn is-ghost is-small" data-cat="del" title="Quita la categoría; las fotos se conservan">Borrar</button>
                <button type="button" class="ad-btn is-danger is-small" data-cat="delall" title="Borra la categoría y todas sus fotos"<?= (int) $k['n'] ? '' : ' hidden' ?>>Borrar con fotos</button>
            </li>
        <?php endforeach; ?>
    </ul>
    <div class="cat-merge" id="catMerge" hidden><b id="catMergeN"></b> <select id="catMergeInto" aria-label="Categoría que se queda"></select> <button type="button" class="ad-btn is-small" id="catMergeGo">Unir en esa categoría</button> <button type="button" class="ad-btn is-ghost is-small" id="catMergeNo">Cancelar</button></div>
    <p class="ad-hint" style="margin-top:1rem">Marca varias con ✓ para unirlas en una. «Borrar» quita la categoría pero conserva las fotos; «Borrar con fotos» las elimina también.</p>
</section>

<form class="ad-card" method="post" action="/admin/categorias" id="catForm">
    <h2>Diseño del indicador</h2>
    <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
    <p class="ad-hint" style="margin:-.6rem 0 1rem">Los mismos diseños y tamaños que el botón «Explorar galerías» de la portada; la tipografía es la de ese botón (Sitio → Tipografías).</p>
    <div class="cat-styles">
        <?php foreach ($styles as $k => $label): ?>
            <label class="cat-st"><input type="radio" name="cat_style" value="<?= e($k) ?>" <?= $c['style'] === $k ? 'checked' : '' ?>>
                <span class="cat-prev" style="--pc-k:<?= e((string) HeroButton::scale($c['size'])) ?>"><i class="pc pc--<?= e($k) ?>">Paisaje</i></span><b><?= e($label) ?></b></label>
        <?php endforeach; ?>
    </div>
    <label style="margin-top:1.2rem">Tamaño: <b id="catSz"><?= e($sizes[$c['size']]) ?></b></label>
    <input type="range" name="cat_size" min="1" max="5" step="1" value="<?= (int) $c['size'] ?>" id="catRange">
    <button class="ad-btn" style="margin-top:1.2rem">Guardar diseño</button>
</form>
