(() => {
  const $ = (s, r = document) => r.querySelector(s);

  // Header translúcido al hacer scroll
  const header = $('#siteHeader');
  const hero = $('[data-parallax] .hero-bg');
  const onScroll = () => {
    const y = window.scrollY;
    header.classList.toggle('scrolled', y > 60);
    if (hero && y < innerHeight) hero.style.setProperty('--py', (y * 0.25) + 'px');
  };
  addEventListener('scroll', onScroll, { passive: true }); onScroll();

  // Aparición progresiva
  const io = new IntersectionObserver((es) => es.forEach(e => {
    if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
  }), { threshold: .08, rootMargin: '0px 0px -5% 0px' });
  document.querySelectorAll('.reveal').forEach(el => io.observe(el));

  // Lightbox
  const grid = $('[data-lightbox]'), lb = $('#lightbox');
  if (!grid || !lb) return;
  const items = [...grid.querySelectorAll('a')];
  const img = $('img', lb), cap = $('figcaption', lb), count = $('.lb-count', lb);
  let idx = 0;

  const show = (i, animate = true) => {
    idx = (i + items.length) % items.length;
    const a = items[idx];
    const set = () => {
      img.src = a.href; cap.textContent = a.dataset.title || '';
      count.textContent = String(idx + 1).padStart(2, '0') + ' / ' + String(items.length).padStart(2, '0');
      img.classList.remove('swap');
    };
    if (animate) { img.classList.add('swap'); setTimeout(set, 180); } else set();
    // precarga vecinas
    [idx + 1, idx - 1].forEach(n => { new Image().src = items[(n + items.length) % items.length].href; });
  };
  const open = (i) => {
    lb.hidden = false; document.body.style.overflow = 'hidden';
    requestAnimationFrame(() => lb.classList.add('open')); show(i, false);
  };
  const close = () => {
    lb.classList.remove('open'); document.body.style.overflow = '';
    setTimeout(() => lb.hidden = true, 450);
  };

  items.forEach((a, i) => a.addEventListener('click', e => { e.preventDefault(); open(i); }));
  $('.lb-close', lb).onclick = close;
  $('.lb-prev', lb).onclick = () => show(idx - 1);
  $('.lb-next', lb).onclick = () => show(idx + 1);
  lb.addEventListener('click', e => { if (e.target === lb) close(); });
  addEventListener('keydown', e => {
    if (lb.hidden) return;
    if (e.key === 'Escape') close();
    if (e.key === 'ArrowRight') show(idx + 1);
    if (e.key === 'ArrowLeft') show(idx - 1);
  });
  // Swipe táctil
  let x0 = null;
  lb.addEventListener('touchstart', e => x0 = e.touches[0].clientX, { passive: true });
  lb.addEventListener('touchend', e => {
    if (x0 === null) return;
    const dx = e.changedTouches[0].clientX - x0;
    if (Math.abs(dx) > 50) show(idx + (dx < 0 ? 1 : -1));
    x0 = null;
  });
})();
