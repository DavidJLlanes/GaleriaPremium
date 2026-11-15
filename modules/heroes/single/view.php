<?php use App\Core\Module; use App\Models\Photo;
$p = $photos[0];
$media = '<img class="hs-img" src="' . e(Photo::url($p['filename'])) . '" alt="' . e($p['title']) . '" fetchpriority="high">';
echo Module::render('components/hero-shell', ['class' => 'hs', 'media' => $media]);
