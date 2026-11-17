/* Subida global: arrastrar imágenes a cualquier zona abre un modal con galería, título y descripción por foto. */
(() => {
  const root = document.getElementById('dropUpload'); if (!root) return;
  const modal = root.querySelector('.du-modal'), list = root.querySelector('.du-list');
  const gal = root.querySelector('.du-gal'), go = root.querySelector('.du-go'), msg = root.querySelector('.du-msg'), aiChk = root.querySelector('.du-aichk');
  const galleries = JSON.parse(root.dataset.galleries || '[]');
  let items = [], busy = false, depth = 0;

  const hasFiles = (e) => [...(e.dataTransfer?.types || [])].includes('Files');
  const inUploader = (e) => e.target.closest?.('#uploader');
  const show = (on) => { root.hidden = false; root.classList.toggle('is-over', on); if (!on && !root.classList.contains('is-open')) root.hidden = true; };

  addEventListener('dragenter', e => { if (!hasFiles(e) || inUploader(e) || busy) return; depth++; show(true); });
  addEventListener('dragover', e => { if (!hasFiles(e) || inUploader(e)) return; e.preventDefault(); });
  addEventListener('dragleave', e => { if (!hasFiles(e)) return; depth = Math.max(0, depth - 1); if (!depth) show(false); });
  addEventListener('drop', e => {
    if (!hasFiles(e)) return; depth = 0; show(false);
    if (inUploader(e) || busy) return;
    e.preventDefault();
    const files = [...e.dataTransfer.files].filter(f => /^image\/(jpeg|png|webp|gif)$/.test(f.type));
    if (!files.length) { toast('Solo se pueden subir imágenes (JPG, PNG, WebP, GIF)', 'error'); return; }
    open(files);
  });

  const toast = (m, t = 'ok') => window.Admin ? Admin.toast(m, t) : alert(m);

  const defaultGallery = () => {
    const m = location.pathname.match(/^\/admin\/galeria\/(\d+)/); if (m) return m[1];
    try { return localStorage.getItem('du_gal') || ''; } catch { return ''; }
  };

  const open = (files) => {
    if (!galleries.length) { root.classList.add('is-open'); root.hidden = false; modal.hidden = false; return; }
    items = []; list.innerHTML = ''; msg.textContent = '';
    const d = defaultGallery(); if (d && galleries.some(g => String(g.id) === d)) gal.value = d;
    if (aiChk) aiChk.checked = !aiChk.disabled && root.dataset.aiAuto === '1';
    files.forEach(file => {
      const el = document.createElement('div'); el.className = 'du-item';
      el.innerHTML = '<img alt=""><div><input type="text" class="t" placeholder="Título"><textarea class="d" placeholder="Descripción (opcional)"></textarea><div class="du-st"></div><div class="du-bar"><i></i></div></div>';
      el.querySelector('img').src = URL.createObjectURL(file);
      el.querySelector('.t').value = file.name.replace(/\.[^.]+$/, '').replace(/[-_]+/g, ' ');
      items.push({ file, el, done: false }); list.appendChild(el);
    });
    go.disabled = false; go.textContent = files.length > 1 ? `Subir ${files.length} fotos` : 'Subir';
    root.hidden = false; root.classList.add('is-open'); modal.hidden = false;
    items[0].el.querySelector('.t').select();
  };

  const close = () => { if (busy) return; root.classList.remove('is-open'); modal.hidden = true; root.hidden = true; items.forEach(i => URL.revokeObjectURL(i.el.querySelector('img').src)); };

  const send = (it, gid) => new Promise(res => {
    const fd = new FormData();
    fd.append('file', it.file); fd.append('title', it.el.querySelector('.t').value); fd.append('description', it.el.querySelector('.d').value);
    fd.append('ai', aiChk && aiChk.checked ? '1' : '0');
    const cp = root.querySelector('[data-cp]'); if (cp && cp._cp) fd.append('cats', cp._cp.json());
    const st = it.el.querySelector('.du-st'), bar = it.el.querySelector('.du-bar i');
    st.className = 'du-st'; st.textContent = 'Subiendo…';
    const x = new XMLHttpRequest(); x.open('POST', `/admin/galeria/${gid}/subir`);
    x.setRequestHeader('X-CSRF-Token', root.dataset.csrf); x.setRequestHeader('Accept', 'application/json');
    x.upload.onprogress = e => { if (e.lengthComputable) { bar.style.width = e.loaded / e.total * 100 + '%'; if (e.loaded >= e.total) st.textContent = 'Procesando…'; } };
    x.onload = () => {
      let j = null; try { j = JSON.parse(x.responseText); } catch {}
      if (x.status === 200 && j?.ok) { it.done = true; bar.style.width = '100%'; st.className = 'du-st is-ok'; st.textContent = '✓ Subida'; if (j.photo?.ai?.error) toast('IA: ' + j.photo.ai.error, 'error'); }
      else { st.className = 'du-st is-err'; st.textContent = j?.error || ('Error ' + x.status); }
      res();
    };
    x.onerror = () => { st.className = 'du-st is-err'; st.textContent = 'Error de red'; res(); };
    x.send(fd);
  });

  go.onclick = async () => {
    busy = true; go.disabled = true; const gid = gal.value;
    try { localStorage.setItem('du_gal', gid); } catch {}
    for (const it of items) if (!it.done) await send(it, gid);
    busy = false;
    const ok = items.filter(i => i.done).length, bad = items.length - ok;
    if (!bad) {
      toast(ok === 1 ? 'Foto subida' : `${ok} fotos subidas`);
      const here = location.pathname === `/admin/galeria/${gid}`;
      close(); if (here) setTimeout(() => location.reload(), 400);
    } else { msg.textContent = `${ok} subidas, ${bad} con error. Pulsa para reintentar.`; go.disabled = false; go.textContent = 'Reintentar'; }
  };
  root.querySelector('.du-x').onclick = close; root.querySelector('.du-cancel')?.addEventListener('click', close);
  root.addEventListener('mousedown', e => { if (e.target === root) close(); });
  addEventListener('keydown', e => { if (e.key === 'Escape' && root.classList.contains('is-open')) close(); });
})();
