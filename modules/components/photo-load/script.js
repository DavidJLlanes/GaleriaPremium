/* Marca como cargadas las fotos con miniatura de carga (data-lq): al llegar la real se anima el desenfoque → nítido. */
(() => {
  const done = (img, instant) => { if (img.classList.contains('is-loaded')) return; if (instant) img.classList.add('lq-instant'); img.classList.add('is-loaded'); };
  const watch = (img) => {
    if (img.complete && img.naturalWidth) done(img, true);
    else { img.addEventListener('load', () => done(img, false), { once: true }); img.addEventListener('error', () => done(img, true), { once: true }); }
  };
  document.querySelectorAll('img[data-lq]').forEach(watch);
})();
