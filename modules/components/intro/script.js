/* Pantalla de entrada: arranca los diseños (clase is-run + evento intro:start), la cierra a su hora o al saltarla,
 * recuerda la visita (sesión / día) y devuelve el scroll. API para los diseños: Intro.at(ms) = cerrar dentro de ms; Intro.finish(). */
(() => {
  const el = document.getElementById('intro'), root = document.documentElement; if (!el) return;
  if (!root.classList.contains('intro-on')) { el.remove(); return; }
  const still = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const dur = Math.max(1.2, (+el.dataset.dur || 3.5) * (still ? .4 : 1)) * 1000;
  let t = 0, over = false;
  const remember = () => { try { if (el.dataset.freq === 'session') sessionStorage.setItem('intro_seen', '1'); else if (el.dataset.freq === 'day') localStorage.setItem('intro_seen_d', String(Date.now())); } catch (e) {} };
  const finish = () => {
    if (over) return; over = true; clearTimeout(t); remember();
    el.classList.add('is-out'); root.classList.remove('intro-on');
    scrollTo(0, 0);
    setTimeout(() => { el.remove(); dispatchEvent(new Event('intro:done')); }, 1250);
  };
  const at = (ms) => { if (over) return; clearTimeout(t); t = setTimeout(finish, ms); };
  window.Intro = { at, finish };
  if (el.dataset.skip === '1') {
    el.addEventListener('click', finish);
    addEventListener('keydown', (e) => { if (!over && (e.key === 'Escape' || e.key === 'Enter' || e.key === ' ')) { e.preventDefault(); finish(); } });
  }
  requestAnimationFrame(() => requestAnimationFrame(() => { el.classList.add('is-run'); dispatchEvent(new Event('intro:start')); }));
  at(el.dataset.manual === '1' ? 16000 : dur);          // los diseños «manuales» (luz) avisan cuando acaban; 16 s es solo la red de seguridad
})();
