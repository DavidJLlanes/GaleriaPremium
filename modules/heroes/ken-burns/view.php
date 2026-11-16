<?php use App\Core\Module; use App\Models\Photo;
$dirs = [[-3, -2, 1.0, 1.22], [3, 2, 1.0, 1.2], [3, -2, 1.22, 1.02], [-3, 2, 1.2, 1.0]];     // dx%, dy%, escala inicial, escala final
$media = '<div class="hxk" data-interval="' . (int) ($interval ?? 6500) . '">';
foreach ($photos as $i => $p) {
    [$dx, $dy, $s0, $s1] = $dirs[$i % 4];
    $media .= '<div class="hxk-slide' . ($i === 0 ? ' is-active' : '') . '" style="--dx:' . $dx . '%;--dy:' . $dy . '%;--s0:' . $s0 . ';--s1:' . $s1 . '">'
            . '<img src="' . e(Photo::url($p['filename'])) . '" alt="' . e($p['title']) . '"' . ($i === 0 ? ' fetchpriority="high"' : ' loading="lazy"') . '></div>';
}
$media .= '<div class="hxk-sweep"></div></div>';
echo Module::render('components/hero-shell', ['class' => 'hx-kenburns', 'media' => $media, 'copy' => $copy ?? []]);
