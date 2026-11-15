<?php use App\Core\Config; use App\Services\Site;
$mode = Site::get('brand_mode'); $logo = Site::brandUrl('logo.webp');
if (!$logo) $mode = 'text'; ?>
<header class="hdr" id="siteHeader">
    <a class="hdr-brand" href="/" aria-label="<?= e(Config::get('app.name')) ?>">
        <?php if ($mode !== 'text'): ?><img class="hdr-logo" src="<?= e($logo) ?>" alt="<?= $mode === 'logo' ? e(Config::get('app.name')) : '' ?>"><?php endif; ?>
        <?php if ($mode !== 'logo'): ?><span><?= e(Config::get('app.name')) ?></span><?php endif; ?>
    </a>
    <nav class="hdr-nav"><a href="/#galerias">Galerías</a></nav>
</header>
