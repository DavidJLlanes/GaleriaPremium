<?php use App\Core\Module; use App\Services\{Csrf, Footer};
$groups = ['Texto' => [['text'], 'ft-wide'], 'Qué mostrar' => [['tag', 'social', 'contact', 'galleries', 'top', 'admin'], 'ft-checks'], 'Firma' => [['sig_text', 'sig_url', 'sig_style'], 'ft-sig']];
$fv = fn(array $keys) => array_intersect_key(Footer::SPEC, array_flip($keys)); ?>
<h1>Pie</h1>
<p class="ad-sub">El pie de página de toda la web: cómo es (diseño), cómo llega al final de la página (efecto), qué contiene y tu firma.</p>
<form method="post" action="/admin/pie" id="ftForm">
    <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">

    <section class="ad-card">
        <label class="ad-check"><input type="checkbox" name="ft[enabled]" value="1" <?= $v['enabled'] === '1' ? 'checked' : '' ?>><span><?= e(Footer::SPEC['enabled']['label']) ?></span></label>
    </section>

    <section class="ad-card ft-prevcard">
        <h2>Vista previa</h2>
        <iframe class="ft-prev" id="ftPrev" title="Vista previa del pie" loading="eager"></iframe>
        <p class="ad-hint"><button type="button" class="ad-btn is-ghost" id="ftPlay">▶ Ver la llegada otra vez</button> Es tu web de verdad, bajando hasta el pie con los ajustes de abajo (sin guardar todavía).</p>
    </section>

    <section class="ad-card"><h2>Diseño del pie</h2><?= Module::render('components/style-picker', ['styles' => $designs, 'name' => 'ft[design]', 'selected' => $v['design']]) ?></section>
    <?php foreach ($designs as $id => $dm): if (empty($dm['options'])) continue; ?>
        <section class="ad-card" data-opts-kind="design" data-opts-id="<?= e($id) ?>" hidden>
            <h2>Ajustes del diseño «<?= e($dm['label']) ?>»</h2>
            <?= Module::render('components/option-fields', ['spec' => $dm['options'], 'values' => $id === $v['design'] ? $v['opts'] : \App\Services\ModuleOptions::values('footers', $id, 'footer_opt'), 'name' => "fo[$id]"]) ?>
        </section>
    <?php endforeach; ?>

    <section class="ad-card"><h2>Llegada y visualización</h2><?= Module::render('components/style-picker', ['styles' => $reveals, 'name' => 'ft[reveal]', 'selected' => $v['reveal']]) ?></section>
    <?php foreach ($reveals as $id => $rm): if (empty($rm['options'])) continue; ?>
        <section class="ad-card" data-opts-kind="reveal" data-opts-id="<?= e($id) ?>" hidden>
            <h2>Ajustes de la llegada «<?= e($rm['label']) ?>»</h2>
            <?= Module::render('components/option-fields', ['spec' => $rm['options'], 'values' => $id === $v['reveal'] ? $v['ropts'] : \App\Services\ModuleOptions::values('footer-reveals', $id, 'footer_ropt'), 'name' => "fr[$id]"]) ?>
        </section>
    <?php endforeach; ?>

    <section class="ad-card"><h2>Aspecto</h2><?= Module::render('components/option-fields', ['spec' => $fv(['tone']), 'values' => $v, 'name' => 'ft']) ?></section>
    <?php foreach ($groups as $title => [$keys, $cls]): ?>
        <section class="ad-card <?= e($cls) ?>"><h2><?= e($title) ?></h2><?= Module::render('components/option-fields', ['spec' => $fv($keys), 'values' => $v, 'name' => 'ft']) ?></section>
    <?php endforeach; ?>
    <p class="ad-hint">La tipografía se elige en <a href="/admin/sitio">Sitio → Tipografías</a>: «Pie de página» (textos) y «Nombre de la web» (nombre grande en los diseños que lo usan). Redes y correo se editan en Sitio.</p>
    <button class="ad-btn">Guardar pie</button>
</form>
