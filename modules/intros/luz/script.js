/* Entrada «Luz»: el motor components/light-pen escribe el texto; al terminar, avisa a la pantalla de entrada para cerrarse. */
(() => {
  const root = document.querySelector('.inl'); if (!root) return;
  const intro = document.getElementById('intro');
  const run = () => window.LightPen.mount({
    box: root, text: root.querySelector('.inl-title'), canvas: root.querySelector('.inl-canvas'), speed: root.dataset.speed, loop: false,
    colour: getComputedStyle(intro).getPropertyValue('--in-ac').trim() || '#ffb45a',
    size: () => Math.max(40, Math.min(innerWidth * .13, 112)),
    onReady: () => root.classList.add('is-ready'),
    onDone: () => { root.classList.add('is-done'); window.Intro && window.Intro.at(1100); },
  });
  const mount = () => { if (!(window.LightPen && run())) { root.classList.add('is-fallback'); window.Intro && window.Intro.at(2600); } };
  if (window.LightPen) mount(); else addEventListener('DOMContentLoaded', mount);
})();
