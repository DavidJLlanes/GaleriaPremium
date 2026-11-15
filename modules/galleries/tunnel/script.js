(() => {
  const root = document.querySelector('.gt');
  if (!root) return;
  const stage = root.querySelector('.gt-stage'), world = root.querySelector('.gt-world');
  const items = [...world.querySelectorAll('.gt-item')];
  const cnt = root.querySelector('.gt-count b'), ttl = root.querySelector('.gt-title');
  const n = items.length;
  const GAP = 520, PERSP = 1000, GOLD = 2.399963;
  let cam = 0, target = 0, raf = 0, cur = -1, W = 0, H = 0;

  const layout = () => {
    W = stage.clientWidth; H = stage.clientHeight;
    const m = Math.min(W, H), R = m * (W < 700 ? .34 : .42);
    const iw = Math.max(170, Math.min(m * (W < 700 ? .5 : .36), 440));
    items.forEach((el, i) => {
      const a = i * GOLD, ar = parseFloat(el.style.getPropertyValue('--ar')) || 1.25;
      const w = ar < 1 ? iw * .82 : iw * 1.1;           // las verticales algo más estrechas
      const x = Math.cos(a) * R * (W < 700 ? .6 : 1.35), y = Math.sin(a) * R * .8;
      el.style.setProperty('--w', w + 'px');
      el.dataset.t = `translate3d(${x.toFixed(1)}px,${y.toFixed(1)}px,${-i * GAP}px) translate(-50%,-50%) rotateY(${(-Math.cos(a) * 12).toFixed(1)}deg) rotateX(${(Math.sin(a) * 8).toFixed(1)}deg)`;
      el.style.transform = el.dataset.t;
    });
    onScroll(); render(true);
  };

  const smooth = (a, b, v) => { const t = Math.min(1, Math.max(0, (v - a) / (b - a))); return t * t * (3 - 2 * t); };

  const render = (force) => {
    world.style.transform = `translateZ(${cam.toFixed(1)}px)`;
    let nearest = Math.round(cam / GAP);
    nearest = Math.max(0, Math.min(n - 1, nearest));
    for (let i = 0; i < n; i++) {
      const d = cam - i * GAP;                          // >0: ya pasó por delante de la cámara
      const el = items[i];
      const inn = smooth(-GAP * 4.2, -GAP * 2.2, d), out = 1 - smooth(PERSP * .25, PERSP * .7, d);
      const o = inn * out;
      if (o < .01 || d > PERSP * .75) { if (el.style.visibility !== 'hidden') el.style.visibility = 'hidden'; el.style.opacity = 0; continue; }
      el.style.visibility = 'visible'; el.style.opacity = o.toFixed(3);
      el.style.pointerEvents = o > .7 ? 'auto' : 'none';
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

  addEventListener('scroll', onScroll, { passive: true });
  addEventListener('resize', layout);
  layout();
})();
