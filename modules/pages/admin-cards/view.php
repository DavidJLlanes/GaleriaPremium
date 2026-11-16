<?php use App\Core\Module; use App\Models\Photo; use App\Services\Csrf;
Module::use('components/card-reveal');  Module::use('components/collection-card'); foreach (array_keys($reveals) as $r) Module::use("reveals/$r"); Module::use('card-titles/classic');
$sample = $sample ?? ['slug' => '', 'title' => 'Otoño en León', 'description' => '', 'cover' => null, 'cover_w' => 4, 'cover_h' => 5, 'photo_count' => 12]; ?>
<h1>Tarjetas</h1>
<p class="ad-sub">Cómo se ven las galerías en la página principal: cómo aparecen al hacer scroll y cómo se muestra el título de cada una.</p>

<form method="post" action="/admin/tarjetas" id="cardsForm" data-title="<?= e($c['style']) ?>">
    <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">

    <section class="ad-card">
        <h2>Diseño de la sección de galerías</h2>
        <?= Module::render('components/style-picker', ['styles' => $layouts, 'name' => 'cards_layout', 'selected' => $c['layout']]) ?>
        <iframe class="cd-frame" id="cdFrame" title="Vista previa de la portada" loading="lazy" src="/?cards_preview=1&amp;cl=<?= e($c['layout']) ?>#galerias"></iframe>
        <p class="ad-hint">Vista previa con tu web de verdad (se actualiza al elegir un diseño; los demás ajustes, al guardar). «Rejilla» y «Carrusel» usan las tarjetas con el efecto de aparición y el diseño de título de abajo; los demás tienen su propia composición y movimiento, y respetan la tipografía, el tamaño y el color de los títulos.</p>
    </section>
    <?php foreach ($layouts as $lid => $lm): if (empty($lm['options'])) continue; ?>
        <section class="ad-card" data-layout-opts="<?= e($lid) ?>" hidden>
            <h2>Ajustes del diseño «<?= e($lm['label']) ?>»</h2>
            <?= Module::render('components/option-fields', ['spec' => $lm['options'], 'values' => $lid === $c['layout'] ? $c['layout_opts'] : \App\Services\ModuleOptions::values('home-layouts', $lid, \App\Services\Cards::LAYOUT_PREFIX), 'name' => "layout_opt[$lid]"]) ?>
        </section>
    <?php endforeach; ?>

    <section class="ad-card">
        <h2>Aparición al hacer scroll</h2>
        <?= Module::render('components/style-picker', ['styles' => $reveals, 'name' => 'cards_reveal', 'selected' => $c['reveal']]) ?>
        <label class="ad-check cd-px"><input type="checkbox" name="cards_parallax" value="1" <?= $c['parallax'] ? 'checked' : '' ?>><span>Parallax suave: la foto se desplaza ligeramente con el scroll, con inercia</span></label>
        <div class="cd-play">
            <button type="button" class="ad-btn is-ghost is-small" id="cdPlay">▶ Probar el efecto</button>
            <div class="coll rv-on cd-prev" id="cdPrev" aria-hidden="true" style="<?= e(App\Services\Cards::vars($c)) ?>">
                <?php for ($i = 0; $i < 2; $i++): ?>
                <a class="ccard rv-<?= e($c['reveal']) ?> is-in ccard--over" href="#" tabindex="-1" style="<?= $i ? '--rv-d:140ms' : '' ?>">
                    <div class="ccard-media">
                        <?php if ($sample['cover'] ?? null): ?><img src="<?= e(Photo::thumbUrl($sample['cover'])) ?>" alt="" width="<?= (int) $sample['cover_w'] ?>" height="<?= (int) $sample['cover_h'] ?>"><?php endif; ?>
                        <div class="ct-classic"><span class="ct-classic-count">Vista previa</span><h2>Galería <?= $i + 1 ?></h2></div>
                    </div>
                </a>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <section class="ad-card">
        <h2>Títulos de las galerías</h2>
        <label class="ad-check"><input type="checkbox" name="cards_titles" id="cdTitles" value="1" <?= $c['titles'] ? 'checked' : '' ?>><span>Mostrar el título de cada galería en su tarjeta</span></label>
        <div id="cdTitleBox">
            <?= Module::render('components/style-picker', ['styles' => $titles, 'name' => 'cards_title_style', 'selected' => $c['style']]) ?>
        </div>
    </section>

    <section class="ad-card cd-type">
        <h2>Tipografía, tamaño y color de los títulos</h2>
        <?= Module::render('components/font-search') ?>
        <div class="ad-row">
            <div>
                <label for="cdFont">Tipografía</label>
                <select id="cdFont" name="cards_title_font" data-font-search>
                    <option value="" <?= $c['font'] === '' ? 'selected' : '' ?>>La de Sitio → Tipografías («Títulos de galerías»)</option>
                    <?php $byCat = []; foreach (App\Services\Fonts::catalog() as $fid => $fm) $byCat[$fm['cat'] ?? 'custom'][$fid] = $fm;
                    foreach (App\Services\Fonts::CATS as $cat => $catLabel): if (empty($byCat[$cat])) continue; ?>
                        <optgroup label="<?= e($catLabel) ?>"><?php foreach ($byCat[$cat] as $fid => $fm): ?><option value="<?= e($fid) ?>" data-fam="<?= e(App\Services\Fonts::family($fid)) ?>" <?= $c['font'] === $fid ? 'selected' : '' ?>><?= e($fm['label']) ?></option><?php endforeach; ?></optgroup>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="cdWeight">Grosor</label>
                <select id="cdWeight" name="cards_title_weight">
                    <option value="0" <?= !$c['weight'] ? 'selected' : '' ?>>El del diseño</option>
                    <?php foreach (App\Services\Fonts::WEIGHTS as $w => $wl): ?><option value="<?= $w ?>" <?= $c['weight'] === $w ? 'selected' : '' ?>><?= e($wl) ?> (<?= $w ?>)</option><?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="cdSize">Tamaño: <b id="cdSizeVal"><?= (int) $c['size'] ?></b> %</label>
                <input id="cdSize" type="range" name="cards_title_size" min="50" max="180" step="5" value="<?= (int) $c['size'] ?>">
            </div>
        </div>
        <div class="ad-row" style="margin-top:1rem">
            <label class="ad-check"><input type="checkbox" name="cards_title_italic" id="cdItalic" value="1" <?= $c['italic'] ? 'checked' : '' ?>><span>Cursiva (si la fuente la tiene)</span></label>
            <label class="ad-check"><input type="checkbox" name="cards_title_color_on" id="cdColorOn" value="1" <?= $c['color_on'] ? 'checked' : '' ?>><span>Usar un color propio</span></label>
            <div><label for="cdColor">Color del título</label><input id="cdColor" type="color" name="cards_title_color" value="<?= e($c['color']) ?>"></div>
        </div>
        <p class="ad-hint">Valen para todos los diseños de título. En el diseño «Luz» el color de la pluma se elige en sus propios ajustes.</p>
    </section>

    <?php foreach ($titles as $tid => $tm): if (empty($tm['options'])) continue; ?>
        <section class="ad-card" data-title-opts="<?= e($tid) ?>" hidden>
            <h2>Ajustes: título «<?= e($tm['label']) ?>»</h2>
            <?= Module::render('components/option-fields', ['spec' => $tm['options'], 'values' => $tid === $c['style'] ? $c['style_opts'] : \App\Services\ModuleOptions::values('card-titles', $tid, \App\Services\Cards::PREFIX), 'name' => "title_opt[$tid]"]) ?>
        </section>
    <?php endforeach; ?>

    <button class="ad-btn">Guardar tarjetas</button>
    <a class="ad-btn is-ghost" href="/#galerias" target="_blank" rel="noopener" style="margin-left:.5rem">Ver la web ↗</a>
</form>
