(() => {
  const form = document.getElementById('inForm'), frame = document.getElementById('inPrev'); if (!form || !frame) return;
  const design = () => form.querySelector('[name="in[design]"]:checked')?.value || '';
  const syncOpts = () => form.querySelectorAll('[data-opts-id]').forEach(c => { c.hidden = c.dataset.optsId !== design(); });
  // Vista previa: la web con ?intro_preview=1 y los ajustes del formulario (sin guardar; solo con sesión de administrador)
  let t = 0;
  const play = () => {
    const q = new URLSearchParams(new FormData(form)); q.delete('_csrf'); q.set('intro_preview', '1'); q.delete('in[enabled]');
    frame.src = '/?' + q;
  };
  const later = () => { clearTimeout(t); t = setTimeout(play, 350); };
  form.addEventListener('input', later); form.addEventListener('change', () => { syncOpts(); later(); });
  document.getElementById('inPlay')?.addEventListener('click', play);
  syncOpts(); play();
})();
