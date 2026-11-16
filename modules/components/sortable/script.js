/* Reordenar arrastrando (ratón y táctil). Cualquier contenedor [data-sortable] cuyos hijos tengan data-id y un tirador .srt-handle.
   Si el contenedor tiene data-reorder-url, envía allí el nuevo orden (ids separados por comas). Siempre lanza el evento "sortable:end".
   Un hijo con data-fixed no se puede mover ni se puede colocar nada antes que él (p. ej. la galería «fijada»).
   Táctil: la fila arrastrada NUNCA cambia de sitio en el DOM (el navegador cancelaría el gesto); se mueven las demás alrededor de ella
   y la fila sigue al dedo con transform. */
(() => {
  const lists = [...document.querySelectorAll('[data-sortable]')];
  if (!lists.length) return;
  let list = null, row = null, before = '', handle = null, pid = null;
  const items = () => [...list.children].filter(c => c.dataset.id !== undefined);
  const ids = () => items().map(r => r.dataset.id).join(',');

  // el tirador nunca debe activar enlaces/botones que lo contengan
  document.addEventListener('click', e => { if (e.target.closest('.srt-handle')) { e.preventDefault(); e.stopPropagation(); } }, true);

  lists.forEach(l => l.addEventListener('pointerdown', e => {
    const h = e.target.closest('.srt-handle'); if (!h || row) return;
    e.preventDefault(); list = l; row = h.closest('[data-id]'); before = ids(); handle = h; pid = e.pointerId;
    try { h.setPointerCapture(pid); } catch {}
    row.classList.add('is-dragging'); list.classList.add('is-sorting');
    h.addEventListener('pointermove', move); h.addEventListener('pointerup', end); h.addEventListener('pointercancel', end); h.addEventListener('lostpointercapture', end);
    follow(e);
  }));

  // un <a> o <img> arrastrable inicia el arrastre nativo del navegador y cancela el nuestro
  document.addEventListener('dragstart', e => { if (row) e.preventDefault(); });
  // en móvil, mientras se arrastra, la página no debe desplazarse por su cuenta
  document.addEventListener('touchmove', e => { if (row) e.preventDefault(); }, { passive: false });

  const follow = e => {   // la fila acompaña al puntero
    row.style.transform = ''; const b = row.getBoundingClientRect();
    row.style.transform = `translate(${e.clientX - (b.left + b.width / 2)}px, ${e.clientY - (b.top + b.height / 2)}px) scale(.98)`;
  };

  const move = e => {
    if (!row) return;
    const all = items(), others = all.filter(r => r !== row);
    // Dónde queda la fila: se cuentan los elementos «anteriores» al puntero (por fila de la rejilla y, dentro de ella, por la izquierda)
    row.style.transform = '';
    let idx = others.length;
    const rects = others.map(r => r.getBoundingClientRect()), col1 = rects.every(b => Math.abs(b.left - rects[0].left) < 4);
    for (let i = 0; i < others.length; i++) {
      const b = rects[i], cy = b.top + b.height / 2, cx = b.left + b.width / 2;
      if (col1 ? e.clientY < cy : (e.clientY < b.top || (e.clientY <= b.bottom && e.clientX < cx))) { idx = i; break; }
    }
    // los «fijos» siempre quedan primero
    const fixed = others.filter(r => r.dataset.fixed !== undefined).length; idx = Math.max(idx, fixed);
    const want = others.slice(0, idx).concat([row], others.slice(idx));
    if (want.map(r => r.dataset.id).join(',') !== ids()) {
      const pos = want.indexOf(row);
      want.slice(0, pos).forEach(r => row.before(r));                // se colocan ANTES de la fila, en orden
      want.slice(pos + 1).reverse().forEach(r => row.after(r));      // y DESPUÉS de la fila, en orden inverso
    }
    follow(e);
    if (e.clientY < 90) scrollBy(0, -16); else if (e.clientY > innerHeight - 90) scrollBy(0, 16);
  };

  const end = async () => {
    if (!row) return;
    handle.removeEventListener('pointermove', move); handle.removeEventListener('pointerup', end); handle.removeEventListener('pointercancel', end); handle.removeEventListener('lostpointercapture', end);
    try { handle.releasePointerCapture(pid); } catch {}
    const l = list, now = ids(), changed = now !== before;
    row.style.transform = ''; row.classList.remove('is-dragging'); l.classList.remove('is-sorting'); row = null; list = null; handle = null;
    if (!changed) return;
    l.dispatchEvent(new CustomEvent('sortable:end', { bubbles: true, detail: { ids: now.split(',') } }));
    if (!l.dataset.reorderUrl || !window.Admin) return;
    try { await Admin.post(l.dataset.reorderUrl, { ids: now }); Admin.toast('Orden guardado'); }
    catch (err) { Admin.toast(err.message, 'error'); }
  };
})();
