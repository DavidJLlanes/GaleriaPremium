/* Llegada «Desvelar»: el pie se queda fijo detrás de la web y la página sube para descubrirlo. */
(() => {
  const f = document.querySelector('.ftr.fr-desvelar'); if (!f) return;
  const fit = () => {
    const h = f.offsetHeight, ok = h > 0 && h < innerHeight * .9 && !matchMedia('(prefers-reduced-motion: reduce)').matches;
    document.body.classList.toggle('ftr-rv', ok); document.body.style.setProperty('--ftr-h', ok ? h + 'px' : '0px');
  };
  fit(); addEventListener('resize', fit); addEventListener('load', fit);
  if ('ResizeObserver' in window) new ResizeObserver(fit).observe(f);
})();
