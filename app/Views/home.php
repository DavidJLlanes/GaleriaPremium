<?php use App\Core\{Config, View}; use App\Models\Photo; $e = [View::class, 'e'];
$hero = $galleries[0] ?? null; ?>
<section class="hero" data-parallax>
    <?php if ($hero && $hero['cover']): ?>
        <div class="hero-bg" style="background-image:url('<?= $e(Photo::url($hero['cover'])) ?>')"></div>
    <?php endif; ?>
    <div class="hero-shade"></div>
    <div class="hero-copy">
        <p class="eyebrow">Portfolio fotográfico</p>
        <h1><?= $e(Config::get('app.tagline')) ?></h1>
        <a class="btn" href="#galerias">Explorar galerías</a>
    </div>
    <div class="scroll-hint" aria-hidden="true"><span></span></div>
</section>

<section class="collections" id="galerias">
    <?php foreach ($galleries as $i => $g): ?>
        <a class="card reveal" href="/galeria/<?= $e($g['slug']) ?>" style="--d:<?= $i * 80 ?>ms">
            <?php if ($g['cover']): ?>
                <img src="<?= $e(Photo::thumbUrl($g['cover'])) ?>" alt="<?= $e($g['title']) ?>"
                     width="<?= (int) $g['cover_w'] ?>" height="<?= (int) $g['cover_h'] ?>" loading="lazy">
            <?php endif; ?>
            <div class="card-info">
                <span class="card-count"><?= (int) $g['photo_count'] ?> fotos</span>
                <h2><?= $e($g['title']) ?></h2>
                <p><?= $e($g['description']) ?></p>
            </div>
        </a>
    <?php endforeach; ?>
    <?php if (!$galleries): ?><p class="empty">Aún no hay galerías publicadas.</p><?php endif; ?>
</section>
