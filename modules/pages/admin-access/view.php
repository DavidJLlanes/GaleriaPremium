<?php use App\Core\Module; use App\Services\Csrf; Module::use('components/passkey');
$fmt = fn ($t) => $t ? date('d/m/Y H:i', (int) $t) : 'nunca'; ?>
<h1>Acceso</h1>
<p class="pnl-sub">Entra al panel con la huella, Face ID, Windows Hello o una llave de seguridad. La contraseña sigue funcionando como alternativa.</p>

<section class="pnl-card" data-passkey-box>
    <h2>Dispositivos registrados <span class="pnl-badge"><?= count($keys) ?></span></h2>
    <?php if (!$keys): ?><p class="pnl-hint">Todavía no hay ninguno. Añade este dispositivo y la próxima vez verás el botón «Entrar con huella o Face ID» en el login.</p><?php endif; ?>
    <ul class="ac-list">
        <?php foreach ($keys as $k): ?>
            <li>
                <b><?= e($k['name']) ?></b>
                <small>Añadido <?= e($fmt($k['created_at'])) ?> · último uso <?= e($fmt($k['last_used'])) ?></small>
                <form method="post" action="/admin/passkey/<?= (int) $k['id'] ?>/borrar" onsubmit="return confirm('¿Quitar este dispositivo? Ya no podrá entrar con él.')">
                    <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
                    <button class="pnl-btn is-ghost is-small">Quitar</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
    <div class="ac-add">
        <input type="text" data-passkey-name maxlength="40" placeholder="Nombre (p. ej. iPhone de David)" aria-label="Nombre del dispositivo">
        <button type="button" class="pnl-btn" data-passkey-add>Añadir este dispositivo</button>
    </div>
    <p class="pk-msg" role="alert"></p>
    <p class="pnl-hint">Solo se guarda la clave pública; tu huella o tu cara nunca salen del dispositivo. Requiere HTTPS (luxaeterna.es lo cumple). Hazlo en cada dispositivo desde el que quieras entrar; con iCloud o Google, las passkeys se sincronizan solas.</p>
</section>
