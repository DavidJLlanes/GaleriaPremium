(() => {
  const form = document.getElementById('heroForm'); if (!form) return;
  const hidden = document.getElementById('heroIds'), sel = document.getElementById('heroSel');
  const empty = document.getElementById('heroEmpty'), hint = document.getElementById('heroHint');
  const picks = [...form.querySelectorAll('.hr-p')];
  const thumb = Object.fromEntries(picks.map(b => [b.dataset.id, b.dataset.thumb]));
  let ids = hidden.value ? hidden.value.split(',') : [];
  const modes = JSON.parse(form.dataset.modes || '{}'), withInterval = JSON.parse(form.dataset.intervals || '[]');
  const mode = () => modes[form.dataset.type] || 'many';          // none | one | many (lo declara el module.php de cada hero)
  const isSingle = () => mode() === 'one';

  const syncMode = () => {
    form.dataset.type = form.querySelector('[name=hero_type]:checked')?.value || 'gallery';
    form.toggleAttribute('data-one', isSingle());
    form.dataset.source = isSingle() ? 'custom' : (form.querySelector('[name=hero_source]:checked')?.value || 'gallery');
    if (isSingle() && ids.length > 1) { ids = ids.slice(0, 1); draw(); }
    hint.textContent = isSingle() ? '(una sola foto)' : '(arrastra ⠿ para ordenar)';
    document.getElementById('heroPhotosCard').hidden = mode() === 'none';
    const ph = document.getElementById('heroPhotoHint'), hint2 = JSON.parse(form.dataset.hints || '{}')[form.dataset.type] || '';
    ph.hidden = !hint2; ph.textContent = hint2;
    document.getElementById('heroSpeed').hidden = !withInterval.includes(form.dataset.type);
    form.querySelectorAll('[data-hero-opts]').forEach(c => { c.hidden = c.dataset.heroOpts !== form.dataset.type; });
  };

  const draw = () => {
    hidden.value = ids.join(',');
    picks.forEach(b => b.classList.toggle('is-sel', ids.includes(b.dataset.id)));
    sel.innerHTML = ids.filter(i => thumb[i]).map((id, k) => `<div class="hr-it" data-id="${id}"><span class="srt-handle" title="Arrastra para ordenar">⠿</span><img src="${thumb[id]}" alt=""><b>${k + 1}</b><button type="button" class="hr-x" data-x="${id}" aria-label="Quitar">×</button></div>`).join('');
    empty.hidden = ids.length > 0;
  };

  form.addEventListener('change', e => { if (e.target.matches('[name=hero_type],[name=hero_source]')) syncMode(); });
  picks.forEach(b => b.addEventListener('click', () => {
    const id = b.dataset.id;
    if (ids.includes(id)) ids = ids.filter(i => i !== id);
    else ids = isSingle() ? [id] : [...ids, id];
    draw();
  }));
  sel.addEventListener('click', e => { const x = e.target.closest('[data-x]'); if (x) { ids = ids.filter(i => i !== x.dataset.x); draw(); } });
  sel.addEventListener('sortable:end', e => { ids = e.detail.ids; draw(); });

  syncMode(); draw();
})();

/* Vídeo de fondo (hero «video»): sube el archivo y guarda su nombre en el campo oculto; se aplica con «Guardar portada». */
(() => {
  const box = document.querySelector('[data-video-box]'); if (!box) return;
  const input = box.querySelector('[data-video-input]'), file = box.querySelector('[data-video-file]'), prev = box.querySelector('.hv-prev');
  const state = box.querySelector('.hv-state'), up = box.querySelector('[data-video-up]'), del = box.querySelector('[data-video-del]');
  up.addEventListener('click', async () => {
    const f = file.files[0]; if (!f) return Admin.toast('Elige primero un vídeo (.mp4 o .webm).', 'error');
    const fd = new FormData(); fd.append('video', f); up.disabled = true; up.textContent = 'Subiendo…'; state.textContent = 'Subiendo el vídeo, espera un momento…';
    try {
      const j = await Admin.post('/admin/portada/video', fd);
      input.value = j.file; prev.src = j.url; prev.hidden = false; del.hidden = false;
      state.textContent = `Vídeo subido (${(j.size / 1048576).toFixed(1)} MB). Pulsa «Guardar portada» para aplicarlo.`; Admin.toast('Vídeo subido.');
    } catch (e) { state.textContent = 'No se pudo subir.'; Admin.toast(e.message, 'error'); }
    up.disabled = false; up.textContent = 'Subir vídeo';
  });
  del.addEventListener('click', () => { input.value = ''; prev.removeAttribute('src'); prev.hidden = true; del.hidden = true; state.textContent = 'Vídeo quitado. Pulsa «Guardar portada» para aplicarlo.'; });

  // Vista previa del botón (diseño, tamaño y texto)
  const hb = document.querySelector('#hbPrev .hb');
  if (hb) {
    const K = { 1: .8, 2: .9, 3: 1, 4: 1.2, 5: 1.45 }, NAMES = ['', 'Muy pequeño', 'Pequeño', 'Mediano', 'Grande', 'Muy grande'], DOWNS = ['', 'Pegado al título', 'Cerca', 'Normal', 'Lejos', 'Muy lejos'];
    const sync = () => {
      const st = document.getElementById('hbs').value, z = +document.getElementById('hbz').value, d = document.getElementById('hbd').value;
      hb.className = 'hb hb--' + st; hb.style.setProperty('--hb-k', K[z] || 1);
      hb.innerHTML = '<span></span>' + (st === 'arrow' ? '<i aria-hidden="true">→</i>' : '');
      hb.firstChild.textContent = document.getElementById('hc').value || 'Explorar galerías';
      document.getElementById('hbzv').textContent = NAMES[z]; document.getElementById('hbdv').textContent = DOWNS[+d];
    };
    ['hbs', 'hbz', 'hbd', 'hc'].forEach(id => document.getElementById(id).addEventListener('input', sync));
  }
})();
