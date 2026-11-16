<?php use App\Services\Csrf;
$chk = static fn (string $k) => $f[$k] === '1' ? 'checked' : '';
$img = static fn (?string $u, string $alt) => $u ? '<img src="' . e($u) . '" alt="' . e($alt) . '">' : '<span class="st-none">Sin subir</span>'; ?>
<h1>Sitio</h1>
<p class="ad-sub">Identidad, marca, colores, webapp y redes de toda la web.</p>

<form method="post" action="/admin/sitio" enctype="multipart/form-data" id="siteForm">
    <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">

    <section class="ad-card">
        <h2>Identidad</h2>
        <div class="ad-row">
            <div><label for="s_name">Nombre de la web</label><input id="s_name" type="text" name="site_name" value="<?= e($f['name']) ?>" maxlength="80"></div>
            <div><label for="s_short">Nombre corto (icono del móvil)</label><input id="s_short" type="text" name="site_short_name" value="<?= e($f['short_name']) ?>" maxlength="14" placeholder="<?= e(\App\Services\Site::shortName()) ?>">
                <p class="ad-hint">Máx. 12–14 letras: es el texto bajo el icono al instalar la webapp.</p></div>
        </div>
        <div class="ad-row">
            <div><label for="s_tag">Eslogan</label><input id="s_tag" type="text" name="site_tagline" value="<?= e($f['tagline']) ?>"></div>
            <div><label for="s_lang">Idioma de la web</label>
                <select id="s_lang" name="site_lang"><?php foreach ($langs as $c => $l): ?><option value="<?= e($c) ?>" <?= $f['lang'] === $c ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select></div>
        </div>
        <div><label for="s_desc">Descripción (Google y redes sociales)</label><textarea id="s_desc" name="site_description" rows="2" maxlength="300"><?= e($f['description']) ?></textarea></div>
    </section>

    <section class="ad-card">
        <h2>Marca</h2>
        <div class="st-grid">
            <div class="st-asset">
                <label>Logo</label>
                <div class="st-prev is-wide"><?= $img($logo, 'Logo') ?></div>
                <input type="file" name="logo" accept="image/png,image/jpeg,image/webp,image/gif">
                <p class="ad-hint">PNG con fondo transparente recomendado. Se guarda en WebP sin pérdida.</p>
                <?php if ($logo): ?><label class="ad-check"><input type="checkbox" name="clear_logo"><span>Quitar el logo</span></label><?php endif; ?>
            </div>
            <div class="st-asset">
                <label>Icono de la webapp</label>
                <div class="st-prev"><?= $img($icon, 'Icono') ?></div>
                <input type="file" name="icon" accept="image/png,image/jpeg,image/webp,image/gif">
                <p class="ad-hint">Cuadrado, 512×512 px o más (si es muy grande, se reduce solo). Con él se crean solos todos los iconos (ver abajo).</p>
                <?php if ($icon): ?><label class="ad-check"><input type="checkbox" name="clear_icon"><span>Quitar el icono</span></label><?php endif; ?>
            </div>
            <div class="st-asset">
                <label>Favicon (opcional)</label>
                <div class="st-prev is-small"><?= $img($favicon, 'Favicon') ?></div>
                <input type="file" name="favicon" accept="image/png,image/jpeg,image/webp,image/gif">
                <p class="ad-hint">Si no subes uno, la pestaña del navegador usa el icono.</p>
                <?php if ($hasFaviconSrc): ?><label class="ad-check"><input type="checkbox" name="clear_favicon"><span>Quitar mi favicon</span></label><?php endif; ?>
            </div>
        </div>
        <div style="margin-top:1.2rem;max-width:360px"><label for="s_brand">Marca en la barra superior</label>
            <select id="s_brand" name="site_brand_mode">
                <option value="text" <?= $f['brand_mode'] === 'text' ? 'selected' : '' ?>>Solo el nombre</option>
                <option value="logo" <?= $f['brand_mode'] === 'logo' ? 'selected' : '' ?>>Solo el logo</option>
                <option value="both" <?= $f['brand_mode'] === 'both' ? 'selected' : '' ?>>Logo + nombre</option>
            </select></div>

        <?php if ($icon): ?>
            <div class="st-gen">
                <p class="ad-hint" style="margin-bottom:.8rem">Iconos generados automáticamente:</p>
                <ul>
                    <li><?= $img($favicon, '') ?><span>Favicon<br>16 · 32 · 48 px + .ico</span></li>
                    <li><?= $img($apple, '') ?><span>iPhone / iPad<br>apple-touch 180 px</span></li>
                    <li><?= $img($icon, '') ?><span>Android / PC<br>192 y 512 px</span></li>
                    <li><?= $img($maskable, '') ?><span>Android adaptativo<br>maskable 512 px</span></li>
                </ul>
                <p class="ad-hint">También Windows (tile 150 px) y el manifest de la webapp.</p>
            </div>
        <?php endif; ?>
    </section>

    <section class="ad-card">
        <h2>Colores</h2>
        <div class="ad-row st-colors">
            <div><label for="s_acc">Acento</label><input id="s_acc" type="color" name="site_accent" value="<?= e($f['accent']) ?>"><p class="ad-hint">Botones, enlaces y detalles.</p></div>
            <div><label for="s_theme">Color del tema</label><input id="s_theme" type="color" name="site_theme" value="<?= e($f['theme']) ?>"><p class="ad-hint">Barra del navegador y fondo de los iconos iOS/Android.</p></div>
            <div><label for="s_bg">Fondo de la web</label><input id="s_bg" type="color" name="site_bg" value="<?= e($f['bg']) ?>"><p class="ad-hint">Fondo general y pantalla de carga.</p></div>
        </div>
    </section>

    <?= \App\Core\Module::render('components/font-picker') ?>

    <section class="ad-card">
        <h2>Barra superior</h2>
        <label class="ad-check st-line"><input type="checkbox" name="site_header_home" <?= $chk('header_home') ?>><span>Mostrar la barra superior en la <b>portada</b></span></label>
        <label class="ad-check st-line"><input type="checkbox" name="site_header_pages" <?= $chk('header_pages') ?>><span>Mostrar la barra superior en las <b>demás páginas</b> (galerías)</span></label>
        <p class="ad-hint">El pie de página (diseño, llegada, textos y firma) se configura en <a href="/admin/pie">Pie</a>.</p>
    </section>

    <section class="ad-card">
        <h2>Webapp</h2>
        <div style="max-width:420px"><label for="s_disp">Modo al instalarla</label>
            <select id="s_disp" name="site_display">
                <option value="standalone" <?= $f['display'] === 'standalone' ? 'selected' : '' ?>>App (sin barra del navegador)</option>
                <option value="fullscreen" <?= $f['display'] === 'fullscreen' ? 'selected' : '' ?>>Pantalla completa total</option>
                <option value="minimal-ui" <?= $f['display'] === 'minimal-ui' ? 'selected' : '' ?>>App con controles mínimos</option>
                <option value="browser" <?= $f['display'] === 'browser' ? 'selected' : '' ?>>Pestaña normal del navegador</option>
            </select></div>
        <p class="ad-hint" style="margin-top:.6rem">Para instalarla: en el móvil «Añadir a pantalla de inicio»; en Chrome/Edge de PC, el icono de instalar en la barra de direcciones.</p>
    </section>

    <section class="ad-card">
        <h2>Redes y contacto</h2>
        <div class="ad-row">
            <?php foreach ($socials as $k => $label): ?>
                <div><label for="s_<?= e($k) ?>"><?= e($label) ?></label><input id="s_<?= e($k) ?>" type="text" inputmode="url" name="site_<?= e($k) ?>" value="<?= e($f[$k]) ?>" placeholder="https://"></div>
            <?php endforeach; ?>
            <div><label for="s_mail">Email de contacto</label><input id="s_mail" type="text" name="site_email" value="<?= e($f['email']) ?>" placeholder="hola@tudominio.es"></div>
        </div>
    </section>

    <section class="ad-card">
        <h2>Buscadores</h2>
        <label class="ad-check st-line"><input type="checkbox" name="site_noindex" <?= $chk('noindex') ?>><span>Pedir a Google que <b>no</b> indexe la web (mientras la preparas)</span></label>
    </section>

    <button class="ad-btn">Guardar sitio</button>
</form>
