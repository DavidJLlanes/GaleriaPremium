<?php use App\Core\Module; use App\Services\{Csrf, ModuleOptions, SiteMenu};
$titles = ['icon' => 'Icono del botón («hamburguesa»)', 'reveal' => 'Efecto de despliegue', 'style' => 'Diseño del menú'];
$chk = static fn (string $k) => ($f[$k] ?? '') === '1' ? 'checked' : '';
$qs = http_build_query(['menu_preview' => 1, 'menu_open' => 1, 'mi' => $m['icon'], 'mr' => $m['reveal'], 'ms' => $m['style']]); ?>
<h1>Barra y menú</h1>
<p class="pnl-sub">La barra superior de la web (nombre o logo y botón «Galerías») y el menú que abre con tus galerías publicadas. Abajo, en orden: la barra, el botón, cómo se despliega y cómo se ve el menú.</p>

<form method="post" action="/admin/menu" id="menuForm" data-base="/?">
    <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">

    <section class="pnl-card mn-prevcard">
        <h2>Vista previa</h2>
        <iframe class="mn-prev" id="mnPrev" title="Vista previa del menú" src="/?<?= e($qs) ?>" loading="eager"></iframe>
        <p class="pnl-hint">Es tu web de verdad (con el menú abierto). Cada cambio que hagas abajo se aplica aquí al instante; usa el botón del menú para cerrarlo y volver a abrirlo. Los ajustes propios de cada diseño se ven al guardar.</p>
    </section>

    <section class="pnl-card">
        <h2>Barra superior</h2>
        <div class="mn-bar">
            <label class="pnl-check"><input type="checkbox" name="site_header_home" <?= $chk('header_home') ?>><span>Mostrar la barra en la <b>portada</b></span></label>
            <label class="pnl-check"><input type="checkbox" name="site_header_pages" <?= $chk('header_pages') ?>><span>Mostrar la barra en las <b>demás páginas</b> (galerías)</span></label>
        </div>
        <div class="mn-brand"><label for="s_brand">Marca en la barra</label>
            <select id="s_brand" name="site_brand_mode">
                <option value="text" <?= $f['brand_mode'] === 'text' ? 'selected' : '' ?>>Solo el nombre</option>
                <option value="logo" <?= $f['brand_mode'] === 'logo' ? 'selected' : '' ?>>Solo el logo</option>
                <option value="both" <?= $f['brand_mode'] === 'both' ? 'selected' : '' ?>>Logo + nombre</option>
            </select>
            <p class="pnl-hint">El logo, el nombre y la tipografía del nombre se eligen en <a href="/admin/sitio">Sitio</a> (Marca, Identidad y Tipografías).<?= $logo ? '' : ' Aún no hay logo subido.' ?></p></div>
        <h3>Colores de la barra</h3>
        <div class="pnl-row mn-colors">
            <?php foreach (['name' => 'Nombre de la web', 'menu' => 'Botón del menú', 'logo' => 'Logo (teñirlo)'] as $hk => $hl): ?>
            <div class="mn-col">
                <label><?= e($hl) ?></label>
                <label class="pnl-check"><input type="checkbox" name="site_hdr_<?= $hk ?>_on" <?= $chk("hdr_{$hk}_on") ?>><span>Usar un color propio</span></label>
                <input type="color" name="site_hdr_<?= $hk ?>_color" value="<?= e(preg_match('/^#[0-9a-fA-F]{6}$/', $f["hdr_{$hk}_color"]) ? $f["hdr_{$hk}_color"] : '#f2efe9') ?>">
            </div>
            <?php endforeach; ?>
        </div>
        <p class="pnl-hint">El tinte pinta la silueta del logo con el color elegido: funciona con logos de fondo transparente (PNG/WebP). Con un logo de fondo opaco saldría un rectángulo de color. Los colores se ven en la vista previa al guardar.</p>
    </section>

    <?php foreach ($kinds as $k => $mods): ?>
        <section class="pnl-card">
            <h2><?= e($titles[$k]) ?></h2>
            <?= Module::render('components/style-picker', ['styles' => $mods, 'name' => "menu_$k", 'selected' => $m[$k]]) ?>
        </section>
    <?php endforeach; ?>

    <?php foreach ($kinds as $k => $mods): foreach ($mods as $id => $mm): if (empty($mm['options'])) continue; ?>
        <section class="pnl-card" data-opts-kind="<?= e($k) ?>" data-opts-id="<?= e($id) ?>" hidden>
            <h2>Ajustes: «<?= e($mm['label']) ?>»</h2>
            <?= Module::render('components/option-fields', ['spec' => $mm['options'], 'values' => $id === $m[$k] ? $m['opts'][$k] : ModuleOptions::values(SiteMenu::KINDS[$k], $id, "menu_{$k}_opt"), 'name' => "opt_{$k}[$id]"]) ?>
        </section>
    <?php endforeach; endforeach; ?>
    <p class="pnl-hint">La tipografía del menú se elige en <a href="/admin/sitio">Sitio → Tipografías</a>: «Menú y etiquetas» (textos pequeños) y «Títulos de galerías» (nombres grandes). El menú muestra tus galerías publicadas, en el orden del panel Galerías.</p>

    <button class="pnl-btn">Guardar barra y menú</button>
    <a class="pnl-btn is-ghost" href="/" target="_blank" rel="noopener" style="margin-left:.5rem">Ver la web ↗</a>
</form>
