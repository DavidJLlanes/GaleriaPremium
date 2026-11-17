/* API común del panel: window.Admin.{post, toast}. La usan los componentes del admin. */
(() => {
  const csrf = document.querySelector('meta[name=csrf]')?.content || '';

  const toast = (msg, type = 'ok') => {
    const box = document.getElementById('adToasts'); if (!box) return;
    const t = document.createElement('div'); t.className = 'ad-toast is-' + type; t.textContent = msg;
    box.appendChild(t); setTimeout(() => t.remove(), type === 'error' ? 7000 : 3500);
  };

  /** POST con CSRF. data: objeto o FormData. Devuelve el JSON (lanza Error con el mensaje del servidor). */
  const post = async (url, data = {}) => {
    const fd = data instanceof FormData ? data : Object.entries(data).reduce((f, [k, v]) => (f.append(k, v), f), new FormData());
    const r = await fetch(url, { method: 'POST', body: fd, headers: { 'X-CSRF-Token': csrf, 'Accept': 'application/json' } });
    let j = null; try { j = await r.json(); } catch { /* respuesta no JSON */ }
    if (!r.ok || !j?.ok) throw Object.assign(new Error(j?.error || `Error ${r.status}`), { data: j });
    return j;
  };

  // Selector de archivos en español (el texto nativo depende del idioma del navegador)
  document.querySelectorAll('input[type=file]:not([hidden])').forEach(inp => {
    const wrap = document.createElement('div'); wrap.className = 'ad-file';
    const btn = document.createElement('button'); btn.type = 'button'; btn.className = 'ad-btn is-ghost is-small'; btn.textContent = 'Elegir archivo';
    const name = document.createElement('span'); name.textContent = 'Ningún archivo seleccionado';
    inp.before(wrap); wrap.append(btn, name); inp.hidden = true;
    btn.onclick = () => inp.click();
    inp.addEventListener('change', () => { name.textContent = inp.files[0]?.name || 'Ningún archivo seleccionado'; });
  });

  window.Admin = { csrf, toast, post };
})();
// En pantallas estrechas la navegación se desplaza: deja a la vista la sección activa
(() => {
  const nav = document.querySelector('.ad-nav'), on = nav && nav.querySelector('.is-on');
  if (on && nav.scrollWidth > nav.clientWidth) nav.scrollLeft = on.offsetLeft - (nav.clientWidth - on.offsetWidth) / 2;
})();
