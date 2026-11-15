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
})();
