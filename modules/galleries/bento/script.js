/* Galería "Bento": la foto pulsada se expande al centro y vuelve a su sitio (técnica FLIP con la Web Animations API).
   Sin librerías ni compilación. El clic normal lo gestiona este módulo; el botón "ampliar" deja pasar al lightbox de la galería. */
(() => {
  const root = document.querySelector('.gb');
  if (!root) return;
  const grid = root.querySelector('.gb-grid');
  const items = [...grid.querySelectorAll('.gb-item')];
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const EASE = 'cubic-bezier(.16,1,.3,1)', DUR = reduce ? 0 : 620;
  let ex = null, bd = null, cur = -1, busy = false, allow = false, lastFocus = null;

  /* El lightbox escucha cada enlace; en fase de captura se corta ese clic salvo cuando lo pide el botón "ampliar". */
  grid.addEventListener('click', e => {
    const a = e.target.closest('.gb-item'); if (!a) return;
    if (allow) return;
    e.preventDefault(); e.stopPropagation();
    open(items.indexOf(a));
  }, true);

  const rectOf = el => { const r = el.getBoundingClientRect(); return { left: r.left, top: r.top, width: r.width, height: r.height }; };
  /* Tamaño final: la foto entera (su proporción) dentro del 88 % × 82 % de la ventana. */
  const target = i => {
    const a = items[i], iw = +a.dataset.w, ih = +a.dataset.h;
    const ar = iw > 0 && ih > 0 ? iw / ih : 1.5;
    const vw = innerWidth, vh = innerHeight, mob = vw < 700;
    const maxW = Math.min(vw * (mob ? .94 : .88), 1200), maxH = vh * (mob ? .8 : .84);
    let w = Math.min(maxW, maxH * ar), h = w / ar;
    return { left: (vw - w) / 2, top: (vh - h) / 2, width: w, height: h };
  };
  const css = r => `left:${r.left}px;top:${r.top}px;width:${r.width}px;height:${r.height}px`;
  const place = (el, r) => { el.style.left = r.left + 'px'; el.style.top = r.top + 'px'; el.style.width = r.width + 'px'; el.style.height = r.height + 'px'; };
  const fly = (el, from, to, dur) => {
    place(el, to);
    if (!dur) return Promise.resolve();
    const an = el.animate([
      { left: from.left + 'px', top: from.top + 'px', width: from.width + 'px', height: from.height + 'px' },
      { left: to.left + 'px', top: to.top + 'px', width: to.width + 'px', height: to.height + 'px' }], { duration: dur, easing: EASE });
    return an.finished.catch(() => {});
  };

  const fill = i => {
    const a = items[i], img = ex.querySelector('img');
    ex.querySelector('.gb-ttl').textContent = a.dataset.title || '';
    ex.querySelector('.gb-dsc').textContent = a.dataset.desc || '';
    const full = a.getAttribute('href');
    if (img.dataset.src !== full) {
      img.dataset.src = full;
      const pre = new Image(); pre.onload = () => { if (img.dataset.src === full) img.src = full; }; pre.src = full;   // miniatura al instante y, al cargar, la grande
    }
  };

  const open = async i => {
    if (busy || ex) return; busy = true; cur = i; lastFocus = document.activeElement;
    const a = items[i], from = rectOf(a);
    bd = document.createElement('div'); bd.className = 'gb-bd'; bd.addEventListener('click', close);
    ex = document.createElement('figure'); ex.className = 'gb-ex'; ex.setAttribute('role', 'dialog'); ex.setAttribute('aria-modal', 'true'); ex.setAttribute('aria-label', a.dataset.title || 'Foto');
    const thumb = a.querySelector('img').getAttribute('src');
    ex.innerHTML = `<img src="${thumb}" alt="" draggable="false"><div class="gb-ctl"><button type="button" class="gb-b gb-zoom" aria-label="Ver a pantalla completa">⤢</button><button type="button" class="gb-b gb-x" aria-label="Cerrar">✕</button></div>`
      + (items.length > 1 ? '<button type="button" class="gb-b gb-nav gb-prev" aria-label="Anterior">←</button><button type="button" class="gb-b gb-nav gb-next" aria-label="Siguiente">→</button>' : '')
      + '<figcaption class="gb-info"><h2 class="gb-ttl"></h2><p class="gb-dsc"></p></figcaption>';
    ex.style.cssText = css(from);
    document.body.append(bd, ex);
    document.body.style.overflow = 'hidden';
    ex.querySelector('.gb-x').onclick = close;
    ex.querySelector('.gb-zoom').onclick = zoom;
    ex.querySelector('.gb-prev')?.addEventListener('click', () => go(-1));
    ex.querySelector('.gb-next')?.addEventListener('click', () => go(1));
    fill(i);
    a.classList.add('is-open');
    requestAnimationFrame(() => bd.classList.add('is-on'));
    await fly(ex, from, target(i), DUR);
    ex.classList.add('is-info'); ex.querySelector('.gb-x').focus({ preventScroll: true });
    busy = false;
  };

  const close = async () => {
    if (busy || !ex) return; busy = true;
    ex.classList.remove('is-info'); bd.classList.remove('is-on');
    const a = items[cur], r = rectOf(a), vis = r.bottom !== 0 && r.top < innerHeight && r.top + r.height > 0;
    if (vis) await fly(ex, rectOf(ex), r, DUR);
    else if (DUR) await ex.animate([{ opacity: 1 }, { opacity: 0 }], { duration: 260 }).finished.catch(() => {});
    items.forEach(x => x.classList.remove('is-open'));
    ex.remove(); bd.remove(); ex = bd = null;
    document.body.style.overflow = ''; busy = false;
    (lastFocus && lastFocus.focus) && lastFocus.focus({ preventScroll: true });
  };

  /* Anterior / siguiente sin cerrar: el marco se reajusta a la proporción de la nueva foto. */
  const go = async d => {
    if (busy || !ex) return; busy = true;
    const prev = cur; cur = (cur + d + items.length) % items.length;
    items[prev].classList.remove('is-open'); items[cur].classList.add('is-open');
    items[cur].scrollIntoView({ block: 'nearest', behavior: 'auto' });
    ex.classList.remove('is-info');
    const img = ex.querySelector('img'); img.style.opacity = 0;
    await new Promise(r => setTimeout(r, reduce ? 0 : 180));
    img.dataset.src = ''; img.src = items[cur].querySelector('img').getAttribute('src'); fill(cur);
    ex.setAttribute('aria-label', items[cur].dataset.title || 'Foto');
    img.style.opacity = 1;
    await fly(ex, rectOf(ex), target(cur), reduce ? 0 : 480);
    ex.classList.add('is-info'); busy = false;
  };

  /* Pantalla completa: se cierra la vista y se abre el lightbox de la galería con esa foto. */
  const zoom = async () => {
    const a = items[cur]; await close();
    allow = true; a.click(); allow = false;
  };

  addEventListener('keydown', e => {
    if (!ex) return;
    if (e.key === 'Escape') { e.preventDefault(); close(); }
    else if (e.key === 'ArrowRight') { e.preventDefault(); go(1); }
    else if (e.key === 'ArrowLeft') { e.preventDefault(); go(-1); }
    else if (e.key === 'Tab') { const f = [...ex.querySelectorAll('button')]; if (!f.length) return; const i = f.indexOf(document.activeElement);
      if (e.shiftKey && i <= 0) { e.preventDefault(); f[f.length - 1].focus(); } else if (!e.shiftKey && i === f.length - 1) { e.preventDefault(); f[0].focus(); } }
  });
  addEventListener('resize', () => { if (ex && !busy) place(ex, target(cur)); });
})();
