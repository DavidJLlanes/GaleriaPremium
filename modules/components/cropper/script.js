/* Editor de recorte: window.Cropper.open({ src, iw, ih, crop, ratio, title, hint, onSave }).
   crop = {x,y,w,h} en fracciones de la foto original. ratio = proporción (ancho/alto en píxeles) o null = libre; fixed = true la bloquea del todo (sin presets ni «Libre»); onCancel se llama al cerrar sin guardar. */
(() => {
  const el = document.getElementById('cropper'); if (!el) return;
  const $ = (s) => el.querySelector(s);
  const stage = $('.cr-stage'), img = $('.cr-img'), rect = $('.cr-rect'), info = $('.cr-info'), zoom = $('.cr-zoom input');
  const presets = $('.cr-presets'), saveBtn = $('.cr-save');
  let st, lock, fixed = false, iw, ih, dw, dh, onSave, onCancel, saved = false, mode = null, drag = null;

  const clamp = (v, a, b) => Math.max(a, Math.min(b, v));
  const ratioOf = (c) => (c.w * iw) / (c.h * ih);
  const fit = (R, prev) => {
    R = clamp(R, .2, 5); let w, h;
    if (iw / ih >= R) { h = 1; w = R * ih / iw; } else { w = 1; h = iw / (R * ih); }
    const cx = prev ? prev.x + prev.w / 2 : .5, cy = prev ? prev.y + prev.h / 2 : .5;
    return { x: clamp(cx - w / 2, 0, 1 - w), y: clamp(cy - h / 2, 0, 1 - h), w, h };
  };

  const draw = () => {
    rect.style.left = st.x * dw + 'px'; rect.style.top = st.y * dh + 'px'; rect.style.width = st.w * dw + 'px'; rect.style.height = st.h * dh + 'px';
    const pw = Math.round(st.w * iw), ph = Math.round(st.h * ih);
    info.textContent = `${pw}×${ph} px · ${(ratioOf(st)).toFixed(2)}:1`;
    if (lock) zoom.value = Math.round(st.w / fit(lock).w * 100);
  };

  const close = () => { el.hidden = true; document.body.style.overflow = ''; if (!saved) onCancel?.(); };

  const open = (o) => {
    iw = o.iw; ih = o.ih; lock = o.ratio || null; fixed = !!o.fixed; onSave = o.onSave; onCancel = o.onCancel; saved = false;
    $('.cr-title').textContent = o.title || 'Recortar'; $('.cr-hint').textContent = o.hint || '';
    presets.hidden = !!lock || fixed; presets.querySelectorAll('button').forEach(b => b.classList.toggle('is-on', b.dataset.r === 'free'));
    zoom.parentElement.hidden = !lock;
    el.hidden = false; document.body.style.overflow = 'hidden';
    img.onload = () => {
      const k = Math.min(Math.min(innerWidth - 80, 900) / iw, (innerHeight * .56) / ih);
      dw = Math.round(iw * k); dh = Math.round(ih * k); img.style.width = dw + 'px'; img.style.height = dh + 'px';
      stage.style.width = dw + 'px'; stage.style.height = dh + 'px';
      st = o.crop ? { ...o.crop } : (lock ? fit(lock) : { x: .05, y: .05, w: .9, h: .9 });
      if (lock && Math.abs(ratioOf(st) / lock - 1) > .01) st = fit(lock, st);
      draw();
    };
    img.src = o.src;
  };

  // ---- interacción ----
  const pt = (e) => { const r = stage.getBoundingClientRect(); return { x: clamp((e.clientX - r.left) / dw, 0, 1), y: clamp((e.clientY - r.top) / dh, 0, 1) }; };

  rect.addEventListener('pointerdown', e => {
    e.preventDefault(); rect.setPointerCapture(e.pointerId);
    const h = e.target.closest('[data-h]')?.dataset.h;
    if (h) {
      const west = h.includes('w'), north = h.startsWith('n');
      mode = 'resize'; drag = { sx: west ? -1 : 1, sy: north ? -1 : 1, ax: west ? st.x + st.w : st.x, ay: north ? st.y + st.h : st.y };
    } else { mode = 'move'; const p = pt(e); drag = { dx: p.x - st.x, dy: p.y - st.y }; }
  });
  rect.addEventListener('pointermove', e => {
    if (!mode) return; const p = pt(e);
    if (mode === 'move') { st.x = clamp(p.x - drag.dx, 0, 1 - st.w); st.y = clamp(p.y - drag.dy, 0, 1 - st.h); }
    else {
      const { sx, sy, ax, ay } = drag;
      let w = Math.max(0, (p.x - ax) * sx), h = Math.max(0, (p.y - ay) * sy);
      const roomW = sx > 0 ? 1 - ax : ax, roomH = sy > 0 ? 1 - ay : ay;
      if (lock) {
        const k = lock * ih / iw;                     // ancho(fracción) = alto(fracción) · k
        w = Math.max(w, h * k); w = Math.min(w, roomW, roomH * k); w = Math.max(w, .04); h = w / k;
      } else { w = Math.max(.04, Math.min(w, roomW)); h = Math.max(.04, Math.min(h, roomH)); }
      st = { x: sx > 0 ? ax : ax - w, y: sy > 0 ? ay : ay - h, w, h };
    }
    draw();
  });
  const stop = () => { mode = null; drag = null; };
  rect.addEventListener('pointerup', stop); rect.addEventListener('pointercancel', stop);

  zoom.addEventListener('input', () => {
    if (!lock) return; const max = fit(lock), w = Math.max(.05, max.w * zoom.value / 100), h = w * iw / (lock * ih);
    const cx = st.x + st.w / 2, cy = st.y + st.h / 2;
    st = { w, h, x: clamp(cx - w / 2, 0, 1 - w), y: clamp(cy - h / 2, 0, 1 - h) }; draw();
  });
  presets.addEventListener('click', e => {
    const b = e.target.closest('button'); if (!b || fixed) return;
    presets.querySelectorAll('button').forEach(x => x.classList.toggle('is-on', x === b));
    if (b.dataset.r === 'free') { lock = null; return; }
    lock = parseFloat(b.dataset.r); st = fit(lock, st); draw();
  });

  $('.cr-x').onclick = close; $('.cr-cancel').onclick = close;
  el.addEventListener('click', e => { if (e.target === el) close(); });
  addEventListener('keydown', e => { if (!el.hidden && e.key === 'Escape') close(); });
  saveBtn.onclick = async () => {
    saveBtn.disabled = true;
    try { await onSave?.({ ...st }); saved = true; close(); } catch (err) { window.Admin?.toast(err.message, 'error'); } finally { saveBtn.disabled = false; }
  };

  window.Cropper = { open };

  // ---- enganche con el panel: botones «Recortar» de cada foto ----
  document.addEventListener('click', e => {
    const b = e.target.closest('[data-crop-open]'); if (!b || !window.Admin) return;
    const row = b.closest('.pr'), list = row.parentElement, first = list.querySelector('.pr') === row;
    const ratio = first ? null : (parseFloat(list.dataset.ratio) || null);
    open({
      src: row.dataset.src, iw: +row.dataset.iw, ih: +row.dataset.ih, crop: row.dataset.crop ? JSON.parse(row.dataset.crop) : null, ratio,
      title: (first ? 'Recorte de la primera foto' : 'Recortar «' + (row.querySelector('.t')?.value || 'foto') + '»'),
      hint: first ? 'Esta foto define la proporción de TODA la galería. Elige un formato o dibuja el recorte libre.'
                  : 'La proporción es la de la primera foto. Mueve el recuadro y ajusta el zoom.',
      onSave: async (c) => {
        const r = await Admin.post(`/admin/foto/${row.dataset.id}/recorte`, c);
        Admin.toast(r.first ? 'Proporción actualizada: se reajustan las demás fotos' : 'Recorte guardado');
        setTimeout(() => location.reload(), 500);
      },
    });
  });
})();
