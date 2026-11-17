<?php use App\Core\Module; use App\Models\Photo; $n = count($photos); ?>
<section class="gt" style="--n:<?= $n ?>" data-count="<?= $n ?>">
    <div class="gt-stage">
        <div class="gt-glow"></div>
        <div class="gt-world" data-lightbox>
            <?php foreach ($photos as $i => $p): ?>
                <a class="gt-item" href="<?= e(Photo::url($p['filename'])) ?>" data-title="<?= e($p['title']) ?>"
                   style="--ar:<?= (int) $p['width'] > 0 && (int) $p['height'] > 0 ? round($p['width'] / $p['height'], 4) : 1.25 ?>">
                    <img src="<?= e(Photo::thumbUrl($p['filename'])) ?>" alt="<?= e($p['title']) ?>" <?= $i > 3 ? 'loading="lazy"' : '' ?>>
                    <?= Module::render('components/photo-cats', ['cats' => $p['cats'] ?? []]) ?>
                    <span class="gt-cap"><?= e($p['title']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="gt-hud">
            <span class="gt-count"><b>01</b> / <?= str_pad((string) $n, 2, '0', STR_PAD_LEFT) ?></span>
            <span class="gt-title"></span>
            <span class="gt-hint">Desplaza para viajar</span>
        </div>
    </div>
</section>
