/* Marca el elemento compartido de la transición: la tarjeta pulsada (portada) ↔ la cabecera de su galería (y al volver).
   Usa los eventos pageswap/pagereveal (Chrome/Edge/Safari recientes); en otros navegadores no se ejecuta nada. */
(() => {
  const slugOf = (u) => { try { const p = new URL(u, location.href).pathname; return p.startsWith('/galeria/') ? decodeURIComponent(p.slice(9)).replace(/\/$/, '') : null; } catch { return null; } };
  const cardFor = (slug) => { const a = [...document.querySelectorAll('.ccard,a[data-pt]')].find(a => slugOf(a.href) === slug); return a && (a.querySelector('.ccard-media,[data-pt-media]') || a); };   // cualquier diseño de portada: enlace con data-pt (+ data-pt-media)
  const mark = (el, vt) => { if (!el) return; el.style.viewTransitionName = 'pt-card'; vt.finished.finally(() => { el.style.viewTransitionName = ''; }); };

  // El panel (/admin) no usa transiciones: en iOS (web app) la animación con redirección al login dejaba la pantalla en negro.
  const toAdmin = (u) => { try { return new URL(u, location.href).pathname.startsWith('/admin'); } catch { return false; } };
  document.addEventListener('click', (e) => { const a = e.target.closest?.('a[href]'); if (a && toAdmin(a.href)) try { sessionStorage.setItem('pt_skip', '1'); } catch {} }, true);

  addEventListener('pageswap', (e) => {             // página que se abandona
    if (!e.viewTransition) return;
    let skip = false; try { skip = sessionStorage.getItem('pt_skip') === '1'; sessionStorage.removeItem('pt_skip'); } catch {}
    if (skip || toAdmin(e.activation?.entry?.url || '')) { e.viewTransition.skipTransition(); return; }
    const to = e.activation?.entry?.url;
    const toSlug = slugOf(to || '');
    if (toSlug) mark(cardFor(toSlug), e.viewTransition);                       // portada → galería: la tarjeta pulsada
    else if (slugOf(location.href)) mark(document.querySelector('.gh'), e.viewTransition);   // galería → otra: la cabecera
  });
  addEventListener('pagereveal', (e) => {           // página que llega
    if (!e.viewTransition) return;
    const from = slugOf(window.navigation?.activation?.from?.url || '');
    if (slugOf(location.href)) mark(document.querySelector('.gh'), e.viewTransition);       // llegada a una galería: su cabecera
    else if (from) mark(cardFor(from), e.viewTransition);                                     // vuelta a la portada: la tarjeta de la que venimos
  });
})();
