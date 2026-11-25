<?php use App\Core\Module; use App\Models\Photo;
$o = $options ?? [];
$dur = ['s' => 1000, 'm' => 1500, 'l' => 2400][$o['duration'] ?? 'm'] ?? 1500;
$noi = ['s' => 2.2, 'm' => 3.5, 'l' => 6][$o['noise'] ?? 'm'] ?? 3.5;
$auto = (int) (($o['autoplay'] ?? 'off') === 'off' ? 0 : $o['autoplay']) * 1000;
$thumbs = !empty($o['thumbs']) && count($photos) > 1; $caps = !empty($o['captions']); $n = count($photos);
$data = array_map(fn($p) => ['src' => Photo::url($p['filename']), 'alt' => $p['title']], $photos); ?>
<section class="gm" tabindex="0" role="region" aria-roledescription="carousel" aria-label="<?= e($gallery['title'] ?? 'Galería') ?>"
         data-dur="<?= $dur ?>" data-noise="<?= $noi ?>" data-auto="<?= $auto ?>">
    <div class="gm-fallback" aria-hidden="true">
        <?php foreach ($photos as $i => $p): ?><img src="<?= e(Photo::url($p['filename'])) ?>" alt="" class="<?= $i === 0 ? 'is-on' : '' ?>" <?= $i > 1 ? 'loading="lazy"' : '' ?> draggable="false"><?php endforeach; ?>
    </div>
    <canvas class="gm-cv" aria-hidden="true"></canvas>
    <div class="gm-shade" aria-hidden="true"></div>
    <div class="gm-src" data-lightbox>
        <?php foreach ($photos as $i => $p): ?><a href="<?= e(Photo::url($p['filename'])) ?>" data-title="<?= e($p['title']) ?>" tabindex="-1" aria-hidden="true"><img src="<?= e(Photo::thumbUrl($p['filename'])) ?>" alt="" data-lb-thumb></a><?php endforeach; ?>
    </div>
    <div class="gm-meta">
        <?php foreach ($photos as $i => $p): ?>
            <div class="gm-m<?= $i === 0 ? ' is-on' : '' ?>">
                <?php if ($caps): ?><h2 class="gm-t"><?= e($p['title']) ?></h2><?php if (!empty($p['description'])): ?><p class="gm-d"><?= e($p['description']) ?></p><?php endif; endif; ?>
                <?= Module::render('components/photo-cats', ['cats' => $p['cats'] ?? []]) ?>
            </div>
        <?php endforeach; ?>
        <span class="gm-n"><b>01</b> / <?= str_pad((string) $n, 2, '0', STR_PAD_LEFT) ?></span>
    </div>
    <?php if ($n > 1): ?>
        <button type="button" class="gm-ar gm-prev" aria-label="Anterior"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg></button>
        <button type="button" class="gm-ar gm-next" aria-label="Siguiente"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></button>
    <?php endif; ?>
    <?php if ($thumbs): ?>
        <ul class="gm-th"><?php foreach ($photos as $i => $p): ?><li><button type="button" class="<?= $i === 0 ? 'is-on' : '' ?>" data-i="<?= $i ?>" aria-label="<?= e($p['title']) ?>"><img src="<?= e(Photo::thumbUrl($p['filename'])) ?>" alt="" loading="lazy" draggable="false"></button></li><?php endforeach; ?></ul>
    <?php endif; ?>
    <script type="application/json" class="gm-data"><?= json_encode($data, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
</section>
