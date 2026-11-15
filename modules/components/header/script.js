(() => {
  const header = document.getElementById('siteHeader');
  if (!header) return;
  const update = () => header.classList.toggle('is-scrolled', scrollY > 60);
  addEventListener('scroll', update, { passive: true });
  update();
})();
