/* Título escrito con una pluma de luz (desde cero, trazo a trazo).
 *
 * Las fuentes solo traen el contorno de cada letra, no el recorrido de la pluma. Por eso se calcula:
 *   1. Se pinta cada letra (con la tipografía, grosor y cursiva de «Título de la portada») en un lienzo pequeño.
 *   2. Esqueleto: adelgazamiento Zhang-Suen → la línea central del trazo. Distancia al borde → su grosor.
 *   3. El esqueleto se ordena con el motor caligráfico (components/stroke-order): cada letra en el orden y sentido
 *      en que se escribe a mano (la «a»: panza y luego palo; la «t»: palo y luego barra; tildes al final…);
 *      en las caligráficas, cada letra de un trazo continuo, como la letra ligada.
 *   4. Una pluma recorre esos trazos con el grosor real y va descubriendo la letra; en la punta, un brillo cálido.
 * Funciona con cualquier fuente, también las subidas desde el panel. */
(() => {
  const root = document.querySelector('.hx-light'); if (!root) return;
  const h1 = root.querySelector('.hxz-title'), cv = root.querySelector('.hxz-canvas');
  const text = h1.textContent.trim().replace(/\s+/g, ' '); if (!text || !cv.getContext) return;
  const speed = Math.max(1, Math.min(10, +root.dataset.speed || 5)), loop = root.dataset.repeat === '1';
  const still = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const colour = getComputedStyle(root).getPropertyValue('--hxz-c').trim() || '#ffb45a';
  const mix = (hex, k) => { const n = parseInt(hex.slice(1), 16); return '#' + [16, 8, 0].map(s => Math.round(((n >> s) & 255) * (1 - k) + 255 * k).toString(16).padStart(2, '0')).join(''); };
  const hot = mix(colour, .6), core = mix(colour, .92);
  const mk = (w, h) => { const c = document.createElement('canvas'); c.width = w; c.height = h; return c; };
  let raf = 0, timers = [], builtW = 0;

  /* ---------- 1. Maquetación: líneas, tamaño y posición de cada letra ---------- */
  const layout = () => {
    const cs = getComputedStyle(h1), fam = cs.fontFamily, wt = cs.fontWeight, st = cs.fontStyle;
    const W = Math.min(root.clientWidth - 2 * parseFloat(getComputedStyle(root).paddingLeft), 1500);
    const ctx = mk(1, 1).getContext('2d'), font = (f) => `${st} ${wt} ${f}px ${fam}`;
    let F = Math.max(44, Math.min(innerWidth * .16, 140)); ctx.font = font(F);
    const words = text.split(' '), widest = Math.max(...words.map(w => ctx.measureText(w).width));
    if (widest > W) { F = Math.floor(F * W / widest); ctx.font = font(F); }
    const wrap = (max) => { const out = []; let c = '';
      words.forEach(w => { const t = c ? c + ' ' + w : w; if (c && ctx.measureText(t).width > max) { out.push(c); c = w; } else c = t; });
      out.push(c); return out; };
    let lines = wrap(W);
    for (let m = W * .96; lines.length > 1 && m > widest; m *= .96) { const t = wrap(m); if (t.length !== lines.length) break; lines = t; }
    const lh = F * 1.12, H = Math.ceil(lines.length * lh + F * .42), chars = [];
    lines.forEach((line, li) => {
      const x0 = (W - ctx.measureText(line).width) / 2, y = F * .98 + li * lh;
      [...line].forEach((ch, k) => { if (ch !== ' ') chars.push({ ch, x: x0 + ctx.measureText(line.slice(0, k)).width, y, m: ctx.measureText(ch) }); });
    });
    return { W, H, F, font: font(F), fontAt: font, chars };
  };

  /* ---------- 2. Esqueleto de una letra ---------- */
  const thin = (b, w, h) => {                                   // Zhang-Suen
    const del = []; let changed = true;
    while (changed) {
      changed = false;
      for (let pass = 0; pass < 2; pass++) {
        del.length = 0;
        for (let y = 1; y < h - 1; y++) for (let x = 1, i = y * w + 1; x < w - 1; x++, i++) {
          if (!b[i]) continue;
          const p2 = b[i - w], p3 = b[i - w + 1], p4 = b[i + 1], p5 = b[i + w + 1], p6 = b[i + w], p7 = b[i + w - 1], p8 = b[i - 1], p9 = b[i - w - 1];
          const B = p2 + p3 + p4 + p5 + p6 + p7 + p8 + p9; if (B < 2 || B > 6) continue;
          const A = (!p2 && p3) + (!p3 && p4) + (!p4 && p5) + (!p5 && p6) + (!p6 && p7) + (!p7 && p8) + (!p8 && p9) + (!p9 && p2); if (A !== 1) continue;
          if (pass === 0 ? (p2 * p4 * p6 || p4 * p6 * p8) : (p2 * p4 * p8 || p2 * p6 * p8)) continue;
          del.push(i);
        }
        for (const i of del) b[i] = 0;
        if (del.length) changed = true;
      }
    }
  };

  const distance = (b, w, h) => {                               // distancia al borde (chaflán 3-4)
    const D = new Uint16Array(w * h);
    for (let i = 0; i < D.length; i++) D[i] = b[i] ? 65535 : 0;
    for (let y = 1; y < h - 1; y++) for (let x = 1, i = y * w + 1; x < w - 1; x++, i++)
      if (D[i]) D[i] = Math.min(D[i], D[i - 1] + 3, D[i - w] + 3, D[i - w - 1] + 4, D[i - w + 1] + 4);
    for (let y = h - 2; y > 0; y--) for (let x = w - 2, i = y * w + x; x > 0; x--, i--)
      if (D[i]) D[i] = Math.min(D[i], D[i + 1] + 3, D[i + w] + 3, D[i + w + 1] + 4, D[i + w - 1] + 4);
    return D;
  };

  /** Esqueleto de cada letra → trazos en coordenadas de pantalla, en orden de escritura: por letra, [{pts:[{x,y,r}], cum, len, lift}] */
  const strokesOf = (L) => {
    const SK = Math.min(2, 100 / L.F), pad = L.F * .3;              // resolución de trabajo ≈ fuente de 100 px
    const glyphs = L.chars.map(c => {
      const bx = c.x - c.m.actualBoundingBoxLeft - pad, by = c.y - c.m.actualBoundingBoxAscent - pad;
      const w = Math.ceil((c.m.actualBoundingBoxLeft + c.m.actualBoundingBoxRight + 2 * pad) * SK) + 2;
      const h = Math.ceil((c.m.actualBoundingBoxAscent + c.m.actualBoundingBoxDescent + 2 * pad) * SK) + 2;
      const g = mk(w, h).getContext('2d', { willReadFrequently: true });
      g.setTransform(SK, 0, 0, SK, -bx * SK, -by * SK); g.font = L.font; g.fillText(c.ch, c.x, c.y);
      const px = g.getImageData(0, 0, w, h).data, b = new Uint8Array(w * h);
      for (let i = 0; i < b.length; i++) b[i] = px[i * 4 + 3] > 110 ? 1 : 0;
      const D = distance(b, w, h); thin(b, w, h);
      return { ch: c.ch, b, w, h, D, bx, by };
    });
    const plans = window.StrokeOrder ? window.StrokeOrder.plan(glyphs) : glyphs.map(() => []);   // components/stroke-order
    return plans.map((runs, gi) => { const { w, D, bx, by } = glyphs[gi]; return runs.map(r => {
      let pts = r.px.map(i => ({ x: bx + ((i % w) + .5) / SK, y: by + ((i / w | 0) + .5) / SK, r: (D[i] / 3 + .8) / SK }));
      for (let pass = 0; pass < 2 && pts.length > 2; pass++)                                 // suaviza el escalonado
        pts = pts.map((p, k) => (k === 0 || k === pts.length - 1) ? p : { x: (pts[k - 1].x + 2 * p.x + pts[k + 1].x) / 4, y: (pts[k - 1].y + 2 * p.y + pts[k + 1].y) / 4, r: p.r });
      const cum = [0]; for (let k = 1; k < pts.length; k++) cum.push(cum[k - 1] + Math.hypot(pts[k].x - pts[k - 1].x, pts[k].y - pts[k - 1].y));
      return { pts, cum, len: cum[cum.length - 1], lift: r.lift };
    }); });
  };

  /* ---------- 3. Animación ---------- */
  const build = () => {
    cancelAnimationFrame(raf); timers.forEach(clearTimeout); timers = [];
    root.classList.remove('is-done', 'hxz-out');
    const L = layout(), s = Math.min(devicePixelRatio || 1, 2), Wd = Math.ceil(L.W * s), Hd = Math.ceil(L.H * s);
    cv.width = Wd; cv.height = Hd; cv.style.width = L.W + 'px';
    const C = cv.getContext('2d'), G = mk(Wd, Hd).getContext('2d'), M = mk(Wd, Hd).getContext('2d');
    [C, G, M].forEach(x => { x.setTransform(s, 0, 0, s, 0, 0); x.font = L.font; x.lineCap = 'round'; x.lineJoin = 'round'; });

    const grad = G.createLinearGradient(0, 0, 0, L.F * 1.12);                     // relleno cálido, más claro arriba
    grad.addColorStop(0, mix(colour, .35)); grad.addColorStop(.6, colour); grad.addColorStop(1, colour);
    L.chars.forEach(c => { G.save(); G.translate(0, c.y - L.F * .98); G.fillStyle = grad; G.fillText(c.ch, c.x, L.F * .98); G.restore(); });
    root.classList.add('is-ready'); builtW = innerWidth;

    const finish = () => { C.setTransform(1, 0, 0, 1, 0, 0); C.clearRect(0, 0, Wd, Hd); C.drawImage(G.canvas, 0, 0); root.classList.add('is-done');
      if (loop && !still) timers.push(setTimeout(() => { root.classList.add('hxz-out'); timers.push(setTimeout(build, 1000)); }, 4500)); };
    if (still) return finish();

    // Línea de tiempo: una sola pluma, trazo tras trazo (con una pequeña pausa al levantarla).
    const v = L.F * (2.6 + speed * 1.15), strokes = []; let t = .35;
    strokesOf(L).forEach((list, ci) => {
      list.forEach((st, k) => { if (st.lift) t += k ? .07 : .1; st.t0 = t; st.dur = Math.max(.04, st.len / v); st.char = ci; st.last = k === list.length - 1; t += st.dur; });
      strokes.push(...list);
    });
    const maxT = 15 - speed;                                                        // títulos largos: se acelera para no eternizarse
    if (t > maxT) { const k = maxT / t; strokes.forEach(st => { st.t0 *= k; st.dur *= k; }); t = maxT; }
    strokes.forEach(st => { st.i = 0; st.done = 0; st.p = st.pts[0]; });
    const BR = 1.18, ease = (p) => .5 * p + .5 * (.5 - .5 * Math.cos(Math.PI * p));
    const at = (st, d) => {                                                         // punto a la distancia d del trazo
      let i = 0; while (i < st.pts.length - 2 && st.cum[i + 1] < d) i++;
      const a = st.pts[i], b = st.pts[Math.min(i + 1, st.pts.length - 1)], seg = (st.cum[i + 1] ?? st.cum[i]) - st.cum[i], f = seg ? (d - st.cum[i]) / seg : 0;
      return { x: a.x + (b.x - a.x) * f, y: a.y + (b.y - a.y) * f, r: a.r + (b.r - a.r) * f, i };
    };
    const seg = (a, b) => { M.lineWidth = 2 * Math.max(a.r, b.r) * BR; M.beginPath(); M.moveTo(a.x, a.y); M.lineTo(b.x, b.y); M.stroke(); };
    M.strokeStyle = M.fillStyle = '#fff';

    const t0 = performance.now();
    const frame = (now) => {
      const T = (now - t0) / 1000; let pen = null;
      for (const st of strokes) {
        if (st.done || T < st.t0) continue;
        const p = Math.min(1, (T - st.t0) / st.dur), d = st.len * ease(p);
        if (st.i === 0 && !st.started) { st.started = 1; M.beginPath(); M.arc(st.p.x, st.p.y, st.p.r * BR, 0, 7); M.fill(); }
        while (st.i < st.pts.length - 1 && st.cum[st.i + 1] <= d) { seg(st.p, st.pts[st.i + 1]); st.p = st.pts[++st.i]; }
        if (st.i < st.pts.length - 1) { const q = at(st, d); seg(st.p, q); st.p = q; }
        if (p >= 1) { st.done = 1; if (st.last) M.fillText(L.chars[st.char].ch, L.chars[st.char].x, L.chars[st.char].y); }   // cierra huecos de la letra
        else pen = { st, d, q: st.p };
      }
      // Lo revelado = letras ∩ recorrido de la pluma
      C.save(); C.setTransform(1, 0, 0, 1, 0, 0); C.globalCompositeOperation = 'source-over'; C.clearRect(0, 0, Wd, Hd);
      C.drawImage(G.canvas, 0, 0); C.globalCompositeOperation = 'destination-in'; C.drawImage(M.canvas, 0, 0); C.restore();
      if (pen) {
        const { st, d, q } = pen, a = at(st, Math.max(0, d - L.F * .55));             // estela incandescente detrás de la punta
        const tg = C.createLinearGradient(a.x, a.y, q.x, q.y); tg.addColorStop(0, hot + '00'); tg.addColorStop(1, hot);
        C.save(); C.globalCompositeOperation = 'source-atop'; C.strokeStyle = tg; C.lineWidth = 2 * q.r * BR; C.beginPath(); C.moveTo(a.x, a.y);
        for (let k = a.i + 1; k <= st.i; k++) C.lineTo(st.pts[k].x, st.pts[k].y);
        C.lineTo(q.x, q.y); C.stroke(); C.restore();
        C.save(); C.shadowColor = colour; C.shadowBlur = L.F * .32 * s; C.fillStyle = hot;   // la punta de luz
        C.beginPath(); C.arc(q.x, q.y, Math.max(2.4, q.r * .95), 0, 7); C.fill(); C.fill();
        C.shadowBlur = L.F * .08 * s; C.fillStyle = core; C.beginPath(); C.arc(q.x, q.y, Math.max(1.3, q.r * .45), 0, 7); C.fill(); C.restore();
      }
      if (T >= t + .15) return finish();
      raf = requestAnimationFrame(frame);
    };
    raf = requestAnimationFrame(frame);
  };

  (async () => {
    if (document.readyState === 'loading') await new Promise(r => addEventListener('DOMContentLoaded', r));   // espera al motor caligráfico
    try { const cs = getComputedStyle(h1); await document.fonts.load(`${cs.fontStyle} ${cs.fontWeight} 64px ${cs.fontFamily}`, text); await document.fonts.ready; } catch { /* fuente de reserva */ }
    build();
    let rt; addEventListener('resize', () => { clearTimeout(rt); rt = setTimeout(() => { if (Math.abs(innerWidth - builtW) > 40) build(); }, 350); });
  })();
})();
