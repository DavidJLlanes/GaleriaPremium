<?php use App\Core\View; use App\Models\Photo; $e = [View::class, 'e']; ?>
<section class="masonry" data-lightbox>
    <?php foreach ($photos as $i => $p): ?>
        <a class="m-item reveal" href="<?= $e(Photo::url($p['filename'])) ?>"
           data-title="<?= $e($p['title']) ?>" style="--d:<?= ($i % 6) * 70 ?>ms">
            <img src="<?= $e(Photo::thumbUrl($p['filename'])) ?>" alt="<?= $e($p['title']) ?>"
                 width="<?= (int) $p['width'] ?>" height="<?= (int) $p['height'] ?>" loading="lazy">
        </a>
    <?php endforeach; ?>
</section>
