<?php use App\Core\Config; use App\Services\Csrf; ?>
<section class="al">
    <h1><?= e(Config::get('app.name')) ?></h1>
    <p class="ad-sub">Acceso al panel de administración</p>
    <form method="post" action="/admin/login" class="ad-card">
        <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
        <div style="margin-bottom:1rem"><label for="u">Usuario</label><input id="u" type="text" name="username" autocomplete="username" required autofocus></div>
        <div style="margin-bottom:1.4rem"><label for="p">Contraseña</label><input id="p" type="password" name="password" autocomplete="current-password" required></div>
        <button class="ad-btn" style="width:100%;justify-content:center">Entrar</button>
    </form>
</section>
