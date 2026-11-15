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
    if (!r.ok || !j?.ok) throw new Error(j?.error || `Error ${r.status}`);
    return j;
  };

  window.Admin = { csrf, toast, post };
})();
