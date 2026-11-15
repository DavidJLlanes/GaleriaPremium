/* Lightbox · Zoom: técnica FLIP. La foto grande parte del rectángulo de su miniatura y termina en su sitio. */
(() => {
  window.LightboxFx = window.LightboxFx || {};

  // Transformación que lleva la foto grande (b) a cubrir exactamente la miniatura (a), recortada como ella
  const frame = (a, b) => {
    const s = Math.max(a.width / b.width, a.height / b.height);
    const cx = (a.left + a.width / 2) - (b.left + b.width / 2), cy = (a.top + a.height / 2) - (b.top + b.height / 2);
    const x = Math.max(0, (b.width - a.width / s) / 2), y = Math.max(0, (b.height - a.height / s) / 2);
    return { transform: `translate(${cx}px,${cy}px) scale(${s})`, clipPath: `inset(${y}px ${x}px ${y}px ${x}px)` };
  };
  const visible = (r) => r && r.width > 4 && r.bottom > 0 && r.top < innerHeight && r.right > 0 && r.left < innerWidth;
  const end = { transform: 'none', clipPath: 'inset(0px 0px 0px 0px)' };

  LightboxFx.zoom = {
    open({ img, from }) {
      const a = from?.getBoundingClientRect(), b = img.getBoundingClientRect();
      if (!visible(a) || !b.width) { img.classList.add('is-in'); return new Promise(r => setTimeout(() => { img.classList.remove('is-in'); r(); }, 550)); }
      return img.animate([frame(a, b), end], { duration: 650, easing: 'cubic-bezier(.22,1,.36,1)' }).finished.catch(() => {});
    },
    close({ img, from }) {
      const a = from?.getBoundingClientRect(), b = img.getBoundingClientRect();
      if (!visible(a) || !b.width) return null;
      return img.animate([end, frame(a, b)], { duration: 480, easing: 'cubic-bezier(.5,0,.2,1)', fill: 'forwards' }).finished.catch(() => {});
    },
  };
})();
