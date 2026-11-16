/* Tipografías del panel: vista previa en vivo (fuente, grosor, cursiva), pesos según la fuente y subida/borrado de fuentes propias. */
(() => {
  const root = document.querySelector('.fp'); if (!root) return;
  const meta = JSON.parse(document.getElementById('fpMeta').textContent);
  const WN = JSON.parse(document.getElementById('fpWeights').textContent);
  const loaded = new Set();
  const esc = (s) => String(s).replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));

  const load = (id) => {                                   // inyecta SOLO el @font-face de la fuente elegida
    if (loaded.has(id) || !meta[id]) return Promise.resolve();
    loaded.add(id); const st = document.createElement('style'); st.textContent = meta[id].css; document.head.appendChild(st);
    return document.fonts.load(`16px ${meta[id].fam.split(',')[0]}`).catch(() => {});
  };

  const rows = () => [...root.querySelectorAll('.fp-row')];
  const fill = (row) => {                                  // pesos disponibles de la fuente actual
    const sel = row.querySelector('.fp-font'), wsel = row.querySelector('.fp-weight'), it = row.querySelector('.fp-italic');
    const id = sel.value || row.dataset.def, m = meta[id], keep = wsel.value;
    wsel.innerHTML = '<option value="">Grosor por defecto</option>' + m.w.map(w => `<option value="${w}">${w} · ${esc(WN[w])}</option>`).join('');
    wsel.value = m.w.map(String).includes(keep) ? keep : '';
    it.disabled = !m.i; if (!m.i) it.checked = false;
  };
  const apply = async (row) => {
    const id = row.querySelector('.fp-font').value || row.dataset.def, prev = row.querySelector('.fp-prev');
    prev.classList.add('is-loading'); await load(id);
    prev.style.fontFamily = meta[id].fam;
    prev.style.fontWeight = row.querySelector('.fp-weight').value || '';
    prev.style.fontStyle = row.querySelector('.fp-italic').checked ? 'italic' : 'normal';
    prev.classList.remove('is-loading');
  };
  rows().forEach(row => {
    row.querySelector('.fp-font').addEventListener('change', () => { fill(row); apply(row); });
    row.querySelector('.fp-weight').addEventListener('change', () => apply(row));
    row.querySelector('.fp-italic').addEventListener('change', () => apply(row));
    apply(row);
  });

  // ---- Fuentes propias ----
  const own = document.getElementById('fpOwn'), list = document.getElementById('fpOwnList');
  const addOption = (id, label) => rows().forEach(row => {
    const sel = row.querySelector('.fp-font'); if (sel.querySelector(`option[value="${id}"]`)) return;
    let g = sel.querySelector('optgroup[data-cat=custom]');
    if (!g) { g = document.createElement('optgroup'); g.label = 'Mis fuentes (subidas)'; g.dataset.cat = 'custom'; sel.appendChild(g); }
    g.appendChild(new Option(label, id));
  });
  const setItem = (j) => {
    meta[j.id] = j.meta; loaded.delete(j.id); addOption(j.id, j.label);
    let li = list.querySelector(`li[data-id="${j.id}"]`);
    if (!li) { li = document.createElement('li'); li.dataset.id = j.id; list.appendChild(li); }
    li.innerHTML = `<b></b> <small></small> <button type="button" class="ad-btn is-ghost is-small fp-del">Borrar</button>`;
    li.querySelector('b').textContent = j.label; li.querySelector('b').style.fontFamily = j.meta.fam;
    li.querySelector('small').textContent = j.variants.join(' · '); own.hidden = false;
    document.head.appendChild(Object.assign(document.createElement('style'), { textContent: j.meta.css }));
    rows().forEach(r => { if (r.querySelector('.fp-font').value === j.id) { fill(r); apply(r); } });
  };

  document.getElementById('fpuBtn').addEventListener('click', async (e) => {
    const btn = e.currentTarget, file = document.getElementById('fpuFile').files[0], name = document.getElementById('fpuName').value.trim();
    if (!name) return Admin.toast('Escribe un nombre para la fuente.', 'error');
    if (!file) return Admin.toast('Elige un archivo de fuente.', 'error');
    const fd = new FormData(); fd.append('name', name); fd.append('file', file);
    fd.append('weight', document.getElementById('fpuWeight').value);
    if (document.getElementById('fpuItalic').checked) fd.append('italic', '1');
    btn.disabled = true; btn.textContent = 'Subiendo…';
    try {
      const j = await Admin.post('/admin/fuentes/subir', fd); setItem(j);
      Admin.toast(`Fuente «${j.label}» añadida. Ya puedes elegirla arriba.`);
      document.getElementById('fpuFile').value = ''; const nm = document.querySelector('.fp-up-file .ad-file span'); if (nm) nm.textContent = 'Ningún archivo seleccionado';
    } catch (err) { Admin.toast(err.message, 'error'); }
    btn.disabled = false; btn.textContent = 'Subir fuente';
  });

  list.addEventListener('click', async (e) => {
    const b = e.target.closest('.fp-del'); if (!b) return;
    const li = b.closest('li'), id = li.dataset.id;
    if (!confirm(`¿Borrar la fuente «${li.querySelector('b').textContent}»? Los textos que la usen volverán a su fuente por defecto.`)) return;
    try {
      await Admin.post('/admin/fuentes/borrar', { id });
      li.remove(); if (!list.children.length) own.hidden = true;
      rows().forEach(row => {
        const sel = row.querySelector('.fp-font'), was = sel.value === id;
        sel.querySelector(`option[value="${id}"]`)?.remove();
        sel.querySelectorAll('optgroup').forEach(g => { if (!g.children.length) g.remove(); });
        if (was) { sel.value = ''; fill(row); apply(row); }
      });
      delete meta[id]; Admin.toast('Fuente borrada.');
    } catch (err) { Admin.toast(err.message, 'error'); }
  });
})();
