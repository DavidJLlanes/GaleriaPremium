/* Selección múltiple: contenedor [data-bulk] con hijos [data-id] y <span class="bk-box">; barra #bulkBar con borrar / mover. */
(() => {
  const bar = document.getElementById('bulkBar'); if (!bar || !window.Admin) return;
  const list = document.querySelector(bar.dataset.list), noun = bar.dataset.noun, kind = bar.dataset.kind;
  if (!list) return;
  const count = bar.querySelector('.bk-count'), move = bar.querySelector('.bk-move');
  const items = () => [...list.children].filter(el => el.dataset.id);
  const sel = () => items().filter(el => el.classList.contains('is-sel'));
  const plural = (n) => n === 1 ? noun : (noun === 'galería' ? 'galerías' : 'fotos');

  const refresh = () => {
    const n = sel().length; bar.hidden = n === 0;
    count.textContent = `${n} ${plural(n)} ${n === 1 ? 'seleccionada' : 'seleccionadas'}`;
  };
  const toggle = (el, on) => { el.classList.toggle('is-sel', on ?? !el.classList.contains('is-sel')); el.querySelector('.bk-box')?.setAttribute('aria-checked', el.classList.contains('is-sel')); refresh(); };

  // los .bk-box están dentro de <a>: evitar navegar y que el arrastre/clic de otros componentes se active
  list.addEventListener('click', e => {
    const box = e.target.closest('.bk-box'); if (!box) return;
    e.preventDefault(); e.stopPropagation(); toggle(box.closest('[data-id]'));
  }, true);
  list.addEventListener('keydown', e => {
    const box = e.target.closest?.('.bk-box'); if (!box || (e.key !== ' ' && e.key !== 'Enter')) return;
    e.preventDefault(); toggle(box.closest('[data-id]'));
  });
  addEventListener('keydown', e => { if (e.key === 'Escape' && sel().length && !document.querySelector('.cr:not([hidden]),.du.is-open')) items().forEach(i => toggle(i, false)); });

  const post = async (url, extra) => {
    const fd = new FormData(); sel().forEach(el => fd.append('ids[]', el.dataset.id));
    Object.entries(extra || {}).forEach(([k, v]) => fd.append(k, v));
    return Admin.post(url, fd);
  };
  const busy = (on) => bar.querySelectorAll('button,select').forEach(b => b.disabled = on);

  bar.addEventListener('click', async e => {
    const b = e.target.closest('[data-bk]'); if (!b) return;
    if (b.dataset.bk === 'all') return items().forEach(i => toggle(i, true));
    if (b.dataset.bk === 'none') return items().forEach(i => toggle(i, false));
    if (b.dataset.bk === 'delete') {
      const n = sel().length;
      const warn = kind === 'galleries' ? `¿Borrar ${n} ${plural(n)} con TODAS sus fotos? No se puede deshacer.` : `¿Borrar ${n} ${plural(n)}? No se puede deshacer.`;
      if (!confirm(warn)) return;
      busy(true);
      try { const r = await post(bar.dataset.deleteUrl); Admin.toast(`${r.count} ${plural(r.count)} ${r.count === 1 ? 'borrada' : 'borradas'}`); setTimeout(() => location.reload(), 500); }
      catch (err) { Admin.toast(err.message, 'error'); busy(false); }
    }
  });

  move?.addEventListener('change', async () => {
    if (!move.value) return;
    const n = sel().length, dest = move.options[move.selectedIndex].text;
    if (!confirm(`¿Mover ${n} ${plural(n)} a «${dest}»?`)) { move.value = ''; return; }
    busy(true);
    try { const r = await post(bar.dataset.moveUrl, { gallery: move.value }); Admin.toast(`${r.moved} ${plural(r.moved)} movidas a «${dest}»`); setTimeout(() => location.reload(), 600); }
    catch (err) { Admin.toast(err.message, 'error'); busy(false); move.value = ''; }
  });
})();
