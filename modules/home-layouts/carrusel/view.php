<?php use App\Core\Module; $n = count($galleries); ?>
<div class="hlc hlc--<?= e($opts['size'] ?? 'm') ?>">
    <div class="hlc-head">
        <span class="hlc-count" aria-hidden="true"><b>01</b> / <?= str_pad((string) $n, 2, '0', STR_PAD_LEFT) ?></span>
        <div class="hlc-nav">
            <button type="button" class="hlc-b" data-dir="-1" aria-label="Anterior" data-magnet><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7" fill="none" stroke="currentColor" stroke-width="1.4"/></svg></button>
            <button type="button" class="hlc-b" data-dir="1" aria-label="Siguiente" data-magnet><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7" fill="none" stroke="currentColor" stroke-width="1.4"/></svg></button>
        </div>
    </div>
    <div class="hlc-track" tabindex="0" aria-label="Galerías">
        <?php foreach ($galleries as $i => $g) echo Module::render('components/collection-card', ['g' => $g, 'n' => $i + 1, 'fx' => $c['reveal'], 'titles' => $c['titles'], 'style' => $c['style'], 'topts' => $c['style_opts']]); ?>
    </div>
    <div class="hlc-bar" aria-hidden="true"><i></i></div>
</div>
