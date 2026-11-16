<?php use App\Core\{Config, Module}; use App\Models\Gallery; use App\Services\{Effects, Intro, Site, SiteMenu};
$showHeader = Site::on(!empty($isHome) ? 'header_home' : 'header_pages') || SiteMenu::previewing();
$fx = Effects::values();
if ($fx['lqip'] === '1') Module::use('components/photo-load');
if ($fx['transition'] === '1') Module::use('components/page-transition');
$iv = !empty($isHome) && ($tmp = Intro::values())['enabled'] === '1' ? $tmp : null;   // pantalla de entrada (solo portada)
$galleries = $showHeader ? Gallery::published() : []; $m = SiteMenu::values(); ?>
<!doctype html>
<html lang="<?= e(Site::get('lang')) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= e($title ?? Config::get('app.name')) ?></title>
<!--assets:css-->
<script>document.documentElement.classList.add('lq')</script>
<?php if ($iv): ?><script>(function(){var d=document.documentElement,f=<?= json_encode($iv['freq']) ?>;try{if(f==='session'&&sessionStorage.getItem('intro_seen'))return d.classList.add('no-intro');if(f==='day'){var t=+localStorage.getItem('intro_seen_d');if(t&&Date.now()-t<864e5)return d.classList.add('no-intro')}}catch(e){}d.classList.add('intro-on')})()</script><?php endif; ?>
<?= Module::render('components/typography') ?>
<?= Module::render('components/head-meta', get_defined_vars()) ?>
</head>
<body class="<?= e(($bodyClass ?? '') . ($showHeader ? '' : ' no-header')) ?>">
<?php if ($iv) echo Module::render('components/intro', ['i' => $iv]); ?>
<?php if ($showHeader) echo Module::render('components/header', ['galleries' => $galleries, 'm' => $m]); ?>
<?php if ($showHeader && $galleries) echo Module::render('components/menu-panel', ['galleries' => $galleries, 'm' => $m]); ?>
<main><?= $content ?></main>
<?= Module::render('components/footer') ?>
<?= Module::render('components/drop-upload') ?>
<?php if ($fx['smooth'] !== 'off') echo Module::render('components/smooth-scroll', ['level' => $fx['smooth']]);
if ($fx['cursor'] !== 'off') {
    $co = Effects::cursorOptions($fx['cursor']); $nat = Module::meta('cursors/' . $fx['cursor'])['native'] ?? 'show'; Module::use('cursors/' . $fx['cursor']);
    echo Module::render('components/cursor', ['design' => $fx['cursor'], 'native' => $nat, 'opts' => $co, 'color' => $co['color'] ?? '', 'label' => $fx['cursor_label'] !== '' ? $fx['cursor_label'] : 'Ver']);
}
if ($fx['grain'] !== 'off') echo Module::render('components/grain', ['level' => $fx['grain']]);
if ($fx['sound'] === '1') echo Module::render('components/sound'); ?>
<!--assets:js-->
</body>
</html>
