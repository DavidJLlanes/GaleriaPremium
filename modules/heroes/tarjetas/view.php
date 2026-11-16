<?php use App\Core\Module; use App\Models\Photo;
$media = '<div class="hxt" data-interval="' . (int) ($interval ?? 6500) . '"><div class="hxt-bg">';
foreach ($photos as $i => $p) $media .= '<img class="' . ($i === 0 ? 'is-active' : '') . '" src="' . e(Photo::thumbUrl($p['filename'])) . '" alt="" aria-hidden="true">';
$media .= '</div><div class="hxt-stack">';
foreach ($photos as $i => $p)
    $media .= '<figure class="hxt-card" data-i="' . $i . '"><img src="' . e(Photo::url($p['filename'])) . '" alt="' . e($p['title']) . '" draggable="false"' . ($i < 3 ? '' : ' loading="lazy"') . '><figcaption>' . e($p['title']) . '</figcaption></figure>';
$media .= '</div></div>';
echo Module::render('components/hero-shell', ['class' => 'hx-tarjetas', 'media' => $media, 'copy' => $copy ?? []]);
