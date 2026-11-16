<?php use App\Core\{Config, Module}; use App\Services\Site;
$mode = Site::get('brand_mode'); $logo = Site::brandUrl('logo.webp');
if (!$logo) $mode = 'text';
$col = fn (string $k) => Site::on("hdr_{$k}_on") && preg_match('/^#[0-9a-fA-F]{6}$/', Site::get("hdr_{$k}_color")) ? Site::get("hdr_{$k}_color") : '';
$vars = ($col('name') ? '--hdr-name:' . $col('name') . ';' : '') . ($col('menu') ? '--hdr-menu:' . $col('menu') . ';' : '');
$tint = $col('logo'); ?>
<header class="hdr" id="siteHeader"<?= $vars ? ' style="' . e($vars) . '"' : '' ?>>
    <a class="hdr-brand" href="/" aria-label="<?= e(Config::get('app.name')) ?>">
        <?php if ($mode !== 'text'): ?><?php if ($tint): /* logo teñido: su silueta (transparencia) hace de máscara sobre el color elegido */ ?><i class="hdr-tint" style="background:<?= e($tint) ?>;-webkit-mask-image:url('<?= e($logo) ?>');mask-image:url('<?= e($logo) ?>')"><?php endif; ?><img class="hdr-logo" src="<?= e($logo) ?>" alt="<?= $mode === 'logo' ? e(Config::get('app.name')) : '' ?>"><?php if ($tint): ?></i><?php endif; ?><?php endif; ?>
        <?php if ($mode !== 'logo'): ?><span><?= e(Config::get('app.name')) ?></span><?php endif; ?>
    </a>
    <?php if (!empty($galleries)): ?><nav class="hdr-nav" aria-label="Galerías"><?= Module::render('components/menu', ['m' => $m]) ?></nav><?php endif; ?>
</header>
