/* Modo «track»: la pista se desplaza hasta la foto activa sin dejar hueco al final. */
(() => {
  (window.SliderModes = window.SliderModes || {}).track = {
    init(s) { s.tr = s.root.querySelector('.sl-track'); s.vw = s.root.querySelector('.sl-view'); s.noSwipe = false; addEventListener('resize', () => this.place(s, true)); this.place(s, true); },
    place(s, instant) {
      const a = s.slides[0], b = s.slides[1] || a, step = b.offsetLeft - a.offsetLeft || a.offsetWidth;
      const max = Math.max(0, s.tr.scrollWidth - s.vw.clientWidth), x = Math.min(s.i * step, max);
      if (instant) { s.tr.style.transition = 'none'; }
      s.tr.style.transform = `translate3d(${-x}px,0,0)`;
      if (instant) { void s.tr.offsetWidth; s.tr.style.transition = ''; }
    },
    show(s) { this.place(s); s.lock = 500; },
  };
})();
