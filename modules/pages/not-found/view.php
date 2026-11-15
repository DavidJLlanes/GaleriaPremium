<?php use App\Core\Module; ?>
<section class="nf">
    <h1>404</h1>
    <p>Esta imagen se ha perdido en el revelado.</p>
    <?= Module::render('components/button', ['href' => '/', 'label' => 'Volver al inicio']) ?>
</section>
