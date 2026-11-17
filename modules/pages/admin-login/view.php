<?php use App\Core\{Config, Module}; use App\Services\Csrf; Module::use('components/passkey'); ?>
<section class="al">
    <h1><?= e(Config::get('app.name')) ?></h1>
    <p class="ad-sub">Acceso al panel de administración</p>
    <form method="post" action="/admin/login" class="ad-card">
        <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
        <div style="margin-bottom:1rem"><label for="u">Usuario</label><input id="u" type="text" name="username" autocomplete="username" required autofocus></div>
        <div style="margin-bottom:1.4rem"><label for="p">Contraseña</label><input id="p" type="password" name="password" autocomplete="current-password" required></div>
        <button class="ad-btn" style="width:100%;justify-content:center">Entrar</button>
    </form>
    <div data-passkey-box>
        <div class="pk-or"><span>o</span></div>
        <button type="button" class="ad-btn is-ghost pk-btn" data-passkey-login>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 11v3a6 6 0 0 1-1 3.4M8 14.5V11a4 4 0 0 1 8 0M5 11a7 7 0 0 1 14 0v2.5M5 14v-3M19 17.5c0 1.2-.2 2.4-.6 3.5M15 14v1.5c0 1.8-.5 3.5-1.4 5M8.2 18.5c-.3.9-.7 1.7-1.2 2.5"/></svg>
            Entrar con huella o Face ID
        </button>
        <p class="pk-msg" role="alert"></p>
    </div>
</section>
