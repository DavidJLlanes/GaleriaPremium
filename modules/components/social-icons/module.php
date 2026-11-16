<?php
/** Iconos de redes sociales (trazo fino, heredan el color del texto). Uso:
 *  Module::render('components/social-icons', ['icon' => 'instagram'])  → solo el SVG (formularios);
 *  Module::render('components/social-icons', ['items' => Site::socials()]) → fila de iconos con su enlace (pie). */
return [
    'label' => 'Iconos de redes sociales',
    'desc'  => 'Iconos SVG de Instagram, X, Facebook, Threads, YouTube, LinkedIn y TikTok, y la fila de enlaces con solo iconos.',
    'icons' => [
        'instagram' => '<rect width="20" height="20" x="2" y="2" rx="5.5"/><circle cx="12" cy="12" r="4.2"/><path d="M17.6 6.4h.01"/>',
        'x'         => '<path d="M4 4h4.4l11.6 16h-4.4z"/><path d="M19.6 4 13 11.3M4.4 20l6.6-7.3"/>',
        'facebook'  => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
        'threads'   => '<path d="M17.5 8.4C16.7 5.8 14.8 4.5 12 4.5c-3.5 0-5.5 2.5-5.5 7.5s2 7.5 5.5 7.5c3 0 4.8-1.6 4.8-3.7 0-2.4-2.2-3.4-4.6-3.4-1.9 0-3.2.8-3.2 2.1 0 1.2 1 1.9 2.3 1.9 2.3 0 3.4-1.8 3.4-5.2 0-1.9-.7-3.3-2-4"/>',
        'youtube'   => '<path d="M2.5 17a24.1 24.1 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.6 49.6 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.1 24.1 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.6 49.6 0 0 1-16.2 0A2 2 0 0 1 2.5 17"/><path d="m10 15 5-3-5-3z"/>',
        'linkedin'  => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/>',
        'tiktok'    => '<path d="M14 3v11.5a3.5 3.5 0 1 1-3.5-3.5"/><path d="M14 3c.3 2.6 2 4.4 5 4.6"/>',
    ],
];
