/* Vista previa en vivo: al elegir una tipografía se inyecta SOLO su @font-face y se aplica a la muestra. */
(() => {
  const root = document.querySelector('.fp'); if (!root) return;
  const faces = JSON.parse(root.dataset.faces), fam = JSON.parse(root.dataset.fam), loaded = new Set();
  const load = (id) => {
    if (loaded.has(id)) return Promise.resolve();
    loaded.add(id); const st = document.createElement('style'); st.textContent = faces[id]; document.head.appendChild(st);
    return document.fonts.load(`16px "${fam[id].split("'")[1]}"`).catch(() => {});
  };
  root.querySelectorAll('.fp-row').forEach(row => {
    const sel = row.querySelector('select'), prev = row.querySelector('.fp-prev');
    const apply = async () => {
      const id = sel.value || prev.dataset.def; prev.classList.add('is-loading');
      await load(id); prev.style.fontFamily = fam[id]; prev.classList.remove('is-loading');
    };
    sel.addEventListener('change', apply);
    load(prev.dataset.cur);
  });
})();
