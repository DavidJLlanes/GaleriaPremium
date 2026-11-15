(() => {
  const root = document.querySelector('.hg');
  if (!root) return;
  const slides = [...root.querySelectorAll('.hg-slide')];
  const dots = [...document.querySelectorAll('.hg-dots i')];
  if (slides.length < 2) return;
  const ms = parseInt(root.dataset.interval, 10) || 6500;
  let i = 0, timer;

  const go = (n) => {
    slides[i].classList.remove('is-active'); dots[i]?.classList.remove('is-active');
    i = (n + slides.length) % slides.length;
    slides[i].classList.add('is-active'); dots[i]?.classList.add('is-active');
  };
  const start = () => { stop(); timer = setInterval(() => go(i + 1), ms); };
  const stop = () => clearInterval(timer);

  dots[0]?.classList.add('is-active');
  dots.forEach((d, n) => { d.style.pointerEvents = 'auto'; d.style.cursor = 'pointer'; d.onclick = () => { go(n); start(); }; });
  document.addEventListener('visibilitychange', () => document.hidden ? stop() : start());
  start();
})();
