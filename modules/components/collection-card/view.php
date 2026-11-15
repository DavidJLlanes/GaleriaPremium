<?php use App\Core\Module; use App\Models\Photo; Module::use('components/reveal'); ?>
<a class="ccard reveal" href="/galeria/<?= e($g['slug']) ?>" style="--d:<?= (int) ($delay ?? 0) ?>ms">
    <?php if ($g['cover']): ?>
        <img src="<?= e(Photo::thumbUrl($g['cover'])) ?>" alt="<?= e($g['title']) ?>"
             width="<?= (int) $g['cover_w'] ?>" height="<?= (int) $g['cover_h'] ?>" loading="lazy">
    <?php endif; ?>
    <div class="ccard-info">
        <span class="ccard-count"><?= (int) $g['photo_count'] ?> fotos</span>
        <h2><?= e($g['title']) ?></h2>
        <p><?= e($g['description']) ?></p>
    </div>
</a>
