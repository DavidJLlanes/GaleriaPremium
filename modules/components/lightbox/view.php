<?php use App\Core\Module;
$fx = isset($fx) && Module::exists("lightboxes/$fx") ? $fx : 'fade';
Module::use("lightboxes/$fx");   // solo se cargan el CSS/JS del efecto elegido para esta galería ?>
<div class="lb" id="lightbox" data-fx="<?= e($fx) ?>" hidden aria-modal="true" role="dialog">
    <div class="lb-bg"></div>
    <figure class="lb-fig"><img class="lb-img" alt="" draggable="false"></figure>
    <p class="lb-cap"></p>
    <button class="lb-close" aria-label="Cerrar">×</button>
    <button class="lb-nav lb-prev" aria-label="Anterior">‹</button>
    <button class="lb-nav lb-next" aria-label="Siguiente">›</button>
    <div class="lb-count"></div>
</div>
