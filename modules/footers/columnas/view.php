<div class="fk">
    <div class="fk-grid">
        <div class="fk-c fk-brand ftr-i"><p class="fk-name"><?= e($f['name']) ?></p><?php if ($f['tag'] !== ''): ?><p class="fk-tag"><?= e($f['tag']) ?></p><?php endif; ?></div>
        <?php if ($f['galleries']): ?>
            <div class="fk-c ftr-i"><h3>Galerías</h3><ul><?php foreach (array_slice($f['galleries'], 0, 6) as $g): ?><li><a href="/galeria/<?= e($g['slug']) ?>"><?= e($g['title']) ?></a></li><?php endforeach; ?></ul></div>
        <?php endif; ?>
        <?php if ($f['links']): ?>
            <div class="fk-c ftr-i"><h3>Sígueme</h3><ul><?php foreach ($f['links'] as $label => $url): ?><li><a href="<?= e($url) ?>" target="_blank" rel="noopener me"><?= e($label) ?></a></li><?php endforeach; ?></ul></div>
        <?php endif; ?>
        <?php if ($f['mail'] !== ''): ?>
            <div class="fk-c ftr-i"><h3>Contacto</h3><ul><li><a href="mailto:<?= e($f['mail']) ?>"><?= e($f['mail']) ?></a></li></ul></div>
        <?php endif; ?>
    </div>
    <div class="fk-bar">
        <span class="ftr-i"><?= e($f['copy']) ?></span>
        <?= \App\Core\Module::render('components/footer-sig', ['f' => $f]) ?>
        <?php if ($f['top'] || $f['admin']): ?><span class="ftr-links ftr-i"><?php if ($f['top']): ?><a href="#" class="ftr-top" data-magnet onclick="scrollTo({top:0,behavior:'smooth'});return false">Arriba ↑</a><?php endif; ?><?php if ($f['admin']): ?><a href="/admin" rel="nofollow">Admin</a><?php endif; ?></span><?php endif; ?>
    </div>
</div>
