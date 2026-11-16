<?php use App\Core\Config; use App\Models\Photo; use App\Services\Site;
$name = Site::get('name'); $desc = $description ?? (Site::get('description') ?: Site::get('tagline'));
$icon = Site::hasBrand('icon-source.png'); $fav = Site::hasBrand('favicon.ico');
$v = static fn (string $f): string => (string) (int) @filemtime(Site::brandFile($f));
$og = Site::hasBrand('og.webp') ? Site::absolute('/uploads/brand/og.webp?v=' . (int) @filemtime(Site::brandFile('og.webp'))) : (!empty($ogImage) ? Site::absolute(Photo::url($ogImage)) : (Site::hasBrand('icon-512.png') ? Site::absolute('/uploads/brand/icon-512.png') : null));
$ogDesc = Site::get('og_text') ?: $desc;
$self = Site::absolute(strtok($_SERVER['REQUEST_URI'] ?? '/', '?'));
?>
<meta name="description" content="<?= e($desc) ?>">
<meta name="theme-color" content="<?= e(Site::get('theme')) ?>">
<?php if (Site::on('noindex')): ?><meta name="robots" content="noindex,nofollow"><?php endif; ?>
<link rel="canonical" href="<?= e($self) ?>">
<?php if ($fav): ?>
<link rel="icon" href="/favicon.ico?v=<?= $v('favicon.ico') ?>" sizes="48x48">
<link rel="icon" type="image/png" sizes="32x32" href="/uploads/brand/favicon-32.png?v=<?= $v('favicon-32.png') ?>">
<link rel="icon" type="image/png" sizes="16x16" href="/uploads/brand/favicon-16.png?v=<?= $v('favicon-16.png') ?>">
<?php else: ?><link rel="icon" href="data:,"><?php endif; ?>
<?php if ($icon): ?>
<link rel="apple-touch-icon" href="/apple-touch-icon.png?v=<?= $v('apple-touch-icon.png') ?>">
<meta name="msapplication-config" content="/browserconfig.xml">
<meta name="msapplication-TileColor" content="<?= e(Site::get('theme')) ?>">
<?php endif; ?>
<link rel="manifest" href="/manifest.webmanifest">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="<?= e(Site::shortName()) ?>">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= e($name) ?>">
<meta property="og:title" content="<?= e($title ?? $name) ?>">
<meta property="og:description" content="<?= e($desc) ?>">
<meta property="og:url" content="<?= e($self) ?>">
<?php if ($og): ?><meta property="og:image" content="<?= e($og) ?>"><meta name="twitter:card" content="summary_large_image"><?php endif; ?>
<style>:root{--accent:<?= e(Site::get('accent')) ?>;--bg:<?= e(Site::get('bg')) ?>}</style>
