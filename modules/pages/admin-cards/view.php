<?php use App\Core\Module; use App\Models\Photo; use App\Services\Csrf;
Module::use('components/card-reveal'); Module::use('components/collection-card'); foreach (array_keys($reveals) as $r) Module::use("reveals/$r"); Module::use('card-titles/classic');
$sample = $sample ?? ['slug' => '', 'title' => 'Otoño en León', 'description' => '', 'cover' => null, 'cover_w' => 4, 'cover_h' => 5, 'photo_count' => 12]; ?>
<h1>Tarjetas</h1>
<p class="ad-sub">Cómo se ven las galerías en la página principal: cómo aparecen al hacer scroll y cómo se muestra el título de cada una.</p>

<form method="post" action="/admin/tarjetas" id="cardsForm" data-title="<?= e($c['style']) ?>">
    <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">

    <section class="ad-card">
        <h2>Aparición al hacer scroll</h2>
        <?= Module::render('components/style-picker', ['styles' => $reveals, 'name' => 'cards_reveal', 'selected' => $c['reveal']]) ?>
        <label class="ad-check cd-px"><input type="checkbox" name="cards_parallax" value="1" <?= $c['parallax'] ? 'checked' : '' ?>><span>Parallax suave: la foto se desplaza ligeramente con el scroll, con inercia</span></label>
        <div class="cd-play">
            <button type="button" class="ad-btn is-ghost is-small" id="cdPlay">▶ Probar el efecto</button>
            <div class="coll rv-on cd-prev" id="cdPrev" aria-hidden="true">
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

    <?php foreach ($titles as $tid => $tm): if (empty($tm['options'])) continue; ?>
        <section class="ad-card" data-title-opts="<?= e($tid) ?>" hidden>
            <h2>Ajustes: título «<?= e($tm['label']) ?>»</h2>
            <?= Module::render('components/option-fields', ['spec' => $tm['options'], 'values' => $tid === $c['style'] ? $c['style_opts'] : \App\Services\ModuleOptions::values('card-titles', $tid, \App\Services\Cards::PREFIX), 'name' => "title_opt[$tid]"]) ?>
        </section>
    <?php endforeach; ?>
    <p class="ad-hint">La tipografía, el grosor y la cursiva de los títulos se eligen en <a href="/admin/sitio">Sitio → Tipografías → «Títulos de galerías»</a>.</p>

    <button class="ad-btn">Guardar tarjetas</button>
    <a class="ad-btn is-ghost" href="/#galerias" target="_blank" rel="noopener" style="margin-left:.5rem">Ver la web ↗</a>
</form>
