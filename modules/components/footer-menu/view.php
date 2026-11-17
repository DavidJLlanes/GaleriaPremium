<?php /* $f = Footer::data(). Desplegable en línea (crece hacia abajo; no se recorta con los efectos de llegada del pie). */
use App\Core\Module;
$id = 'fm-' . substr(md5('fm'), 0, 6); ?>
<div class="fm ftr-i" data-fm>
    <button class="fm-b" type="button" aria-expanded="false" aria-controls="<?= $id ?>" aria-haspopup="true">
        <span class="fm-t">Más</span>
        <svg class="fm-c" viewBox="0 0 12 12" aria-hidden="true" focusable="false"><path d="M2.5 4.5 6 8l3.5-3.5" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>
    <div class="fm-w" id="<?= $id ?>" inert>
        <ul class="fm-l">
            <?php $n = 0; ?>
            <li style="--n:<?= $n++ ?>"><button type="button" class="fm-i" data-legal-open>Información legal</button></li>
            <?php if ($f['mail'] !== ''): ?><li style="--n:<?= $n++ ?>"><a class="fm-i" href="mailto:<?= e($f['mail']) ?>">Contacto</a></li><?php endif; ?>
            <?php if ($f['admin']): ?><li style="--n:<?= $n++ ?>"><a class="fm-i" href="/admin/login" rel="nofollow">Administración</a></li><?php endif; ?>
        </ul>
    </div>
</div>
<?= Module::render('components/legal-modal') ?>
