/* Galería "Círculo": la foto elegida brota de su punto como un círculo que crece hasta cubrirlo todo (clip-path animado con rAF);
   la anterior reaparece como círculo en el centro y rebota hasta su punto. Sin GSAP ni librerías. Pulsar la foto abre el lightbox (foto entera). */
(() => {
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const io3 = t => t < .5 ? 4 * t ** 3 : 1 - (-2 * t + 2) ** 3 / 2, in4 = t => t ** 4, out3 = t => 1 - (1 - t) ** 3;
  const bounce = t => { const n1 = 7.5625, d1 = 2.75; if (t < 1 / d1) return n1 * t * t; if (t < 2 / d1) return n1 * (t -= 1.5 / d1) * t + .75; if (t < 2.5 / d1) return n1 * (t -= 2.25 / d1) * t + .9375; return n1 * (t -= 2.625 / d1) * t + .984375; };
  const lerp = (a, b, p) => a + (b - a) * p;

  document.querySelectorAll('.gc').forEach(root => {
    const frame = root.querySelector('.gc-frame'), L = [...frame.querySelectorAll('.gc-ly')], n = L.length;
    const tabs = [...frame.querySelectorAll('.gc-tabs button')], cap = root.querySelector('.gc-cap');
    const stage = root.querySelector('.gc-stage'), AUTO = reduce ? 0 : +root.dataset.auto;
    let cur = 0, busy = false, queued = null, hover = false, moved = false, W = 0, H = 0, r = 7, dots = [];
    root.classList.add('is-js');

    const geo = () => {
      W = frame.clientWidth; H = frame.clientHeight;
      const g = 10 / 7;                                                      // separación = 10/7 del radio (proporción original)
      r = Math.max(4, Math.min(Math.min(W, H) * .0175, (W * .9) / (2 * n + (n - 1) * g)));
      const gap = r * g, tot = n * 2 * r + (n - 1) * gap, y = H - 4.3 * r;
      dots = L.map((_, i) => ({ x: W / 2 - tot / 2 + r + i * (2 * r + gap), y }));
      tabs.forEach((b, i) => { const s = (r + 2) * 2; b.style.cssText = `left:${dots[i].x}px;top:${dots[i].y}px;width:${s}px;height:${s}px`; });
    };
    const clip = (l, x, y, rad) => { l.style.clipPath = `circle(${rad}px at ${x}px ${y}px)`; };
    const asDot = i => { clip(L[i], dots[i].x, dots[i].y, r); L[i].style.zIndex = 5; };
    const settle = () => L.forEach((l, i) => { l.getAnimations?.(); if (i === cur) { l.style.clipPath = 'none'; l.style.zIndex = 1; } else if (!l._tk) asDot(i); });

    const tw = (l, dur, ease, fn) => new Promise(res => {
      const tk = {}; l._tk = tk; const t0 = performance.now();
      const f = now => { if (l._tk !== tk) return res(false); const p = Math.min((now - t0) / dur, 1); fn(ease(p), p); if (p < 1) requestAnimationFrame(f); else { l._tk = null; res(true); } };
      requestAnimationFrame(f);
    });
    const far = () => Math.hypot(W, H) / 2 + 4;

    const ui = () => {
      tabs.forEach((b, k) => { b.classList.toggle('is-on', k === cur); k === cur ? b.setAttribute('aria-current', 'true') : b.removeAttribute('aria-current'); });
      L.forEach((l, k) => { l.classList.toggle('is-now', k === cur); l.tabIndex = k === cur ? 0 : -1; });
      if (cap) { const t = L[cur].dataset.title || ''; if (cap.textContent !== t) { cap.classList.add('is-sw'); setTimeout(() => { cap.textContent = t; cap.classList.remove('is-sw'); }, reduce ? 0 : 160); } }
    };

    const go = async i => {
      i = ((i % n) + n) % n; if (i === cur) return;
      if (busy) { queued = i; return; }
      const out = L[cur], inc = L[i], oi = cur; cur = i; ui();
      if (reduce) { settle(); return; }
      busy = true; inc._tk = null; inc.style.zIndex = 6;
      const d = dots[i], cx = W / 2, cy = H / 2;
      await tw(inc, 200, io3, p => clip(inc, lerp(d.x, cx, p), lerp(d.y, cy, p), lerp(r, 7 * r, p)));      // el punto sube al centro
      await tw(inc, 400, in4, p => clip(inc, cx, cy, lerp(7 * r, far(), p)));                                // y se abre hasta cubrir todo
      inc.style.clipPath = 'none'; inc.style.zIndex = 1;
      busy = false;
      /* La anterior vuelve como círculo en el centro y rebota hasta su punto. */
      out.style.opacity = 0; out.style.zIndex = 5; clip(out, cx, cy, 7 * r); void out.offsetWidth; out.style.opacity = 1;
      const o = dots[oi];
      tw(out, 1000, x => x, p => { clip(out, lerp(cx, o.x, out3(p)), lerp(cy, o.y, bounce(p)), lerp(7 * r, r, out3(Math.min(p * 2, 1)))); }).then(ok => { if (ok) asDot(oi); });
      if (queued !== null) { const q = queued; queued = null; go(q); }
    };
    const step = d => go(cur + d);

    root.querySelector('.gc-prev')?.addEventListener('click', () => step(-1)); root.querySelector('.gc-next')?.addEventListener('click', () => step(1));
    tabs.forEach(b => b.addEventListener('click', () => go(+b.dataset.i)));

    /* Deslizar para cambiar; un arrastre no abre el lightbox. */
    let sx = null, sy = 0;
    frame.addEventListener('pointerdown', e => { if (e.target.closest('.gc-tabs button')) return; sx = e.clientX; sy = e.clientY; moved = false; });
    frame.addEventListener('pointermove', e => { if (sx !== null && Math.abs(e.clientX - sx) > 8) moved = true; });
    frame.addEventListener('pointerup', e => { if (sx === null) return; const dx = e.clientX - sx, dy = e.clientY - sy; sx = null; if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy)) step(dx < 0 ? 1 : -1); setTimeout(() => moved = false, 0); });
    frame.addEventListener('pointercancel', () => { sx = null; moved = false; });
    frame.addEventListener('click', e => { if (moved) { e.preventDefault(); e.stopPropagation(); } }, true);

    root.addEventListener('mouseenter', () => hover = true); root.addEventListener('mouseleave', () => hover = false);
    addEventListener('keydown', e => {
      if (window.LightboxApi?.open || !(hover || root.contains(document.activeElement))) return;
      if (e.key === 'ArrowRight') { e.preventDefault(); step(1); } else if (e.key === 'ArrowLeft') { e.preventDefault(); step(-1); }
    });
    if (AUTO && n > 1) setInterval(() => { if (!hover && !document.hidden && !busy && !window.LightboxApi?.open && !root.contains(document.activeElement)) step(1); }, AUTO);

    new ResizeObserver(() => { geo(); settle(); }).observe(frame);
    geo(); settle(); ui();
  });
})();
