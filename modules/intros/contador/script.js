/* Entrada «Contador»: cuenta de 0 a 100 durante ~80 % del tiempo y entonces muestra el nombre. */
(() => {
  const root = document.querySelector('.ino'), intro = document.getElementById('intro'); if (!root || !intro) return;
  const n = root.querySelector('.ino-n'); let started = false;
  const go = () => {
    if (started) return; started = true;
    const d = (parseFloat(getComputedStyle(intro).getPropertyValue('--in-d')) || 3.5) * 800, t0 = performance.now();
    const named = root.classList.contains('ino--name'), end = () => { root.classList.add('is-shown'); window.Intro && window.Intro.at(named ? 1900 : 450); };
    if (matchMedia('(prefers-reduced-motion:reduce)').matches) { n.textContent = '100'; end(); return; }
    const tick = (t) => {
      const k = Math.min(1, (t - t0) / d), e = 1 - Math.pow(1 - k, 2.2);
      n.textContent = Math.round(e * 100);
      if (k < 1) requestAnimationFrame(tick); else end();
    };
    requestAnimationFrame(tick);
  };
  intro.classList.contains('is-run') ? go() : addEventListener('intro:start', go);
})();
