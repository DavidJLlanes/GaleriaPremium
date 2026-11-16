<?php use App\Core\Config; use App\Services\{Auth, Csrf, Flash};
$flash = Flash::pull(); $nav = $nav ?? ''; $bare = $bare ?? false; ?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<meta name="csrf" content="<?= e(Csrf::token()) ?>">
<title><?= e($title ?? 'Admin') ?> · <?= e(Config::get('app.name')) ?></title>
<!--assets:css-->
<?= App\Core\Module::render('components/typography', ['defaults' => true]) ?>
<?= App\Core\Module::render('components/head-meta', ['title' => $title ?? 'Admin', 'description' => 'Panel de administración']) ?>
</head>
<body class="ad <?= $bare ? 'ad-bare' : '' ?>" data-accent="<?= e(\App\Services\Site::get('accent')) ?>">
<?php App\Core\Module::use('components/color-accent'); ?>
<?php if (!$bare): ?>
<header class="ad-top">
    <a class="ad-logo" href="/admin"><?= e(Config::get('app.name')) ?> <small>Admin</small></a>
    <nav class="ad-nav" aria-label="Administración">
        <a href="/admin" title="Crear y ordenar galerías" class="<?= $nav === 'galerias' ? 'is-on' : '' ?>">Galerías</a>
        <a href="/admin/fotos" title="Todas las fotos de todas las galerías" class="<?= $nav === 'fotos' ? 'is-on' : '' ?>">Fotos</a>
        <i class="ad-sep" aria-hidden="true"></i>
        <a href="/admin/entrada" title="1. Pantalla de entrada (solo en la portada)" class="<?= $nav === 'entrada' ? 'is-on' : '' ?>">Entrada</a>
        <a href="/admin/menu" title="2. Barra superior y menú de galerías" class="<?= $nav === 'menu' ? 'is-on' : '' ?>">Barra y menú</a>
        <a href="/admin/portada" title="3. Sección grande con la que empieza la web" class="<?= $nav === 'portada' ? 'is-on' : '' ?>">Portada</a>
        <a href="/admin/tarjetas" title="4. Galerías en la página principal" class="<?= $nav === 'tarjetas' ? 'is-on' : '' ?>">Tarjetas</a>
        <a href="/admin/pie" title="5. Pie de página" class="<?= $nav === 'pie' ? 'is-on' : '' ?>">Pie</a>
        <i class="ad-sep" aria-hidden="true"></i>
        <a href="/admin/sitio" title="Identidad, marca, colores, tipografías, webapp y buscadores" class="<?= $nav === 'sitio' ? 'is-on' : '' ?>">Sitio</a>
        <a href="/admin/efectos" title="Cursor, scroll, transiciones, grano y sonido" class="<?= $nav === 'efectos' ? 'is-on' : '' ?>">Efectos</a>
        <a href="/admin/ajustes" title="Inteligencia artificial para nombres y descripciones" class="<?= $nav === 'ajustes' ? 'is-on' : '' ?>">IA</a>
        <a href="/" target="_blank" rel="noopener" class="ad-nav-web">Ver web ↗</a>
    </nav>
    <form method="post" action="/admin/logout" class="ad-logout">
        <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
        <span><?= e(Auth::username()) ?></span><button class="ad-link">Salir</button>
    </form>
</header>
<?php endif; ?>
<main class="ad-main">
    <?php if ($flash): ?><div class="ad-flash is-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
    <?= $content ?>
</main>
<div class="ad-toasts" id="adToasts" aria-live="polite"></div>
<?= App\Core\Module::render('components/drop-upload') ?>
<!--assets:js-->
</body>
</html>
