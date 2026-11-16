<?php use App\Core\Module;
foreach ($galleries as $i => $g) echo Module::render('components/collection-card', ['g' => $g, 'n' => $i + 1, 'fx' => $c['reveal'], 'titles' => $c['titles'], 'style' => $c['style'], 'topts' => $c['style_opts']]);
