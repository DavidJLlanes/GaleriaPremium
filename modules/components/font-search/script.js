/* Buscador para listas largas (tipografías): sustituye visualmente a cada <select data-font-search> por un botón + panel con
 * buscador. El <select> original se mantiene (oculto) y es el que manda: se le cambia el valor y se dispara «change»,
 * así funcionan los scripts que ya escuchan ese evento. Admite opciones añadidas después (fuentes subidas). */
(() => {
  const norm = (s) => s.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
  // Cada opción se muestra con su propia tipografía (la etiqueta del catálogo es el nombre de la familia). Solo se aplica a las que se ven,
  // para que el navegador no descargue las ~150 fuentes de golpe.
  const fam = (t) => { const m = /^Por defecto \((.+)\)$/.exec(t); const n = m ? m[1] : t; return /^(Igual que|El del|La de)/.test(n) ? '' : `'${n.replace(/'/g, '')}',sans-serif`; };
  const lazy = new IntersectionObserver((es) => es.forEach(e => { if (e.isIntersecting) { e.target.style.fontFamily = e.target.dataset.fam; lazy.unobserve(e.target); } }), { rootMargin: '120px' });
  const enhance = (sel) => {
    if (sel._fs) return; sel._fs = true;
    const wrap = document.createElement('div'); wrap.className = 'fs';
    const btn = document.createElement('button'); btn.type = 'button'; btn.className = 'fs-btn'; btn.setAttribute('aria-haspopup', 'listbox'); btn.setAttribute('aria-expanded', 'false');
    const pop = document.createElement('div'); pop.className = 'fs-pop'; pop.hidden = true;
    const q = document.createElement('input'); q.type = 'search'; q.className = 'fs-q'; q.placeholder = 'Buscar tipografía…'; q.autocomplete = 'off'; q.setAttribute('aria-label', 'Buscar tipografía');
    const list = document.createElement('ul'); list.className = 'fs-list'; list.setAttribute('role', 'listbox');
    pop.append(q, list); sel.before(wrap); wrap.append(btn, pop, sel); sel.classList.add('fs-native'); sel.tabIndex = -1;
    let items = [], act = -1;

    const label = () => { const t = sel.selectedOptions[0] ? sel.selectedOptions[0].textContent : ''; btn.textContent = t; btn.style.fontFamily = fam(t); };
    const render = () => {
      const f = norm(q.value.trim()); list.textContent = ''; items = [];
      const add = (o) => {
        if (f && !norm(o.textContent).includes(f)) return false;
        const li = document.createElement('li'); li.className = 'fs-o' + (o.value === sel.value ? ' is-sel' : ''); li.setAttribute('role', 'option');
        li.dataset.v = o.value; li.textContent = o.textContent; list.append(li); items.push(li);
        const ff = fam(o.textContent); if (ff) { li.dataset.fam = ff; lazy.observe(li); }
        return true;
      };
      [...sel.children].forEach(n => {
        if (n.tagName === 'OPTGROUP') {
          const h = document.createElement('li'); h.className = 'fs-g'; h.textContent = n.label; list.append(h);
          const any = [...n.children].map(add).some(Boolean); if (!any) h.remove();
        } else add(n);
      });
      if (!items.length) { const e = document.createElement('li'); e.className = 'fs-none'; e.textContent = 'Sin resultados'; list.append(e); }
      act = items.findIndex(i => i.classList.contains('is-sel')); if (f || act < 0) act = items.length ? 0 : -1; mark(false);
    };
    const mark = (scroll = true) => { items.forEach((i, k) => i.classList.toggle('is-act', k === act)); if (scroll && items[act]) items[act].scrollIntoView({ block: 'nearest' }); };
    const open = () => { q.value = ''; render(); pop.hidden = false; btn.setAttribute('aria-expanded', 'true'); q.focus(); if (items[act]) items[act].scrollIntoView({ block: 'center' }); };
    const close = (focus) => { pop.hidden = true; btn.setAttribute('aria-expanded', 'false'); if (focus) btn.focus(); };
    const pick = (v) => { if (sel.value !== v) { sel.value = v; sel.dispatchEvent(new Event('change', { bubbles: true })); } label(); close(true); };

    btn.addEventListener('click', () => pop.hidden ? open() : close());
    q.addEventListener('input', render);
    list.addEventListener('click', (e) => { const li = e.target.closest('.fs-o'); if (li) pick(li.dataset.v); });
    pop.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') { e.preventDefault(); close(true); }
      else if (e.key === 'ArrowDown') { e.preventDefault(); act = Math.min(items.length - 1, act + 1); mark(); }
      else if (e.key === 'ArrowUp') { e.preventDefault(); act = Math.max(0, act - 1); mark(); }
      else if (e.key === 'Enter') { e.preventDefault(); if (items[act]) pick(items[act].dataset.v); }
    });
    btn.addEventListener('keydown', (e) => { if (e.key === 'ArrowDown' || e.key === 'ArrowUp') { e.preventDefault(); open(); } });
    document.addEventListener('mousedown', (e) => { if (!pop.hidden && !wrap.contains(e.target)) close(false); });
    sel.addEventListener('change', label);
    new MutationObserver(label).observe(sel, { childList: true, subtree: true });
    label();
  };
  const run = () => document.querySelectorAll('select[data-font-search]').forEach(enhance);
  document.readyState === 'loading' ? addEventListener('DOMContentLoaded', run) : run();
})();
