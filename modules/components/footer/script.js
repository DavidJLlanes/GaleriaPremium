/* Pie de página: numera los elementos (--i, para la llegada en cascada) y «arma» la llegada: el efecto solo se prepara con JS y
 * se dispara una vez cuando el pie entra en pantalla (sin JS o con «reducir movimiento» el pie se ve normal). */
(() => {
  const f = document.querySelector('.ftr'); if (!f) return;
  f.querySelectorAll('.ftr-i').forEach((el, i) => el.style.setProperty('--i', i));
  if (f.dataset.armed !== '1' || matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) return;
  f.classList.add('is-armed');
  // Se observa un testigo de 1 px justo encima del pie (el propio pie puede estar recortado por el efecto y no contaría como visible)
  const sent = document.createElement('i'); sent.setAttribute('aria-hidden', 'true'); sent.style.cssText = 'display:block;height:1px;margin-bottom:-1px;pointer-events:none';
  f.before(sent);
  const io = new IntersectionObserver((es) => { if (es.some(e => e.isIntersecting)) { io.disconnect(); requestAnimationFrame(() => requestAnimationFrame(() => f.classList.add('is-in'))); } }, { rootMargin: '0px 0px -12% 0px' });
  io.observe(sent);
})();

// «Arriba ↑»: sube suavemente (sin «reducir movimiento»). Se usa window.scrollTo: dentro de un onclick en línea, `scrollTo` apuntaría al propio enlace.
document.addEventListener('click', (e) => {
  const a = e.target.closest && e.target.closest('.ftr-top'); if (!a) return;
  e.preventDefault();
  window.scrollTo({ top: 0, behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' });
});
