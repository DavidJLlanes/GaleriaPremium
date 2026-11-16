<?php /* Menú «Tarjetas». Datos: $galleries, $current. */ use App\Models\Photo; ?>
<ul class="mt">
    <?php foreach ($galleries as $i => $g): ?>
        <li><a class="mnp-item mt-a" style="--i:<?= (int) $i ?>" href="/galeria/<?= e($g['slug']) ?>"<?= $g['slug'] === $current ? ' aria-current="page"' : '' ?>>
            <?php if ($g['cover']): ?><img src="<?= e(Photo::thumbUrl($g['cover'])) ?>" alt="" width="<?= (int) $g['cover_w'] ?>" height="<?= (int) $g['cover_h'] ?>" loading="lazy"><?php endif; ?>
            <span class="mt-t"><?= e($g['title']) ?></span>
        </a></li>
    <?php endforeach; ?>
</ul>
