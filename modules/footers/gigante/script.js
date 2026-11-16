/* Pie «Gigante»: ajusta el tamaño de letra para que el nombre ocupe exactamente el ancho disponible, sea cual sea la tipografía
 * (las manuscritas son mucho más anchas o estrechas que las de palo seco). */
(() => {
  const box = document.querySelector('.fg'), w = box && box.querySelector('.fg-word'), t = w && w.firstElementChild; if (!t) return;
  const fit = () => {
    w.style.setProperty('--fg-size', '100px');
    const avail = box.clientWidth, cur = t.getBoundingClientRect().width; if (!avail || !cur) return;
    w.style.setProperty('--fg-size', Math.min(avail / cur * 100 * .98, innerWidth * .26).toFixed(1) + 'px');
  };
  fit(); addEventListener('resize', fit);
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(fit);
  addEventListener('load', fit);
})();
