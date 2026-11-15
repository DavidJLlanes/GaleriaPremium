/* Muestra solo las opciones del diseño seleccionado en el selector de diseño. */
(() => {
  const radios = [...document.querySelectorAll('input[name=style]')]; if (!radios.length) return;
  const sync = () => {
    const v = document.querySelector('input[name=style]:checked')?.value;
    document.querySelectorAll('.so').forEach(f => f.hidden = f.dataset.style !== v);
  };
  radios.forEach(r => r.addEventListener('change', sync)); sync();
})();
