/* Cursor «Estela»: dibuja en un canvas la trayectoria reciente del ratón con una cola que se desvanece. */
(() => {
  const c = document.querySelector('.cur--estela'); if (!c || !matchMedia('(pointer:fine)').matches) return;
  if (matchMedia('(prefers-reduced-motion:reduce)').matches) return;
  const cv = document.createElement('canvas'); cv.className = 'cur-trail'; cv.setAttribute('aria-hidden', 'true'); document.body.appendChild(cv);
  const g = cv.getContext('2d'); let dpr = 1, W = 0, H = 0, pts = [], raf = 0;
  const colour = getComputedStyle(c).getPropertyValue('--cur-c').trim() || '#d8b36a';
  const len = Math.max(1, Math.min(10, +(c.dataset.length || 5))), life = 140 + len * 70;      // ms que dura cada punto de la cola
  const size = () => { dpr = Math.min(2, devicePixelRatio || 1); W = innerWidth; H = innerHeight; cv.width = W * dpr; cv.height = H * dpr; g.setTransform(dpr, 0, 0, dpr, 0, 0); };
  size(); addEventListener('resize', size);
  addEventListener('pointermove', (e) => { if (e.pointerType === 'touch') return; pts.push({ x: e.clientX, y: e.clientY, t: performance.now() }); if (!raf) raf = requestAnimationFrame(draw); }, { passive: true });
  function draw(now) {
    g.clearRect(0, 0, W, H);
    pts = pts.filter(p => now - p.t < life);
    g.lineCap = 'round'; g.lineJoin = 'round'; g.strokeStyle = colour; g.shadowColor = colour; g.shadowBlur = 10;
    for (let i = 1; i < pts.length; i++) {
      const a = pts[i - 1], b = pts[i], k = 1 - (now - b.t) / life;
      g.globalAlpha = Math.max(0, k * .75); g.lineWidth = 1 + 4.5 * k;
      g.beginPath(); g.moveTo(a.x, a.y); g.lineTo(b.x, b.y); g.stroke();
    }
    raf = pts.length > 1 ? requestAnimationFrame(draw) : 0;
    if (!raf) g.clearRect(0, 0, W, H);
  }
})();
