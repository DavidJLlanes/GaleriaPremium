<?php
/* Barra flotante de selección múltiple.
   $kind: 'galleries' | 'photos' · $galleries: destinos para «Mover a…» (solo photos) · $listSel: selector del contenedor */
$kind = $kind ?? 'photos'; $galleries = $galleries ?? [];
$noun = $kind === 'galleries' ? 'galería' : 'foto';
?>
<div class="bk" id="bulkBar" hidden data-kind="<?= e($kind) ?>" data-list="<?= e($listSel ?? '[data-sortable]') ?>"
     data-delete-url="<?= $kind === 'galleries' ? '/admin/galerias/borrar' : '/admin/fotos/borrar' ?>" data-move-url="/admin/fotos/mover"
     data-noun="<?= e($noun) ?>">
    <span class="bk-count">0 seleccionadas</span>
    <button type="button" class="bk-btn" data-bk="all">Todo</button>
    <button type="button" class="bk-btn" data-bk="none">Ninguna</button>
    <?php if ($kind === 'photos' && $galleries): ?>
        <select class="bk-move" aria-label="Mover a otra galería">
            <option value="">Mover a…</option>
            <?php foreach ($galleries as $g): ?><option value="<?= (int) $g['id'] ?>"><?= e($g['title']) ?></option><?php endforeach; ?>
        </select>
    <?php endif; ?>
    <button type="button" class="bk-btn is-danger" data-bk="delete">Borrar</button>
</div>
