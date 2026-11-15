/* Vista previa inmediata de los archivos elegidos (antes de guardar). */
(() => {
  document.querySelectorAll('.st-asset input[type=file]').forEach(inp => inp.addEventListener('change', () => {
    const f = inp.files[0]; if (!f) return;
    const box = inp.closest('.st-asset').querySelector('.st-prev');
    box.innerHTML = ''; const im = new Image(); im.src = URL.createObjectURL(f); box.appendChild(im);
  }));
})();
