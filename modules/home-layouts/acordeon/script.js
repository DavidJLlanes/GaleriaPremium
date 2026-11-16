/* Portada «Acordeón»: siempre hay una franja abierta (la que se señala o enfoca). En táctil: primer toque abre, segundo entra. */
(() => {
  document.querySelectorAll('.hlx').forEach((root) => {   // varias secciones en la portada si hay sliders entre medias
  const ss = [...root.querySelectorAll('.hlx-s')];
  const open = (s) => ss.forEach(x => x.classList.toggle('is-open', x === s));
  ss.forEach(s => {
    s.addEventListener('pointerenter', (e) => { if (e.pointerType === 'mouse') open(s); });
    s.addEventListener('focus', () => open(s));
    s.addEventListener('click', (e) => { if (!s.classList.contains('is-open')) { e.preventDefault(); open(s); } });
  });
  if (matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) return;
  root.classList.add('is-js');
  const io = new IntersectionObserver((es) => { if (es[0].isIntersecting) { io.disconnect(); requestAnimationFrame(() => requestAnimationFrame(() => root.classList.add('is-in'))); } }, { threshold: .15 });
  io.observe(root);
});
})();
