<?php use App\Core\Module; use App\Models\Photo; use App\Services\Crop;
$o = $options ?? []; $n = count($photos);
$ratio = (float) ($gallery['crop_ratio'] ?? 0) ?: 1;
$auto = ($o['autoplay'] ?? 'off') === 'off' ? 0 : ($o['autoplay'] === '7' ? 7000 : 4500); ?>
<section class="gc" data-auto="<?= $auto ?>" style="--gc-ar:<?= round($ratio, 5) ?>">
    <div class="gc-stage" tabindex="0" role="region" aria-roledescription="carousel" aria-label="<?= e($gallery['title'] ?? 'Galería') ?>">
        <div class="gc-frame" data-lightbox>
            <?php foreach ($photos as $i => $p): $c = Crop::of($p); ?>
                <a class="gc-ly<?= $i === 0 ? ' is-now' : '' ?>" href="<?= e(Photo::url($p['filename'])) ?>" data-title="<?= e($p['title']) ?>" draggable="false" aria-label="<?= e($p['title']) ?>"<?= $i > 0 ? ' tabindex="-1"' : '' ?>>
                    <img src="<?= e(Photo::thumbUrl($p['filename'])) ?>" alt="" draggable="false" <?= $i > 2 ? 'loading="lazy"' : '' ?>
                         <?= $c ? sprintf('class="gc-c" style="width:%s%%;left:-%s%%;top:-%s%%"', round(100 / $c['w'], 4), round($c['x'] / $c['w'] * 100, 4), round($c['y'] / $c['h'] * 100, 4)) : 'class="is-cover"' ?>>
                    <?= Module::render('components/photo-cats', ['cats' => $p['cats'] ?? []]) ?>
                </a>
            <?php endforeach; ?>
            <?php if ($n > 1): ?>
                <div class="gc-tabs"><?php foreach ($photos as $i => $p): ?><button type="button" data-i="<?= $i ?>" aria-label="<?= e($p['title']) ?>"></button><?php endforeach; ?></div>
            <?php endif; ?>
        </div>
        <?php if ($n > 1): ?>
            <button type="button" class="gc-b gc-prev" aria-label="Anterior"><svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></button>
            <button type="button" class="gc-b gc-next" aria-label="Siguiente"><svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></button>
        <?php endif; ?>
    </div>
    <?php if (!empty($o['captions'])): ?><p class="gc-cap" aria-live="polite"><?= e($photos[0]['title'] ?? '') ?></p><?php endif; ?>
</section>
