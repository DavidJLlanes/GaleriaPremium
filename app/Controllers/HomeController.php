<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\{Config, View};
use App\Models\Gallery;

final class HomeController
{
    public function index(array $params = []): void
    {
        View::render('home', [
            'title'     => Config::get('app.name') . ' — ' . Config::get('app.tagline'),
            'galleries' => Gallery::published(),
            'isHome'    => true,
        ]);
    }
}
