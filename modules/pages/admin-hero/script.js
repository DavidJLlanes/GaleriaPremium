(() => {
  const form = document.getElementById('heroForm'); if (!form) return;
  const hidden = document.getElementById('heroIds'), sel = document.getElementById('heroSel');
  const empty = document.getElementById('heroEmpty'), hint = document.getElementById('heroHint');
  const picks = [...form.querySelectorAll('.hr-p')];
  const thumb = Object.fromEntries(picks.map(b => [b.dataset.id, b.dataset.thumb]));
  let ids = hidden.value ? hidden.value.split(',') : [];
  const isSingle = () => form.dataset.type === 'single';

  const syncMode = () => {
    form.dataset.type = form.querySelector('[name=hero_type]:checked')?.value || 'gallery';
    form.dataset.source = isSingle() ? 'custom' : (form.querySelector('[name=hero_source]:checked')?.value || 'gallery');
    if (isSingle() && ids.length > 1) { ids = ids.slice(0, 1); draw(); }
    hint.textContent = isSingle() ? '(una sola foto)' : '(arrastra ⠿ para ordenar)';
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
