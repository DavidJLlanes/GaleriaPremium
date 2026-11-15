/* Reordenar arrastrando (ratón y táctil). Cualquier contenedor [data-sortable] cuyos hijos tengan data-id y un tirador .srt-handle.
   Si el contenedor tiene data-reorder-url, envía allí el nuevo orden (ids separados por comas). Siempre lanza el evento "sortable:end". */
(() => {
  const lists = [...document.querySelectorAll('[data-sortable]')];
  if (!lists.length) return;
  let list = null, row = null, before = '';
  const items = () => [...list.children].filter(c => c.dataset.id !== undefined);
  const ids = () => items().map(r => r.dataset.id).join(',');

  // el tirador nunca debe activar enlaces/botones que lo contengan
  document.addEventListener('click', e => { if (e.target.closest('.srt-handle')) { e.preventDefault(); e.stopPropagation(); } }, true);

  lists.forEach(l => l.addEventListener('pointerdown', e => {
    const h = e.target.closest('.srt-handle'); if (!h) return;
    e.preventDefault(); list = l; row = h.closest('[data-id]'); before = ids();
    row.classList.add('is-dragging'); list.classList.add('is-sorting');
    document.addEventListener('pointermove', move); document.addEventListener('pointerup', end); document.addEventListener('pointercancel', end);
  }));

  // un <a> o <img> arrastrable inicia el arrastre nativo del navegador y cancela el nuestro
  document.addEventListener('dragstart', e => { if (row) e.preventDefault(); });

  // Los eventos van al document: al reordenar el DOM el navegador suelta la captura del puntero
  const move = e => {
    if (!row) return;
    const t = items().find(r => { if (r === row) return false; const b = r.getBoundingClientRect(); return e.clientX >= b.left && e.clientX <= b.right && e.clientY >= b.top && e.clientY <= b.bottom; });
    if (t) {
      const a = row.getBoundingClientRect(), b = t.getBoundingClientRect();
      const sameRow = Math.abs(a.top - b.top) < Math.min(a.height, b.height) / 2;
      const isBefore = sameRow ? e.clientX < b.left + b.width / 2 : e.clientY < b.top + b.height / 2;
      isBefore ? list.insertBefore(row, t) : t.after(row);
    }
    if (e.clientY < 70) scrollBy(0, -14); else if (e.clientY > innerHeight - 70) scrollBy(0, 14);
  };

  const end = async () => {
    document.removeEventListener('pointermove', move); document.removeEventListener('pointerup', end); document.removeEventListener('pointercancel', end);
    if (!row) return;
    const l = list, now = ids(), changed = now !== before;
    row.classList.remove('is-dragging'); l.classList.remove('is-sorting'); row = null; list = null;
    if (!changed) return;
    l.dispatchEvent(new CustomEvent('sortable:end', { bubbles: true, detail: { ids: now.split(',') } }));
    if (!l.dataset.reorderUrl || !window.Admin) return;
    try { await Admin.post(l.dataset.reorderUrl, { ids: now }); Admin.toast('Orden guardado'); }
    catch (err) { Admin.toast(err.message, 'error'); }
  };
})();
