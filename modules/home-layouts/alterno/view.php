<?php use App\Models\Photo; ?>
<div class="hla">
    <?php foreach ($galleries as $i => $g): ?>
    <article class="hla-row">
        <a class="hla-media" href="/galeria/<?= e($g['slug']) ?>" data-pt aria-label="<?= e($g['title']) ?>" tabindex="-1">
            <?php if ($g['cover']): ?><img src="<?= e(Photo::url($g['cover'])) ?>" alt="" loading="lazy" width="<?= (int) $g['cover_w'] ?>" height="<?= (int) $g['cover_h'] ?>"><?php endif; ?>
        </a>
        <div class="hla-txt">
            <b class="hla-n" aria-hidden="true"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></b>
            <span class="hla-c"><?= (int) $g['photo_count'] ?> fotos</span>
            <h2 class="hla-t"><a href="/galeria/<?= e($g['slug']) ?>"><?= e($g['title']) ?></a></h2>
            <?php if ($g['description'] !== ''): ?><p class="hla-d"><?= e($g['description']) ?></p><?php endif; ?>
            <a class="hla-more" href="/galeria/<?= e($g['slug']) ?>" data-magnet>Ver galería <i aria-hidden="true">→</i></a>
        </div>
    </article>
    <?php endforeach; ?>
</div>
