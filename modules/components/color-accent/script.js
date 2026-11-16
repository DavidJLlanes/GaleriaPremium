/* Junto a TODO selector de color del panel aparece «Color de acento»: un clic y el color pasa a ser el acento de la web
 * (el que se está editando en Sitio si estás en esa página, y si no, el guardado). Se dispara «input» y «change»,
 * así funcionan las vistas previas en vivo. El propio selector del acento no lo lleva (data-no-accent). */
(() => {
  const accent = () => {
    const live = document.querySelector('input[type=color][name="site_accent"]');
    return (live && live.value) || document.body.dataset.accent || '#d8b36a';
  };
  const enhance = (inp) => {
    if (inp._ca || inp.hasAttribute('data-no-accent') || inp.name === 'site_accent') return; inp._ca = true;
    const wrap = document.createElement('span'); wrap.className = 'ca';
    const btn = document.createElement('button'); btn.type = 'button'; btn.className = 'ca-btn'; btn.title = 'Usar el color de acento de la web';
    const dot = document.createElement('i'); btn.append(dot, 'Color de acento');
    inp.before(wrap); wrap.append(inp, btn);
    const sync = () => { dot.style.background = accent(); btn.classList.toggle('is-on', inp.value.toLowerCase() === accent().toLowerCase()); };
    btn.addEventListener('click', () => { inp.value = accent(); inp.dispatchEvent(new Event('input', { bubbles: true })); inp.dispatchEvent(new Event('change', { bubbles: true })); sync(); });
    inp.addEventListener('input', sync); document.addEventListener('input', (e) => { if (e.target.name === 'site_accent') sync(); }); sync();
  };
  const run = () => document.querySelectorAll('input[type=color]').forEach(enhance);
  new MutationObserver(run).observe(document.documentElement, { childList: true, subtree: true });
  document.readyState === 'loading' ? addEventListener('DOMContentLoaded', run) : run();
})();
