<?php /* Menú «Con portada». Datos: $galleries, $current. */ use App\Models\Photo; ?>
<ul class="mp">
    <?php foreach ($galleries as $i => $g): ?>
        <li><a class="mnp-item mp-a" style="--i:<?= (int) $i ?>" href="/galeria/<?= e($g['slug']) ?>"<?= $g['slug'] === $current ? ' aria-current="page"' : '' ?>>
            <span class="mp-img"><?php if ($g['cover']): ?><img src="<?= e(Photo::thumbUrl($g['cover'])) ?>" alt="" width="<?= (int) $g['cover_w'] ?>" height="<?= (int) $g['cover_h'] ?>" loading="lazy"><?php endif; ?></span>
            <span class="mp-tx"><span class="mp-t"><?= e($g['title']) ?></span><span class="mp-n"><?= (int) $g['photo_count'] ?> fotos</span></span>
        </a></li>
    <?php endforeach; ?>
</ul>
