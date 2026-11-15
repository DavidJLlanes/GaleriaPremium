<?php use App\Core\Module; use App\Models\Photo; Module::use('components/reveal'); ?>
<section class="gj" data-lightbox>
    <?php foreach ($photos as $i => $p):
        $ar = $p['height'] > 0 ? round($p['width'] / $p['height'], 4) : 1; ?>
        <a class="gj-item reveal" href="<?= e(Photo::url($p['filename'])) ?>" data-title="<?= e($p['title']) ?>"
           style="--ar:<?= $ar ?>;--d:<?= ($i % 5) * 70 ?>ms">
            <img src="<?= e(Photo::thumbUrl($p['filename'])) ?>" alt="<?= e($p['title']) ?>" loading="lazy">
        </a>
    <?php endforeach; ?>
</section>
