<?php use App\Core\Module; use App\Models\Photo;
$a = $photos[0]; $b = $photos[1] ?? $photos[0]; $c = $photos[2] ?? null;
$img = static fn (array $p, string $cls, bool $big = false) => '<img class="' . $cls . '" src="' . e($big ? Photo::url($p['filename']) : Photo::thumbUrl($p['filename'])) . '" alt="' . e($p['title']) . '" draggable="false">';
$media = '<div class="hxp" style="--ar:' . max(.5, min(1.9, ($b['width'] ?? 3) / max(1, $b['height'] ?? 2))) . '">'
       . '<div class="hxp-back" data-d=".35">' . $img($a, 'hxp-bg', true) . '</div>'
       . ($c ? '<figure class="hxp-card hxp-small" data-d="1.9">' . $img($c, 'hxp-i') . '</figure>' : '')
       . '<figure class="hxp-card hxp-main" data-d="1">' . $img($b, 'hxp-i', true) . '</figure></div>';
echo Module::render('components/hero-shell', ['class' => 'hx-parallax', 'media' => $media, 'copy' => $copy ?? []]);
