<?php /* Sección de galerías de la portada. El diseño (cómo se muestran) es un módulo de modules/home-layouts/, elegido en Admin → Tarjetas. */
use App\Core\Module; use App\Services\Cards;
$c = Cards::values(); $L = $c['layout']; $cards = !empty(Module::meta("home-layouts/$L")['cards']);   // ¿usa las tarjetas (components/collection-card)?
if ($cards) { Module::use('reveals/' . $c['reveal']); Module::use('card-titles/' . $c['style']); Module::use('components/card-reveal'); } ?>
<?php if (($face = Cards::face($c)) !== ''): ?><style><?= $face ?></style><?php endif; ?>
<section class="coll coll--<?= e($L) ?>" id="galerias" style="<?= e(Cards::vars($c)) ?>"<?php if ($cards): ?> data-rv="<?= e($c['reveal']) ?>" data-parallax="<?= $c['parallax'] ? '1' : '0' ?>"<?php endif; ?>>
    <?php if ($cards && $c['reveal'] !== 'none'): ?><script>document.currentScript.parentNode.classList.add('rv-on')</script><?php endif; ?>
    <?php if ($galleries) echo Module::render("home-layouts/$L", ['galleries' => $galleries, 'c' => $c, 'opts' => $c['layout_opts']]); ?>
    <?php if (!$galleries): ?><p class="coll-empty">Aún no hay galerías publicadas.</p><?php endif; ?>
</section>
