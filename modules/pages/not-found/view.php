<?php use App\Core\Module; use App\Models\Gallery; use App\Services\Site;
$gs = array_slice(Gallery::published(), 0, 4); ?>
<section class="nf">
    <p class="nf-eyebrow"><?= e(Site::get('name')) ?></p>
    <h1 class="nf-num" aria-label="Error 404">404</h1>
    <p class="nf-msg">Esta imagen se ha perdido en el revelado.</p>
    <?= Module::render('components/button', ['href' => '/', 'label' => 'Volver al inicio']) ?>
    <?php if ($gs): ?>
        <nav class="nf-list" aria-label="Galerías">
            <span>O mira una galería</span>
            <?php foreach ($gs as $g): ?><a href="/galeria/<?= e($g['slug']) ?>"><?= e($g['title']) ?></a><?php endforeach; ?>
        </nav>
    <?php endif; ?>
</section>
