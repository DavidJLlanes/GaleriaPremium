/* Lumina: transición WebGL "cristal" entre fotos (sin librerías). Textura actual + vecinas en memoria (LRU de 3). */
(() => {
  const root = document.querySelector('.gl');
  if (!root) return;
  const $ = (s) => root.querySelector(s);
  const canvas = $('.gl-canvas'), items = [...root.querySelectorAll('.gl-item')];
  const titleEl = $('.gl-title'), descEl = $('.gl-desc'), curEl = $('.gl-cur'), fb = [...root.querySelectorAll('.gl-fallback img')];
  const lbLinks = [...root.querySelectorAll('.gl-lbsrc a')];
  const N = items.length; if (!N) return;
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const AUTO = 6000, DUR = reduce ? 300 : 2300;

  let cur = 0, busy = false, timer = 0, gl = null, prog, uni = {}, tex = new Map(), center = [.5, .5], hidden = false;
  const url = (i) => items[i].dataset.src;
  const ease = (t) => t < .5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
  const pad = (v) => String(v).padStart(2, '0');

  // ---------- WebGL ----------
  const VS = 'attribute vec2 a;varying vec2 v;void main(){v=a*.5+.5;gl_Position=vec4(a,0.,1.);}';
  const FS = `precision highp float;varying vec2 v;uniform sampler2D t1,t2;uniform float p;uniform vec2 res,s1,s2,ctr;
    vec2 cover(vec2 uv,vec2 ts){vec2 s=res/ts;float k=max(s.x,s.y);vec2 ss=ts*k;return (uv*res-(res-ss)*.5)/ss;}
    void main(){
      vec2 px=v*res,c=ctr*res;float d=length(px-c);
      float R=length(max(c,res-c))*1.08,br=p*R,nd=d/max(br,.001);
      float inside=smoothstep(br+2.5,br-2.5,d);
      vec2 dir=d>0.?(px-c)/d:vec2(0.);
      float rim=smoothstep(.45,1.,nd),k=.05*pow(rim,2.2);
      vec2 u2=cover(v,s2)-dir*k*vec2(res.y/res.x,1.);
      u2+=vec2(sin(p*6.+nd*5.),cos(p*5.+nd*4.))*.006*rim;
      float ca=.008*pow(smoothstep(.5,1.,nd),1.5);
      vec3 n=vec3(texture2D(t2,u2+dir*ca*1.2).r,texture2D(t2,u2+dir*ca*.2).g,texture2D(t2,u2-dir*ca*.8).b);
      n+=smoothstep(.94,1.,nd)*(1.-smoothstep(1.,1.012,nd))*.1;
      vec3 o=texture2D(t1,cover(v,s1)).rgb;
      gl_FragColor=vec4(mix(o,n,inside),1.);}`;

  const initGL = () => {
    gl = canvas.getContext('webgl', { antialias: false, alpha: false, powerPreference: 'high-performance' });
    if (!gl) return false;
    const sh = (t, s) => { const o = gl.createShader(t); gl.shaderSource(o, s); gl.compileShader(o); return gl.getShaderParameter(o, gl.COMPILE_STATUS) ? o : null; };
    const vs = sh(gl.VERTEX_SHADER, VS), fs = sh(gl.FRAGMENT_SHADER, FS); if (!vs || !fs) return false;
    prog = gl.createProgram(); gl.attachShader(prog, vs); gl.attachShader(prog, fs); gl.linkProgram(prog);
    if (!gl.getProgramParameter(prog, gl.LINK_STATUS)) return false;
    gl.useProgram(prog);
    gl.bindBuffer(gl.ARRAY_BUFFER, gl.createBuffer());
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 1, -1, -1, 1, 1, 1]), gl.STATIC_DRAW);
    const a = gl.getAttribLocation(prog, 'a'); gl.enableVertexAttribArray(a); gl.vertexAttribPointer(a, 2, gl.FLOAT, false, 0, 0);
    ['t1', 't2', 'p', 'res', 's1', 's2', 'ctr'].forEach(n => uni[n] = gl.getUniformLocation(prog, n));
    gl.uniform1i(uni.t1, 0); gl.uniform1i(uni.t2, 1);
    gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, true);
    return true;
  };

  const resize = () => {
    const dpr = Math.min(devicePixelRatio || 1, 2), w = root.clientWidth, h = root.clientHeight;
    canvas.width = Math.round(w * dpr); canvas.height = Math.round(h * dpr);
    if (gl) { gl.viewport(0, 0, canvas.width, canvas.height); gl.uniform2f(uni.res, canvas.width, canvas.height); draw(prog_p, cur, cur); }
  };

  const loadTex = (i) => {
    if (tex.has(i)) return tex.get(i);
    const pr = new Promise((res, rej) => {
      const img = new Image();
      img.onload = () => {
        const t = gl.createTexture(); gl.bindTexture(gl.TEXTURE_2D, t);
        gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGB, gl.RGB, gl.UNSIGNED_BYTE, img);
        [[gl.TEXTURE_MIN_FILTER, gl.LINEAR], [gl.TEXTURE_MAG_FILTER, gl.LINEAR], [gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE], [gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE]]
          .forEach(([k, v]) => gl.texParameteri(gl.TEXTURE_2D, k, v));
        res({ t, w: img.naturalWidth, h: img.naturalHeight });
      };
      img.onerror = rej; img.src = url(i);
    });
    tex.set(i, pr); return pr;
  };
  const keep = (...ids) => {                     // solo se conservan las texturas cercanas
    const ok = new Set(ids.map(i => (i + N) % N));
    tex.forEach((pr, i) => { if (!ok.has(i)) { tex.delete(i); pr.then(o => gl.deleteTexture(o.t)).catch(() => {}); } });
  };

  let prog_p = 0;
  const draw = async (p, from, to) => {
    if (!gl) return;
    const [a, b] = await Promise.all([loadTex(from), loadTex(to)]);
    prog_p = p;
    gl.activeTexture(gl.TEXTURE0); gl.bindTexture(gl.TEXTURE_2D, a.t);
    gl.activeTexture(gl.TEXTURE1); gl.bindTexture(gl.TEXTURE_2D, b.t);
    gl.uniform1f(uni.p, p); gl.uniform2f(uni.s1, a.w, a.h); gl.uniform2f(uni.s2, b.w, b.h); gl.uniform2f(uni.ctr, center[0], center[1]);
    gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4);
  };

  // ---------- Texto ----------
  const split = (t) => [...t].map(c => `<span style="opacity:0">${c === ' ' ? '&nbsp;' : c.replace(/[&<>]/g, x => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[x]))}</span>`).join('');
  const FX = [   // 6 entradas distintas, rota con el índice
    { from: 'translateY(22px)' }, { from: 'translateY(-22px) scale(.9)' }, { from: 'scale(1.6)', blur: 10 },
    { from: 'scale(0)' }, { from: 'rotateX(90deg)' }, { from: 'translateX(30px)' },
  ];
  const animIn = (i) => {
    const fx = FX[i % FX.length], spans = [...titleEl.children];
    spans.forEach((s, k) => s.animate(
      [{ opacity: 0, transform: fx.from, filter: `blur(${fx.blur || 0}px)` }, { opacity: 1, transform: 'none', filter: 'blur(0)' }],
      { duration: reduce ? 1 : 800, delay: reduce ? 0 : (fx.blur ? Math.random() * 450 : k * 32), easing: 'cubic-bezier(.22,1,.36,1)', fill: 'forwards' }));
    descEl.animate([{ opacity: 0, transform: 'translateY(18px)' }, { opacity: 1, transform: 'none' }], { duration: reduce ? 1 : 900, delay: reduce ? 0 : 220, easing: 'cubic-bezier(.22,1,.36,1)', fill: 'forwards' });
  };
  const setText = (i, first) => {
    const go = () => {
      titleEl.innerHTML = split(items[i].dataset.title || ''); descEl.textContent = items[i].dataset.desc || '';
      descEl.style.opacity = 0; animIn(i);
    };
    if (first || reduce) return go();
    [...titleEl.children].forEach((s, k) => s.animate([{ opacity: 1 }, { opacity: 0, transform: 'translateY(-16px)' }], { duration: 380, delay: k * 12, fill: 'forwards' }));
    descEl.animate([{ opacity: 1 }, { opacity: 0 }], { duration: 300, fill: 'forwards' });
    setTimeout(go, 460);
  };

  // ---------- Navegación ----------
  const setNav = (i) => {
    items.forEach((el, k) => { el.classList.toggle('is-on', k === i); el.querySelector('i').style.width = '0'; });
    curEl.textContent = pad(i + 1);
    const nav = $('.gl-nav'), el = items[i];
    if (nav.scrollHeight > nav.clientHeight && getComputedStyle(nav).display === 'grid') nav.scrollTo({ top: el.offsetTop - nav.clientHeight / 2, behavior: 'smooth' });
  };
  const fill = (i, pct) => { const b = items[i].querySelector('i'); b.style.transition = 'none'; b.style.width = pct + '%'; };

  const goTo = async (to, origin) => {
    to = (to + N) % N;
    if (busy || to === cur) return;
    busy = true; stopTimer(); center = origin || [.5, .5];
    const from = cur; cur = to; setNav(to); setText(to);
    if (!gl) { fb.forEach((im, k) => im.classList.toggle('is-on', k === to)); setTimeout(() => { busy = false; startTimer(); }, 900); return; }
    try { await Promise.all([loadTex(from), loadTex(to)]); } catch { busy = false; return; }
    const t0 = performance.now();
    await new Promise(done => {
      const step = (now) => {
        const t = Math.min(1, (now - t0) / DUR);
        draw(ease(t), from, to);
        t < 1 ? requestAnimationFrame(step) : done();
      };
      requestAnimationFrame(step);
    });
    await draw(0, to, to); keep(to - 1, to, to + 1);
    loadTex(to + 1); busy = false; startTimer();
  };

  const startTimer = () => {
    stopTimer(); if (hidden) return;
    const t0 = performance.now();
    const tick = () => { const pct = Math.min(100, (performance.now() - t0) / AUTO * 100); fill(cur, pct); if (pct >= 100) goTo(cur + 1); else timer = requestAnimationFrame(tick); };
    timer = requestAnimationFrame(tick);
  };
  const stopTimer = () => { cancelAnimationFrame(timer); timer = 0; };

  // ---------- Eventos ----------
  items.forEach(el => el.addEventListener('click', e => goTo(+el.dataset.i, [e.clientX / innerWidth, 1 - e.clientY / innerHeight])));
  $('.gl-open').addEventListener('click', () => lbLinks[cur]?.click());
  addEventListener('keydown', e => {
    const r = root.getBoundingClientRect(); if (r.bottom < 0 || r.top > innerHeight || document.getElementById('lightbox')?.hidden === false) return;
    if (e.key === 'ArrowRight') goTo(cur + 1); if (e.key === 'ArrowLeft') goTo(cur - 1);
  });
  let x0 = null;
  root.addEventListener('touchstart', e => x0 = e.touches[0].clientX, { passive: true });
  root.addEventListener('touchend', e => { if (x0 === null) return; const dx = e.changedTouches[0].clientX - x0; if (Math.abs(dx) > 50) goTo(cur + (dx < 0 ? 1 : -1)); x0 = null; });
  document.addEventListener('visibilitychange', () => { hidden = document.hidden; hidden ? stopTimer() : (!busy && startTimer()); });
  addEventListener('resize', resize);

  // ---------- Arranque ----------
  if (initGL()) {
    resize();
    draw(0, 0, 0).then(() => { setText(0, true); loadTex(1 % N); if (N > 1) startTimer(); });
  } else {
    root.classList.add('no-webgl');
    fb.forEach(im => im.src = im.dataset.src); fb[0]?.classList.add('is-on');
    setText(0, true); if (N > 1) startTimer();
  }
})();
