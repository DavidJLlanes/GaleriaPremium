/* Lightbox genérico: abre cualquier contenedor [data-lightbox] cuyos <a href="foto"> tengan data-title.
   El efecto (data-fx) se define en CSS por módulo (modules/lightboxes/*); el de zoom añade JS vía window.LightboxFx. */
(() => {
  const lb = document.getElementById('lightbox');
  const boxes = [...document.querySelectorAll('[data-lightbox]')];   // puede haber varios en la portada (un slider cada uno): al pulsar una foto se usa SU contenedor
  if (!lb || !boxes.length) return;

  let items = [...boxes[0].querySelectorAll('a[href]')];
  if (!items.length && boxes.length === 1) return;
  const fx0 = lb.dataset.fx;
  const img = lb.querySelector('.lb-img'), cap = lb.querySelector('.lb-cap'), count = lb.querySelector('.lb-count'), bg = lb.querySelector('.lb-bg');
  const fx = () => (window.LightboxFx || {})[lb.dataset.fx] || {};
  const ms = (v, d) => { const n = parseFloat(getComputedStyle(lb).getPropertyValue(v)); return isNaN(n) ? d : n; };
  const wait = (t) => new Promise(r => setTimeout(r, t));
  let idx = 0, busy = false;

  // Escala la foto para llenar el área útil de la pantalla actual (hasta 3× su tamaño) sin deformarla
  const fit = () => {
    const nw = +img.dataset.nw, nh = +img.dataset.nh; if (!nw) return;
    const vw = window.visualViewport?.width || innerWidth, vh = window.visualViewport?.height || innerHeight;
    const aw = vw * .96, ah = vh - (vw < 700 ? 118 : 100) - (+lb.dataset.extra || 0);   // data-extra: espacio extra que reservan los complementos (miniaturas, datos…)
    const s = Math.max(.05, Math.min(aw / nw, Math.max(80, ah) / nh, 3));
    img.style.width = Math.round(nw * s) + 'px'; img.style.height = Math.round(nh * s) + 'px';
  };
  const setContent = async (i) => {
    const a = items[i], pre = new Image(); pre.src = a.href;
    let src = a.href;
    try { await pre.decode(); } catch {
      const t = thumb(i), mini = t?.tagName === 'IMG' ? t : t?.querySelector('img');
      if (mini?.src) src = mini.src;
    }
    img.dataset.nw = pre.naturalWidth || 1600; img.dataset.nh = pre.naturalHeight || 1067;
    img.src = src; img.alt = a.dataset.title || ''; fit();
    cap.textContent = a.dataset.title || '';
    count.textContent = String(i + 1).padStart(2, '0') + ' / ' + String(items.length).padStart(2, '0');
    bg.style.backgroundImage = `url("${src}")`;
    [i + 1, i - 1].forEach(n => { new Image().src = items[(n + items.length) % items.length].href; });
  };
  const thumb = (i) => items[i].querySelector('[data-lb-thumb]') || items[i].querySelector('img');

  const open = async (i) => {
    if (busy) return; busy = true; idx = i;
    await setContent(i);
    lb.dataset.dir = 'open'; lb.hidden = false; document.body.style.overflow = 'hidden';
    lb.classList.remove('is-closing'); void lb.offsetWidth; lb.classList.add('is-open');
    dispatchEvent(new CustomEvent('lb:open', { detail: { index: i } }));
    const f = fx();
    if (f.open) await f.open({ lb, img, from: thumb(i) });
    else { img.classList.add('is-in'); await wait(ms('--lb-in', 450)); img.classList.remove('is-in'); }
    busy = false;
  };

  const swap = async (to, dir) => {
    if (busy || lb.hidden) return; busy = true;
    lb.dataset.dir = dir > 0 ? 'next' : 'prev';
    img.classList.add('is-out'); await wait(ms('--lb-out', 250));
    idx = (to + items.length) % items.length; await setContent(idx);
    dispatchEvent(new CustomEvent('lb:change', { detail: { index: idx } }));
    img.classList.remove('is-out'); img.classList.add('is-in'); await wait(ms('--lb-in', 450)); img.classList.remove('is-in');
    busy = false;
  };

  const close = async () => {
    if (busy || lb.hidden) return; busy = true;
    const p = fx().close?.({ lb, img, from: thumb(idx) });
    lb.classList.remove('is-open'); lb.classList.add('is-closing');
    await Promise.all([p, wait(ms('--lb-close', 380))]);
    lb.hidden = true; lb.classList.remove('is-closing'); img.getAnimations?.().forEach(a => a.cancel());
    document.body.style.overflow = ''; busy = false;
    dispatchEvent(new Event('lb:close'));
  };

  boxes.forEach(box => {
    const list = [...box.querySelectorAll('a[href]')];
    list.forEach((a, i) => a.addEventListener('click', e => {
      e.preventDefault(); if (busy) return;
      if (items !== list) { items = list; lb.dataset.fx = box.dataset.lbfx || fx0; dispatchEvent(new CustomEvent('lb:source', { detail: { links: list } })); }
      open(i);
    }));
  });
  lb.querySelector('.lb-close').onclick = close;
  lb.querySelector('.lb-prev').onclick = () => swap(idx - 1, -1);
  lb.querySelector('.lb-next').onclick = () => swap(idx + 1, 1);
  lb.addEventListener('click', e => { if (e.target === lb || e.target.classList.contains('lb-fig')) close(); });
  addEventListener('keydown', e => {
    if (lb.hidden) return;
    if (e.key === 'Escape') close();
    if (e.key === 'ArrowRight') swap(idx + 1, 1);
    if (e.key === 'ArrowLeft') swap(idx - 1, -1);
  });
  addEventListener('resize', fit);
  window.visualViewport?.addEventListener('resize', fit);
  // Gesto de deslizar para cambiar de foto: solo horizontal, con un dedo y sin zoom (el zoom/arrastre lo gestiona components/lightbox-extras)
  let x0 = null, y0 = null;
  lb.addEventListener('touchstart', e => { if (e.touches.length > 1 || lb.dataset.zoom === '1') { x0 = null; return; } x0 = e.touches[0].clientX; y0 = e.touches[0].clientY; }, { passive: true });
  lb.addEventListener('touchend', e => {
    if (x0 === null || lb.dataset.zoom === '1') return;
    const dx = e.changedTouches[0].clientX - x0, dy = e.changedTouches[0].clientY - y0;
    if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy) * 1.4) swap(idx + (dx < 0 ? 1 : -1), dx < 0 ? 1 : -1);
    x0 = null;
  });
  window.LightboxApi = { get items() { return items; }, go: (i) => { if (i !== idx) swap(i, i > idx ? 1 : -1); }, close, get index() { return idx; }, get count() { return items.length; }, get open() { return !lb.hidden; } };
})();
