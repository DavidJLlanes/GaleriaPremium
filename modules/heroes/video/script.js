/* Vídeo de fondo: autoplay silencioso en bucle. Se descarta con ahorro de datos, movimiento reducido o (si se pide) en móvil;
 * se pausa fuera de pantalla y con la pestaña oculta. La foto de respaldo queda siempre debajo. */
(() => {
  const box = document.querySelector('.hxv'), v = box?.querySelector('.hxv-video'); if (!v) return;
  const saver = navigator.connection?.saveData || matchMedia('(prefers-reduced-motion: reduce)').matches;
  const mobileOff = box.dataset.mobileOff === '1' && matchMedia('(max-width: 700px)').matches;
  if (saver || mobileOff) { v.remove(); return; }
  v.muted = true; v.addEventListener('playing', () => v.classList.add('is-playing'), { once: true });
  const play = () => v.play().catch(() => { /* el navegador bloqueó el autoplay: queda la foto */ });
  new IntersectionObserver(([e]) => e.isIntersecting ? play() : v.pause(), { threshold: .05 }).observe(box);
  document.addEventListener('visibilitychange', () => document.hidden ? v.pause() : play());
  play();
})();
