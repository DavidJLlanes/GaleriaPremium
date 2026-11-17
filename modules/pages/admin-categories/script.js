(() => {
  if (!window.Admin) return;
  // — Diseño: tamaño en vivo —
  const rng = document.getElementById('catRange'), out = document.getElementById('catSz');
  if (rng) {
    const names = ['Muy pequeño', 'Pequeño', 'Mediano', 'Grande', 'Muy grande'], k = [.8, .9, 1, 1.2, 1.45];
    rng.addEventListener('input', () => { out.textContent = names[rng.value - 1]; document.querySelectorAll('.cat-prev').forEach(p => p.style.setProperty('--pc-k', k[rng.value - 1])); });
  }
  const list = document.getElementById('catList'); if (!list) return;
  const rows = () => [...list.children];
  const done = (msg, ms = 700) => { Admin.toast(msg); setTimeout(() => location.reload(), ms); };
  const fail = (e) => Admin.toast(e.message || 'No se pudo completar', 'error');

  // — Crear —
  document.getElementById('catNew')?.addEventListener('submit', async (e) => {
    e.preventDefault(); const i = e.target.querySelector('input');
    try { await Admin.post('/admin/categorias/crear', { name: i.value }); done('Categoría creada', 400); } catch (err) { fail(err); }
  });
  // — Buscar y ordenar —
  const find = document.getElementById('catFind'), sort = document.getElementById('catSort');
  const apply = () => {
    const q = find.value.trim().toLowerCase();
    const by = { az: (a, b) => a.dataset.name.localeCompare(b.dataset.name), n: (a, b) => b.dataset.n - a.dataset.n || a.dataset.name.localeCompare(b.dataset.name), n0: (a, b) => a.dataset.n - b.dataset.n || a.dataset.name.localeCompare(b.dataset.name) }[sort.value];
    rows().sort(by).forEach(r => { list.append(r); r.hidden = q !== '' && !r.dataset.name.includes(q); });
  };
  find.addEventListener('input', apply); sort.addEventListener('change', apply);
  // — Limpiar vacías —
  document.getElementById('catClean')?.addEventListener('click', async () => {
    const vacias = rows().filter(r => r.dataset.n === '0').length;
    if (!vacias) { Admin.toast('No hay categorías vacías'); return; }
    if (!confirm(`¿Borrar las ${vacias} categorías que no usa ninguna foto?`)) return;
    try { const r = await Admin.post('/admin/categorias/limpiar'); done(`${r.count} categorías borradas`); } catch (err) { fail(err); }
  });
  // — Unir marcadas —
  const mg = document.getElementById('catMerge'), mgInto = document.getElementById('catMergeInto'), mgN = document.getElementById('catMergeN');
  const marked = () => rows().filter(r => r.classList.contains('is-sel'));
  const refreshMerge = () => {
    const m = marked(); mg.hidden = m.length < 2; if (m.length < 2) return;
    mgN.textContent = `${m.length} marcadas →`; const keep = mgInto.value;
    mgInto.replaceChildren(...m.map(r => Object.assign(document.createElement('option'), { value: r.dataset.id, textContent: r.querySelector('input').value })));
    if (keep && m.some(r => r.dataset.id === keep)) mgInto.value = keep;
  };
  const toggle = (r) => { r.classList.toggle('is-sel'); r.querySelector('.cat-chk').setAttribute('aria-checked', r.classList.contains('is-sel')); refreshMerge(); };
  list.addEventListener('click', (e) => { const b = e.target.closest('.cat-chk'); if (b) toggle(b.closest('li')); });
  list.addEventListener('keydown', (e) => { const b = e.target.closest?.('.cat-chk'); if (b && (e.key === ' ' || e.key === 'Enter')) { e.preventDefault(); toggle(b.closest('li')); } });
  document.getElementById('catMergeNo').addEventListener('click', () => { marked().forEach(toggle); });
  // — Acciones por fila —
  list.addEventListener('click', async (e) => {
    const b = e.target.closest('[data-cat]'); if (!b || b.tagName === 'SELECT') return; const li = b.closest('li'), id = li.dataset.id, nm = li.querySelector('input').defaultValue, n = li.dataset.n;
    try {
      if (b.dataset.cat === 'save') {
        try { await Admin.post(`/admin/categoria/${id}/renombrar`, { name: li.querySelector('input').value }); done('Renombrada', 400); }
        catch (err) { if (err.data?.dup && confirm(err.message)) { await Admin.post('/admin/categorias/unir', mergeData([id], err.data.dup)); done('Categorías unidas'); } else if (!err.data?.dup) throw err; }
      } else if (b.dataset.cat === 'del') {
        if (confirm(`¿Borrar la categoría «${nm}»?${n > 0 ? ` Se quita de sus ${n} fotos, que se conservan.` : ''}`)) { await Admin.post(`/admin/categoria/${id}/borrar`); done('Categoría borrada', 400); }
      } else if (b.dataset.cat === 'delall') {
        if (!confirm(`¿Borrar «${nm}» Y sus ${n} fotos? Se eliminan del servidor y NO se puede deshacer.`)) return;
        if (prompt(`Para confirmar, escribe el nombre de la categoría: ${nm}`) !== nm) { Admin.toast('Nombre incorrecto: no se ha borrado nada', 'error'); return; }
        const r = await Admin.post(`/admin/categoria/${id}/borrar`, { with_photos: 1 }); done(`${r.photos} fotos borradas`);
      }
    } catch (err) { fail(err); }
  });
  list.addEventListener('change', async (e) => {
    const s = e.target.closest('select[data-cat=move]'); if (!s || !s.value) return; const li = s.closest('li'), to = s.value, dest = s.options[s.selectedIndex].text, nm = li.querySelector('input').defaultValue;
    if (!confirm(`¿Pasar las ${li.dataset.n} fotos de «${nm}» a «${dest}»? La categoría «${nm}» se conserva, vacía.`)) { s.value = ''; return; }
    try { const r = await Admin.post(`/admin/categoria/${li.dataset.id}/mover`, { to }); done(`${r.moved} fotos movidas a «${dest}»`); } catch (err) { fail(err); s.value = ''; }
  });
  function mergeData(ids, into) { const fd = new FormData(); ids.forEach(i => fd.append('ids[]', i)); fd.append('into', into); return fd; }
  // (unir: reescrito con FormData)
  const go = document.getElementById('catMergeGo'), go2 = go.cloneNode(true); go.replaceWith(go2);
  go2.addEventListener('click', async () => {
    const m = marked(), into = mgInto.value, name = mgInto.options[mgInto.selectedIndex].text;
    if (!confirm(`¿Unir ${m.length} categorías en «${name}»? Las demás desaparecen y sus fotos pasan a «${name}» (las fotos se conservan).`)) return;
    try { const r = await Admin.post('/admin/categorias/unir', mergeData(m.map(x => x.dataset.id).filter(i => i !== into), into)); done(`Unidas · ${r.moved} fotos`); } catch (err) { fail(err); }
  });
})();
