/* Acciones de categoría sobre la selección de components/bulk (la barra #bulkBar y su lista las define bulk). */
(() => {
  const bar = document.getElementById('bulkBar'); if (!bar || !window.Admin) return;
  const list = document.querySelector(bar.dataset.list); if (!list) return;
  const ids = () => [...list.children].filter(el => el.dataset.id && el.classList.contains('is-sel')).map(el => el.dataset.id);
  const run = async (data, ok) => {
    const fd = new FormData(); ids().forEach(i => fd.append('ids[]', i)); Object.entries(data).forEach(([k, v]) => fd.append(k, v));
    bar.querySelectorAll('button,select').forEach(b => b.disabled = true);
    try {
      const r = await Admin.post('/admin/categorias/fotos', fd);
      Admin.toast(ok(r) + (r.skipped ? ` · ${r.skipped} ya tenían 3 categorías y no se tocaron` : '')); setTimeout(() => location.reload(), 900);
    } catch (e) { Admin.toast(e.message, 'error'); bar.querySelectorAll('button,select').forEach(b => b.disabled = false); }
  };
  const cur = Number(document.querySelector('[data-cat-current]')?.dataset.catCurrent || 0);
  const pick = (sel) => ({ id: sel.value, name: sel.options[sel.selectedIndex].text });
  bar.querySelector('.bc-add')?.addEventListener('change', (e) => {
    const c = pick(e.target); if (!c.id) return; const n = ids().length;
    if (!confirm(`¿Añadir «${c.name}» a ${n} ${n === 1 ? 'foto' : 'fotos'}? (máximo 3 categorías por foto)`)) { e.target.value = ''; return; }
    run({ action: 'add', to: c.id }, r => `«${c.name}» añadida a ${r.changed} ${r.changed === 1 ? 'foto' : 'fotos'}`);
  });
  bar.querySelector('.bc-move')?.addEventListener('change', (e) => {
    const c = pick(e.target); if (!c.id) return; const n = ids().length;
    if (!confirm(`¿Pasar ${n} ${n === 1 ? 'foto' : 'fotos'} a la categoría «${c.name}»?`)) { e.target.value = ''; return; }
    run({ action: 'move', from: cur, to: c.id }, r => `${r.changed} ${r.changed === 1 ? 'foto movida' : 'fotos movidas'} a «${c.name}»`);
  });
  bar.querySelector('.bc-rem')?.addEventListener('click', () => {
    const n = ids().length; if (!confirm(`¿Quitar esta categoría de ${n} ${n === 1 ? 'foto' : 'fotos'}? Las fotos se conservan.`)) return;
    run({ action: 'remove', from: cur }, r => `Categoría quitada de ${r.changed} ${r.changed === 1 ? 'foto' : 'fotos'}`);
  });
})();
