/* Motor de los sliders. Cada [data-slider] se controla igual (pase automático, flechas, puntos, deslizar, teclado) y delega el movimiento en un
   «modo» (window.SliderModes[nombre] = { init(s), show(s, to, from, dir) }). Modo por defecto: «stack» (transiciones de modules/slider-fx). */
(() => {
  const modes = (window.SliderModes = window.SliderModes || {});
  const calm = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const cssMs = (el, v, d) => { const t = getComputedStyle(el).getPropertyValue(v).trim(); const n = parseFloat(t); return isNaN(n) ? d : (/ms$/.test(t) ? n : /s$/.test(t) ? n * 1000 : n); };

  modes.stack = {
    init(s) { s.slides[0]?.classList.add('is-active'); },
    show(s, to, from, dir) {
      const a = s.slides[from], b = s.slides[to], t = cssMs(s.root, '--sl-t', 900);
      s.root.dataset.dir = dir > 0 ? 'next' : 'prev';
      b.classList.remove('is-leaving'); b.classList.add('is-enter'); void b.offsetWidth;
      b.classList.remove('is-enter'); b.classList.add('is-active');
      a.classList.remove('is-active'); a.classList.add('is-leaving');
      clearTimeout(a._t); a._t = setTimeout(() => a.classList.remove('is-leaving'), t + 80);
      s.lock = t * .45;
    },
  };

  const init = (root) => {
    const s = { root, slides: [...root.querySelectorAll('.sl-slide')], i: 0, lock: 0, busy: false, moved: false, visible: true, hover: false };
    s.n = s.slides.length; if (!s.n) return;
    s.loop = root.dataset.loop !== '0'; s.hoverPause = root.dataset.hover !== '0';
    s.ms = Math.max(1500, (parseFloat(root.dataset.int) || 5) * 1000); s.auto = root.dataset.auto === '1' && !calm && s.n > 1;
    s.mode = modes[root.dataset.mode || 'stack'] || modes.stack;
    root.style.setProperty('--sl-int', s.ms + 'ms');
    const dotsBox = root.querySelector('[data-sl-dots]'), thumbs = [...root.querySelectorAll('[data-sl-thumb]')];
    if (dotsBox) {
      dotsBox.innerHTML = s.slides.map((_, k) => `<button type="button" class="sl-dot" data-sl-dot="${k}" aria-label="Foto ${k + 1}"></button>`).join('');
    }
    const dots = [...root.querySelectorAll('[data-sl-dot]')].concat(thumbs);
    const cur = root.querySelector('[data-sl-cur]'), tot = root.querySelector('[data-sl-tot]');
    if (tot) tot.textContent = String(s.n).padStart(2, '0');
    const prev = root.querySelector('[data-sl-prev]'), next = root.querySelector('[data-sl-next]');

    const ui = () => {
      dots.forEach((d, k) => { d.classList.toggle('is-on', k === s.i); if (k === s.i) d.setAttribute('aria-current', 'true'); else d.removeAttribute('aria-current'); });
      thumbs.forEach((t, k) => { if (k === s.i && t.parentElement) t.parentElement.scrollTo({ left: t.offsetLeft - t.parentElement.clientWidth / 2 + t.clientWidth / 2, behavior: calm ? 'auto' : 'smooth' }); });
      if (cur) cur.textContent = String(s.i + 1).padStart(2, '0');
      if (!s.loop) { if (prev) prev.disabled = s.i === 0; if (next) next.disabled = s.i === s.n - 1; }
      root.dataset.playing = s.auto && s.visible && !(s.hover && s.hoverPause) ? '1' : '0';
    };
    const clear = () => { clearTimeout(s.timer); s.timer = 0; };
    const arm = () => {
      clear(); ui();
      if (!s.auto || !s.visible || (s.hover && s.hoverPause) || document.hidden) return;
      root.style.setProperty('--sl-restart', root.style.getPropertyValue('--sl-restart') === '1' ? '0' : '1');   // reinicia la barra de progreso
      s.timer = setTimeout(() => go(s.i + 1, 1, true), s.ms);
    };
    const go = (to, dir, auto) => {
      if (s.n < 2 || s.busy) return;
      if (to < 0 || to >= s.n) { if (!s.loop) { if (auto) { s.auto = false; } return arm(); } to = (to + s.n) % s.n; }
      if (to === s.i) return;
      const from = s.i; s.i = to; s.busy = true;
      s.mode.show(s, to, from, dir || (to > from ? 1 : -1));
      setTimeout(() => { s.busy = false; }, s.lock || 300);
      arm();
    };
    s.go = go; root._slider = s;
    s.mode.init?.(s); ui();

    prev?.addEventListener('click', () => go(s.i - 1, -1)); next?.addEventListener('click', () => go(s.i + 1, 1));
    root.addEventListener('click', (e) => { const d = e.target.closest('[data-sl-dot]') || e.target.closest('[data-sl-thumb]'); if (d && root.contains(d)) go(+(d.dataset.slDot ?? d.dataset.slThumb), (+(d.dataset.slDot ?? d.dataset.slThumb)) > s.i ? 1 : -1); });
    root.addEventListener('keydown', (e) => { if (e.key === 'ArrowRight') go(s.i + 1, 1); else if (e.key === 'ArrowLeft') go(s.i - 1, -1); });
    root.addEventListener('mouseenter', () => { s.hover = true; arm(); }); root.addEventListener('mouseleave', () => { s.hover = false; arm(); });
    root.addEventListener('focusin', () => { s.hover = true; arm(); }); root.addEventListener('focusout', () => { s.hover = false; arm(); });
    document.addEventListener('visibilitychange', arm);
    if ('IntersectionObserver' in window) new IntersectionObserver((es) => { s.visible = es.some(e => e.isIntersecting); arm(); }, { threshold: .25 }).observe(root);

    // deslizar con el dedo / ratón (horizontal); un arrastre no abre el lightbox
    let x0 = null, y0 = 0;
    root.addEventListener('pointerdown', (e) => { if (e.target.closest('button')) return; x0 = e.clientX; y0 = e.clientY; s.moved = false; });
    root.addEventListener('pointermove', (e) => { if (x0 !== null && Math.abs(e.clientX - x0) > 8) s.moved = true; });
    const up = (e) => {
      if (x0 === null) return; const dx = e.clientX - x0, dy = e.clientY - y0; x0 = null;
      if (Math.abs(dx) > 45 && Math.abs(dx) > Math.abs(dy) * 1.3 && !s.noSwipe) go(s.i + (dx < 0 ? 1 : -1), dx < 0 ? 1 : -1);
      setTimeout(() => { s.moved = false; }, 0);
    };
    root.addEventListener('pointerup', up); root.addEventListener('pointercancel', () => { x0 = null; });
    root.addEventListener('click', (e) => { if (s.moved) { e.preventDefault(); e.stopPropagation(); } }, true);
    arm();
  };

  const start = () => document.querySelectorAll('[data-slider]').forEach(init);
  // los diseños registran su modo en su propio script (todos «defer», antes de DOMContentLoaded): el motor arranca al final
  if (document.readyState === 'complete') start(); else document.addEventListener('DOMContentLoaded', start);
})();
