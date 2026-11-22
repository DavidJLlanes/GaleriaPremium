/* Complementos del lightbox (se enganchan por eventos lb:open / lb:change / lb:close y por window.LightboxApi):
   · zoom con doble toque/clic, pellizco y rueda, y arrastre cuando hay zoom
   · deslizar hacia abajo para cerrar (táctil)
   · tira de miniaturas, descripción y datos de cámara (EXIF; el GPS nunca se guarda)
   · resplandor con el color medio de la foto */
(() => {
  const start = () => {
    const lb = document.getElementById('lightbox'), api = window.LightboxApi, raw = document.getElementById('lbxInfo');
    if (!lb || !api) return;
    const info = raw ? JSON.parse(raw.textContent || '{}') : {};
    const img = lb.querySelector('.lb-img'), fig = lb.querySelector('.lb-fig'), bg = lb.querySelector('.lb-bg');
    let links = api.items.slice();
    const key = (a) => decodeURIComponent(a.getAttribute('href').split('/').pop().split('?')[0]);
    lb.classList.add('lbx');

    // — DOM propio —
    const glow = Object.assign(document.createElement('div'), { className: 'lbx-glow' }); bg.after(glow);
    const box = Object.assign(document.createElement('div'), { className: 'lbx-info' });
    const desc = Object.assign(document.createElement('p'), { className: 'lbx-desc' }), exif = Object.assign(document.createElement('p'), { className: 'lbx-exif' });
    const cats = Object.assign(document.createElement('div'), { className: 'pcats pcats--inline' }), infoEl = document.getElementById('lbxInfo');
    cats.style.setProperty('--pc-k', infoEl?.dataset.pck || '1');
    box.append(cats, desc, exif); lb.append(box);
    const strip = Object.assign(document.createElement('div'), { className: 'lbx-strip' });
    let ths = [];
    const buildStrip = () => {   // (re)crea las miniaturas; se repite si se abre la foto de OTRO contenedor (varios sliders en la portada)
      strip.textContent = '';
      ths = links.map((a, i) => {
        const b = Object.assign(document.createElement('button'), { type: 'button', className: 'lbx-th' }); b.setAttribute('aria-label', a.dataset.title || 'Foto ' + (i + 1));
        const im = Object.assign(document.createElement('img'), { src: info[key(a)]?.t || a.href, alt: '', loading: 'lazy', draggable: false }); b.append(im);
        b.addEventListener('click', (e) => { e.stopPropagation(); api.go(i); }); strip.append(b); return b;
      });
      strip.hidden = links.length < 2;
    };
    buildStrip();
    addEventListener('lb:source', (e) => { links = e.detail.links.slice(); buildStrip(); });
    const hint = Object.assign(document.createElement('div'), { className: 'lbx-zoomhint', textContent: 'Doble clic para ampliar' });
    lb.append(strip); lb.append(hint);
    const desktop = () => innerWidth >= 700;
    box.prepend(lb.querySelector('.lb-cap'));      // el pie de foto pasa al bloque inferior (título, descripción y datos van apilados)
    // Reparte el alto: arriba contador/cierre, abajo pie + datos (+ miniaturas en escritorio); la foto se ajusta al hueco que queda
    const reserve = () => {
      const strip3 = links.length > 1, pt = 56, pb = desktop() ? (strip3 ? 156 : 104) : (strip3 ? 190 : 132), ib = desktop() ? (strip3 ? 78 : 24) : 68;
      lb.style.setProperty('--lbx-pt', pt + 'px'); lb.style.setProperty('--lbx-pb', pb + 'px'); lb.style.setProperty('--lbx-ib', ib + 'px');
      lb.dataset.extra = String(pt + pb + 24 - (desktop() ? 100 : 118)); dispatchEvent(new Event('resize'));
    };

    // — Zoom y arrastre —
    let s = 1, tx = 0, ty = 0;
    const MAX = 4;
    const apply = (anim) => {
      lb.classList.toggle('is-animating', !!anim);
      img.style.scale = s === 1 ? '' : String(s); img.style.translate = s === 1 && !tx && !ty ? '' : `${tx}px ${ty}px`;
      lb.dataset.zoom = s > 1.02 ? '1' : '0';
    };
    const clampT = () => {
      const w = img.offsetWidth * s, h = img.offsetHeight * s, mx = Math.max(0, (w - innerWidth) / 2), my = Math.max(0, (h - innerHeight) / 2);
      tx = Math.max(-mx, Math.min(mx, tx)); ty = Math.max(-my, Math.min(my, ty));
    };
    const center = () => { const r = img.getBoundingClientRect(); return { x: r.left + r.width / 2 - tx, y: r.top + r.height / 2 - ty }; };
    const zoomAt = (qx, qy, s2, anim) => {
      s2 = Math.max(1, Math.min(MAX, s2)); const c = center();
      tx = qx - c.x - s2 * (qx - c.x - tx) / s; ty = qy - c.y - s2 * (qy - c.y - ty) / s; s = s2;
      if (s === 1) { tx = 0; ty = 0; } else clampT();
      apply(anim);
    };
    const reset = (anim) => { s = 1; tx = ty = 0; apply(anim); lb.classList.remove('is-drag'); lb.style.transition = ''; lb.style.opacity = ''; };
    let hintShown = false;
    const showHint = () => { if (hintShown || !desktop()) return; hintShown = true; hint.classList.add('is-on'); setTimeout(() => hint.classList.remove('is-on'), 2600); };

    lb.addEventListener('wheel', (e) => {
      if (lb.hidden) return; e.preventDefault();
      zoomAt(e.clientX, e.clientY, s * Math.exp(-e.deltaY * (e.ctrlKey ? .01 : .0018)), false);
    }, { passive: false });
    img.addEventListener('dblclick', (e) => { e.preventDefault(); zoomAt(e.clientX, e.clientY, s > 1.05 ? 1 : 2.4, true); });

    // Punteros: arrastre con zoom, pellizco, doble toque y deslizar hacia abajo para cerrar
    const ptrs = new Map(); let pinch = null, pan = null, dismiss = null, lastTap = { t: 0, x: 0, y: 0 };
    fig.addEventListener('pointerdown', (e) => {
      if (lb.hidden) return;
      ptrs.set(e.pointerId, { x: e.clientX, y: e.clientY });
      if (ptrs.size === 2) { const [a, b] = [...ptrs.values()]; pinch = { d: Math.hypot(a.x - b.x, a.y - b.y), s0: s }; pan = dismiss = null; }
      else if (ptrs.size === 1) { pan = { x: e.clientX, y: e.clientY, tx, ty, moved: false }; dismiss = null; }
    });
    fig.addEventListener('pointermove', (e) => {
      if (!ptrs.has(e.pointerId)) return; ptrs.set(e.pointerId, { x: e.clientX, y: e.clientY });
      if (pinch && ptrs.size === 2) {
        const [a, b] = [...ptrs.values()], d = Math.hypot(a.x - b.x, a.y - b.y);
        zoomAt((a.x + b.x) / 2, (a.y + b.y) / 2, pinch.s0 * d / pinch.d, false); return;
      }
      if (!pan) return;
      const dx = e.clientX - pan.x, dy = e.clientY - pan.y;
      if (Math.abs(dx) + Math.abs(dy) > 6) pan.moved = true;
      if (s > 1.02) { tx = pan.tx + dx; ty = pan.ty + dy; clampT(); lb.classList.add('is-drag'); apply(false); return; }
      if (e.pointerType !== 'mouse' && (dismiss || (dy > 12 && dy > Math.abs(dx) * 1.3))) {      // deslizar hacia abajo para cerrar
        dismiss = { dy }; ty = dy; tx = 0; lb.style.transition = 'none'; lb.style.opacity = String(Math.max(.25, 1 - dy / 420)); apply(false);
      }
    });
    const end = (e) => {
      if (!ptrs.has(e.pointerId)) return; ptrs.delete(e.pointerId);
      if (pinch) { swallow = performance.now() + 250; if (ptrs.size < 2) { pinch = null; if (s < 1.05) reset(true); } return; }
      const moved = pan?.moved; if (moved || s > 1.02) swallow = performance.now() + 120; pan = null; lb.classList.remove('is-drag');
      if (dismiss) {
        const far = dismiss.dy > 110; dismiss = null; lb.style.transition = '';
        if (far) { lb.style.opacity = ''; reset(false); api.close(); } else reset(true);
        return;
      }
      if (!moved && e.pointerType !== 'mouse') {          // doble toque
        const n = performance.now();
        if (n - lastTap.t < 320 && Math.hypot(e.clientX - lastTap.x, e.clientY - lastTap.y) < 28) { zoomAt(e.clientX, e.clientY, s > 1.05 ? 1 : 2.4, true); lastTap.t = 0; }
        else lastTap = { t: n, x: e.clientX, y: e.clientY };
      }
    };
    fig.addEventListener('pointerup', end); fig.addEventListener('pointercancel', end);
    // Tras arrastrar/pellizcar no debe contar como «clic fuera» (que cierra el lightbox)
    let swallow = 0;
    lb.addEventListener('click', (e) => { if (performance.now() < swallow) { e.stopImmediatePropagation(); e.preventDefault(); } }, true);

    // — Datos de la foto y resplandor —
    const avg = () => {
      try {
        const c = document.createElement('canvas'); c.width = c.height = 8; const g = c.getContext('2d', { willReadFrequently: true });
        g.drawImage(img, 0, 0, 8, 8); const d = g.getImageData(0, 0, 8, 8).data; let r = 0, gr = 0, b = 0, n = 0;
        for (let i = 0; i < d.length; i += 4) { r += d[i]; gr += d[i + 1]; b += d[i + 2]; n++; }
        r /= n; gr /= n; b /= n; const m = (r + gr + b) / 3, k = 1.35;        // un poco más saturado
        const f = (v) => Math.max(0, Math.min(255, Math.round(m + (v - m) * k)));
        return `rgb(${f(r)},${f(gr)},${f(b)})`;
      } catch { return ''; }
    };
    const show = async (i) => {
      const a = links[i], d = info[key(a)] || {};
      cats.replaceChildren(...(d.c || []).map(n => Object.assign(document.createElement('i'), { className: 'pc pc--' + (infoEl?.dataset.pcs || 'glass'), textContent: n }))); cats.hidden = !(d.c || []).length;
      desc.textContent = d.d || ''; desc.hidden = !d.d;
      exif.replaceChildren(...(d.x || []).map(t => Object.assign(document.createElement('span'), { textContent: t }))); exif.hidden = !(d.x || []).length;
      ths.forEach((t, k) => t.classList.toggle('is-on', k === i));
      ths[i]?.scrollIntoView({ block: 'nearest', inline: 'center', behavior: 'smooth' });
      try { await img.decode(); } catch { /* sigue */ }
      const c = avg(); if (c) lb.style.setProperty('--lbx-tint', c);
    };
    addEventListener('lb:open', (e) => { reset(false); reserve(); show(e.detail.index); showHint(); });
    addEventListener('lb:change', (e) => { reset(false); show(e.detail.index); });
    addEventListener('lb:close', () => reset(false));
    addEventListener('resize', () => { if (!lb.hidden) { clampT(); apply(false); } });
  };
  if (window.LightboxApi) start(); else addEventListener('DOMContentLoaded', start);
})();
