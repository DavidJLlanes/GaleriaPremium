<?php use App\Core\Config; use App\Services\{Auth, Csrf, Flash};
$flash = Flash::pull(); $nav = $nav ?? ''; $bare = $bare ?? false; ?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<meta name="robots" content="noindex,nofollow">
<meta name="csrf" content="<?= e(Csrf::token()) ?>">
<title><?= e($title ?? 'Admin') ?> · <?= e(Config::get('app.name')) ?></title>
<!--assets:css-->
<?= App\Core\Module::render('components/typography', ['defaults' => true]) ?>
<?= App\Core\Module::render('components/head-meta', ['title' => $title ?? 'Admin', 'description' => 'Panel de administración']) ?>
</head>
<body class="pnl <?= $bare ? 'pnl-bare' : '' ?>" data-accent="<?= e(\App\Services\Site::get('accent')) ?>">
<?php App\Core\Module::use('components/color-accent'); ?>
<?php if (!$bare): ?>
<header class="pnl-top">
    <a class="pnl-logo" href="/admin"><?= e(Config::get('app.name')) ?> <small>Admin</small></a>
    <nav class="pnl-nav" aria-label="Administración">
        <a href="/admin" title="Crear y ordenar galerías" class="<?= $nav === 'galerias' ? 'is-on' : '' ?>">Galerías</a>
        <a href="/admin/sliders" title="Pases de fotos entre las galerías" class="<?= $nav === 'sliders' ? 'is-on' : '' ?>">Sliders</a>
        <a href="/admin/fotos" title="Todas las fotos de todas las galerías" class="<?= $nav === 'fotos' ? 'is-on' : '' ?>">Fotos</a>
        <a href="/admin/categorias" title="Categorías de las fotos y su diseño" class="<?= $nav === 'categorias' ? 'is-on' : '' ?>">Categorías</a>
        <i class="pnl-sep" aria-hidden="true"></i>
        <a href="/admin/entrada" title="1. Pantalla de entrada (solo en la portada)" class="<?= $nav === 'entrada' ? 'is-on' : '' ?>">Entrada</a>
        <a href="/admin/menu" title="2. Barra superior y menú de galerías" class="<?= $nav === 'menu' ? 'is-on' : '' ?>">Barra y menú</a>
        <a href="/admin/portada" title="3. Sección grande con la que empieza la web" class="<?= $nav === 'portada' ? 'is-on' : '' ?>">Portada</a>
        <a href="/admin/tarjetas" title="4. Galerías en la página principal" class="<?= $nav === 'tarjetas' ? 'is-on' : '' ?>">Tarjetas</a>
        <a href="/admin/pie" title="5. Pie de página" class="<?= $nav === 'pie' ? 'is-on' : '' ?>">Pie</a>
        <i class="pnl-sep" aria-hidden="true"></i>
        <a href="/admin/sitio" title="Identidad, marca, colores, tipografías, webapp y buscadores" class="<?= $nav === 'sitio' ? 'is-on' : '' ?>">Sitio</a>
        <a href="/admin/efectos" title="Cursor, scroll, transiciones, grano y sonido" class="<?= $nav === 'efectos' ? 'is-on' : '' ?>">Efectos</a>
        <a href="/admin/ajustes" title="Inteligencia artificial para nombres y descripciones" class="<?= $nav === 'ajustes' ? 'is-on' : '' ?>">IA</a>
        <a href="/admin/acceso" title="Entrar con huella o Face ID" class="<?= $nav === 'acceso' ? 'is-on' : '' ?>">Acceso</a>
        <a href="/" target="_blank" rel="noopener" class="pnl-nav-web">Ver web ↗</a>
    </nav>
    <form method="post" action="/admin/logout" class="pnl-logout">
        <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
        <span><?= e(Auth::username()) ?></span><button class="pnl-link">Salir</button>
    </form>
</header>
<?php endif; ?>
<main class="pnl-main">
    <?php if ($flash): ?><div class="pnl-flash is-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
    <?= $content ?>
</main>
<div class="pnl-toasts" id="pnlToasts" aria-live="polite"></div>
<?= App\Core\Module::render('components/drop-upload') ?>
<!--assets:js-->
</body>
</html>
