<?php use App\Core\Module; use App\Services\Fonts;
$fid = (string) ($opts['font'] ?? ''); $fid = $fid !== '' && isset(Fonts::catalog()[$fid]) ? $fid : '';
$st = ($fid !== '' ? 'font-family:' . Fonts::family($fid) . ';' : '') . (($opts['color_on'] ?? '0') === '1' ? 'color:' . e($opts['color'] ?? '#f2efe9') . ';--ff:' . e($opts['color'] ?? '#f2efe9') . ';' : ''); ?>
<?php if ($fid !== ''): ?><style><?= Fonts::face($fid) ?></style><?php endif; ?>
<div class="fg <?= ($opts['outline'] ?? '0') === '1' ? 'fg--outline' : '' ?> <?= ($opts['caps'] ?? '1') === '1' ? 'fg--caps' : '' ?>">
    <p class="fg-word ftr-i" style="<?= $st ?>" aria-label="<?= e($f['name']) ?>"><span aria-hidden="true"><?= e($f['name']) ?></span></p>
    <?php if ($f['tag'] !== ''): ?><p class="fg-tag ftr-i"><?= e($f['tag']) ?></p><?php endif; ?>
    <div class="fg-bar">
        <p class="fg-copy ftr-i"><?= e($f['copy']) ?></p>
        <?php if ($f['links']): ?><div class="ftr-i"><?= Module::render('components/social-icons', ['items' => $f['links']]) ?></div><?php endif; ?>
        <?= Module::render('components/footer-menu', ['f' => $f]) ?>
        <?= Module::render('components/footer-sig', ['f' => $f]) ?>
        <?php if ($f['top']) echo Module::render('components/footer-top'); ?>
    </div>
</div>
