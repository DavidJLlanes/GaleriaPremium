/* Subida múltiple: cada foto lleva su nombre y descripción; se sube de una en una con progreso. */
(() => {
  const root = document.getElementById('uploader');
  if (!root || !window.Admin) return;
  const input = root.querySelector('#upInput'), drop = root.querySelector('#upDrop'), queue = root.querySelector('#upQueue');
  const bar = root.querySelector('#upBar'), go = root.querySelector('#upGo'), clear = root.querySelector('#upClear');
  const count = root.querySelector('#upCount'), ai = root.querySelector('#upAi');
  let items = [], busy = false;

  const refresh = () => {
    bar.hidden = items.length === 0;
    count.textContent = items.length ? `(${items.length})` : '';
    go.disabled = busy || items.length === 0;
  };

  const add = (files) => {
    [...files].filter(f => /^image\//.test(f.type)).forEach(file => {
      const el = document.createElement('div'); el.className = 'up-item';
      const base = file.name.replace(/\.[^.]+$/, '');
      el.innerHTML = `
        <img alt="">
        <div class="up-fields">
          <input type="text" class="t" placeholder="Nombre (vacío = IA o «${base.replace(/"/g, '')}»)">
          <textarea class="d" placeholder="Descripción (vacía = la genera la IA si está activada)"></textarea>
          <div class="up-prog"><i></i></div>
        </div>
        <div class="up-state"><button type="button" class="up-x" title="Quitar">×</button></div>`;
      el.querySelector('img').src = URL.createObjectURL(file);
      const it = { file, el, done: false };
      el.querySelector('.up-x').onclick = () => { items = items.filter(i => i !== it); el.remove(); refresh(); };
      items.push(it); queue.appendChild(el);
    });
    refresh();
  };

  const send = (it) => new Promise((resolve) => {
    const fd = new FormData();
    fd.append('file', it.file);
    fd.append('title', it.el.querySelector('.t').value);
    fd.append('description', it.el.querySelector('.d').value);
    fd.append('ai', ai && ai.checked ? '1' : '0');
    const state = it.el.querySelector('.up-state'), bar = it.el.querySelector('.up-prog i');
    state.textContent = 'Subiendo…'; state.className = 'up-state';

    const xhr = new XMLHttpRequest();
    xhr.open('POST', root.dataset.url);
    xhr.setRequestHeader('X-CSRF-Token', Admin.csrf); xhr.setRequestHeader('Accept', 'application/json');
    xhr.upload.onprogress = (e) => { if (e.lengthComputable) { const p = e.loaded / e.total * 100; bar.style.width = p + '%'; if (p >= 100) state.textContent = 'Procesando…'; } };
    xhr.onload = () => {
      let j = null; try { j = JSON.parse(xhr.responseText); } catch {}
      if (xhr.status === 200 && j?.ok) {
        it.done = true; bar.style.width = '100%'; state.className = 'up-state is-ok'; state.textContent = '✓ Subida';
        it.el.querySelector('.t').value = j.photo.title; it.el.querySelector('.d').value = j.photo.caption;
        if (j.photo.ai?.error) Admin.toast('IA: ' + j.photo.ai.error, 'error');
      } else {
        state.className = 'up-state is-err'; state.textContent = j?.error || ('Error ' + xhr.status);
      }
      resolve();
    };
    xhr.onerror = () => { state.className = 'up-state is-err'; state.textContent = 'Error de red'; resolve(); };
    xhr.send(fd);
  });

  go.onclick = async () => {
    busy = true; refresh();
    for (const it of items) if (!it.done) await send(it);
    busy = false; refresh();
    const failed = items.filter(i => !i.done).length;
    if (!failed) { Admin.toast('Fotos subidas'); setTimeout(() => location.reload(), 1400); }
    else Admin.toast(`${failed} foto(s) con error. Revisa los avisos en rojo.`, 'error');
  };
  clear.onclick = () => { items = []; queue.innerHTML = ''; refresh(); };

  input.onchange = () => { add(input.files); input.value = ''; };
  ['dragenter', 'dragover'].forEach(ev => drop.addEventListener(ev, e => { e.preventDefault(); drop.classList.add('is-over'); }));
  ['dragleave', 'drop'].forEach(ev => drop.addEventListener(ev, e => { e.preventDefault(); drop.classList.remove('is-over'); }));
  drop.addEventListener('drop', e => add(e.dataTransfer.files));
})();
