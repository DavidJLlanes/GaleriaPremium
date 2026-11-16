(() => {
  const form = document.getElementById('cardsForm'); if (!form) return;
  // Ajustes del diseño de título elegido
  const syncTitle = () => {
    const sel = form.querySelector('[name=cards_title_style]:checked')?.value, on = document.getElementById('cdTitles').checked;
    form.querySelectorAll('[data-title-opts]').forEach(c => { c.hidden = !on || c.dataset.titleOpts !== sel; });
    document.getElementById('cdTitleBox').classList.toggle('is-off', !on);
  };
  form.addEventListener('change', syncTitle); syncTitle();

  // Diseño de la sección: ajustes propios y vista previa (la web real en un iframe)
  const frame = document.getElementById('cdFrame');
  const syncLayout = (reload) => {
    const l = form.querySelector('[name=cards_layout]:checked')?.value || 'rejilla';
    form.querySelectorAll('[data-layout-opts]').forEach(c => { c.hidden = c.dataset.layoutOpts !== l; });
    if (reload) frame.src = '/?cards_preview=1&cl=' + encodeURIComponent(l) + '#galerias';
  };
  form.querySelectorAll('[name=cards_layout]').forEach(r => r.addEventListener('change', () => syncLayout(true))); syncLayout(false);

  // Tipografía, tamaño y color de los títulos: la vista previa se actualiza al momento (mismas variables CSS que la web)
  const pv = document.getElementById('cdPrev'), g = (id) => document.getElementById(id);
  const type = () => {
    const opt = g('cdFont').selectedOptions[0], w = +g('cdWeight').value;
    const set = (k, v) => v ? pv.style.setProperty(k, v) : pv.style.removeProperty(k);
    set('--f-title', opt && opt.dataset.fam); set('--w-title', w ? String(w) : ''); set('--st-title', g('cdItalic').checked ? 'italic' : '');
    pv.style.setProperty('--ct-s', String(g('cdSize').value / 100)); g('cdSizeVal').textContent = g('cdSize').value;
    set('--ct-c', g('cdColorOn').checked ? g('cdColor').value : '');
  };
  ['cdFont', 'cdWeight', 'cdSize', 'cdItalic', 'cdColorOn', 'cdColor'].forEach(id => g(id).addEventListener('input', type)); type();

  // Aspecto de la tarjeta y textos secundarios: también al momento
  const look = () => {
    const opt = g('cdSubFont').selectedOptions[0], w = +g('cdSubWeight').value;
    const set = (k, v) => v ? pv.style.setProperty(k, v) : pv.style.removeProperty(k);
    pv.style.setProperty('--cc-ar', g('cdRatio').value); pv.style.setProperty('--cc-rad', g('cdRadius').value + 'px'); g('cdRadiusVal').textContent = g('cdRadius').value;
    pv.style.setProperty('--cc-vs', g('cdVeil').value); g('cdVeilVal').textContent = g('cdVeil').value; pv.style.setProperty('--cc-vc', g('cdVeilColor').value);
    set('--f-sub', opt && opt.dataset.fam); set('--w-sub', w ? String(w) : ''); set('--st-sub', g('cdSubItalic').checked ? 'italic' : '');
    pv.style.setProperty('--cs-s', String(g('cdSubSize').value / 100)); g('cdSubSizeVal').textContent = g('cdSubSize').value;
    set('--cs-c', g('cdSubColorOn').checked ? g('cdSubColor').value : '');
  };
  ['cdRatio', 'cdRadius', 'cdVeil', 'cdVeilColor', 'cdSubFont', 'cdSubWeight', 'cdSubSize', 'cdSubItalic', 'cdSubColorOn', 'cdSubColor'].forEach(id => g(id).addEventListener('input', look)); look();

  // Vista previa del efecto: se vuelve al estado inicial y se reproduce
  const prev = document.getElementById('cdPrev'), cards = [...prev.querySelectorAll('.ccard')];
  const play = () => {
    const fx = form.querySelector('[name=cards_reveal]:checked')?.value || 'fade-up';
    cards.forEach(c => { c.className = c.className.replace(/\brv-[\w-]+|\bis-in\b|\bis-done\b/g, '').replace(/\s+/g, ' ').trim() + ' rv-' + fx; });
    prev.classList.toggle('rv-on', fx !== 'none');
    prev.classList.add('is-reset'); void prev.offsetWidth;      // estado inicial SIN transición (si no, la tarjeta «viaja» hacia él y el efecto se estropea)
    prev.classList.remove('is-reset'); void prev.offsetWidth;
    requestAnimationFrame(() => requestAnimationFrame(() => cards.forEach(c => c.classList.add('is-in'))));
  };
  document.getElementById('cdPlay').addEventListener('click', play);
  form.querySelectorAll('[name=cards_reveal]').forEach(r => r.addEventListener('change', play));
})();
