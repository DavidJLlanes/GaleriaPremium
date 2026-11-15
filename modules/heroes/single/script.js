(() => {
  const img = document.querySelector('.hs-img');
  if (!img) return;
  const f = () => { if (scrollY < innerHeight) img.style.setProperty('--py', (scrollY * 0.25) + 'px'); };
  addEventListener('scroll', f, { passive: true }); f();
})();
