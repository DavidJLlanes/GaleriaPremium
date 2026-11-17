<?php use App\Services\Csrf;
$chk = static fn (string $k) => $f[$k] === '1' ? 'checked' : '';
$img = static fn (?string $u, string $alt) => $u ? '<img src="' . e($u) . '" alt="' . e($alt) . '">' : '<span class="st-none">Sin subir</span>'; ?>
<h1>Sitio</h1>
<p class="pnl-sub">Lo que afecta a toda la web: identidad, marca, colores, tipografías, webapp, redes y buscadores. La barra superior está en <a href="/admin/menu">Barra y menú</a>; el pie, en <a href="/admin/pie">Pie</a>.</p>

<form method="post" action="/admin/sitio" enctype="multipart/form-data" id="siteForm">
    <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">

    <section class="pnl-card">
        <h2>Identidad</h2>
        <div class="pnl-row">
            <div><label for="s_name">Nombre de la web</label><input id="s_name" type="text" name="site_name" value="<?= e($f['name']) ?>" maxlength="80"></div>
            <div><label for="s_tag">Eslogan</label><input id="s_tag" type="text" name="site_tagline" value="<?= e($f['tagline']) ?>"></div>
            <div><label for="s_lang">Idioma de la web</label>
                <select id="s_lang" name="site_lang"><?php foreach ($langs as $c => $l): ?><option value="<?= e($c) ?>" <?= $f['lang'] === $c ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select></div>
        </div>
        <p class="pnl-hint">El nombre y el eslogan se usan en la portada, la barra superior, el pie y las pestañas del navegador.</p>
    </section>

    <section class="pnl-card">
        <h2>Marca</h2>
        <div class="st-grid">
            <div class="st-asset">
                <label>Logo</label>
                <div class="st-prev is-wide"><?= $img($logo, 'Logo') ?></div>
                <input type="file" name="logo" accept="image/png,image/jpeg,image/webp,image/gif">
                <p class="pnl-hint">PNG con fondo transparente recomendado. Se guarda en WebP sin pérdida.</p>
                <?php if ($logo): ?><label class="pnl-check"><input type="checkbox" name="clear_logo"><span>Quitar el logo</span></label><?php endif; ?>
            </div>
            <div class="st-asset">
                <label>Icono de la webapp</label>
                <div class="st-prev"><?= $img($icon, 'Icono') ?></div>
                <input type="file" name="icon" accept="image/png,image/jpeg,image/webp,image/gif">
                <p class="pnl-hint">Cuadrado, 512×512 px o más (si es muy grande, se reduce solo). Con él se crean solos todos los iconos (ver abajo).</p>
                <?php if ($icon): ?><label class="pnl-check"><input type="checkbox" name="clear_icon"><span>Quitar el icono</span></label><?php endif; ?>
            </div>
            <div class="st-asset">
                <label>Imagen Open Graph</label>
                <div class="st-prev st-og-prev is-wide"><?= $ogImage ? '<img src="' . e($ogImage) . '" alt="Imagen Open Graph">' : '<span class="st-none">Imagen 1200 × 630</span>' ?></div>
                <input type="file" name="og_image" id="ogFile" accept="image/png,image/jpeg,image/webp">
                <input type="hidden" name="og_crop" id="ogCrop">
                <p class="pnl-hint">Al elegir la imagen se abre el recorte, siempre con la proporción fija 1,91:1. Se guarda como WebP 1200 × 630 al 75 %. Es la que se ve al compartir la web en redes y mensajería.</p>
                <?php if ($ogImage): ?><label class="pnl-check"><input type="checkbox" name="clear_og"><span>Quitar imagen</span></label><?php endif; ?>
            </div>
            <div class="st-asset">
                <label>Favicon (opcional)</label>
                <div class="st-prev is-small"><?= $img($favicon, 'Favicon') ?></div>
                <input type="file" name="favicon" accept="image/png,image/jpeg,image/webp,image/gif">
                <p class="pnl-hint">Si no subes uno, la pestaña del navegador usa el icono.</p>
                <?php if ($hasFaviconSrc): ?><label class="pnl-check"><input type="checkbox" name="clear_favicon"><span>Quitar mi favicon</span></label><?php endif; ?>
            </div>
        </div>
        <?php if ($icon): ?>
            <div class="st-gen">
                <p class="pnl-hint" style="margin-bottom:.8rem">Iconos generados automáticamente:</p>
                <ul>
                    <li><?= $img($favicon, '') ?><span>Favicon<br>16 · 32 · 48 px + .ico</span></li>
                    <li><?= $img($apple, '') ?><span>iPhone / iPad<br>apple-touch 180 px</span></li>
                    <li><?= $img($icon, '') ?><span>Android / PC<br>192 y 512 px</span></li>
                    <li><?= $img($maskable, '') ?><span>Android adaptativo<br>maskable 512 px</span></li>
                </ul>
                <p class="pnl-hint">También Windows (tile 150 px) y el manifest de la webapp.</p>
            </div>
        <?php endif; ?>
    </section>

    <section class="pnl-card">
        <h2>Colores</h2>
        <div class="pnl-row st-colors">
            <div><label for="s_acc">Acento</label><input id="s_acc" type="color" name="site_accent" value="<?= e($f['accent']) ?>"><p class="pnl-hint">Botones, enlaces y detalles.</p></div>
            <div><label for="s_theme">Color del tema</label><input id="s_theme" type="color" name="site_theme" value="<?= e($f['theme']) ?>"><p class="pnl-hint">Barra del navegador y fondo de los iconos iOS/Android.</p></div>
            <div><label for="s_bg">Fondo de la web</label><input id="s_bg" type="color" name="site_bg" value="<?= e($f['bg']) ?>"><p class="pnl-hint">Fondo general y pantalla de carga.</p></div>
        </div>
    </section>

    <?= \App\Core\Module::render('components/font-picker') ?>

    <section class="pnl-card">
        <h2>Webapp</h2>
        <div class="pnl-row">
            <div><label for="s_short">Nombre corto (icono del móvil)</label><input id="s_short" type="text" name="site_short_name" value="<?= e($f['short_name']) ?>" maxlength="14" placeholder="<?= e(\App\Services\Site::shortName()) ?>">
                <p class="pnl-hint">Máx. 12–14 letras: es el texto bajo el icono al instalar la webapp.</p></div>
            <div><label for="s_disp">Modo al instalarla</label>
            <select id="s_disp" name="site_display">
                <option value="standalone" <?= $f['display'] === 'standalone' ? 'selected' : '' ?>>App (sin barra del navegador)</option>
                <option value="fullscreen" <?= $f['display'] === 'fullscreen' ? 'selected' : '' ?>>Pantalla completa total</option>
                <option value="minimal-ui" <?= $f['display'] === 'minimal-ui' ? 'selected' : '' ?>>App con controles mínimos</option>
                <option value="browser" <?= $f['display'] === 'browser' ? 'selected' : '' ?>>Pestaña normal del navegador</option>
            </select></div>
        </div>
        <p class="pnl-hint">Para instalarla: en el móvil «Añadir a pantalla de inicio»; en Chrome/Edge de PC, el icono de instalar en la barra de direcciones.</p>
    </section>

    <section class="pnl-card">
        <h2>Redes y contacto</h2>
        <p class="pnl-hint" style="margin-bottom:1rem">Escribe solo tu <b>usuario</b>, sin la @ (si pegas la dirección completa, se queda con el usuario): la web completa el enlace. En la web aparecen únicamente los iconos de las redes que rellenes, cada uno con su enlace (se muestran en el <a href="/admin/pie">pie</a>).</p>
        <div class="st-socs">
            <?php foreach ($socials as $k => $label): ?>
                <div class="st-soc">
                    <label for="s_<?= e($k) ?>"><span class="st-soc-i"><?= \App\Core\Module::render('components/social-icons', ['icon' => $k]) ?></span><?= e($label) ?></label>
                    <div class="st-soc-in"><span class="st-soc-pre"><?= e(\App\Services\Site::SOCIAL_PREFIX[$k]) ?></span><input id="s_<?= e($k) ?>" type="text" name="site_<?= e($k) ?>" value="<?= e(\App\Services\Site::socialHandle($k)) ?>" placeholder="tuusuario" autocapitalize="none" autocomplete="off" spellcheck="false" maxlength="80"></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="st-mail"><label for="s_mail">Email de contacto</label><input id="s_mail" type="text" name="site_email" value="<?= e($f['email']) ?>" placeholder="hola@tudominio.es"></div>
    </section>

    <section class="pnl-card">
        <h2>Buscadores</h2>
        <div><label for="s_og_text">Texto al compartir (Open Graph)</label><textarea id="s_og_text" name="site_og_text" maxlength="200" rows="2"><?= e($f['og_text']) ?></textarea><p class="pnl-hint">Hasta 200 caracteres.</p></div>
        <div><label for="s_desc">Descripción (Google y redes sociales)</label><textarea id="s_desc" name="site_description" rows="2" maxlength="300"><?= e($f['description']) ?></textarea></div>
        <label class="pnl-check st-line" style="margin-top:1rem"><input type="checkbox" name="site_noindex" <?= $chk('noindex') ?>><span>Pedir a Google que <b>no</b> indexe la web (mientras la preparas)</span></label>
    </section>

    <?= \App\Core\Module::render('components/cropper') ?>
    <button class="pnl-btn">Guardar sitio</button>
</form>
