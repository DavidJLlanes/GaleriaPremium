<?php /* $icon = 'instagram' (solo el SVG) | $items = [['key','label','url'], …] (fila de iconos con enlace) */
$paths = \App\Core\Module::meta('components/social-icons')['icons'];
$svg = static fn (string $k): string => '<svg class="soc-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . ($paths[$k] ?? '') . '</svg>';
if (!empty($icon)) { echo $svg($icon); return; }
if (empty($items)) return; ?>
<ul class="soc <?= e($class ?? '') ?>" aria-label="Redes sociales">
    <?php foreach ($items as $it): ?>
        <li><a class="soc-a" href="<?= e($it['url']) ?>" target="_blank" rel="noopener me" aria-label="<?= e($it['label']) ?>" title="<?= e($it['label']) ?>" data-magnet><?= $svg($it['key']) ?></a></li>
    <?php endforeach; ?>
</ul>
