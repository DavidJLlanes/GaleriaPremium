<?php /* Datos: $i = Intro::values(). El diseño elegido va dentro; su CSS/JS se registran en el layout. */
use App\Core\Module;
$own = !empty(Module::meta('intros/' . $i['design'])['own_exit']); ?>
<div id="intro" class="intro in-<?= e($i['design']) ?> <?= $own ? '' : 'exit-' . e($i['exit']) ?>" role="presentation"
     data-dur="<?= e($i['dur']) ?>" data-freq="<?= e($i['freq']) ?>" data-skip="<?= $i['skip'] === '1' ? '1' : '0' ?>" data-manual="<?= !empty(Module::meta('intros/' . $i['design'])['manual']) ? '1' : '0' ?>"
     style="--in-bg:<?= e($i['bg']) ?>;--in-fg:<?= e($i['fg']) ?>;--in-ac:<?= e($i['accent']) ?>;--in-d:<?= e($i['dur']) ?>s">
    <div class="intro-stage"><?= Module::render('intros/' . $i['design'], ['text' => $i['text'], 'sub' => $i['sub'], 'opts' => $i['opts']]) ?></div>
    <?php if ($i['skip'] === '1'): ?><button type="button" class="intro-skip" aria-label="Saltar la entrada">Saltar</button><?php endif; ?>
</div>
