<?php use App\Core\Module; use App\Services\Site;
$o = Module::meta('components/legal-modal')['owner']; $site = Site::get('name'); ?>
<dialog class="lg" id="lg" aria-labelledby="lg-h" data-lenis-prevent>
    <div class="lg-p">
        <button class="lg-x" type="button" data-lg-close aria-label="Cerrar"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6 6 18" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg></button>
        <header class="lg-h">
            <p class="lg-e"><?= e($site) ?></p>
            <h2 id="lg-h">Información legal</h2>
            <nav class="lg-n" aria-label="Secciones">
                <a href="#lg-aviso" class="is-on">Aviso legal</a><a href="#lg-priv">Privacidad</a><a href="#lg-cook">Cookies</a><i class="lg-ink" aria-hidden="true"></i>
            </nav>
        </header>
        <div class="lg-s" tabindex="0">
            <section id="lg-aviso">
                <h3><span>01</span>Aviso legal</h3>
                <p><b>Titular.</b> Este sitio web pertenece y es gestionado por <?= e($o['name']) ?>, con residencia en <?= e($o['city']) ?> (España).</p>
                <p><b>Objeto.</b> <?= e($site) ?> es una galería fotográfica en línea cuyo fin es mostrar el trabajo fotográfico de su titular. Su uso implica la aceptación de este aviso.</p>
                <p><b>Propiedad intelectual.</b> Las fotografías, textos, diseño y código de esta web son obra de su titular, salvo indicación expresa, y están protegidos por la normativa de propiedad intelectual. No se permite su reproducción, distribución ni uso comercial sin autorización previa y por escrito.</p>
                <p><b>Responsabilidad.</b> El titular procura que la información sea correcta y esté actualizada, pero no garantiza la ausencia de errores ni la disponibilidad continua del sitio. Los enlaces a páginas de terceros (por ejemplo, redes sociales) son informativos: su contenido y sus políticas son responsabilidad de sus propietarios.</p>
                <p><b>Legislación aplicable.</b> Este aviso se rige por la ley española, en particular la Ley 34/2002 de servicios de la sociedad de la información y de comercio electrónico (LSSI-CE).</p>
            </section>
            <section id="lg-priv">
                <h3><span>02</span>Política de privacidad</h3>
                <p><b>Responsable.</b> <?= e($o['name']) ?>, <?= e($o['city']) ?> (España).</p>
                <p><b>Qué datos se tratan.</b> Esta web no tiene registro de usuarios ni formularios públicos, y no solicita datos personales a quienes la visitan. Como en cualquier servidor web, se registran datos técnicos de la conexión (dirección IP, fecha y hora, página solicitada y navegador) con fines de seguridad y buen funcionamiento.</p>
                <p><b>Base y conservación.</b> El tratamiento se basa en el interés legítimo de mantener el sitio seguro (art. 6.1.f RGPD) y los registros se conservan solo el tiempo imprescindible. Si te pones en contacto por correo o redes sociales, se usarán tus datos únicamente para responderte.</p>
                <p><b>Destinatarios.</b> No se ceden datos a terceros, salvo obligación legal. El proveedor de alojamiento actúa como encargado del tratamiento.</p>
                <p><b>Tus derechos.</b> Puedes ejercer los derechos de acceso, rectificación, supresión, oposición, limitación y portabilidad a través de los canales de contacto publicados en esta web. Si consideras que no se han atendido, puedes reclamar ante la Agencia Española de Protección de Datos (aepd.es).</p>
            </section>
            <section id="lg-cook">
                <h3><span>03</span>Política de cookies</h3>
                <p><b>Qué se usa.</b> Solo almacenamiento técnico imprescindible: una cookie de sesión para el acceso al panel de administración y datos del propio navegador para recordar preferencias de uso (por ejemplo, si ya has visto la pantalla de entrada).</p>
                <p><b>Qué no se usa.</b> No hay cookies de analítica, publicidad ni seguimiento, ni de terceros. Por eso no se solicita consentimiento (art. 22.2 LSSI-CE).</p>
                <p><b>Cómo eliminarlas.</b> Puedes borrar o bloquear estos datos en los ajustes de tu navegador en cualquier momento; el sitio seguirá funcionando.</p>
            </section>
        </div>
    </div>
</dialog>
