<?php use App\Models\Photo; $n = count($photos); $pad = static fn (int $v): string => str_pad((string) $v, 2, '0', STR_PAD_LEFT); ?>
<section class="gl" data-count="<?= $n ?>">
    <canvas class="gl-canvas" aria-hidden="true"></canvas>
    <div class="gl-fallback" aria-hidden="true">
        <?php foreach ($photos as $p): ?><img data-src="<?= e(Photo::url($p['filename'])) ?>" alt=""><?php endforeach; ?>
    </div>
    <div class="gl-shade"></div>

    <span class="gl-num"><b class="gl-cur">01</b><i></i><span class="gl-tot"><?= $pad($n) ?></span></span>

    <div class="gl-copy">
        <h2 class="gl-title"></h2>
        <p class="gl-desc"></p>
        <button type="button" class="gl-open">Ver foto completa</button>
    </div>

    <nav class="gl-nav" aria-label="Fotos de la galería">
        <?php foreach ($photos as $i => $p): ?>
            <button type="button" class="gl-item<?= $i === 0 ? ' is-on' : '' ?>" data-i="<?= $i ?>"
                    data-src="<?= e(Photo::url($p['filename'])) ?>" data-title="<?= e($p['title']) ?>" data-desc="<?= e($p['caption']) ?>">
                <span class="gl-line"><i></i></span><span class="gl-name"><?= e($p['title']) ?></span>
            </button>
        <?php endforeach; ?>
    </nav>

    <div class="gl-lbsrc" data-lightbox hidden>
        <?php foreach ($photos as $p): ?><a href="<?= e(Photo::url($p['filename'])) ?>" data-title="<?= e($p['title']) ?>"></a><?php endforeach; ?>
    </div>
</section>
