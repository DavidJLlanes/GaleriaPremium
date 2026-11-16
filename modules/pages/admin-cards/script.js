(() => {
  const form = document.getElementById('cardsForm'); if (!form) return;
  // Ajustes del diseño de título elegido
  const syncTitle = () => {
    const sel = form.querySelector('[name=cards_title_style]:checked')?.value, on = document.getElementById('cdTitles').checked;
    form.querySelectorAll('[data-title-opts]').forEach(c => { c.hidden = !on || c.dataset.titleOpts !== sel; });
    document.getElementById('cdTitleBox').classList.toggle('is-off', !on);
  };
  form.addEventListener('change', syncTitle); syncTitle();

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
