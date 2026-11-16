<?php use App\Core\Module;
if ($hero['type']) echo Module::render('heroes/' . $hero['type'], ['photos' => $hero['photos'], 'copy' => $hero['copy'] ?? [], 'interval' => $hero['interval'] ?? 6500, 'opts' => $hero['opts'] ?? [], 'stats' => $hero['stats'] ?? [], 'galleries' => $galleries]);
echo Module::render('sections/collections', ['galleries' => $galleries]);
