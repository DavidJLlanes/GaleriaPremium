/* Selector de categorías: chips + desplegable con las guardadas (window.CatAll); Enter crea o elige; máx. window.CatMax. Evento «cp:change». */
(() => {
  const MAX = () => window.CatMax || 3;
  const norm = (s) => s.replace(/[\u0000-\u001f<>"\\]+/g, '').replace(/\s+/g, ' ').trim().slice(0, 30);
  const same = (a, b) => a.toLowerCase() === b.toLowerCase();

  function init(root) {
    if (root._cp) return;
    const chips = root.querySelector('.cp-chips'), input = root.querySelector('.cp-in'), list = root.querySelector('.cp-list');
    let val = []; try { val = JSON.parse(root.dataset.value || '[]').filter(Boolean).slice(0, MAX()); } catch (e) {}
    let hi = -1, opts = [];

    const emit = () => root.dispatchEvent(new CustomEvent('cp:change', { bubbles: true }));
    const draw = () => {
      chips.textContent = '';
      val.forEach((n, i) => {
        const c = document.createElement('span'); c.className = 'cp-chip'; c.append(document.createTextNode(n));
        const x = document.createElement('button'); x.type = 'button'; x.setAttribute('aria-label', 'Quitar ' + n); x.textContent = '×';
        x.addEventListener('click', (e) => { e.stopPropagation(); val.splice(i, 1); draw(); emit(); input.focus(); });
        c.append(x); chips.append(c);
      });
      root.classList.toggle('is-full', val.length >= MAX());
    };
    const add = (n) => {
      n = norm(n); if (!n || val.length >= MAX() || val.some(v => same(v, n))) return;
      const known = (window.CatAll || []).find(v => same(v, n)); n = known || n;
      if (!known) (window.CatAll = window.CatAll || []).push(n);
      val.push(n); input.value = ''; close(); draw(); emit();
    };
    const close = () => { list.hidden = true; input.setAttribute('aria-expanded', 'false'); hi = -1; };
    const open = () => {
      const q = input.value.trim().toLowerCase(); list.textContent = ''; opts = [];
      (window.CatAll || []).filter(n => !val.some(v => same(v, n)) && n.toLowerCase().includes(q)).forEach(n => opts.push({ n, make: false }));
      const t = norm(input.value);
      if (t && !(window.CatAll || []).some(n => same(n, t)) && !val.some(v => same(v, t))) opts.push({ n: t, make: true });
      if (!opts.length) { const e = document.createElement('li'); e.className = 'cp-empty'; e.textContent = val.length >= MAX() ? 'Máximo alcanzado' : 'Escribe para crear una categoría'; list.append(e); }
      opts.forEach((o, i) => {
        const li = document.createElement('li'); li.className = 'cp-opt'; li.setAttribute('role', 'option');
        li.append(document.createTextNode(o.make ? 'Crear «' + o.n + '»' : o.n));
        li.addEventListener('mousedown', (e) => { e.preventDefault(); add(o.n); input.focus(); });
        list.append(li);
      });
      list.hidden = false; input.setAttribute('aria-expanded', 'true'); hi = -1;
    };
    const mark = () => [...list.querySelectorAll('.cp-opt')].forEach((li, i) => li.classList.toggle('is-hi', i === hi));

    input.addEventListener('focus', open); input.addEventListener('input', open);
    input.addEventListener('blur', () => setTimeout(close, 120));
    input.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowDown') { e.preventDefault(); if (list.hidden) open(); hi = Math.min(opts.length - 1, hi + 1); mark(); }
      else if (e.key === 'ArrowUp') { e.preventDefault(); hi = Math.max(0, hi - 1); mark(); }
      else if (e.key === 'Enter' || e.key === ',') { e.preventDefault(); const o = opts[hi] || opts.find(o => o.make) || opts[0]; if (hi >= 0 || input.value.trim()) add(hi >= 0 ? opts[hi].n : (o && (o.make || same(o.n, input.value.trim())) ? o.n : input.value)); }
      else if (e.key === 'Escape') { close(); }
      else if (e.key === 'Backspace' && !input.value && val.length) { val.pop(); draw(); emit(); }
    });
    root.querySelector('.cp-box').addEventListener('click', () => input.focus());
    root._cp = { get: () => val.slice(), set: (a) => { val = (a || []).slice(0, MAX()); draw(); }, json: () => JSON.stringify(val) };
    draw();
  }
  window.CatPicker = { init, initAll: (r = document) => r.querySelectorAll('[data-cp]').forEach(init) };
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', () => window.CatPicker.initAll()); else window.CatPicker.initAll();
})();
