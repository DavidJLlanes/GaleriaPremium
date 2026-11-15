(() => {
  const prov = document.getElementById('prov'); if (!prov) return;
  const show = () => document.querySelectorAll('.as-prov').forEach(el => el.classList.toggle('is-on', el.dataset.prov === prov.value));
  prov.addEventListener('change', show); show();

  const btn = document.getElementById('aiTest'), out = document.getElementById('aiTestOut');
  btn.addEventListener('click', async () => {
    btn.disabled = true; out.className = 'ad-hint'; out.textContent = 'Probando…';
    try {
      const r = await Admin.post('/admin/ajustes/probar');
      out.className = 'ad-hint is-ok'; out.textContent = `✓ Funciona. Ejemplo de título: «${r.title}»`;
    } catch (e) { out.className = 'ad-hint is-err'; out.textContent = '✗ ' + e.message; }
    finally { btn.disabled = false; }
  });

  // ---- Selector de modelos: carga la lista del proveedor y deja elegir ----
  const esc = (t) => String(t).replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
  document.querySelectorAll('.as-prov').forEach(box => {
    const id = box.dataset.prov, btn = box.querySelector('.as-load'), panel = box.querySelector('.as-models');
    const input = box.querySelector(`[name=ai_model_${id}]`), q = panel.querySelector('.as-mq');
    const vis = panel.querySelector('.as-mvis'), list = panel.querySelector('.as-mlist'), count = panel.querySelector('.as-mcount');
    let models = [];

    const draw = () => {
      const term = q.value.trim().toLowerCase();
      const rows = models.filter(m => (!vis.checked || m.vision !== false) && (m.id + ' ' + m.name).toLowerCase().includes(term));
      count.textContent = `${rows.length} de ${models.length}`;
      list.innerHTML = rows.slice(0, 300).map(m => `<button type="button" class="as-m ${m.id === input.value ? 'is-sel' : ''}" data-id="${esc(m.id)}">
        <b>${esc(m.name)}${m.vision ? '<i>visión</i>' : ''}</b><small>${esc(m.id)}</small>${m.price ? `<em>${esc(m.price)}</em>` : ''}</button>`).join('')
        || '<p class="ad-hint" style="padding:.6rem">Sin resultados. Prueba a quitar el filtro «Solo con visión».</p>';
    };

    btn.addEventListener('click', async () => {
      const f = (n) => box.querySelector(`[name=${n}_${id}]`)?.value || '';
      btn.disabled = true; const prev = btn.textContent; btn.textContent = 'Cargando…';
      try {
        const r = await Admin.post('/admin/ajustes/modelos', { provider: id, key: f('ai_key'), base_url: f('ai_base_url') });
        models = r.models; if (!models.some(m => m.vision !== null)) { vis.checked = false; vis.parentElement.hidden = true; }
        panel.hidden = false; q.value = ''; draw(); q.focus();
      } catch (e) { Admin.toast(e.message, 'error'); }
      finally { btn.disabled = false; btn.textContent = prev; }
    });
    q.addEventListener('input', draw); vis.addEventListener('change', draw);
    list.addEventListener('click', e => {
      const b = e.target.closest('.as-m'); if (!b) return;
      input.value = b.dataset.id; draw(); Admin.toast('Modelo elegido. Pulsa «Guardar ajustes».');
    });
  });
})();
