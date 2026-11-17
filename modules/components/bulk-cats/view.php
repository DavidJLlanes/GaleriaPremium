<?php /* Controles de categoría para la barra de selección. Params: $all (categorías), [$current] (id de la categoría que se está viendo; 0/null = ninguna). */
$current = (int) ($current ?? 0); ?>
<select class="bk-move bc-add" aria-label="Añadir una categoría"><option value="">+ Añadir categoría…</option><?php foreach ($all as $k): if ((int) $k['id'] === $current) continue; ?><option value="<?= (int) $k['id'] ?>"><?= e($k['name']) ?></option><?php endforeach; ?></select>
<?php if ($current > 0): ?>
<select class="bk-move bc-move" aria-label="Mover a otra categoría"><option value="">↦ Mover a categoría…</option><?php foreach ($all as $k): if ((int) $k['id'] === $current) continue; ?><option value="<?= (int) $k['id'] ?>"><?= e($k['name']) ?></option><?php endforeach; ?></select>
<button type="button" class="bk-btn bc-rem" data-from="<?= $current ?>">Quitar de esta categoría</button>
<?php endif; ?>
