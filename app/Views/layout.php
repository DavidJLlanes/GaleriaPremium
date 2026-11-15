<?php use App\Core\{Config, View}; $e = [View::class, 'e']; ?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $e($title ?? Config::get('app.name')) ?></title>
<meta name="theme-color" content="#0a0a0b">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,500&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="<?= !empty($isHome) ? 'is-home' : '' ?>">
<header class="site-header" id="siteHeader">
    <a class="brand" href="/"><?= $e(Config::get('app.name')) ?></a>
    <nav><a href="/#galerias">Galerías</a></nav>
</header>
<main><?= $content ?></main>
<footer class="site-footer">
    <span>© <?= date('Y') ?> <?= $e(Config::get('app.name')) ?></span>
    <span><?= $e(Config::get('app.tagline')) ?></span>
</footer>
<script src="/assets/js/app.js" defer></script>
</body>
</html>
