<?php /* Tarjeta de galería de la página principal. Datos: $g (galería), $delay, $n (posición), $fx (efecto de aparición), $titles (bool), $style (diseño de título), $topts (sus ajustes). */
use App\Core\Module; use App\Models\Photo;
$fx = $fx ?? 'none'; $style = $style ?? 'classic'; $place = Module::meta("card-titles/$style")['place'] ?? 'over';
$title = !empty($titles) ? Module::render("card-titles/$style", ['g' => $g, 'n' => $n ?? 1, 'opts' => $topts ?? []]) : '';
?>
<div class="ccell"><a class="ccard rv-<?= e($fx) ?><?= $title !== '' && $place === 'over' ? ' ccard--over' : '' ?>" href="/galeria/<?= e($g['slug']) ?>"<?= $title === '' ? ' aria-label="' . e($g['title']) . '"' : '' ?>>
    <div class="ccard-media<?= !empty($g['featured']) ? ' gfx' : '' ?>">
        <?php if ($g['cover']): ?>
            <img src="<?= e(Photo::thumbUrl($g['cover'])) ?>" alt="<?= e($g['title']) ?>"
                 width="<?= (int) $g['cover_w'] ?>" height="<?= (int) $g['cover_h'] ?>" loading="lazy">
        <?php endif; ?>
        <?= Module::render('components/gallery-badge', ['g' => $g]) ?>
        <?= $place === 'over' ? $title : '' ?>
    </div>
    <?= $place === 'below' ? $title : '' ?>
</a></div>
