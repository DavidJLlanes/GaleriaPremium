<?php /* Datos: $v = Footer::values(). */
use App\Core\Module; use App\Services\Footer;
$f = Footer::data($v); $rm = Module::meta('footer-reveals/' . $v['reveal']); Module::use('footer-reveals/' . $v['reveal']);
$vars = ''; foreach ($v['ropts'] as $k => $val) $vars .= '--fr-' . preg_replace('/[^a-z0-9-]/', '', strtolower((string) $k)) . ':' . preg_replace('/[^a-z0-9#.%-]/i', '', (string) $val) . ';'; ?>
<footer class="ftr ftr--<?= e($v['tone']) ?> fd-<?= e($v['design']) ?> fr-<?= e($v['reveal']) ?>" data-fr="<?= e($v['reveal']) ?>" data-armed="<?= !empty($rm['armed']) ? '1' : '0' ?>" style="<?= e($vars) ?>">
    <?= Module::render('footers/' . $v['design'], ['f' => $f, 'opts' => $v['opts']]) ?>
</footer>
