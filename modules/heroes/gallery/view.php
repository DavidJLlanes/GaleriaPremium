<?php use App\Core\Module; use App\Models\Photo;
$media = '<div class="hg" data-interval="' . (int) ($interval ?? 6500) . '">';
foreach ($photos as $i => $p) {
    $media .= '<img class="hg-slide' . ($i === 0 ? ' is-active' : '') . '" src="' . e(Photo::url($p['filename'])) . '" alt="' . e($p['title']) . '"'
            . ($i === 0 ? ' fetchpriority="high"' : ' loading="lazy"') . '>';
}
$media .= '</div>';
if (count($photos) > 1) {
    $media .= '<div class="hg-dots" aria-hidden="true">' . str_repeat('<i></i>', count($photos)) . '</div>';
}
echo Module::render('components/hero-shell', ['class' => 'hg-hero', 'media' => $media, 'copy' => $copy ?? []]);
