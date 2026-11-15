<?php
declare(strict_types=1);
/**
 * Instalador: crea la base de datos SQLite y, con --demo, datos de ejemplo.
 *   php bin/install.php          -> solo esquema
 *   php bin/install.php --demo   -> esquema + galerías demo generadas con GD
 */
require __DIR__ . '/../app/bootstrap.php';

use App\Core\{Config, Database};

$pdo = Database::pdo();
$pdo->exec(file_get_contents(ROOT . '/database/schema.sql'));
echo "✔ Base de datos lista\n";

if (!in_array('--demo', $argv, true)) exit(0);

if ((int) $pdo->query('SELECT COUNT(*) FROM galleries')->fetchColumn() > 0) {
    echo "• Ya hay galerías; no se añaden datos demo.\n"; exit(0);
}

/** Genera una imagen abstracta suave (manchas de color) y su miniatura. */
function makeImage(int $w, int $h, array $palette, int $seed, string $path, string $thumb, int $thumbW): void
{
    mt_srand($seed);
    $sw = 48; $sh = max(8, (int) round(48 * $h / $w));
    $small = imagecreatetruecolor($sw, $sh);
    $blobs = [];
    for ($i = 0; $i < 5; $i++) {
        $blobs[] = [mt_rand(0, $sw), mt_rand(0, $sh), $palette[array_rand($palette)], mt_rand(14, 34)];
    }
    for ($y = 0; $y < $sh; $y++) for ($x = 0; $x < $sw; $x++) {
        $r = $g = $b = 0; $tw = 0.0001;
        foreach ($blobs as [$bx, $by, $c, $rad]) {
            $d = hypot($x - $bx, $y - $by);
            $wt = exp(-($d * $d) / (2 * $rad * $rad * 0.35));
            $r += $c[0] * $wt; $g += $c[1] * $wt; $b += $c[2] * $wt; $tw += $wt;
        }
        $vig = 1 - 0.35 * hypot($x / $sw - .5, $y / $sh - .5);
        imagesetpixel($small, $x, $y, imagecolorallocate($small,
            (int) min(255, $r / $tw * $vig), (int) min(255, $g / $tw * $vig), (int) min(255, $b / $tw * $vig)));
    }
    $big = imagescale($small, $w, $h, IMG_BICUBIC);
    imagefilter($big, IMG_FILTER_GAUSSIAN_BLUR);
    imagejpeg($big, $path, 88);
    $t = imagescale($big, $thumbW, -1, IMG_BICUBIC);
    imagejpeg($t, $thumb, 82);
}

$dir = Config::get('uploads.dir');
$tw  = (int) Config::get('uploads.thumb_w');
$demo = [
    ['aurora', 'Aurora', 'Luz verde y violeta sobre el horizonte frío.', [[20,200,150],[110,60,220],[10,30,70],[60,220,200]]],
    ['ocaso',  'Ocaso',  'Las últimas horas del día en tonos ardientes.', [[255,120,60],[200,40,90],[40,10,60],[255,200,100]]],
    ['abismo', 'Abismo', 'Profundidades azules, silencio y contraste.',  [[10,60,140],[0,20,50],[30,140,200],[5,5,25]]],
];
$sizes = [[1400,1900],[1900,1300],[1500,1500],[1300,1950],[2000,1250],[1600,1600]];

$insG = $pdo->prepare('INSERT INTO galleries (slug,title,description,style,sort_order) VALUES (?,?,?,?,?)');
$insP = $pdo->prepare('INSERT INTO photos (gallery_id,filename,title,width,height,sort_order) VALUES (?,?,?,?,?,?)');

foreach ($demo as $gi => [$slug, $title, $desc, $palette]) {
    $insG->execute([$slug, $title, $desc, 'masonry', $gi]);
    $gid = (int) $pdo->lastInsertId();
    foreach ($sizes as $i => [$w, $h]) {
        $name = sprintf('%s-%02d.jpg', $slug, $i + 1);
        makeImage($w, $h, $palette, $gi * 100 + $i, "$dir/originals/$name", "$dir/thumbs/$name", $tw);
        $insP->execute([$gid, $name, "$title " . ($i + 1), $w, $h, $i]);
    }
    echo "✔ Galería demo: $title\n";
}
