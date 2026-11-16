/* Modo «coverflow»: coloca cada foto según su distancia a la activa (con vuelta al principio si «volver a la primera» está activo). */
(() => {
  (window.SliderModes = window.SliderModes || {}).coverflow = {
    init(s) {
      s.noSwipe = false;
      s.slides.forEach((a, i) => a.addEventListener('click', (e) => { if (!a.classList.contains('is-active')) { e.preventDefault(); e.stopPropagation(); s.go(i, i > s.i ? 1 : -1); } }, true));
      this.place(s);
    },
    place(s) {
      const n = s.n;
      s.slides.forEach((a, i) => {
        let o = i - s.i; if (s.loop && n > 2) { if (o > n / 2) o -= n; else if (o < -n / 2) o += n; }
        a.style.setProperty('--o', o); a.style.setProperty('--a', Math.abs(o));
        a.classList.toggle('is-active', o === 0); a.toggleAttribute('data-far', Math.abs(o) > 2);
      });
    },
    show(s) { this.place(s); s.lock = 450; },
  };
})();
