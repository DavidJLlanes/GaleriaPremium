<?php use App\Core\{Config, Module}; use App\Services\Site;
$showHeader = Site::on(!empty($isHome) ? 'header_home' : 'header_pages'); ?>
<!doctype html>
<html lang="<?= e(Site::get('lang')) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= e($title ?? Config::get('app.name')) ?></title>
<!--assets:css-->
<?= Module::render('components/typography') ?>
<?= Module::render('components/head-meta', get_defined_vars()) ?>
</head>
<body class="<?= e(($bodyClass ?? '') . ($showHeader ? '' : ' no-header')) ?>">
<?php if ($showHeader) echo Module::render('components/header'); ?>
<main><?= $content ?></main>
<?= Module::render('components/footer') ?>
<?= Module::render('components/drop-upload') ?>
<!--assets:js-->
</body>
</html>
