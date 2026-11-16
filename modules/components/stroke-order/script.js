/* Orden caligráfico: window.StrokeOrder.plan([{ch, b, w, h}])
 *
 * Las fuentes solo guardan el contorno de las letras, no cómo se escriben. Aquí está ese dato que falta:
 * PAUTA = para cada letra, sus trazos en el orden y sentido en que se enseñan a escribir (cada «M» empieza un trazo;
 * coordenadas libres: se normalizan a la caja de la letra). Varias entradas = formas alternativas (a/g de imprenta o de caligrafía).
 * plan() casa el esqueleto real de cada letra (en la tipografía que sea) con su pauta y devuelve los tramos del esqueleto
 * ordenados y orientados como los haría la pluma. Las tildes y diéresis van al final, como al escribir.
 * Si la tipografía es caligráfica (letra ligada), cada letra se escribe de un trazo continuo: ver cursive(). */
(() => {
  const BOWL = 'M46,52 Q30,35 15,52 Q0,70 15,90 Q32,105 46,86';   // las panzas no llegan a tocar el palo: así cada punto del esqueleto tiene dueño claro
  const O = 'M35,0 Q4,4 4,50 Q4,96 35,100 Q66,96 66,50 Q66,4 35,0';
  const PAUTA = {
    a: [BOWL + ' M50,40 L50,100', 'M10,52 Q30,35 48,50 L50,90 Q52,100 60,97 M42,68 Q10,62 8,84 Q10,102 28,98 Q38,94 42,86'],
    b: ['M10,0 L10,100 M14,60 Q25,38 42,48 Q55,65 45,88 Q30,105 14,88'],
    c: ['M48,52 Q30,35 14,52 Q0,70 14,90 Q30,104 48,88'],
    d: [BOWL + ' M50,0 L50,100'],
    e: ['M8,70 L50,70 Q50,42 28,40 Q5,45 6,72 Q10,100 30,100 Q42,99 50,88'],
    f: ['M45,8 Q30,-5 22,15 L22,100 M5,42 L42,42', 'M48,6 Q34,-6 26,14 L18,120 Q14,142 0,134 M5,42 L42,42'],
    g: [BOWL + ' M50,40 L50,120 Q45,142 25,140 Q12,138 8,128',
        'M30,40 Q10,42 10,58 Q12,74 30,74 Q48,72 48,57 Q46,42 30,40 M18,72 Q8,82 18,90 L42,94 Q58,100 54,118 Q45,140 25,138 Q2,132 6,112 Q10,100 20,92 M44,46 L58,40'],
    h: ['M10,0 L10,100 M14,60 Q25,38 40,45 Q48,50 48,65 L48,100'],
    i: ['M10,40 L10,100 M10,18 L10,22'],
    j: ['M30,40 L30,120 Q28,140 10,138 M30,18 L30,22'],
    k: ['M10,0 L10,100 M45,40 L14,72 M24,66 L48,100'],
    l: ['M10,0 L10,100', 'M10,0 L10,88 Q12,102 26,98'],
    m: ['M8,40 L8,100 M12,58 Q18,38 30,44 Q36,50 36,62 L36,100 M40,58 Q46,38 58,44 Q64,50 64,62 L64,100'],
    n: ['M10,40 L10,100 M14,60 Q25,38 40,45 Q48,50 48,65 L48,100'],
    o: ['M30,40 Q8,42 6,70 Q8,100 30,100 Q52,98 54,70 Q52,42 30,40'],
    p: ['M10,40 L10,140 M14,58 Q25,36 42,46 Q55,65 45,88 Q30,105 14,86'],
    q: [BOWL + ' M50,40 L50,140'],
    r: ['M10,40 L10,100 M14,62 Q22,40 42,44'],
    s: ['M46,50 Q30,36 16,46 Q6,58 26,68 Q50,78 44,92 Q30,106 8,90'],
    t: ['M22,10 L22,85 Q24,102 44,96 M5,42 L42,42', 'M22,10 L22,100 M5,42 L42,42'],
    u: ['M10,40 L10,80 Q14,102 30,98 Q40,92 44,80 M48,40 L48,100'],
    v: ['M5,40 L27,100 L50,40'],
    w: ['M4,40 L20,100 L36,40 L52,100 L68,40'],
    x: ['M8,40 L48,100 M48,40 L8,100'],
    y: ['M6,40 L28,100 M50,40 L28,100 L16,132 Q10,142 2,138', 'M10,40 L10,80 Q14,102 30,98 Q44,92 48,78 M48,40 L48,120 Q45,142 25,140 Q12,138 8,128'],
    z: ['M8,40 L48,40 L8,100 L48,100'],
    A: ['M35,0 L5,100 M35,0 L65,100 M16,65 L54,65'],
    B: ['M10,0 L10,100 M14,0 L35,0 Q58,4 56,25 Q54,46 30,48 Q62,52 62,74 Q60,98 36,100 L14,100'],
    C: ['M62,22 Q48,-2 28,2 Q2,12 4,50 Q4,90 30,99 Q50,102 62,80'],
    D: ['M10,0 L10,100 M14,0 L30,0 Q66,8 66,50 Q64,94 30,100 L14,100'],
    E: ['M10,0 L10,100 M14,0 L55,0 M14,48 L48,48 M14,100 L56,100'],
    F: ['M10,0 L10,100 M14,0 L55,0 M14,48 L46,48'],
    G: ['M62,22 Q48,-2 28,2 Q2,12 4,50 Q4,90 30,99 Q52,100 62,84 L62,55 L38,55'],
    H: ['M10,0 L10,100 M60,0 L60,100 M14,48 L56,48'],
    I: ['M10,0 L10,100'],
    J: ['M40,0 L40,75 Q38,100 20,100 Q6,98 4,82'],
    K: ['M10,0 L10,100 M58,0 L14,56 M28,46 L60,100'],
    L: ['M10,0 L10,100 L52,100'],
    M: ['M8,0 L8,100 M8,0 L40,100 L72,0 L72,100'],
    N: ['M10,0 L10,100 M10,0 L60,100 L60,0'],
    O: [O],
    P: ['M10,0 L10,100 M14,0 L35,0 Q60,4 58,27 Q56,50 34,52 L14,52'],
    Q: [O + ' M42,78 L66,104'],
    R: ['M10,0 L10,100 M14,0 L35,0 Q60,4 58,26 Q56,48 34,50 L14,50 M34,54 L62,100'],
    S: ['M56,18 Q44,-2 26,2 Q4,10 8,28 Q12,44 32,50 Q58,58 58,76 Q54,100 30,100 Q10,98 4,80'],
    T: ['M35,0 L35,100 M4,0 L66,0'],
    U: ['M10,0 L10,68 Q12,100 35,100 Q58,100 60,68 L60,0'],
    V: ['M5,0 L35,100 L65,0'],
    W: ['M4,0 L24,100 L44,0 L64,100 L84,0'],
    X: ['M8,0 L60,100 M60,0 L8,100'],
    Y: ['M5,0 L35,50 M65,0 L35,50 L35,100'],
    Z: ['M6,0 L60,0 L6,100 L62,100'],
    0: [O],
    1: ['M12,22 L34,0 L34,100'],
    2: ['M8,22 Q14,0 34,0 Q58,4 54,30 Q48,52 6,100 L60,100'],
    3: ['M8,14 Q22,-2 38,2 Q58,10 52,30 Q46,46 28,48 Q58,54 58,76 Q52,100 30,100 Q12,98 6,84'],
    4: ['M44,0 L4,68 L62,68 M44,0 L44,100'],
    5: ['M16,0 L10,44 Q26,34 42,40 Q60,52 56,76 Q48,100 28,100 Q12,98 6,84 M16,0 L54,0'],
    6: ['M52,10 Q40,-2 26,4 Q4,20 6,62 Q8,98 32,100 Q56,96 56,70 Q54,46 32,46 Q14,48 7,66'],
    7: ['M6,0 L60,0 L24,100'],
    8: ['M54,18 Q44,-2 28,2 Q6,10 12,30 Q18,44 34,50 Q58,60 56,80 Q50,100 30,100 Q8,98 8,78 Q10,60 32,50 Q54,40 56,22'],
    9: ['M54,34 Q50,4 30,2 Q8,6 8,30 Q12,54 32,54 Q52,50 54,30 L54,60 Q50,98 26,100 Q12,98 8,88'],
  };

  /* ---------- Pauta → puntos normalizados ---------- */
  const normalizer = (x0, y0, x1, y1) => {            // caja → 0..1 (las formas muy estrechas se centran, sin estirarse)
    const w = x1 - x0, h = y1 - y0, sx = Math.max(w, .3 * h, 1e-6), sy = Math.max(h, .3 * w, 1e-6);
    return (x, y) => [.5 + (x - (x0 + x1) / 2) / sx, .5 + (y - (y0 + y1) / 2) / sy];
  };
  const cache = {};
  const pauta = (key, v, dotless) => {
    const id = key + v + (dotless ? '*' : ''); if (cache[id]) return cache[id];
    let strokes = [], cur, x = 0, y = 0, m; const re = /([MLQC])([^MLQC]*)/g;
    const line = (X, Y) => { const n = Math.max(1, Math.ceil(Math.hypot(X - x, Y - y) / 3)); for (let k = 1; k <= n; k++) cur.push([x + (X - x) * k / n, y + (Y - y) * k / n]); x = X; y = Y; };
    while ((m = re.exec(PAUTA[key][v]))) {
      const n = m[2].trim().split(/[\s,]+/).map(Number);
      if (m[1] === 'M') { cur = [[n[0], n[1]]]; strokes.push(cur); x = n[0]; y = n[1]; }
      else if (m[1] === 'L') for (let k = 0; k < n.length; k += 2) line(n[k], n[k + 1]);
      else for (let k = 0; k < n.length; k += 4) { const [cx, cy, X, Y] = n.slice(k, k + 4), a = x, b = y;
        for (let j = 1; j <= 14; j++) { const t = j / 14, u = 1 - t; cur.push([u * u * a + 2 * u * t * cx + t * t * X, u * u * b + 2 * u * t * cy + t * t * Y]); } x = X; y = Y; }
    }
    if (dotless) strokes = strokes.slice(0, -1);                                           // í, ï…: sin el punto
    const all = strokes.flat(), xs = all.map(p => p[0]), ys = all.map(p => p[1]);
    const nz = normalizer(Math.min(...xs), Math.min(...ys), Math.max(...xs), Math.max(...ys)), pts = [];
    strokes.forEach((st, s) => st.forEach((p, k) => { const [nx, ny] = nz(p[0], p[1]); pts.push({ x: nx, y: ny, s, u: st.length > 1 ? k / (st.length - 1) : 0 }); }));
    return (cache[id] = { pts, n: strokes.length });
  };

  /* ---------- Esqueleto → tramos (entre extremos y cruces) ---------- */
  const trace = (b, w, h) => {
    const N = [1, w, -1, -w, w + 1, w - 1, -w - 1, -w + 1], RING = [-w, -w + 1, 1, w + 1, w, w - 1, -1, -w - 1];
    const size = w * h, kind = new Uint8Array(size), pix = [];                              // 1 normal · 2 extremo · 3 cruce
    for (let i = 0; i < size; i++) if (b[i]) {
      let c = 0, d = 0; for (let k = 0; k < 8; k++) { if (b[i + RING[k]]) d++; if (!b[i + RING[k]] && b[i + RING[(k + 1) & 7]]) c++; }
      kind[i] = d <= 1 ? 2 : c >= 3 ? 3 : 1; pix.push(i);
    }
    const flood = (label, ok) => { let n = 0; for (const i of pix) { if (label[i] || !ok(i)) continue; n++; const st = [i]; label[i] = n;
      while (st.length) { const p = st.pop(); for (const o of N) { const q = p + o; if (b[q] && !label[q] && ok(q)) { label[q] = n; st.push(q); } } } } return n; };
    const cl = new Int32Array(size), comp = new Int32Array(size);
    flood(cl, i => kind[i] >= 2); const comps = flood(comp, () => true);
    const sizes = new Int32Array(comps + 1); for (const i of pix) sizes[comp[i]]++;
    const near = (i, c, same) => { for (const o of N) { const q = i + o; if (kind[q] >= 2 && (cl[q] === c) === same) return q; } return -1; };
    const vis = new Uint8Array(size), chains = [], used = new Uint8Array(size);
    const follow = (start, first) => {
      const c0 = cl[start], px = [start, first]; vis[first] = 1; let cur = first;
      for (;;) {
        const o = near(cur, c0, false); if (o >= 0) { px.push(o); used[o] = 1; return px; }
        let nx = -1;
        for (const d of N) { const q = cur + d; if (kind[q] !== 1 || vis[q]) continue; if (px.length < 4 && near(q, c0, true) >= 0) continue; nx = q; break; }
        if (nx < 0) { if (px.length > 3) { const a = near(cur, c0, true); if (a >= 0) px.push(a); } return px; }
        vis[nx] = 1; px.push(nx); cur = nx;
      }
    };
    for (const i of pix) if (kind[i] >= 2) for (const o of N) { const q = i + o; if (kind[q] === 1 && !vis[q]) { const px = follow(i, q); if (px.length > 2) { used[i] = 1; chains.push({ px }); } } }
    for (const i of pix) if (kind[i] === 1 && !vis[i]) {                                    // anillos sin cruces (o, 0…)
      const px = [i]; vis[i] = 1; let cur = i, go = true;
      while (go) { go = false; for (const d of N) { const q = cur + d; if (kind[q] === 1 && !vis[q]) { vis[q] = 1; px.push(q); cur = q; go = true; break; } } }
      if (px.length > 2) chains.push({ px, closed: Math.abs(px[0] % w - cur % w) <= 2 && Math.abs((px[0] / w | 0) - (cur / w | 0)) <= 2 });
    }
    for (const i of pix) if (kind[i] === 2 && !used[i] && sizes[comp[i]] <= 2) chains.push({ px: [i] });     // puntos sueltos (el de la i)
    chains.forEach(c => { const a = c.px[0], z = c.px[c.px.length - 1]; c.comp = comp[c.px[c.px.length >> 1]];
      c.freeA = kind[a] === 2; c.freeB = kind[z] === 2; c.cA = kind[a] >= 2 ? cl[a] : 0; c.cB = kind[z] >= 2 ? cl[z] : 0; });
    return { chains, sizes };
  };

  /* ---------- Casar esqueleto y pauta ---------- */
  const match = (ch, { chains, sizes }, w) => {
    const nfd = ch.normalize('NFD'), base = nfd[0], marks = nfd.length > 1;
    const other = base === base.toLowerCase() ? base.toUpperCase() : base.toLowerCase();    // fuentes solo de mayúsculas o versalitas
    const refs = [base, ...(other !== base ? [other] : [])].flatMap(k => (PAUTA[k] || []).map((_, v) => pauta(k, v, marks && (k === 'i' || k === 'j'))));
    if (!refs.length || !chains.length) return null;
    let main = 0; if (marks) for (let c = 1; c < sizes.length; c++) if (sizes[c] > sizes[main]) main = c;
    const body = chains.filter(c => !marks || c.comp === main), extra = chains.filter(c => marks && c.comp !== main);
    if (!body.length) return null;
    const X = i => i % w, Y = i => i / w | 0;
    let x0 = 1e9, y0 = 1e9, x1 = -1e9, y1 = -1e9;
    body.forEach(c => c.px.forEach(i => { x0 = Math.min(x0, X(i)); x1 = Math.max(x1, X(i)); y0 = Math.min(y0, Y(i)); y1 = Math.max(y1, Y(i)); }));
    const nz = normalizer(x0, y0, x1, y1); body.forEach(c => { c.q = c.px.map(i => nz(X(i), Y(i))); });
    const all = body.flatMap(c => c.q);

    // La forma de la pauta que más se parece. Antes de medir, la letra se encaja sobre la pauta (escala y posición por eje):
    // así los remates, colas y adornos de cada tipografía no descolocan el resto.
    const fit = (r) => {
      let ax = 1, bx = 0, ay = 1, by = 0, sc = 9;
      for (let it = 0; it < 6; it++) {
        const pairs = [], seen = new Float32Array(r.pts.length).fill(9), who = new Int32Array(r.pts.length); let a = 0;
        all.forEach(([qx, qy], j) => { const px = ax * qx + bx, py = ay * qy + by; let m = 9, mk = 0;
          for (let k = 0; k < r.pts.length; k++) { const d = Math.hypot(px - r.pts[k].x, py - r.pts[k].y); if (d < m) { m = d; mk = k; } if (d < seen[k]) { seen[k] = d; who[k] = j; } }
          a += m; if (m < .25) pairs.push([qx, qy, r.pts[mk].x, r.pts[mk].y]); });
        let bb = 0; r.pts.forEach((p, k) => { bb += seen[k]; pairs.push([all[who[k]][0], all[who[k]][1], p.x, p.y]); });
        sc = a / all.length + bb / r.pts.length; if (it === 5) break;
        const axis = (i) => { let n = pairs.length, sx = 0, sy = 0, sxx = 0, sxy = 0; for (const q of pairs) { sx += q[i]; sy += q[i + 2]; sxx += q[i] * q[i]; sxy += q[i] * q[i + 2]; }
          const den = n * sxx - sx * sx; let m = den > 1e-6 ? (n * sxy - sx * sy) / den : 1; m = Math.max(.6, Math.min(1.6, m)); return [m, (sy - m * sx) / n]; };
        [ax, bx] = axis(0); [ay, by] = axis(1);
      }
      return { sc, ax, bx, ay, by };
    };
    let best = null, f = null;
    for (const r of refs) { const t = fit(r); if (!f || t.sc < f.sc) { f = t; best = r; } }
    return { score: f.sc, runs: () => {
    body.forEach(c => { c.q = c.q.map(([qx, qy]) => [f.ax * qx + f.bx, f.ay * qy + f.by]); });
    const runs = [];
    for (const c of body) {
      const n = c.q.length, S = best.n, dist = [], uu = [];
      for (const [px, py] of c.q) { const d = new Float32Array(S).fill(9), u = new Float32Array(S);
        for (const r of best.pts) { const e = Math.hypot(px - r.x, py - r.y); if (e < d[r.s]) { d[r.s] = e; u[r.s] = r.u; } } dist.push(d); uu.push(u); }
      // A qué trazo de la pauta pertenece cada punto (Viterbi: cambiar de trazo a mitad de tramo cuesta)
      const SW = .45; let cost = Array.from(dist[0]); const back = [];
      for (let p = 1; p < n; p++) { const nc = new Array(S), bk = new Int8Array(S); let m = 0; for (let s = 1; s < S; s++) if (cost[s] < cost[m]) m = s;
        for (let s = 0; s < S; s++) { if (cost[s] <= cost[m] + SW) { nc[s] = cost[s]; bk[s] = s; } else { nc[s] = cost[m] + SW; bk[s] = m; } nc[s] += dist[p][s]; } cost = nc; back.push(bk); }
      let s = 0; for (let k = 1; k < S; k++) if (cost[k] < cost[s]) s = k;
      const state = new Int8Array(n); state[n - 1] = s; for (let p = n - 1; p > 0; p--) { s = back[p - 1][s]; state[p - 1] = s; }
      for (let a = 0; a < n;) {
        let z = a; while (z + 1 < n && state[z + 1] === state[a]) z++;
        let px = c.px.slice(a, z + 1), u = uu.slice(a, z + 1).map(v => v[state[a]]);
        const whole = a === 0 && z === n - 1, len = px.length;
        if (whole && c.closed && len > 6) {                                                 // anillo: empieza donde empieza la pauta
          let k0 = 0; for (let k = 1; k < len; k++) if (u[k] < u[k0]) k0 = k;
          px = px.slice(k0).concat(px.slice(0, k0)); u = u.slice(k0).concat(u.slice(0, k0));
          if (u[len >> 2] > u[(3 * len) >> 2]) { px = [px[0], ...px.slice(1).reverse()]; u = [u[0], ...u.slice(1).reverse()]; }
          px.push(px[0]);
        } else if (len > 1) {
          const half = len >> 1, avg = (i, j) => { let t = 0; for (let k = i; k < j; k++) t += u[k]; return t / Math.max(1, j - i); };
          const slope = avg(len - half, len) - avg(0, half), med = [...u].sort((p, q) => p - q)[half];
          let rev = slope < 0;
          if (Math.abs(slope) < .04 && whole && c.freeA !== c.freeB) rev = (med < .5) !== c.freeA;   // remates: entran al trazo al empezar, salen al acabar
          if (rev) { px.reverse(); u.reverse(); }
        }
        runs.push({ px, key: state[a] + [...u].sort((p, q) => p - q)[u.length >> 1] * .999 });
        a = z + 1;
      }
    }
    runs.sort((p, q) => p.key - q.key);
    extra.sort((p, q) => X(p.px[0]) - X(q.px[0])).forEach(c => {                            // tildes, diéresis: al final
      const a = c.px[0], z = c.px[c.px.length - 1], dx = X(z) - X(a), dy = Y(z) - Y(a);
      if (Math.abs(dy) > Math.abs(dx) ? dy < 0 : dx < 0) c.px.reverse();
      runs.push({ px: c.px });
    });
    runs.forEach((r, k) => { const p = k ? runs[k - 1].px[runs[k - 1].px.length - 1] : -1; r.lift = k === 0 || Math.hypot(X(p) - X(r.px[0]), Y(p) - Y(r.px[0])) > 3; });
    return runs;
    } };
  };

  /* ---------- Letra ligada (caligráficas) y signos sin pauta: un trazo continuo ----------
   * En la letra ligada cada letra es una sola línea: entra por la izquierda, sigue sin levantar la pluma (en los cruces
   * continúa recta, así salen los bucles) y sale por la derecha; después van puntos, barras y tildes. */
  const cursive = (chains, w, ligada) => {
    const X = i => i % w, Y = i => i / w | 0, runs = [];
    chains.filter(c => c.closed).forEach(c => {                                             // anillos: desde arriba y hacia la izquierda
      let k0 = 0; c.px.forEach((p, k) => { if (Y(p) < Y(c.px[k0])) k0 = k; });
      let px = c.px.slice(k0).concat(c.px.slice(0, k0)); if (X(px[px.length >> 2]) > X(px[0])) px = [px[0], ...px.slice(1).reverse()];
      px.push(px[0]); c.px = px; c.cA = c.cB = 0;
    });
    // 1) En cada cruce se emparejan los tramos que siguen rectos uno en otro: cada cadena de tramos es un trazo de pluma.
    const ends = new Map();
    chains.forEach(c => [0, 1].forEach(e => { const cl = e ? c.cB : c.cA; if (!cl) return; const m = c.px.length, o = e ? c.px[m - 1] : c.px[0], q = e ? c.px[Math.max(0, m - 9)] : c.px[Math.min(m - 1, 8)];
      const dx = X(q) - X(o), dy = Y(q) - Y(o), dl = Math.hypot(dx, dy) || 1; (ends.get(cl) || ends.set(cl, []).get(cl)).push({ c, e, dx: dx / dl, dy: dy / dl }); }));
    const link = new Map(), key = (c, e) => chains.indexOf(c) * 2 + e;
    for (const list of ends.values()) {
      const pairs = []; for (let i = 0; i < list.length; i++) for (let j = i + 1; j < list.length; j++) if (list[i].c !== list[j].c) pairs.push([-(list[i].dx * list[j].dx + list[i].dy * list[j].dy), list[i], list[j]]);
      pairs.sort((p, q) => q[0] - p[0]).forEach(([sc, a, b]) => { if (sc < .25 || link.has(key(a.c, a.e)) || link.has(key(b.c, b.e))) return; link.set(key(a.c, a.e), b); link.set(key(b.c, b.e), a); });
    }
    const seen = new Set(), strokes = [];
    const grow = (c, e, out, push) => { for (;;) { const n = link.get(key(c, e)); if (!n || seen.has(n.c)) return; seen.add(n.c); c = n.c; e = 1 - n.e; push(out, { c, rev: n.e === 1 }, e); } };
    for (const c of chains) { if (seen.has(c)) continue; seen.add(c);
      const parts = [{ c, rev: false }]; grow(c, 1, parts, (o, p) => o.push(p)); grow(c, 0, parts, (o, p) => o.unshift({ c: p.c, rev: !p.rev }));
      const px = parts.map(p => p.rev ? [...p.c.px].reverse() : p.c.px), f = parts[0], l = parts[parts.length - 1];
      strokes.push({ px, n: px.reduce((t, q) => t + q.length, 0), comp: c.comp,
        a: { p: px[0][0], free: f.rev ? f.c.freeB : f.c.freeA, cl: f.rev ? f.c.cB : f.c.cA },
        z: { p: px[px.length - 1].at(-1), free: l.rev ? l.c.freeA : l.c.freeB, cl: l.rev ? l.c.cA : l.c.cB } });
    }
    const flip = (s) => { s.px = s.px.reverse().map(q => [...q].reverse()); [s.a, s.z] = [s.z, s.a]; return s; };
    const natural = (s) => { const dx = X(s.z.p) - X(s.a.p), dy = Y(s.z.p) - Y(s.a.p); return (Math.abs(dx) >= Math.abs(dy) ? dx < 0 : dy < 0) ? flip(s) : s; };   // →  o  ↓
    const emit = (s, lift) => s.px.forEach((px, k) => runs.push({ px, lift: lift && k === 0 }));
    // 2) Orden: el cuerpo de la letra y después puntos, barras y tildes.
    const comps = [...new Set(strokes.map(s => s.comp))].map(id => { const ss = strokes.filter(s => s.comp === id); return { ss, n: ss.reduce((t, s) => t + s.n, 0), x: Math.min(...ss.map(s => Math.min(X(s.a.p), X(s.z.p)))) }; });
    const big = Math.max(...comps.map(c => c.n));
    comps.sort((p, q) => (p.n < big * .35) - (q.n < big * .35) || p.x - q.x);
    for (const co of comps) {
      const todo = new Set(co.ss);
      if (ligada && co.n >= big * .35) {
        // La letra ligada termina en la salida de la derecha: ese es el último trazo; hacia atrás se encadenan los que desembocan en su arranque.
        let last = null, bx = -1e9;
        for (const s of todo) for (const e of [s.a, s.z]) if (e.free && X(e.p) > bx) { bx = X(e.p); last = s; if (e === s.a) flip(s); }
        if (last && last.z.free && X(last.z.p) !== bx) flip(last);
        if (last) {
          const body = [last]; todo.delete(last);
          for (;;) { const h = body[0].a; if (h.free) break; let nx = null;
            const near = (e) => Math.hypot(X(e.p) - X(h.p), Y(e.p) - Y(h.p)) <= 7;             // mismo cruce (o pegado a él)
            for (const s of todo) if ((near(s.a) || near(s.z)) && (!nx || s.n > nx.n)) nx = s;
            if (!nx) break; if (!near(nx.z)) flip(nx); todo.delete(nx); body.unshift(nx); }
          body.forEach((s, k) => emit(s, k === 0));
        }
      }
      [...todo].map(natural).sort((p, q) => X(p.a.p) - X(q.a.p)).forEach(s => emit(s, true));   // barras, cruces, puntos, tildes
    }
    return runs;
  };

  /** glyphs: [{ch, b, w, h}] (b = esqueleto) → por cada letra, sus tramos [{px:[índices], lift}] en orden de escritura. */
  const plan = (glyphs) => {
    const info = glyphs.map(g => { const t = trace(g.b, g.w, g.h); return { g, t, m: match(g.ch, t, g.w) }; });
    const sc = info.filter(i => i.m && /\p{L}/u.test(i.g.ch)).map(i => i.m.score).sort((a, b) => a - b);
    const ligada = sc.length > 0 && sc[sc.length >> 1] > .135;                              // la tipografía no se parece a la letra de imprenta → caligráfica
    return info.map(i => !i.t.chains.length ? [] : (!ligada && i.m && i.m.score <= .3) ? i.m.runs() : cursive(i.t.chains, i.g.w, ligada));
  };

  window.StrokeOrder = { plan };
})();
