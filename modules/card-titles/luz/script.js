/* Título de tarjeta «Pluma de luz»: cada título se escribe (components/light-pen) cuando su tarjeta entra en pantalla. */
(() => {
  const boxes = [...document.querySelectorAll('.ct-luz')]; if (!boxes.length) return;
  const go = (box) => {
    if (box._lp) return;
    const h = box.querySelector('.ct-luz-title'), cv = box.querySelector('.ct-luz-canvas');
    box._lp = window.LightPen && window.LightPen.mount({
      box, text: h, canvas: cv, speed: box.dataset.speed, loop: false, defer: true, maxW: 640,
      colour: getComputedStyle(box).getPropertyValue('--ctl-c').trim() || '#ffb45a',
      size: (W) => Math.max(28, Math.min(W * .17, 60)),
      onStart: () => box.classList.remove('is-done'), onReady: () => box.classList.add('is-ready'), onDone: () => box.classList.add('is-done'),
    });
    if (!box._lp) fb(box);
  };
  const fb = (box) => box.classList.add('is-fallback');   // sin motor o sin canvas: se enseña el título normal
  const start = () => {
    if (!window.LightPen) return boxes.forEach(fb);
    const io = new IntersectionObserver((es) => es.forEach(e => { if (e.isIntersecting) { io.unobserve(e.target); setTimeout(() => go(e.target), 250); } }), { threshold: .55 });
    boxes.forEach(b => io.observe(b));
  };
  // Los scripts van con defer y en orden: el motor puede cargarse después; DOMContentLoaded llega cuando todos han corrido
  if (window.LightPen) start(); else addEventListener('DOMContentLoaded', start);
})();
