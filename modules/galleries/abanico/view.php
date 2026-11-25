<?php use App\Core\Module; use App\Models\Photo; use App\Services\Crop;
$o = $options ?? []; $n = count($photos);
$ratio = (float) ($gallery['crop_ratio'] ?? 0) ?: .72;
$spread = ['s' => .52, 'm' => .64, 'l' => .78][$o['spread'] ?? 'm'] ?? .64;
$tilt = ['s' => 5, 'm' => 8.5, 'l' => 12][$o['tilt'] ?? 'm'] ?? 8.5;
$caps = !empty($o['captions']); ?>
<section class="gf" data-ratio="<?= round($ratio, 5) ?>" data-spread="<?= $spread ?>" data-tilt="<?= $tilt ?>" data-loop="<?= !empty($o['loop']) ? 1 : 0 ?>">
    <div class="gf-stage" tabindex="0" role="region" aria-roledescription="carousel" aria-label="<?= e($gallery['title'] ?? 'Galería') ?>">
        <div class="gf-row" data-lightbox>
            <?php foreach ($photos as $i => $p): $c = Crop::of($p); ?>
                <a class="gf-card" href="<?= e(Photo::url($p['filename'])) ?>" data-title="<?= e($p['title']) ?>" data-i="<?= $i ?>" draggable="false" aria-label="<?= e($p['title']) ?>">
                    <span class="gf-box">
                        <img src="<?= e(Photo::thumbUrl($p['filename'])) ?>" alt="" draggable="false" <?= $i > 4 ? 'loading="lazy"' : '' ?>
                             <?= $c ? sprintf('class="gf-c" style="width:%s%%;left:-%s%%;top:-%s%%"', round(100 / $c['w'], 4), round($c['x'] / $c['w'] * 100, 4), round($c['y'] / $c['h'] * 100, 4)) : 'class="is-cover"' ?>>
                        <i class="gf-sh"></i>
                        <?= Module::render('components/photo-cats', ['cats' => $p['cats'] ?? []]) ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="gf-foot">
        <?php if ($caps): ?><p class="gf-cap" aria-live="polite"></p><?php endif; ?>
        <?php if ($n > 1): ?>
            <div class="gf-ctl">
                <button type="button" class="gf-b gf-prev" aria-label="Anterior"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg></button>
                <?php if ($n <= 14): ?>
                    <span class="gf-dots"><?php for ($i = 0; $i < $n; $i++): ?><button type="button" data-i="<?= $i ?>" aria-label="Foto <?= $i + 1 ?>"></button><?php endfor; ?></span>
                <?php else: ?>
                    <span class="gf-num"><b>01</b> / <?= str_pad((string) $n, 2, '0', STR_PAD_LEFT) ?></span>
                <?php endif; ?>
                <button type="button" class="gf-b gf-next" aria-label="Siguiente"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></button>
            </div>
        <?php endif; ?>
    </div>
</section>
