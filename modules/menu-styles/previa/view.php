<?php /* Menú «Con vista previa». Datos: $galleries, $current. */ use App\Models\Photo; ?>
<div class="mv">
    <ul class="mv-list">
        <?php foreach ($galleries as $i => $g): ?>
            <li><a class="mnp-item mv-a" style="--i:<?= (int) $i ?>" href="/galeria/<?= e($g['slug']) ?>"<?= $g['slug'] === $current ? ' aria-current="page"' : '' ?>>
                <span class="mv-t"><?= e($g['title']) ?></span><span class="mv-n"><?= (int) $g['photo_count'] ?></span>
                <?php if ($g['cover']): ?><img class="mv-p" src="<?= e(Photo::thumbUrl($g['cover'])) ?>" alt="" width="<?= (int) $g['cover_w'] ?>" height="<?= (int) $g['cover_h'] ?>" loading="lazy"><?php endif; ?>
            </a></li>
        <?php endforeach; ?>
    </ul>
</div>
