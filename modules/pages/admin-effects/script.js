(() => {
  const form = document.querySelector('form[action="/admin/efectos"]'); if (!form) return;
  const sync = () => { const sel = form.querySelector('[name="fx[cursor]"]:checked')?.value; form.querySelectorAll('[data-cur-opts]').forEach(c => { c.hidden = c.dataset.curOpts !== sel; }); };
  form.addEventListener('change', sync); sync();
})();
