<?php use App\Core\Module;
if ($hero['type']) echo Module::render('heroes/' . $hero['type'], ['photos' => $hero['photos'], 'copy' => $hero['copy'] ?? [], 'interval' => $hero['interval'] ?? 6500]);
echo Module::render('sections/collections', ['galleries' => $galleries]);
