/* Reordenar fotos del panel arrastrando (ratón y táctil). Envía el nuevo orden a data-reorder-url. */
(() => {
  const list = document.querySelector('.pr-list[data-reorder-url]');
  if (!list || !window.Admin) return;
  let row = null, before = '';
  const ids = () => [...list.querySelectorAll('.pr')].map(r => r.dataset.id).join(',');

  list.addEventListener('pointerdown', e => {
    const h = e.target.closest('.pr-handle'); if (!h) return;
    e.preventDefault(); row = h.closest('.pr'); before = ids();
    row.classList.add('is-dragging'); list.classList.add('is-sorting');
    document.addEventListener('pointermove', move); document.addEventListener('pointerup', end); document.addEventListener('pointercancel', end);
  });

  // Los eventos van al document: al reordenar el DOM el navegador suelta la captura del puntero
  const move = e => {
    if (!row) return;
    const rows = [...list.querySelectorAll('.pr')].filter(r => r !== row);
    const next = rows.find(r => { const b = r.getBoundingClientRect(); return e.clientY < b.top + b.height / 2; });
    if (next) { if (row.nextElementSibling !== next) list.insertBefore(row, next); }
    else if (list.lastElementChild !== row) list.appendChild(row);
    // autoscroll cerca de los bordes de la pantalla
    if (e.clientY < 70) scrollBy(0, -14); else if (e.clientY > innerHeight - 70) scrollBy(0, 14);
  };

  const end = async () => {
    document.removeEventListener('pointermove', move); document.removeEventListener('pointerup', end); document.removeEventListener('pointercancel', end);
    if (!row) return;
    row.classList.remove('is-dragging'); list.classList.remove('is-sorting'); row = null;
    const now = ids(); if (now === before) return;
    try { await Admin.post(list.dataset.reorderUrl, { ids: now }); Admin.toast('Orden guardado'); }
    catch (err) { Admin.toast(err.message, 'error'); }
  };
})();
