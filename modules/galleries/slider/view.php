<?php use App\Models\Photo; use App\Services\Crop;
$o = $options ?? []; $ratio = (float) ($gallery['crop_ratio'] ?? 0) ?: 1.5;
$hh = ['s' => 52, 'm' => 66, 'l' => 80][$o['size'] ?? 'm'] ?? 66; $n = count($photos); ?>
<section class="gs" data-ratio="<?= round($ratio, 5) ?>" data-fit="<?= (int) ($o['fit'] ?? 1) ?>" data-gap="<?= (int) ($o['gap'] ?? 16) ?>" data-h="<?= $hh ?>"
         style="--r:<?= round($ratio, 5) ?>;--gap:<?= (int) ($o['gap'] ?? 16) ?>px;--hh:<?= $hh ?>vh">
    <div class="gs-view" data-lightbox>
        <div class="gs-track">
            <?php foreach ($photos as $i => $p):
                $c = Crop::of($p);
                $img = $c
                    ? sprintf('style="width:%.4f%%;left:-%.4f%%;top:-%.4f%%"', 100 / $c['w'], $c['x'] / $c['w'] * 100, $c['y'] / $c['h'] * 100)
                    : 'class="is-cover"'; ?>
                <a class="gs-slide" href="<?= e(Photo::url($p['filename'])) ?>" data-title="<?= e($p['title']) ?>" draggable="false">
                    <span class="gs-crop" data-lb-thumb><img src="<?= e(Photo::url($p['filename'])) ?>" alt="<?= e($p['title']) ?>" draggable="false" <?= $img ?> <?= $i > 2 ? 'loading="lazy"' : '' ?>></span>
                    <?php if (!empty($o['captions'])): ?><span class="gs-cap"><?= e($p['title']) ?></span><?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="gs-nav" <?= $n < 2 ? 'hidden' : '' ?>>
        <button type="button" class="gs-btn gs-prev" aria-label="Anterior">←</button>
        <div class="gs-dots"></div>
        <button type="button" class="gs-btn gs-next" aria-label="Siguiente">→</button>
    </div>
</section>
