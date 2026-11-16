<?php /* Sección de galerías de la portada. El diseño (cómo se muestran) es un módulo de modules/home-layouts/, elegido en Admin → Tarjetas.
 * Si hay sliders entre las galerías ($items mezcla ambos, en el orden del panel), la lista se parte en tandas: cada tanda de galerías se pinta con el diseño
 * elegido y cada slider con el suyo (modules/sliders/<diseño>). */
use App\Core\Module; use App\Services\{Cards, Slider};
$c = Cards::values(); $L = $c['layout']; $cards = !empty(Module::meta("home-layouts/$L")['cards']);   // ¿usa las tarjetas (components/collection-card)?
if ($cards) { Module::use('reveals/' . $c['reveal']); Module::use('card-titles/' . $c['style']); Module::use('components/card-reveal'); }
$items = $items ?? array_values($galleries);
$runs = []; foreach ($items as $it) { $isS = ($it['kind'] ?? 'gallery') === 'slider'; $last = array_key_last($runs);
    if ($isS) $runs[] = ['slider', $it]; elseif ($last !== null && $runs[$last][0] === 'g') $runs[$last][1][] = $it; else $runs[] = ['g', [$it]]; }
if (($face = Cards::face($c)) !== ''): ?><style><?= $face ?></style><?php endif;
$first = true;
foreach ($runs as [$type, $data]):
    if ($type === 'slider'):
        $s = Slider::forView($data); $design = Slider::config($data)['design']; ?>
<section class="sl-sec<?= !empty($data['featured']) ? ' is-feat' : '' ?>"<?= $first ? ' id="galerias"' : '' ?> aria-label="<?= e($data['title']) ?>">
    <?= Module::render('components/gallery-badge', ['g' => $data]) ?>
    <?= Module::render("sliders/$design", $s) ?>
</section>
<?php else: ?>
<section class="coll coll--<?= e($L) ?>"<?= $first ? ' id="galerias"' : '' ?> style="<?= e(Cards::vars($c)) ?>"<?php if ($cards): ?> data-rv="<?= e($c['reveal']) ?>" data-parallax="<?= $c['parallax'] ? '1' : '0' ?>"<?php endif; ?>>
    <?php if ($cards && $c['reveal'] !== 'none'): ?><script>document.currentScript.parentNode.classList.add('rv-on')</script><?php endif; ?>
    <?= Module::render("home-layouts/$L", ['galleries' => $data, 'c' => $c, 'opts' => $c['layout_opts']]) ?>
</section>
<?php endif; $first = false; endforeach;
$sl = array_values(array_filter($items, fn ($i) => ($i['kind'] ?? 'gallery') === 'slider'));
if ($sl) {   // un solo lightbox para todos los sliders; cada slider trae su efecto (data-lbfx)
    $all = []; foreach ($sl as $s) { Module::use('lightboxes/' . ($s['lightbox'] ?? 'fade')); $all = array_merge($all, \App\Models\Photo::forGallery((int) $s['id'])); }
    echo Module::render('components/lightbox', ['fx' => $sl[0]['lightbox'] ?? 'fade']);
    echo Module::render('components/lightbox-extras', ['photos' => $all]);
}
if (!$runs): ?><section class="coll coll--<?= e($L) ?>" id="galerias"><p class="coll-empty">Aún no hay galerías publicadas.</p></section><?php endif; ?>
