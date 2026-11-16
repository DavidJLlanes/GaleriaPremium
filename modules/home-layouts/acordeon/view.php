<?php use App\Models\Photo; ?>
<div class="hlx" style="--n:<?= count($galleries) ?>">
    <?php foreach ($galleries as $i => $g): ?>
    <a class="hlx-s<?= $i === 0 ? ' is-open' : '' ?>" href="/galeria/<?= e($g['slug']) ?>" data-pt style="--i:<?= $i ?>">
        <span class="hlx-media" data-pt-media><?php if ($g['cover']): ?><img src="<?= e(Photo::url($g['cover'])) ?>" alt="<?= e($g['title']) ?>" loading="lazy" width="<?= (int) $g['cover_w'] ?>" height="<?= (int) $g['cover_h'] ?>"><?php endif; ?></span>
        <span class="hlx-v" aria-hidden="true"><i><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></i><?= e($g['title']) ?></span>
        <span class="hlx-t">
            <small><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?> · <?= (int) $g['photo_count'] ?> fotos</small>
            <strong role="heading" aria-level="2"><?= e($g['title']) ?></strong>
            <em>Ver galería <i aria-hidden="true">→</i></em>
        </span>
    </a>
    <?php endforeach; ?>
</div>
