<?php use App\Core\Module;
echo Module::render('sections/gallery-head', ['gallery' => $gallery]);
echo Module::render('galleries/' . $style, ['gallery' => $gallery, 'photos' => $photos, 'options' => $options ?? []]);
echo Module::render('components/lightbox', ['fx' => $gallery['lightbox'] ?? 'fade']);
echo Module::render('components/lightbox-extras', ['photos' => $photos, 'showExif' => $showExif ?? true]);
