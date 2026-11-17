(() => {
  const csrf = document.querySelector('meta[name=csrf]')?.content || '';
  const dec = s => { s = s.replace(/-/g, '+').replace(/_/g, '/'); s += '='.repeat((4 - s.length % 4) % 4); const b = atob(s), u = new Uint8Array(b.length); for (let i = 0; i < b.length; i++) u[i] = b.charCodeAt(i); return u.buffer; };
  const enc = buf => { const u = new Uint8Array(buf); let s = ''; u.forEach(c => s += String.fromCharCode(c)); return btoa(s).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, ''); };
  const post = async (url, data) => {
    const r = await fetch(url, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-Token': csrf }, body: JSON.stringify(data || {}) });
    const j = await r.json().catch(() => ({}));
    if (!r.ok || !j.ok) throw new Error(j.error || 'No se pudo completar la operación.');
    return j;
  };
  const supported = !!(window.PublicKeyCredential && navigator.credentials && window.isSecureContext);
  const say = (el, t, err) => { if (!el) return; el.textContent = t || ''; el.classList.toggle('is-err', !!err); };
  const human = e => e && (e.name === 'NotAllowedError' || e.name === 'AbortError') ? 'Cancelado o sin tiempo. Inténtalo de nuevo.'
    : e && e.name === 'InvalidStateError' ? 'Este dispositivo ya está registrado.' : (e && e.message) || 'Algo ha fallado.';

  document.querySelectorAll('[data-passkey-login]').forEach(btn => {
    const box = btn.closest('[data-passkey-box]') || btn.parentElement, msg = box.querySelector('.pk-msg');
    if (!supported) { box.hidden = true; return; }
    btn.addEventListener('click', async () => {
      btn.disabled = true; say(msg, '');
      try {
        const { publicKey: o } = await post('/admin/passkey/entrar/opciones');
        const cred = await navigator.credentials.get({ publicKey: { challenge: dec(o.challenge), rpId: o.rpId, timeout: o.timeout, userVerification: o.userVerification, allowCredentials: [] } });
        const r = cred.response;
        const res = await post('/admin/passkey/entrar', { id: cred.id, clientDataJSON: enc(r.clientDataJSON), authenticatorData: enc(r.authenticatorData), signature: enc(r.signature) });
        location.href = res.redirect || '/admin';
      } catch (e) { say(msg, human(e), true); btn.disabled = false; }
    });
  });

  document.querySelectorAll('[data-passkey-add]').forEach(btn => {
    const box = btn.closest('[data-passkey-box]') || btn.parentElement, msg = box.querySelector('.pk-msg'), name = box.querySelector('[data-passkey-name]');
    if (!supported) { btn.disabled = true; say(msg, 'Este navegador o conexión no admite passkeys (hace falta HTTPS).', true); return; }
    btn.addEventListener('click', async () => {
      btn.disabled = true; say(msg, '');
      try {
        const { publicKey: o } = await post('/admin/passkey/registro/opciones');
        o.challenge = dec(o.challenge); o.user.id = dec(o.user.id);
        o.excludeCredentials = (o.excludeCredentials || []).map(id => ({ type: 'public-key', id: dec(id) }));
        const cred = await navigator.credentials.create({ publicKey: o });
        const r = cred.response;
        if (!r.getPublicKey) throw new Error('Este navegador es demasiado antiguo para registrar passkeys.');
        await post('/admin/passkey/registro', { id: cred.id, clientDataJSON: enc(r.clientDataJSON), publicKey: enc(r.getPublicKey()), alg: r.getPublicKeyAlgorithm(), name: name ? name.value : '' });
        location.reload();
      } catch (e) { say(msg, human(e), true); btn.disabled = false; }
    });
  });
})();
