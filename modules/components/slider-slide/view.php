<?php /* $p = foto; $captions = mostrar nombre; $i = posición. El enlace a la foto abre el lightbox ([data-lightbox] en el contenedor). */
use App\Models\Photo; use App\Services\Crop;
$c = Crop::of($p);
$img = $c ? sprintf('style="width:%.4f%%;left:-%.4f%%;top:-%.4f%%"', 100 / $c['w'], $c['x'] / $c['w'] * 100, $c['y'] / $c['h'] * 100) : 'class="is-cover"'; ?>
<a class="sl-slide" href="<?= e(Photo::url($p['filename'])) ?>" data-title="<?= e($p['title']) ?>" draggable="false" aria-label="<?= e($p['title'] !== '' ? $p['title'] : 'Foto ' . ((int) ($i ?? 0) + 1)) ?>">
    <span class="sl-crop" data-lb-thumb><img src="<?= e(Photo::url($p['filename'])) ?>" alt="<?= e($p['title']) ?>" draggable="false" <?= $img ?> <?= ($i ?? 0) > 1 ? 'loading="lazy"' : '' ?>></span>
    <?= \App\Core\Module::render('components/photo-cats', ['cats' => $p['cats'] ?? []]) ?>
    <?php if (!empty($captions) && $p['title'] !== ''): ?><span class="sl-cap"><?= e($p['title']) ?></span><?php endif; ?>
</a>
