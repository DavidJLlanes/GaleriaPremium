<?php use App\Core\Module; use App\Models\Photo;
$p = $photos[0]; $u = e(Photo::url($p['filename'])); $a = e($p['title']);
$media = '<div class="hxl" style="--rad:' . (float) ($opts['radius'] ?? 26) . '"><img class="hxl-dim" src="' . $u . '" alt="' . $a . '" fetchpriority="high"><img class="hxl-lit" src="' . $u . '" alt="" aria-hidden="true"></div>';
echo Module::render('components/hero-shell', ['class' => 'hx-linterna', 'media' => $media, 'copy' => $copy ?? []]);
