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
<body class="ad <?= $bare ? 'ad-bare' : '' ?>">
<?php if (!$bare): ?>
<header class="ad-top">
    <a class="ad-logo" href="/admin"><?= e(Config::get('app.name')) ?> <small>Admin</small></a>
    <nav class="ad-nav">
        <a href="/admin" class="<?= $nav === 'galerias' ? 'is-on' : '' ?>">Galerías</a>
        <a href="/admin/fotos" class="<?= $nav === 'fotos' ? 'is-on' : '' ?>">Fotos</a>
        <a href="/admin/portada" class="<?= $nav === 'portada' ? 'is-on' : '' ?>">Portada</a>
        <a href="/admin/efectos" class="<?= $nav === 'efectos' ? 'is-on' : '' ?>">Efectos</a>
        <a href="/admin/entrada" class="<?= $nav === 'entrada' ? 'is-on' : '' ?>">Entrada</a>
        <a href="/admin/menu" class="<?= $nav === 'menu' ? 'is-on' : '' ?>">Menú</a>
        <a href="/admin/tarjetas" class="<?= $nav === 'tarjetas' ? 'is-on' : '' ?>">Tarjetas</a>
        <a href="/admin/sitio" class="<?= $nav === 'sitio' ? 'is-on' : '' ?>">Sitio</a>
        <a href="/admin/ajustes" class="<?= $nav === 'ajustes' ? 'is-on' : '' ?>">IA</a>
        <a href="/" target="_blank" rel="noopener">Ver web ↗</a>
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
