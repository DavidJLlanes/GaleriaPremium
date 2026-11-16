// Genera public/fonts/<id>/*.woff2 y config/fonts.php a partir de paquetes @fontsource (solo subset latin).
// Uso (desde la raíz del repo):  mkdir /tmp/f && cd /tmp/f && npm i @fontsource/<id> @fontsource-variable/<id> ...
//                                node tools/fonts/build.mjs /tmp/f/node_modules
// Lista y categorías: tools/fonts/list.json (el orden marca la prioridad; CAP limita cada categoría).
import fs from 'node:fs'; import path from 'node:path';
const NM = process.argv[2]; const ROOT = path.resolve(import.meta.dirname, '../..');
const CAP = { serif: 36, sans: 36, display: 30, script: 40, mono: 6 };
const FALL = { serif: 'Georgia,serif', sans: 'system-ui,sans-serif', display: 'system-ui,sans-serif', script: 'cursive', mono: 'ui-monospace,monospace' };
const UP = new Set(['dm', 'eb', 'pt', 'ibm', 'tt']); const OVR = { 'jetbrains-mono': 'JetBrains Mono', 'old-standard-tt': 'Old Standard TT' };
const label = (id) => OVR[id] ?? id.split('-').map(w => UP.has(w) ? w.toUpperCase() : w[0].toUpperCase() + w.slice(1)).join(' ');
const list = JSON.parse(fs.readFileSync(path.join(ROOT, 'tools/fonts/list.json'), 'utf8'));
const out = path.join(ROOT, 'public/fonts'); fs.rmSync(out, { recursive: true, force: true }); fs.mkdirSync(out, { recursive: true });
const cnt = {}; const cat = {}; const skipped = [];
const has = (p) => fs.existsSync(p);
for (const { id, cat: c } of list) {
  if ((cnt[c] ?? 0) >= CAP[c]) continue;
  const vd = path.join(NM, '@fontsource-variable', id), sd = path.join(NM, '@fontsource', id);
  const dst = path.join(out, id); const entry = { label: label(id), cat: c, fallback: FALL[c] };
  const cp = (src, name) => { fs.mkdirSync(dst, { recursive: true }); fs.copyFileSync(src, path.join(dst, name)); return name; };
  const vf = (st) => path.join(vd, 'files', `${id}-latin-wght-${st}.woff2`);
  if (has(vf('normal'))) {                                    // variable (eje de peso)
    const css = fs.readFileSync(path.join(vd, 'wght.css'), 'utf8');
    const m = css.match(new RegExp(`/\\* ${id}-latin-wght-normal \\*/[\\s\\S]*?font-weight: ([\\d ]+);`));
    entry.n = { var: cp(vf('normal'), `${id}-wght-n.woff2`), range: m ? m[1].trim() : '100 900' };
    if (has(vf('italic'))) entry.i = { var: cp(vf('italic'), `${id}-wght-i.woff2`), range: entry.n.range };
  } else if (has(sd)) {                                       // estática (un archivo por peso)
    const f = (w, st) => path.join(sd, 'files', `${id}-latin-${w}-${st}.woff2`);
    const all = fs.readdirSync(path.join(sd, 'files')).map(x => x.match(new RegExp(`^${id}-latin-(\\d+)-normal\\.woff2$`))?.[1]).filter(Boolean).map(Number).sort((a, b) => a - b);
    let ws = [300, 400, 500, 700].filter(w => all.includes(w));
    if (!ws.length) ws = [...all].sort((a, b) => Math.abs(a - 400) - Math.abs(b - 400)).slice(0, 3).sort((a, b) => a - b);
    if (!ws.length) { skipped.push(id); continue; }
    entry.n = { static: Object.fromEntries(ws.map(w => [w, cp(f(w, 'normal'), `${id}-${w}-n.woff2`)])) };
    const wi = [400, 700].filter(w => has(f(w, 'italic')));
    if (wi.length) entry.i = { static: Object.fromEntries(wi.map(w => [w, cp(f(w, 'italic'), `${id}-${w}-i.woff2`)])) };
  } else { skipped.push(id); continue; }
  cat[id] = entry; cnt[c] = (cnt[c] ?? 0) + 1;
}
const q = (s) => `'${String(s).replace(/'/g, "\\'")}'`;
const face = (o) => 'var' in o ? `['var' => ${q(o.var)}, 'range' => ${q(o.range)}]`
  : `['static' => [${Object.entries(o.static).map(([w, f]) => `${w} => ${q(f)}`).join(', ')}]]`;
let php = `<?php
// Catálogo de tipografías alojadas en local (public/fonts/<id>/*.woff2). Solo subset latin (incluye acentos y ñ).
// GENERADO con tools/fonts/build.mjs — no editar a mano.
// n = estilo normal · i = cursiva (si existe) · 'var' = fuente variable con eje de peso (range) · 'static' = un archivo por peso.
return [\n`;
for (const [id, e] of Object.entries(cat))
  php += `    ${q(id)} => [${['label', 'cat', 'fallback'].map(k => `'${k}' => ${q(e[k])}`).join(', ')}, 'n' => ${face(e.n)}${e.i ? `, 'i' => ${face(e.i)}` : ''}],\n`;
fs.writeFileSync(path.join(ROOT, 'config/fonts.php'), php + '];\n');
console.log('fuentes:', Object.keys(cat).length, cnt, 'omitidas:', skipped.join(','), 'con cursiva:', Object.values(cat).filter(e => e.i).length);
