<?php use App\Core\Module; use App\Models\Photo;
$cells = 7; $n = count($photos);
$media = '<div class="hxm" data-interval="' . (int) ($interval ?? 6500) . '" data-photos="' . e(json_encode(array_map(fn ($p) => ['u' => Photo::thumbUrl($p['filename']), 'a' => $p['title']], $photos), JSON_UNESCAPED_SLASHES)) . '">';
for ($i = 0; $i < $cells; $i++) {
    $p = $photos[$i % $n];
    $media .= '<div class="hxm-cell hxm-c' . $i . '"><div class="hxm-flip"><img class="hxm-f" src="' . e(Photo::thumbUrl($p['filename'])) . '" alt="' . e($p['title']) . '"' . ($i < 3 ? '' : ' loading="lazy"') . '><img class="hxm-b" alt="" aria-hidden="true"></div></div>';
}
$media .= '</div>';
echo Module::render('components/hero-shell', ['class' => 'hx-mosaico', 'media' => $media, 'copy' => $copy ?? []]);
