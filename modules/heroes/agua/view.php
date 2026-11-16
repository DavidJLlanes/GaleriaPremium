<?php use App\Core\Module; use App\Models\Photo;
$p = $photos[0]; $u = e(Photo::url($p['filename']));
$media = '<div class="hxw" data-src="' . $u . '" data-strength="' . (float) ($opts['strength'] ?? 1.2) . '"><img class="hxw-img" src="' . $u . '" alt="' . e($p['title']) . '" fetchpriority="high"><canvas class="hxw-canvas" aria-hidden="true"></canvas></div>';
echo Module::render('components/hero-shell', ['class' => 'hx-agua', 'media' => $media, 'copy' => $copy ?? []]);
