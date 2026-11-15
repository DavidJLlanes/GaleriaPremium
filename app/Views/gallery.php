<?php use App\Core\View; $e = [View::class, 'e']; ?>
<section class="gallery-head">
    <a class="back" href="/#galerias">← Galerías</a>
    <h1><?= $e($gallery['title']) ?></h1>
    <?php if ($gallery['description']): ?><p><?= $e($gallery['description']) ?></p><?php endif; ?>
</section>
<?= App\Core\View::partial('gallery/' . $style, compact('gallery', 'photos')) ?>
<div class="lightbox" id="lightbox" hidden aria-modal="true" role="dialog">
    <button class="lb-close" aria-label="Cerrar">×</button>
    <button class="lb-nav lb-prev" aria-label="Anterior">‹</button>
    <figure><img alt=""><figcaption></figcaption></figure>
    <button class="lb-nav lb-next" aria-label="Siguiente">›</button>
    <div class="lb-count"></div>
</div>
