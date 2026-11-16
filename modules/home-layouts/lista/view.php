<?php use App\Models\Photo; ?>
<div class="hll">
    <ol class="hll-list">
        <?php foreach ($galleries as $i => $g): ?>
        <li class="hll-row" style="--i:<?= $i ?>">
            <a href="/galeria/<?= e($g['slug']) ?>" data-pt data-i="<?= $i ?>">
                <span class="hll-n"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                <?php if ($g['cover']): ?><span class="hll-th" data-pt-media><img src="<?= e(Photo::thumbUrl($g['cover'])) ?>" alt="" loading="lazy" width="<?= (int) $g['cover_w'] ?>" height="<?= (int) $g['cover_h'] ?>"></span><?php endif; ?>
                <h2 class="hll-t"><span><?= e($g['title']) ?></span></h2>
                <span class="hll-c"><?= (int) $g['photo_count'] ?> fotos</span>
                <i class="hll-arrow" aria-hidden="true">↗</i>
            </a>
        </li>
        <?php endforeach; ?>
    </ol>
    <div class="hll-float" aria-hidden="true">
        <?php foreach ($galleries as $i => $g): if ($g['cover']): ?><img data-i="<?= $i ?>" src="<?= e(Photo::thumbUrl($g['cover'])) ?>" alt="" loading="lazy"><?php endif; endforeach; ?>
    </div>
</div>
