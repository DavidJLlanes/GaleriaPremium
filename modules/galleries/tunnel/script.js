(() => {
  const root = document.querySelector('.gt');
  if (!root) return;
  const stage = root.querySelector('.gt-stage'), world = root.querySelector('.gt-world');
  const items = [...world.querySelectorAll('.gt-item')];
  const cnt = root.querySelector('.gt-count b'), ttl = root.querySelector('.gt-title');
  const n = items.length;
  const GAP = 520;
  let cam = 0, target = 0, raf = 0, cur = -1, W = 0, H = 0;

  /* Cada foto cabe SIEMPRE en el encuadre (ancho y alto máximos) y se desplaza poco del centro:
     la actual queda casi centrada y las siguientes asoman detrás, más pequeñas y tenues. */
  const layout = () => {
    W = stage.clientWidth; H = stage.clientHeight;
    const mob = W < 700, hud = mob ? 96 : 70;
    const maxW = W * (mob ? .84 : .56), maxH = (H - hud - 60) * (mob ? .82 : .86);
    items.forEach((el, i) => {
      const ar = parseFloat(el.style.getPropertyValue('--ar')) || 1.25;
      const w = Math.min(maxW, maxH * ar);                       // nunca más grande que el encuadre
      const side = i % 2 ? 1 : -1, k = ((i * 37) % 10) / 10;     // reparto estable (no aleatorio)
      const x = side * (W - w) * (mob ? .16 : .2) * (.5 + k * .6);
      const y = (((i * 53) % 7) - 3) * H * .012 - hud * .25;
      el.style.setProperty('--w', w.toFixed(1) + 'px');
      el.dataset.t = `translate3d(${x.toFixed(1)}px,${y.toFixed(1)}px,${-i * GAP}px) translate(-50%,-50%) rotateY(${(-side * (4 + k * 4)).toFixed(1)}deg) rotateZ(${(side * (k * 2.4 - .6)).toFixed(1)}deg)`;
      el.style.transform = el.dataset.t;
    });
    onScroll(); render(true);
  };

  const smooth = (a, b, v) => { const t = Math.min(1, Math.max(0, (v - a) / (b - a))); return t * t * (3 - 2 * t); };

  const render = (force) => {
    world.style.transform = `translateZ(${cam.toFixed(1)}px)`;
    const nearest = Math.max(0, Math.min(n - 1, Math.round(cam / GAP)));
    for (let i = 0; i < n; i++) {
      const d = cam - i * GAP;                          // d<0: aún por delante · d>0: ya pasó la cámara
      const el = items[i];
      const inn = smooth(-GAP * 2.6, -GAP * 0.9, d);    // asoman de lejos (se ven ~2 detrás de la actual)
      const out = 1 - smooth(GAP * .12, GAP * .6, d);   // al pasar se desvanecen antes de crecer demasiado
      const o = inn * out;
      if (o < .01) { if (el.style.visibility !== 'hidden') el.style.visibility = 'hidden'; el.style.opacity = 0; continue; }
      el.style.visibility = 'visible'; el.style.opacity = o.toFixed(3);
      el.style.filter = d < -GAP * .5 ? `brightness(${(.55 + .45 * smooth(-GAP * 2.6, -GAP * .5, d)).toFixed(2)})` : '';
    }
    if (nearest !== cur || force) {
      cur = nearest; cnt.textContent = String(nearest + 1).padStart(2, '0');
      ttl.style.opacity = 0; setTimeout(() => { ttl.textContent = items[nearest].dataset.title || ''; ttl.style.opacity = 1; }, 160);
    }
  };

  const onScroll = () => {
    const top = root.getBoundingClientRect().top;
    const scrolled = Math.min(Math.max(-top, 0), Math.max(0, root.offsetHeight - H));
    target = Math.min(scrolled, (n - 1) * GAP);
    if (scrolled > 20) root.classList.add('is-moved');
    if (!raf) raf = requestAnimationFrame(tick);
  };

  const tick = () => {
    cam += (target - cam) * .09;
    if (Math.abs(target - cam) < .3) cam = target;
    render(false);
    raf = cam === target ? 0 : requestAnimationFrame(tick);
  };

  // El hit-testing de elementos con transformaciones 3D no es fiable: se detecta el clic sobre la foto ACTIVA por su posición en pantalla.
  const hit = (e) => {
    const el = items[cur]; if (!el || +el.style.opacity < .6) return false;
    const r = el.getBoundingClientRect();
    return e.clientX >= r.left && e.clientX <= r.right && e.clientY >= r.top && e.clientY <= r.bottom;
  };
  stage.addEventListener('click', e => { if (hit(e)) items[cur].click(); });
  stage.addEventListener('mousemove', e => { stage.style.cursor = hit(e) ? 'zoom-in' : ''; });

  addEventListener('scroll', onScroll, { passive: true });
  addEventListener('resize', layout);
  layout();
})();
