<?php /* Botón de la portada. Ajustes en Admin → Portada (hero_btn_style / hero_btn_size / hero_btn_down). Params: href, label, [preview] */
use App\Services\HeroButton;
$b = HeroButton::values(); ?>
<a class="hb hb--<?= e($b['style']) ?><?= empty($preview) ? ' hb-d' . (int) $b['down'] : '' ?>" href="<?= e($href ?? '#') ?>" style="--hb-k:<?= e((string) HeroButton::scale($b['size'])) ?>"><span><?= e($label ?? '') ?></span><?php if ($b['style'] === 'arrow'): ?><i aria-hidden="true">→</i><?php endif; ?></a>
