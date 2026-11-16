/* Título dibujado con luz.
 * El título se convierte en un SVG: cada letra es un <text> propio, con tres capas
 *   · trazo  — el contorno que se va dibujando (stroke-dashoffset)
 *   · cabeza — un tramo corto y brillante que va justo en la punta del trazo
 *   · relleno— que se enciende cuando el contorno de la letra está completo, con halo cálido.
 * Se usa la tipografía real del título (--f-hero, grosor y cursiva elegidos en Admin → Sitio). */
(() => {
  const root = document.querySelector('.hx-neon'); if (!root) return;
  const h1 = root.querySelector('.hxo-title'), svg = root.querySelector('.hxo-svg'), NS = 'http://www.w3.org/2000/svg';
  const text = h1.textContent.trim().replace(/\s+/g, ' '); if (!text) return;
  const speed = Math.max(1, Math.min(10, +root.dataset.speed || 5)), loop = root.dataset.repeat === '1';
  const still = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const colour = getComputedStyle(root).getPropertyValue('--hxo-c').trim() || '#ffb45a';
  const mix = (hex, k) => { const n = parseInt(hex.slice(1), 16); return '#' + [16, 8, 0].map(s => Math.round(((n >> s) & 255) * (1 - k) + 255 * k).toString(16).padStart(2, '0')).join(''); };
  const hot = mix(colour, .62);
  let timers = [], anims = [], builtW = 0, uid = 0;
  const el = (name, attrs = {}, parent) => { const e = document.createElementNS(NS, name); for (const k in attrs) e.setAttribute(k, attrs[k]); parent?.appendChild(e); return e; };

  const build = () => {
    timers.forEach(clearTimeout); timers = []; anims.forEach(a => a.cancel()); anims = []; svg.replaceChildren(); root.classList.remove('is-done', 'hxo-out');
    const cs = getComputedStyle(h1), fam = cs.fontFamily, wt = cs.fontWeight, st = cs.fontStyle;
    const W = Math.min(root.clientWidth - 2 * parseFloat(getComputedStyle(root).paddingLeft), 1500);
    let F = Math.max(44, Math.min(innerWidth * .095, 140));
    const ctx = document.createElement('canvas').getContext('2d'); const font = (f) => (ctx.font = `${st} ${wt} ${f}px ${fam}`);
    font(F); const words = text.split(' '); const widest = Math.max(...words.map(w => ctx.measureText(w).width));
    if (widest > W) { F = Math.floor(F * W / widest); font(F); }
    const wrap = (max) => { const out = []; let c = '';
      words.forEach(w => { const t = c ? c + ' ' + w : w; if (c && ctx.measureText(t).width > max) { out.push(c); c = w; } else c = t; });
      out.push(c); return out; };
    let lines = wrap(W);
    for (let m = W * .96; lines.length > 1 && m > widest; m *= .96) { const t = wrap(m); if (t.length !== lines.length) break; lines = t; }   // reparte las líneas por igual
    const lh = F * 1.06, H = Math.ceil(lines.length * lh + F * .5);
    svg.setAttribute('viewBox', `0 0 ${W} ${H}`); svg.setAttribute('width', W); svg.setAttribute('height', H);
    svg.style.cssText = `font-family:${fam};font-weight:${wt};font-style:${st};font-size:${F}px`;

    const id = 'hxo' + (++uid);
    const defs = el('defs', {}, svg);
    const f1 = el('filter', { id: id + 'g', x: '-30%', y: '-30%', width: '160%', height: '160%' }, defs); el('feGaussianBlur', { stdDeviation: F * .035 }, f1);
    const f2 = el('filter', { id: id + 'b', x: '-50%', y: '-50%', width: '200%', height: '200%' }, defs); el('feGaussianBlur', { stdDeviation: F * .12 }, f2);
    const gFill = el('g', { id: id + 'f' }, svg), gStroke = el('g', {}, svg), gHead = el('g', { filter: `url(#${id}g)` }, svg);
    const glow = el('use', { href: '#' + id + 'f', filter: `url(#${id}b)`, class: 'hxo-glow' }, svg);
    svg.insertBefore(glow, gFill);                                                       // el halo va por debajo

    const sw = Math.max(1.2, F * .011), L = F * 9, hd = F * .32;
    const Dv = Math.max(.5, 3 - .25 * speed) * 1000, Dd = Dv * 2;                       // ms: trazar el contorno / recorrido completo del guion
    const chars = lines.join('').replace(/ /g, '').length, stagger = Math.min(Dv * .26, 5200 / Math.max(1, chars - 1));
    const ease = 'cubic-bezier(.45,.05,.35,1)'; let j = 0, end = 0;

    lines.forEach((line, li) => {
      font(F); const start = (W - ctx.measureText(line).width) / 2, y = F * .92 + li * lh;
      [...line].forEach((ch, k) => {
        if (ch === ' ') return;
        const x = start + ctx.measureText(line.slice(0, k)).width;
        const mk = (parent, a) => { const t = el('text', { x, y, ...a }, parent); t.textContent = ch; return t; };
        const fill = mk(gFill, { fill: colour, 'fill-opacity': still ? 1 : 0 });
        const stroke = mk(gStroke, { fill: 'none', stroke: colour, 'stroke-width': sw, 'stroke-linejoin': 'round', 'stroke-dasharray': `${L} ${L}`, 'stroke-dashoffset': still ? L * 2 : L, 'stroke-opacity': still ? .35 : 1 });
        const head = mk(gHead, { fill: 'none', stroke: hot, 'stroke-width': sw * 2.4, 'stroke-linecap': 'round', 'stroke-dasharray': `${hd} ${L}`, 'stroke-dashoffset': hd, opacity: still ? 0 : 1 });
        if (!still) {
          const delay = j * stagger, o = { duration: Dd, delay, easing: ease, fill: 'forwards' };
          anims.push(
            stroke.animate([{ strokeDashoffset: L }, { strokeDashoffset: 0 }], o),
            head.animate([{ strokeDashoffset: hd }, { strokeDashoffset: hd - L }], o),
            head.animate([{ opacity: 1 }, { opacity: 1, offset: .5 }, { opacity: 0 }], { duration: Dd * .75, delay, fill: 'forwards' }),
            fill.animate([{ fillOpacity: 0 }, { fillOpacity: 1 }], { duration: Dv * .7, delay: delay + Dd * .43, easing: 'ease-out', fill: 'forwards' }),
            stroke.animate([{ strokeOpacity: 1 }, { strokeOpacity: .35 }], { duration: Dv * .7, delay: delay + Dd * .5, fill: 'forwards' }));
          end = Math.max(end, delay + Dd * .43 + Dv * .7);
        }
        j++;
      });
    });
    root.classList.add('is-ready');
    const done = () => { root.classList.add('is-done'); if (loop && !still) timers.push(setTimeout(() => { root.classList.add('hxo-out'); timers.push(setTimeout(build, 1000)); }, 4500)); };
    still ? done() : timers.push(setTimeout(done, end + 200));
    builtW = innerWidth;
  };

  const run = async () => {
    try { const cs = getComputedStyle(h1); await document.fonts.load(`${cs.fontStyle} ${cs.fontWeight} 64px ${cs.fontFamily}`, text); await document.fonts.ready; } catch { /* sigue con la fuente de reserva */ }
    build();
    let rt; addEventListener('resize', () => { clearTimeout(rt); rt = setTimeout(() => { if (Math.abs(innerWidth - builtW) > 40) build(); }, 350); });
  };
  run();
})();
