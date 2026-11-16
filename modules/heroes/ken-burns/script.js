(() => {
  const hero = document.querySelector('.hx-kenburns'); if (!hero) return;
  // Título: cada letra en su <span> (con aria-label en el h1 para lectores de pantalla)
  const h1 = hero.querySelector('.herocopy h1');
  if (h1) {
    const text = h1.textContent; let i = 0; h1.setAttribute('aria-label', text); h1.textContent = '';
    text.split(/(\s+)/).forEach(part => {
      if (/^\s+$/.test(part)) { h1.append(' '); return; }
      const w = document.createElement('span'); w.className = 'hxk-w'; w.setAttribute('aria-hidden', 'true');
      for (const ch of part) { const l = document.createElement('span'); l.className = 'hxk-l'; l.style.setProperty('--i', i++); l.textContent = ch; w.appendChild(l); }
      h1.appendChild(w);
    });
    hero.style.setProperty('--n', i);
  }
  // Pase de fotos con fundido; cada foto tiene su propio movimiento
  const box = hero.querySelector('.hxk'), slides = [...hero.querySelectorAll('.hxk-slide')];
  const ms = parseInt(box.dataset.interval, 10) || 6500; box.style.setProperty('--hxk-t', (ms * 1.5 / 1000) + 's');
  if (slides.length < 2) return;
  let n = 0, timer; const start = () => { clearInterval(timer); timer = setInterval(() => {
    slides[n].classList.remove('is-active'); n = (n + 1) % slides.length; slides[n].classList.add('is-active');
    const im = slides[n].querySelector('img'); im.style.animation = 'none'; void im.offsetWidth; im.style.animation = '';   // reinicia el movimiento
  }, ms); };
  document.addEventListener('visibilitychange', () => document.hidden ? clearInterval(timer) : start()); start();
})();
