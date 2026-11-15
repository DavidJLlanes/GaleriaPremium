<?php use App\Core\Module;
echo Module::render('sections/gallery-head', ['gallery' => $gallery]);
echo Module::render('galleries/' . $style, ['gallery' => $gallery, 'photos' => $photos]);
echo Module::render('components/lightbox');
