(() => {
  const form = document.getElementById('menuForm'), frame = document.getElementById('mnPrev'); if (!form || !frame) return;
  const val = (n) => form.querySelector(`[name=${n}]:checked`)?.value || '';
  // Ajustes propios del icono / efecto / diseño elegido
  const syncOpts = () => form.querySelectorAll('[data-opts-kind]').forEach(c => { c.hidden = val('menu_' + c.dataset.optsKind) !== c.dataset.optsId; });
  // Vista previa: la propia web en un iframe, con el menú abierto y los ajustes de los selectores (solo con sesión de administrador)
  let t = 0;
  const preview = () => {
    const q = new URLSearchParams({ menu_preview: 1, menu_open: 1, mi: val('menu_icon'), mr: val('menu_reveal'), ms: val('menu_style') });
    clearTimeout(t); t = setTimeout(() => { frame.src = '/?' + q; }, 120);
  };
  form.addEventListener('change', () => { syncOpts(); preview(); }); syncOpts();
})();
