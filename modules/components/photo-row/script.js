/* Edición de cada foto: guardar, generar con IA (nombre / descripción), portada y borrar. */
(() => {
  const list = document.querySelector('.pr-list');
  if (!list || !window.Admin) return;

  const run = async (btn, fn) => {
    btn.classList.add('is-busy');
    try { await fn(); } catch (e) { Admin.toast(e.message, 'error'); } finally { btn.classList.remove('is-busy'); }
  };

  list.addEventListener('input', e => e.target.closest('.pr')?.classList.add('is-dirty'));

  list.addEventListener('click', e => {
    const btn = e.target.closest('button'); if (!btn) return;
    const row = btn.closest('.pr'); const id = row.dataset.id;
    const t = row.querySelector('.t'), d = row.querySelector('.d');

    if (btn.dataset.ai) run(btn, async () => {
      const prev = btn.textContent; btn.textContent = 'Pensando…';
      try {
        const r = await Admin.post(`/admin/foto/${id}/ia`, { title: t.value });
        if (btn.dataset.ai === 'title') t.value = r.title; else d.value = r.description;
        row.classList.add('is-dirty'); Admin.toast('Propuesta lista. Revísala y pulsa Guardar.');
      } finally { btn.textContent = prev; }
    });

    if (btn.dataset.act === 'save') run(btn, async () => {
      await Admin.post(`/admin/foto/${id}/editar`, { title: t.value, description: d.value });
      row.classList.remove('is-dirty'); Admin.toast('Guardado');
    });

    if (btn.dataset.act === 'cover') run(btn, async () => {
      await Admin.post(`/admin/foto/${id}/portada`);
      list.querySelectorAll('[data-act=cover]').forEach(b => b.textContent = '☆ Portada');
      btn.textContent = '★ Portada'; Admin.toast('Portada actualizada');
    });

    if (btn.dataset.act === 'delete') {
      if (!confirm('¿Borrar esta foto? No se puede deshacer.')) return;
      run(btn, async () => {
        await Admin.post(`/admin/foto/${id}/borrar`);
        row.classList.add('is-gone'); setTimeout(() => row.remove(), 400); Admin.toast('Foto borrada');
      });
    }
  });
})();
