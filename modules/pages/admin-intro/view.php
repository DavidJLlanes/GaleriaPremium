<?php use App\Core\Module; use App\Services\{Csrf, Intro, ModuleOptions};
$common = array_intersect_key(Intro::SPEC, array_flip(['text', 'sub', 'dur', 'freq', 'exit', 'skip']));
$colors = array_intersect_key(Intro::SPEC, array_flip(['bg', 'fg', 'accent'])); ?>
<h1>Entrada</h1>
<p class="pnl-sub">Una pantalla breve con la identidad de la web que aparece al abrir la portada. Elige el diseño, el texto y cuándo se muestra.</p>
<form method="post" action="/admin/entrada" id="inForm">
    <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">

    <section class="pnl-card">
        <label class="pnl-check"><input type="checkbox" name="in[enabled]" value="1" <?= $v['enabled'] === '1' ? 'checked' : '' ?>><span><?= e(Intro::SPEC['enabled']['label']) ?></span></label>
        <p class="pnl-hint">Solo se muestra en la portada. Se puede saltar con un clic o una tecla y respeta «reducir movimiento» del sistema.</p>
    </section>

    <section class="pnl-card in-prevcard">
        <h2>Vista previa</h2>
        <iframe class="in-prev" id="inPrev" title="Vista previa de la entrada" loading="lazy"></iframe>
        <p class="pnl-hint"><button type="button" class="pnl-btn is-ghost" id="inPlay">▶ Ver otra vez</button> Es tu web de verdad con los ajustes de abajo (sin guardar todavía). Cambia cualquier ajuste y se vuelve a reproducir.</p>
    </section>

    <section class="pnl-card">
        <h2>Diseño</h2>
        <?= Module::render('components/style-picker', ['styles' => $designs, 'name' => 'in[design]', 'selected' => $v['design']]) ?>
    </section>

    <?php foreach ($designs as $id => $dm): if (empty($dm['options'])) continue; ?>
        <section class="pnl-card" data-opts-id="<?= e($id) ?>" hidden>
            <h2>Ajustes: «<?= e($dm['label']) ?>»</h2>
            <?= Module::render('components/option-fields', ['spec' => $dm['options'], 'values' => $id === $v['design'] ? $v['opts'] : Intro::options($id), 'name' => "in_opt[$id]"]) ?>
        </section>
    <?php endforeach; ?>

    <section class="pnl-card">
        <h2>Texto y tiempos</h2>
        <?= Module::render('components/option-fields', ['spec' => $common, 'values' => $v, 'name' => 'in']) ?>
    </section>
    <section class="pnl-card">
        <h2>Colores</h2>
        <?= Module::render('components/option-fields', ['spec' => $colors, 'values' => $v, 'name' => 'in']) ?>
    </section>
    <p class="pnl-hint">La tipografía (fuente, grosor y cursiva) se elige en <a href="/admin/sitio">Sitio → Tipografías</a>: «Pantalla de entrada» (texto grande) y «Menú y etiquetas» (texto pequeño).</p>
    <button class="pnl-btn">Guardar entrada</button>
</form>
