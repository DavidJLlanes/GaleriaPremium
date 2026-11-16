<?php use App\Core\Module; use App\Models\Photo; use App\Services\HeroVideo;
$p = $photos[0]; $file = (string) ($opts['file'] ?? ''); $has = $file !== '' && HeroVideo::exists($file);
$media = '<div class="hxv" style="--ov:' . max(0, min(.8, (float) ($opts['overlay'] ?? 35) / 100)) . '" data-mobile-off="' . (($opts['mobile'] ?? '0') === '1' ? '1' : '0') . '">'
       . '<img class="hxv-poster" src="' . e(Photo::url($p['filename'])) . '" alt="' . e($p['title']) . '" fetchpriority="high">'
       . ($has ? '<video class="hxv-video" muted loop playsinline autoplay preload="auto" disablepictureinpicture aria-hidden="true"><source src="' . e(HeroVideo::url($file)) . '" type="' . HeroVideo::mime($file) . '"></video>' : '')
       . '<div class="hxv-veil"></div></div>';
echo Module::render('components/hero-shell', ['class' => 'hx-video', 'media' => $media, 'copy' => $copy ?? []]);
