<?php use App\Core\Module; use App\Models\Photo; use App\Services\Csrf; Module::use('components/sortable');
$ids = implode(',', array_map(fn ($p) => $p['id'], $selected)); $byGallery = [];
foreach ($photos as $p) $byGallery[$p['gallery_title']][] = $p; ?>
<h1>Portada</h1>
<p class="ad-sub">La sección grande con la que empieza tu web: tipo, fotos (y su orden) y textos.</p>

<form method="post" action="/admin/portada" id="heroForm" data-type="<?= e($type) ?>">
    <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
    <input type="hidden" name="hero_photo_ids" id="heroIds" value="<?= e($ids) ?>">

    <section class="ad-card">
        <h2>Tipo de portada</h2>
        <?= Module::render('components/style-picker', ['styles' => $heroes, 'name' => 'hero_type', 'selected' => $type]) ?>
    </section>

    <section class="ad-card">
        <h2>Fotos de la portada</h2>
        <div class="hr-source" id="heroSource">
            <label class="ad-check"><input type="radio" name="hero_source" value="gallery" <?= $source === 'gallery' ? 'checked' : '' ?>> Todas las fotos de una galería</label>
            <label class="ad-check"><input type="radio" name="hero_source" value="custom" <?= $source === 'custom' ? 'checked' : '' ?>> Mi selección (elijo y ordeno las fotos)</label>
        </div>

        <div class="hr-gallery" id="heroGallery">
            <label for="hg">Galería</label>
            <select id="hg" name="hero_gallery_id">
                <option value="0">La primera galería</option>
                <?php foreach ($galleries as $g): ?><option value="<?= (int) $g['id'] ?>" <?= $galleryId === (int) $g['id'] ? 'selected' : '' ?>><?= e($g['title']) ?> (<?= (int) $g['photo_count'] ?> fotos)</option><?php endforeach; ?>
            </select>
            <p class="ad-hint">El orden es el de las fotos dentro de la galería.</p>
        </div>

        <div class="hr-custom" id="heroCustom">
            <label>Seleccionadas <span class="ad-hint" id="heroHint"></span></label>
            <div class="hr-sel" id="heroSel" data-sortable></div>
            <p class="ad-hint hr-empty" id="heroEmpty">Aún no has elegido ninguna: toca fotos abajo.</p>

            <label style="margin-top:1.4rem">Toca las fotos que quieras en la portada</label>
            <?php foreach ($byGallery as $title => $list): ?>
                <h3 class="hr-gt"><?= e($title) ?></h3>
                <div class="hr-pick">
                    <?php foreach ($list as $p): ?>
                        <button type="button" class="hr-p" data-id="<?= (int) $p['id'] ?>" data-thumb="<?= e(Photo::thumbUrl($p['filename'])) ?>" title="<?= e($p['title']) ?>">
                            <img src="<?= e(Photo::thumbUrl($p['filename'])) ?>" alt="<?= e($p['title']) ?>" loading="lazy"><i>✓</i>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            <?php if (!$photos): ?><p class="ad-hint">Todavía no hay fotos subidas.</p><?php endif; ?>
        </div>
    </section>

    <section class="ad-card">
        <h2>Textos y velocidad</h2>
        <div class="ad-row">
            <div><label for="he">Texto pequeño superior</label><input id="he" type="text" name="hero_eyebrow" value="<?= e($eyebrow) ?>" maxlength="80"></div>
            <div><label for="ht">Título grande (vacío = el eslogan del sitio)</label><input id="ht" type="text" name="hero_title" value="<?= e($title) ?>" maxlength="160"></div>
        </div>
        <div class="ad-row">
            <div><label for="hc">Texto del botón</label><input id="hc" type="text" name="hero_cta" value="<?= e($cta) ?>" maxlength="40"></div>
            <div id="heroSpeed"><label for="hi">Segundos entre fotos</label><input id="hi" type="text" inputmode="decimal" name="hero_interval" value="<?= e($interval) ?>"></div>
        </div>
    </section>

    <button class="ad-btn">Guardar portada</button>
    <a class="ad-btn is-ghost" href="/" target="_blank" rel="noopener" style="margin-left:.5rem">Ver la web ↗</a>
</form>
