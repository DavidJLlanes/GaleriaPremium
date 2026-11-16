<?php use App\Core\Module; use App\Models\Photo;
$p = $photos[0]; $dir = ($opts['direction'] ?? 'v') === 'h' ? 'h' : 'v';
$media = '<img class="hxc-img" src="' . e(Photo::url($p['filename'])) . '" alt="' . e($p['title']) . '" fetchpriority="high">'
       . '<div class="hxc-panel hxc-a"></div><div class="hxc-panel hxc-b"></div><div class="hxc-seam"></div>';
echo Module::render('components/hero-shell', ['class' => 'hx-cortina hxc-' . $dir, 'media' => $media, 'copy' => $copy ?? []]);
