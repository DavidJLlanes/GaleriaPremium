<?php use App\Core\{Config, Module}; use App\Models\Gallery; use App\Services\{Site, SiteMenu};
$showHeader = Site::on(!empty($isHome) ? 'header_home' : 'header_pages') || SiteMenu::previewing();
$galleries = $showHeader ? Gallery::published() : []; $m = SiteMenu::values(); ?>
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
<?php if ($showHeader) echo Module::render('components/header', ['galleries' => $galleries, 'm' => $m]); ?>
<?php if ($showHeader && $galleries) echo Module::render('components/menu-panel', ['galleries' => $galleries, 'm' => $m]); ?>
<main><?= $content ?></main>
<?= Module::render('components/footer') ?>
<?= Module::render('components/drop-upload') ?>
<!--assets:js-->
</body>
</html>
