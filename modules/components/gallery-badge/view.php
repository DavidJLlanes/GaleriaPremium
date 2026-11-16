<?php /* $g = fila con `pinned` / `featured`; $inline = true → en línea (sin posición absoluta). Si es fijada y destacada, salen las dos. */
$pin = !empty($g['pinned']); $feat = !empty($g['featured']); if (!$pin && !$feat) return;
$ico = [
  'pin'  => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9 3h6l-1 6 3 3v2H7v-2l3-3zM12 14v7" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
  'feat' => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z" fill="currentColor"/></svg>',
]; ?>
<i class="gbd<?= !empty($inline) ? ' gbd--inline' : '' ?>">
    <?php if ($pin): ?><b class="gbd-i gbd-i--pin"><?= $ico['pin'] ?>Fijada</b><?php endif; ?>
    <?php if ($feat): ?><b class="gbd-i gbd-i--feat"><?= $ico['feat'] ?>Destacada</b><?php endif; ?>
</i>
