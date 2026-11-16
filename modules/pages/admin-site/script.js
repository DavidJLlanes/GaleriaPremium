/* Previews and fixed-ratio Open Graph crop selection. */
(() => {
  document.querySelectorAll('.st-asset input[type=file]').forEach(inp => inp.addEventListener('change', () => {
    const f = inp.files[0]; if (!f) return;
    const box = inp.closest('.st-asset').querySelector('.st-prev');
    if (inp.id !== 'ogFile') {
      box.innerHTML = ''; const im = new Image(); im.src = URL.createObjectURL(f); box.appendChild(im); return;
    }
    const url = URL.createObjectURL(f), im = new Image();
    const R = 1200 / 630;
    im.onload = () => window.Cropper.open({
      src: url, iw: im.naturalWidth, ih: im.naturalHeight, ratio: R, fixed: true,
      title: 'Recortar imagen Open Graph',
      hint: 'El recuadro tiene siempre la proporción 1,91:1 (1200 × 630). Muévelo y ajusta el zoom.',
      onCancel: () => { inp.value = ''; document.getElementById('ogCrop').value = ''; },
      onSave: crop => {
        document.getElementById('ogCrop').value = JSON.stringify(crop);
        // Vista previa del recorte elegido (exactamente lo que se guardará)
        const cv = document.createElement('canvas'); cv.width = 600; cv.height = 315;
        cv.getContext('2d').drawImage(im, crop.x * im.naturalWidth, crop.y * im.naturalHeight, crop.w * im.naturalWidth, crop.h * im.naturalHeight, 0, 0, 600, 315);
        cv.style.cssText = 'max-width:90%;max-height:100px;aspect-ratio:1200/630;border-radius:3px';
        box.innerHTML = ''; box.appendChild(cv);
      }
    });
    im.src = url;
  }));
})();
