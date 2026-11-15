<?php use App\Core\Module;
if ($hero['type']) echo Module::render('heroes/' . $hero['type'], ['photos' => $hero['photos']]);
echo Module::render('sections/collections', ['galleries' => $galleries]);
