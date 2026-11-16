/* Hero «Título dibujado con luz»: usa el motor components/light-pen (la pluma de luz escribe el nombre de la web desde cero). */
(() => {
  const root = document.querySelector('.hx-light'); if (!root) return;
  const h1 = root.querySelector('.hxz-title'), cv = root.querySelector('.hxz-canvas');
  const mount = () => window.LightPen && window.LightPen.mount({
    box: root, text: h1, canvas: cv, speed: root.dataset.speed, loop: root.dataset.repeat === '1',
    colour: getComputedStyle(root).getPropertyValue('--hxz-c').trim() || '#ffb45a',
    size: () => Math.max(44, Math.min(innerWidth * .16, 140)),
    onStart: () => root.classList.remove('is-done', 'hxz-out'), onReady: () => root.classList.add('is-ready'),
    onDone: () => root.classList.add('is-done'), onOut: () => root.classList.add('hxz-out'),
  });
  // Los scripts van con defer y en orden de registro: el motor (components/light-pen) puede cargarse después de este; DOMContentLoaded llega cuando todos han corrido
  if (window.LightPen) mount(); else addEventListener('DOMContentLoaded', mount);
})();
