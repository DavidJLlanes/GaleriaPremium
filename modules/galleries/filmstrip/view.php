<?php use App\Core\Module; use App\Models\Photo; ?>
<section class="gf">
    <div class="gf-track" data-lightbox>
        <?php foreach ($photos as $i => $p): ?>
            <a class="gf-slide" href="<?= e(Photo::url($p['filename'])) ?>" data-title="<?= e($p['title']) ?>">
                <img src="<?= e(Photo::url($p['filename'])) ?>" alt="<?= e($p['title']) ?>" width="<?= (int) $p['width'] ?>" height="<?= (int) $p['height'] ?>" <?= $i > 1 ? 'loading="lazy"' : '' ?>>
                <?= Module::render('components/photo-cats', ['cats' => $p['cats'] ?? []]) ?>
                <span class="gf-cap"><small><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?> / <?= str_pad((string) count($photos), 2, '0', STR_PAD_LEFT) ?></small><?= e($p['title']) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
    <div class="gf-bar"><i></i></div>
</section>
