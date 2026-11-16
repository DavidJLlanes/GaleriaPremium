<?php use App\Models\Photo; $n = count($galleries); ?>
<div class="hlp hlp--<?= e($opts['align'] ?? 'center') ?>">
    <?php foreach ($galleries as $i => $g): ?>
    <a class="hlp-p" href="/galeria/<?= e($g['slug']) ?>" data-pt>
        <span class="hlp-media<?= !empty($g['featured']) ? ' gfx' : '' ?>" data-pt-media><?php if ($g['cover']): ?><img src="<?= e(Photo::url($g['cover'])) ?>" alt="<?= e($g['title']) ?>" loading="lazy" width="<?= (int) $g['cover_w'] ?>" height="<?= (int) $g['cover_h'] ?>"><?php endif; ?></span>
        <?= \App\Core\Module::render('components/gallery-badge', ['g' => $g]) ?>
        <span class="hlp-txt">
            <span class="hlp-n"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?> / <?= str_pad((string) $n, 2, '0', STR_PAD_LEFT) ?></span>
            <span class="hlp-tw"><strong class="hlp-t" role="heading" aria-level="2"><?= e($g['title']) ?></strong></span>
            <?php if ($g['description'] !== ''): ?><span class="hlp-d"><?= e($g['description']) ?></span><?php endif; ?>
            <span class="hlp-more"><?= (int) $g['photo_count'] ?> fotos · Ver galería <i aria-hidden="true">→</i></span>
        </span>
    </a>
    <?php endforeach; ?>
</div>
