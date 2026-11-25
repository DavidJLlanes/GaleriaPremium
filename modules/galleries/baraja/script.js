/* Galería "Baraja": cartas de pie sobre el suelo; la delantera se vuelca sobre su borde inferior y la pila avanza.
   Una sola cifra (la posición de la baraja) lo gobierna todo; se obtiene del scroll de la página (sin secuestrar la rueda).
   Se recicla un pequeño conjunto de elementos: el hueco j muestra la carta virtual k ≡ j (mod pool) y solo cambia de foto
   mientras es invisible (bajo la pila del suelo o más allá del fondo). Sin librerías ni compilación. */
(() => {
  const clamp01 = v => (v < 0 ? 0 : v > 1 ? 1 : v);
  const wrap = (k, n) => (n ? ((k % n) + n) % n : 0);

  /* Ángulo de la bisagra (grados, rotateX): 0 = de pie, -90 = plana hacia el espectador.
     Cae acelerando (gravedad), rebota una vez sobre el suelo y se asienta. */
  const fallAngle = t => {
    const u = clamp01(t), land = .78;
    if (u <= land) { const k = u / land; return -90 * k * k * k; }
    const k = (u - land) / (1 - land);
    return -90 + 6 * Math.sin(Math.PI * k) * (1 - k);
  };
  /* Cuánto ha avanzado la pila (en huecos) cuando la baraja está en `pos`. */
  const deckAdvance = pos => {
    const i = Math.floor(pos), f = pos - i, k = clamp01((f - .28) / .6);
    return i + k * k * (3 - 2 * k);
  };
  const cardPose = (k, pos, depth) => {
    const t = pos - k;
    const lift = (k - Math.floor(pos) + 3) * .6;                  // las del suelo, a un pelo unas de otras (la más nueva, arriba)
    if (t >= 1) return { angle: -90, z: 0, lift, opacity: t < 3 ? 1 : 0, flat: true };
    if (t > 0) return { angle: fallAngle(t), z: 0, lift, opacity: 1, flat: false };
    const rel = k - deckAdvance(pos);
    return { angle: 0, z: -Math.max(rel, 0), lift: 0, opacity: clamp01(depth + 1 - rel), flat: false };
  };

  const init = root => {
    const stage = root.querySelector('.gv-stage'), scene = root.querySelector('.gv-scene');
    let items = [];
    try { items = JSON.parse(root.querySelector('.gv-data').textContent) || []; } catch (e) {}
    const n = items.length;
    if (!n) return;
    const anchors = [...root.querySelectorAll('.gv-src a')];
    const cnt = root.querySelector('.gv-count b'), ttl = root.querySelector('.gv-title');
    const ratio = parseFloat(root.dataset.ratio) || 1.5, depth = parseInt(root.dataset.depth, 10) || 5, per = parseFloat(root.dataset.per) || 1;
    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
    const pool = depth + 5;                                       // dos en el suelo, la que cae, la delantera, `depth` detrás y una que asoma

    const els = [], imgs = [], slotK = [];
    for (let j = 0; j < pool; j++) {
      const c = document.createElement('div'); c.className = 'gv-card';
      const im = document.createElement('img'); im.alt = ''; im.draggable = false; im.decoding = 'async';
      c.appendChild(im); scene.appendChild(c); els.push(c); imgs.push(im); slotK.push(null);
    }

    let pos = 0, target = 0, raf = 0, last = 0, idle = 0, cur = -1, H = 0, gap = 44, ch = 200, perPx = 300;

    const setImg = (im, it) => {
      im.src = it.src; im.alt = it.alt || '';
      if (it.c) { im.className = ''; im.style.cssText = `width:${it.c[0]}%;left:-${it.c[1]}%;top:-${it.c[2]}%`; }
      else { im.className = 'is-cover'; im.style.cssText = ''; }
    };

    const paint = () => {
      const base = Math.floor(pos) - 2;
      for (let j = 0; j < pool; j++) {
        const el = els[j];
        const k = base + wrap(j - base, pool);
        if (slotK[j] !== k) {
          slotK[j] = k;
          if (k >= 0 && k < n) setImg(imgs[j], items[k]);
        }
        if (k < 0 || k >= n) { el.style.visibility = 'hidden'; continue; }   // la baraja termina en la primera y la última foto
        const p = cardPose(k, pos, depth);
        el.style.transform = `translate3d(0,${-p.lift}px,${p.z * gap}px) rotateX(${p.angle}deg)`;
        el.style.opacity = p.opacity;
        el.style.visibility = p.opacity > 0 ? 'visible' : 'hidden';
        el.style.setProperty('--gv-shade', p.flat ? .06 : (-p.angle / 90) * .06);   // la luz cae desde arriba: se oscurece al inclinarse
      }
      const c = Math.max(0, Math.min(n - 1, Math.round(pos)));
      if (c !== cur) {
        cur = c;
        if (cnt) cnt.textContent = String(c + 1).padStart(2, '0');
        if (ttl) { ttl.style.opacity = 0; setTimeout(() => { if (cur === c) { ttl.textContent = items[c].alt || ''; ttl.style.opacity = 1; } }, 150); }
      }
    };

    /* Encaje: la carta, la pila de arriba y la carta plana de abajo caben dentro del encuadre. */
    const fit = () => {
      const W0 = stage.clientWidth; H = stage.clientHeight;
      const wFrac = W0 < 420 ? .76 : .655;
      const h = Math.max(60, Math.min((W0 * wFrac) / ratio, H * .40, 620)), w = h * ratio, top = H / 2 - .62 * h;
      ch = h; gap = .24 * h;
      scene.style.perspective = `${6 * h}px`;
      scene.style.perspectiveOrigin = `50% ${top - 1.66 * h}px`;
      stage.style.setProperty('--gv-w', w + 'px'); stage.style.setProperty('--gv-h', h + 'px'); stage.style.setProperty('--gv-top', top + 'px');
      perPx = per * .38 * H;
      readScroll(); pos = target; paint();
    };

    const tick = now => {
      const dt = Math.min(.05, (now - last) / 1000 || .016); last = now;
      pos += (target - pos) * (1 - Math.exp(-dt * 9));
      if (Math.abs(target - pos) < 2e-3) { pos = target; raf = 0; } else raf = requestAnimationFrame(tick);
      paint();
    };
    const go = () => {
      if (reduce) { pos = target; paint(); return; }
      if (!raf) { last = performance.now(); raf = requestAnimationFrame(tick); }
    };

    let scrolled = 0;
    function readScroll() {
      const top = root.getBoundingClientRect().top;
      scrolled = Math.min(Math.max(-top, 0), Math.max(0, root.offsetHeight - H)) / perPx;
      scrolled = Math.min(scrolled, n - 1);
      target = scrolled;
    }
    const onScroll = () => {
      readScroll();
      if (scrolled > .05) root.classList.add('is-moved');
      clearTimeout(idle);
      go();
      idle = setTimeout(() => { target = Math.round(scrolled); go(); }, reduce ? 0 : 170);   // al parar, nunca queda una carta a medio caer
      if (reduce) { target = Math.round(scrolled); go(); }
    };

    /* Botones y teclas: llevan el scroll de la página a la carta siguiente/anterior. */
    const step = dir => {
      const k = Math.max(0, Math.min(n - 1, Math.round(scrolled) + dir));
      const y = root.getBoundingClientRect().top + scrollY + k * perPx;
      scrollTo({ top: y, behavior: reduce ? 'auto' : 'smooth' });
    };
    root.querySelector('.gv-prev')?.addEventListener('click', () => step(-1));
    root.querySelector('.gv-next')?.addEventListener('click', () => step(1));
    const engaged = () => { const r = stage.getBoundingClientRect(); return r.top < H * .5 && r.bottom > H * .5 && !(window.LightboxApi && window.LightboxApi.open); };
    addEventListener('keydown', e => {
      if (e.altKey || e.ctrlKey || e.metaKey || !engaged()) return;
      if (e.key === 'ArrowRight') { e.preventDefault(); step(1); }
      else if (e.key === 'ArrowLeft') { e.preventDefault(); step(-1); }
      else if (e.key === 'Enter' && document.activeElement === stage) { e.preventDefault(); openFront(); }
    });

    /* Clic en la carta delantera → lightbox. (El hit-test de elementos con 3D no es fiable: se mira su caja en pantalla.) */
    const front = () => { const k = Math.max(0, Math.min(n - 1, Math.round(pos))); return slotK.indexOf(k); };
    const openFront = () => { const k = Math.max(0, Math.min(n - 1, Math.round(pos))); anchors[k]?.click(); };
    const hit = e => {
      const j = front(); if (j < 0 || Math.abs(pos - Math.round(pos)) > .25) return false;
      const r = els[j].getBoundingClientRect();
      return e.clientX >= r.left && e.clientX <= r.right && e.clientY >= r.top && e.clientY <= r.bottom;
    };
    stage.addEventListener('click', e => { if (e.target.closest('.gv-b')) return; if (hit(e)) openFront(); });
    stage.addEventListener('mousemove', e => { stage.style.cursor = hit(e) ? 'zoom-in' : ''; });

    stage.classList.add('is-ready');
    root.style.setProperty('--gv-ar', ratio);
    addEventListener('scroll', onScroll, { passive: true });
    addEventListener('resize', fit);
    fit();
  };

  document.querySelectorAll('.gv').forEach(init);
})();
