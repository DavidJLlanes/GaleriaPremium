<?php /* Indicador de categorías. Params: $cats (lista de nombres), [$inline]. No pinta nada si no hay categorías. */
use App\Core\Module; use App\Services\{Categories, HeroButton};
if (empty($cats)) return;
Module::use('components/photo-cats'); $c = Categories::values(); ?>
<span class="pcats<?= !empty($inline) ? ' pcats--inline' : '' ?>" style="--pc-k:<?= e((string) HeroButton::scale($c['size'])) ?>"><?php foreach ($cats as $n): ?><i class="pc pc--<?= e($c['style']) ?>"><?= e($n) ?></i><?php endforeach; ?></span>
