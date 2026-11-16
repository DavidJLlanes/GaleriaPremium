<?php use App\Core\Module; use App\Models\Photo;
$n = count($photos); $per = max(8, $n);                     // fotos por mitad de pista (la pista se duplica para el bucle)
$row = static function (int $offset, bool $rev) use ($photos, $n, $per): string {
    $h = '<div class="hxr-row' . ($rev ? ' is-rev' : '') . '"><div class="hxr-track">';
    for ($rep = 0; $rep < 2; $rep++)
        for ($i = 0; $i < $per; $i++) {
            $p = $photos[($offset + ($rev ? -$i : $i) + $n * 50) % $n];
            $h .= '<img src="' . e(Photo::thumbUrl($p['filename'])) . '" alt="' . ($rep ? '' : e($p['title'])) . '"' . ($rep ? ' aria-hidden="true"' : '') . ' loading="lazy" decoding="async" draggable="false">';
        }
    return $h . '</div></div>';
};
$media = '<div class="hxr" style="--t:' . (float) ($opts['speed'] ?? 90) . 's">' . $row(0, false) . $row(intdiv($n, 2) + 1, true) . '</div>';
echo Module::render('components/hero-shell', ['class' => 'hx-cinta', 'media' => $media, 'copy' => $copy ?? []]);
