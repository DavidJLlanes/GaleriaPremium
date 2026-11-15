<?php use App\Core\{Config, Module}; ?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? Config::get('app.name')) ?></title>
<meta name="theme-color" content="#0a0a0b">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,500&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="icon" href="data:,">
<!--assets:css-->
</head>
<body class="<?= e($bodyClass ?? '') ?>">
<?= Module::render('components/header') ?>
<main><?= $content ?></main>
<?= Module::render('components/footer') ?>
<!--assets:js-->
</body>
</html>
