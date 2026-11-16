(() => {
  const form = document.getElementById('ftForm'), frame = document.getElementById('ftPrev'); if (!form || !frame) return;
  const val = (n) => form.querySelector(`[name="${n}"]:checked`)?.value || '';
  const syncOpts = () => form.querySelectorAll('[data-opts-id]').forEach(c => { c.hidden = c.dataset.optsId !== val('ft[' + c.dataset.optsKind + ']'); });
  // Vista previa: la web con ?footer_preview=1 y los ajustes del formulario; al cargar baja hasta el pie para que se vea la llegada
  const show = () => {
    const w = frame.contentWindow; if (!w) return;
    try { w.scrollTo(0, 0); setTimeout(() => w.scrollTo({ top: 1e7, behavior: 'smooth' }), 500); } catch (e) {}
  };
  frame.addEventListener('load', show);
  const play = () => {
    const q = new URLSearchParams(new FormData(form)); q.delete('_csrf'); q.delete('ft[enabled]'); q.set('footer_preview', '1');
    frame.src = '/?' + q;
  };
  let t = 0; const later = () => { clearTimeout(t); t = setTimeout(play, 350); };
  form.addEventListener('input', later); form.addEventListener('change', () => { syncOpts(); later(); });
  document.getElementById('ftPlay')?.addEventListener('click', play);
  syncOpts(); play();
})();
