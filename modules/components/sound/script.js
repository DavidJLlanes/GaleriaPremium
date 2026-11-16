/* Sonidos suaves sintetizados (sin archivos de audio). Escucha eventos de la web: mn:open, mn:close, lb:open, lb:change.
   El contexto de audio se crea con el primer gesto del usuario (política de los navegadores). */
(() => {
  if (!document.querySelector('.sfx-cfg')) return;
  let ctx = null;
  const tone = (f1, f2, dur, vol) => {
    try {
      ctx = ctx || new (window.AudioContext || window.webkitAudioContext)();
      if (ctx.state === 'suspended') ctx.resume();
      const t = ctx.currentTime, o = ctx.createOscillator(), g = ctx.createGain(), lp = ctx.createBiquadFilter();
      o.type = 'sine'; o.frequency.setValueAtTime(f1, t); o.frequency.exponentialRampToValueAtTime(f2, t + dur);
      lp.type = 'lowpass'; lp.frequency.value = 2200;
      g.gain.setValueAtTime(0, t); g.gain.linearRampToValueAtTime(vol, t + .012); g.gain.exponentialRampToValueAtTime(.0001, t + dur);
      o.connect(lp); lp.connect(g); g.connect(ctx.destination); o.start(t); o.stop(t + dur + .02);
    } catch { /* sin audio disponible */ }
  };
  const S = { 'mn:open': () => tone(420, 640, .16, .05), 'mn:close': () => tone(520, 330, .14, .04), 'lb:open': () => tone(360, 540, .2, .045), 'lb:change': () => tone(880, 700, .07, .03) };
  Object.keys(S).forEach(k => addEventListener(k, S[k]));
})();
