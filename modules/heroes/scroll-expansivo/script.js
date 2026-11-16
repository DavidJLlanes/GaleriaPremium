/* Scroll expansivo: --p (0→1) sube mientras se recorre la altura extra de la sección. */
(() => {
  const el = document.querySelector('.hx-expand'); if (!el) return;
  const upd = () => {
    const max = el.offsetHeight - innerHeight; if (max <= 0) return;
    const p = Math.max(0, Math.min(1, (scrollY - el.offsetTop) / (max * .92)));
    el.style.setProperty('--p', p.toFixed(4));
  };
  addEventListener('scroll', upd, { passive: true }); addEventListener('resize', upd); upd();
})();
