/* Previews and fixed-ratio Open Graph crop selection. */
(() => {
  document.querySelectorAll('.st-asset input[type=file]').forEach(inp => inp.addEventListener('change', () => {
    const f = inp.files[0]; if (!f) return;
    const box = inp.closest('.st-asset').querySelector('.st-prev');
    if (inp.id !== 'ogFile') {
      box.innerHTML = ''; const im = new Image(); im.src = URL.createObjectURL(f); box.appendChild(im); return;
    }
    const url = URL.createObjectURL(f), im = new Image();
    im.onload = () => window.Cropper.open({
      src: url, iw: im.naturalWidth, ih: im.naturalHeight, ratio: 1200 / 630,
      title: 'Recortar imagen Open Graph',
      hint: 'Ajusta el área que aparecerá al compartir la web (1200 × 630).',
      onSave: crop => {
        document.getElementById('ogCrop').value = JSON.stringify(crop);
        box.innerHTML = ''; const preview = new Image(); preview.src = url;
        preview.style.width = '100%'; preview.style.height = '100%'; preview.style.objectFit = 'cover'; preview.style.aspectRatio = '1200 / 630'; box.appendChild(preview);
      }
    });
    im.src = url;
  }));
})();
