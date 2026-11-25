/* Galería "Abanico": cartas en abanico; la central de frente, las vecinas inclinadas y atenuadas.
   Pulsar la central deja pasar el clic al lightbox (foto entera); pulsar otra carta la trae al centro. Sin librerías ni compilación. */
(() => {
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  document.querySelectorAll('.gf').forEach(root => {
    const row = root.querySelector('.gf-row'), stage = root.querySelector('.gf-stage');
    const cards = [...row.querySelectorAll('.gf-card')], n = cards.length;
    const dots = [...root.querySelectorAll('.gf-dots button')], num = root.querySelector('.gf-num b'), cap = root.querySelector('.gf-cap');
    const prev = root.querySelector('.gf-prev'), next = root.querySelector('.gf-next');
    const ratio = parseFloat(root.dataset.ratio) || .72, spread = parseFloat(root.dataset.spread) || .64, tilt = parseFloat(root.dataset.tilt) || 8.5;
    const loop = root.dataset.loop === '1' && n > 2;
    let active = 0, hover = false, moved = false, last = new Array(n).fill(0), geo = {};

    const fit = () => {
      const W = stage.clientWidth, H = stage.clientHeight;
      let h = H * .8, w = h * ratio;
      const maxW = W * (W < 640 ? .6 : .34);
      if (w > maxW) { w = maxW; h = w / ratio; }
      geo = { W, w, h };
      root.style.setProperty('--gf-w', w + 'px'); root.style.setProperty('--gf-h', h + 'px'); root.style.setProperty('--gf-r', Math.max(14, w * .1) + 'px');
      paint(true);
    };
    const off = i => {                        // posición relativa con signo
      let d = i - active;
      if (loop) { d = ((d % n) + n) % n; if (d > n / 2) d -= n; }
      return d;
    };
    const VIS = () => geo.W < 640 ? 2 : 3;
    function paint(instant) {
      const { w, h } = geo, vis = VIS();
      cards.forEach((c, i) => {
        const d = off(i), a = Math.abs(d), s = Math.sign(d);
        const x = s * w * spread * (a - (a > 1 ? (a - 1) * .1 : 0)), y = a * a * h * .028 + a * h * .02;
        const hid = a > vis;
        if (Math.abs(d - last[i]) > 1.5 || instant) { c.classList.add('is-jump'); }   // el salto del final al principio no cruza la pantalla
        c.style.setProperty('--x', x.toFixed(1) + 'px'); c.style.setProperty('--y', y.toFixed(1) + 'px');
        c.style.setProperty('--r', (s * Math.min(a, 3) * tilt).toFixed(2) + 'deg'); c.style.setProperty('--s', (1 - Math.min(a, 3) * .05).toFixed(3));
        c.style.setProperty('--o', hid ? 0 : 1); c.style.setProperty('--z', 100 - Math.round(a * 10)); c.style.setProperty('--sh', Math.min(a * .22, .7).toFixed(2));
        c.classList.toggle('is-off', hid); c.classList.toggle('is-now', d === 0);
        c.tabIndex = d === 0 ? 0 : -1;
        if (c.classList.contains('is-jump')) { void c.offsetWidth; c.classList.remove('is-jump'); }
        last[i] = d;
      });
      dots.forEach((b, k) => { b.classList.toggle('is-on', k === active); if (k === active) b.setAttribute('aria-current', 'true'); else b.removeAttribute('aria-current'); });
      if (num) num.textContent = String(active + 1).padStart(2, '0');
      if (prev) prev.disabled = !loop && active === 0; if (next) next.disabled = !loop && active === n - 1;
      if (cap) { const t = cards[active].dataset.title || ''; if (cap.textContent !== t) { cap.classList.add('is-sw'); setTimeout(() => { cap.textContent = t; cap.classList.remove('is-sw'); }, reduce ? 0 : 160); } }
    }
    const go = i => { i = loop ? ((i % n) + n) % n : Math.max(0, Math.min(n - 1, i)); if (i === active) return; active = i; paint(); };

    prev?.addEventListener('click', () => go(active - 1)); next?.addEventListener('click', () => go(active + 1));
    dots.forEach(b => b.addEventListener('click', () => go(+b.dataset.i)));

    /* Pulsar una carta lateral la trae al centro; la central deja pasar el clic al lightbox. */
    row.addEventListener('click', e => {
      const c = e.target.closest('.gf-card'); if (!c) return;
      const i = cards.indexOf(c);
      if (moved || i !== active) { e.preventDefault(); e.stopPropagation(); if (!moved) go(i); }
    }, true);

    /* Deslizar. */
    let sx = null, sy = 0;
    stage.addEventListener('pointerdown', e => { sx = e.clientX; sy = e.clientY; moved = false; });
    stage.addEventListener('pointermove', e => { if (sx !== null && Math.abs(e.clientX - sx) > 8) moved = true; });
    stage.addEventListener('pointerup', e => {
      if (sx === null) return; const dx = e.clientX - sx, dy = e.clientY - sy; sx = null;
      if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy)) go(active + (dx < 0 ? 1 : -1));
      setTimeout(() => moved = false, 0);
    });
    stage.addEventListener('pointercancel', () => { sx = null; moved = false; });

    root.addEventListener('mouseenter', () => hover = true); root.addEventListener('mouseleave', () => hover = false);
    addEventListener('keydown', e => {
      if (window.LightboxApi?.open) return;
      if (!(hover || root.contains(document.activeElement))) return;
      if (e.key === 'ArrowRight') { e.preventDefault(); go(active + 1); } else if (e.key === 'ArrowLeft') { e.preventDefault(); go(active - 1); }
    });
    new ResizeObserver(fit).observe(stage);
    if (cap) cap.textContent = cards[0].dataset.title || '';
    fit();
  });
})();
