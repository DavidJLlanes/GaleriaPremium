<?php use App\Core\Module; use App\Services\{Csrf, ModuleOptions, SiteMenu};
$titles = ['icon' => 'Icono del botón («hamburguesa»)', 'reveal' => 'Efecto de despliegue', 'style' => 'Diseño del menú'];
$qs = http_build_query(['menu_preview' => 1, 'menu_open' => 1, 'mi' => $m['icon'], 'mr' => $m['reveal'], 'ms' => $m['style']]); ?>
<h1>Menú</h1>
<p class="ad-sub">El texto «Galerías» de la barra superior es un botón que abre un menú con tus galerías publicadas. Elige cómo es el botón, cómo se despliega y cómo se ve el menú.</p>

<form method="post" action="/admin/menu" id="menuForm" data-base="/?">
    <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">

    <section class="ad-card mn-prevcard">
        <h2>Vista previa</h2>
        <iframe class="mn-prev" id="mnPrev" title="Vista previa del menú" src="/?<?= e($qs) ?>" loading="eager"></iframe>
        <p class="ad-hint">Es tu web de verdad (con el menú abierto). Cada cambio que hagas abajo se aplica aquí al instante; usa el botón del menú para cerrarlo y volver a abrirlo. Los ajustes propios de cada diseño se ven al guardar.</p>
    </section>

    <?php foreach ($kinds as $k => $mods): ?>
        <section class="ad-card">
            <h2><?= e($titles[$k]) ?></h2>
            <?= Module::render('components/style-picker', ['styles' => $mods, 'name' => "menu_$k", 'selected' => $m[$k]]) ?>
        </section>
    <?php endforeach; ?>

    <?php foreach ($kinds as $k => $mods): foreach ($mods as $id => $mm): if (empty($mm['options'])) continue; ?>
        <section class="ad-card" data-opts-kind="<?= e($k) ?>" data-opts-id="<?= e($id) ?>" hidden>
            <h2>Ajustes: «<?= e($mm['label']) ?>»</h2>
            <?= Module::render('components/option-fields', ['spec' => $mm['options'], 'values' => $id === $m[$k] ? $m['opts'][$k] : ModuleOptions::values(SiteMenu::KINDS[$k], $id, "menu_{$k}_opt"), 'name' => "opt_{$k}[$id]"]) ?>
        </section>
    <?php endforeach; endforeach; ?>
    <p class="ad-hint">La tipografía del menú se elige en <a href="/admin/sitio">Sitio → Tipografías</a>: «Menú y etiquetas» (textos pequeños) y «Títulos de galerías» (nombres grandes). El menú muestra tus galerías publicadas, en el orden del panel Galerías.</p>

    <button class="ad-btn">Guardar menú</button>
    <a class="ad-btn is-ghost" href="/" target="_blank" rel="noopener" style="margin-left:.5rem">Ver la web ↗</a>
</form>
