/* Galerías: fijar como primera / destacar desde la tarjeta (sin entrar en la galería). */
document.addEventListener('click', async (e) => {
  const b = e.target.closest('.adg-flag'); if (!b) return;
  e.preventDefault(); e.stopPropagation();
  if (b.disabled) return; b.disabled = true;
  try { await Admin.post(b.dataset.url, { flag: b.dataset.flag }); location.reload(); }
  catch (err) { Admin.toast(err.message, 'error'); b.disabled = false; }
}, true);
