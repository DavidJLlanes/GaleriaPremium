(() => {
  const track = document.querySelector('.gf-track');
  const bar = document.querySelector('.gf-bar i');
  if (!track) return;

  // La rueda del ratón desplaza en horizontal mientras haya recorrido
  track.addEventListener('wheel', (e) => {
    if (Math.abs(e.deltaY) <= Math.abs(e.deltaX)) return;
    const max = track.scrollWidth - track.clientWidth;
    const atStart = track.scrollLeft <= 0 && e.deltaY < 0;
    const atEnd = track.scrollLeft >= max - 1 && e.deltaY > 0;
    if (atStart || atEnd) return;
    e.preventDefault();
    track.scrollLeft += e.deltaY;
  }, { passive: false });

  // Arrastrar con el ratón
  let down = false, sx = 0, sl = 0, moved = false;
  track.addEventListener('pointerdown', e => { if (e.pointerType !== 'mouse') return; down = true; moved = false; sx = e.clientX; sl = track.scrollLeft; });
  addEventListener('pointerup', () => { down = false; track.style.cursor = ''; });
  addEventListener('pointermove', e => {
    if (!down) return;
    const dx = e.clientX - sx;
    if (Math.abs(dx) > 5) { moved = true; track.style.cursor = 'grabbing'; }
    track.scrollLeft = sl - dx;
  });
  track.addEventListener('click', e => { if (moved) { e.preventDefault(); e.stopPropagation(); moved = false; } }, true);

  const progress = () => {
    const max = track.scrollWidth - track.clientWidth;
    bar.style.width = (max > 0 ? (track.scrollLeft / max) * 100 : 100) + '%';
  };
  track.addEventListener('scroll', progress, { passive: true });
  progress();
})();
