<?php /* Datos para los complementos del lightbox. Datos: $photos (fotos de la galería). El resto lo construye script.js dentro de #lightbox. */
use App\Models\Photo;
$info = [];
foreach ($photos as $p) {
    $ex = json_decode((string) ($p['exif'] ?? ''), true) ?: [];
    $line = array_values(array_filter([$ex['cam'] ?? '', $ex['f'] ?? '', $ex['exp'] ?? '', $ex['iso'] ?? '', $ex['mm'] ?? '', $ex['when'] ?? '']));
    $info[$p['filename']] = ['t' => Photo::thumbUrl($p['filename']), 'd' => (string) ($p['caption'] ?? ''), 'x' => $line, 'c' => array_values($p['cats'] ?? [])];
} ?>
<?php $pc = \App\Services\Categories::values(); \App\Core\Module::use('components/photo-cats'); ?>
<script type="application/json" id="lbxInfo" data-pcs="<?= e($pc['style']) ?>" data-pck="<?= e((string) \App\Services\HeroButton::scale($pc['size'])) ?>" data-show-exif="<?= !isset($showExif) || $showExif ? '1' : '0' ?>"><?= json_encode($info, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
