<?php use App\Core\Module; ?>
<section class="coll" id="galerias">
    <?php foreach ($galleries as $i => $g): ?>
        <?= Module::render('components/collection-card', ['g' => $g, 'delay' => $i * 80]) ?>
    <?php endforeach; ?>
    <?php if (!$galleries): ?><p class="coll-empty">Aún no hay galerías publicadas.</p><?php endif; ?>
</section>
