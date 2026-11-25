<?php use App\Core\Module; use App\Models\Photo; use App\Services\Crop;
$o = $options ?? []; $n = count($photos);
$ratio = (float) ($gallery['crop_ratio'] ?? 0) ?: 1.5;
$depth = max(2, min(8, (int) ($o['depth'] ?? 5)));
$per = ['s' => 1.5, 'm' => 1.0, 'f' => .65][$o['speed'] ?? 'm'] ?? 1.0;
$data = [];
foreach ($photos as $p) {
    $c = Crop::of($p);
    $data[] = [
        'src' => Photo::url($p['filename']), 'alt' => $p['title'],
        'c'   => $c ? [round(100 / $c['w'], 4), round($c['x'] / $c['w'] * 100, 4), round($c['y'] / $c['h'] * 100, 4)] : null,
    ];
} ?>
<section class="gv" data-ratio="<?= round($ratio, 5) ?>" data-depth="<?= $depth ?>" data-per="<?= $per ?>" style="--n:<?= $n ?>;--per:<?= $per ?>">
    <div class="gv-stage" tabindex="0" role="region" aria-roledescription="carousel" aria-label="<?= e($gallery['title']) ?>">
        <div class="gv-glow"></div>
        <div class="gv-scene" aria-hidden="true"></div>

        <?php /* Enlaces reales a las fotos: el lightbox los usa y, sin JavaScript, se ve la lista de fotos. Se colocan sobre la carta delantera. */ ?>
        <div class="gv-src" data-lightbox>
            <?php foreach ($photos as $i => $p): $d = $data[$i]; ?>
                <a href="<?= e(Photo::url($p['filename'])) ?>" data-title="<?= e($p['title']) ?>" draggable="false">
                    <img src="<?= e(Photo::thumbUrl($p['filename'])) ?>" alt="<?= e($p['title']) ?>" draggable="false" loading="lazy"
                         <?= $d['c'] ? sprintf('class="gv-c" style="width:%s%%;left:-%s%%;top:-%s%%"', $d['c'][0], $d['c'][1], $d['c'][2]) : 'class="is-cover"' ?>>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if ($n > 1 || !empty($o['captions'])): ?>
        <div class="gv-hud">
            <?php if (!empty($o['captions'])): ?>
                <span class="gv-count"><b>01</b> / <?= str_pad((string) $n, 2, '0', STR_PAD_LEFT) ?></span>
                <span class="gv-title"></span>
            <?php endif; ?>
            <?php if ($n > 1): ?>
                <span class="gv-btns"><button type="button" class="gv-b gv-prev" aria-label="Anterior">←</button><button type="button" class="gv-b gv-next" aria-label="Siguiente">→</button></span>
            <?php endif; ?>
        </div>
        <span class="gv-hint">Desplaza para voltear</span>
        <?php endif; ?>
    </div>
    <script type="application/json" class="gv-data"><?= json_encode($data, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
</section>
