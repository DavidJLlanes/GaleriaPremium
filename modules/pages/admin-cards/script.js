(() => {
  const form = document.getElementById('cardsForm'); if (!form) return;
  // Ajustes del diseño de título elegido
  const syncTitle = () => {
    const sel = form.querySelector('[name=cards_title_style]:checked')?.value, on = document.getElementById('cdTitles').checked;
    form.querySelectorAll('[data-title-opts]').forEach(c => { c.hidden = !on || c.dataset.titleOpts !== sel; });
    document.getElementById('cdTitleBox').classList.toggle('is-off', !on);
  };
  form.addEventListener('change', syncTitle); syncTitle();

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
