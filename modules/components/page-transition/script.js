/* Transiciones de página: se conserva el fundido global. Las fotos de tarjeta no se
   emparejan con la cabecera de la galería, porque tienen estructuras y tamaños distintos. */
(() => {
  const isGallery = (u) => { try { return new URL(u, location.href).pathname.startsWith('/galeria/'); } catch { return false; } };
  const toAdmin = (u) => { try { return new URL(u, location.href).pathname.startsWith('/admin'); } catch { return false; } };
  document.addEventListener('click', (e) => {
    const a = e.target.closest?.('a[href]');
    if (a && toAdmin(a.href)) try { sessionStorage.setItem('pt_skip', '1'); } catch {}
  }, true);
  addEventListener('pageswap', (e) => {
    if (!e.viewTransition) return;
    let skip = false;
    try { skip = sessionStorage.getItem('pt_skip') === '1'; sessionStorage.removeItem('pt_skip'); } catch {}
    if (skip || toAdmin(e.activation?.entry?.url || '') || isGallery(location.href) || isGallery(e.activation?.entry?.url || '')) e.viewTransition.skipTransition();
  });
})();
