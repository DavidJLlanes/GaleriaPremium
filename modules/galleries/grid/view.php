<?php use App\Core\Module; use App\Models\Photo; Module::use('components/reveal'); ?>
<section class="gg" data-lightbox>
    <?php foreach ($photos as $i => $p): ?>
        <a class="gg-item reveal" href="<?= e(Photo::url($p['filename'])) ?>" data-title="<?= e($p['title']) ?>" style="--d:<?= ($i % 4) * 80 ?>ms">
            <img src="<?= e(Photo::thumbUrl($p['filename'])) ?>" alt="<?= e($p['title']) ?>" loading="lazy">
            <span class="gg-cap"><small><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></small><?= e($p['title']) ?></span>
        </a>
    <?php endforeach; ?>
</section>
