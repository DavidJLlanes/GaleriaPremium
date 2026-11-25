/* Galería "Morph": una pasada de shader a pantalla completa sobre dos texturas. Un ruido fbm da a cada píxel un umbral y el progreso lo barre,
   sesgado por la luminosidad de la foto entrante (sus zonas claras aparecen primero). WebGL puro, sin librerías ni compilación.
   Sin WebGL (o si el contexto falla) queda un fundido DOM. Se renderiza solo mientras hay animación. */
(() => {
  const VERT = 'attribute vec2 a;varying vec2 v_uv;void main(){v_uv=a*.5+.5;gl_Position=vec4(a,0.,1.);}';
  const FRAG = `precision highp float;
uniform sampler2D u_from;uniform sampler2D u_to;uniform float u_progress;uniform vec2 u_res;uniform float u_fa;uniform float u_ta;uniform float u_scale;uniform float u_dir;uniform float u_edge;uniform float u_drift;varying vec2 v_uv;
vec3 permute(vec3 x){return mod(((x*34.0)+1.0)*x,289.0);}
float snoise(vec2 v){const vec4 C=vec4(0.211324865405187,0.366025403784439,-0.577350269189626,0.024390243902439);
vec2 i=floor(v+dot(v,C.yy));vec2 x0=v-i+dot(i,C.xx);vec2 i1=(x0.x>x0.y)?vec2(1.0,0.0):vec2(0.0,1.0);vec4 x12=x0.xyxy+C.xxzz;x12.xy-=i1;i=mod(i,289.0);
vec3 p=permute(permute(i.y+vec3(0.0,i1.y,1.0))+i.x+vec3(0.0,i1.x,1.0));
vec3 m=max(0.5-vec3(dot(x0,x0),dot(x12.xy,x12.xy),dot(x12.zw,x12.zw)),0.0);m=m*m;m=m*m;
vec3 x=2.0*fract(p*C.www)-1.0;vec3 h=abs(x)-0.5;vec3 ox=floor(x+0.5);vec3 a0=x-ox;
m*=1.79284291400159-0.85373472095314*(a0*a0+h*h);vec3 g;g.x=a0.x*x0.x+h.x*x0.y;g.yz=a0.yz*x12.xz+h.yz*x12.yw;return 130.0*dot(m,g);}
float fbm(vec2 v){float value=0.0;float amp=0.5;for(int i=0;i<5;i++){value+=amp*snoise(v);v*=2.0;amp*=0.5;}return value;}
vec2 mirror(vec2 uv){return 1.0-abs(1.0-mod(uv,2.0));}
vec2 coverUV(vec2 uv,float ia){float ca=u_res.x/u_res.y;vec2 s=(ca>ia)?vec2(1.0,ia/ca):vec2(ca/ia,1.0);return mirror((uv-0.5)*s+0.5);}
void main(){
  float adjusted=u_progress*(1.0+2.0*u_edge)-u_edge;
  float noise=fbm(v_uv*u_scale+vec2(0.0,u_progress*u_dir))*0.5+0.5;
  noise=smoothstep(0.0,2.0,length(texture2D(u_to,coverUV(v_uv,u_ta)).rgb)+noise);
  float mixF=1.0-smoothstep(adjusted-u_edge,adjusted+u_edge,noise);
  vec2 fromUV=coverUV(v_uv+vec2(0.0,noise*u_progress*u_drift*u_dir),u_fa);
  vec2 toUV=coverUV(v_uv+vec2(0.0,noise*(1.0-u_progress)*-0.5*u_drift*u_dir),u_ta);
  gl_FragColor=mix(texture2D(u_from,fromUV),texture2D(u_to,toUV),mixF);
}`;
  const ease = t => { const x = Math.min(Math.max(t, 0), 1); return x < .5 ? 16 * x ** 5 : 1 - (-2 * x + 2) ** 5 / 2; };
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

  document.querySelectorAll('.gm').forEach(root => {
    const items = JSON.parse(root.querySelector('.gm-data').textContent), n = items.length;
    const cv = root.querySelector('.gm-cv'), fb = [...root.querySelectorAll('.gm-fallback img')];
    const metas = [...root.querySelectorAll('.gm-m')], ths = [...root.querySelectorAll('.gm-th button')];
    const anchors = [...root.querySelectorAll('.gm-src a')], cnt = root.querySelector('.gm-n b');
    const DUR = reduce ? 0 : +root.dataset.dur, SC = +root.dataset.noise, AUTO = reduce ? 0 : +root.dataset.auto;
    if (ths.length) root.classList.add('has-th');
    let active = 0, paused = false;

    const show = i => {          // parte DOM común: texto, contador y miniaturas
      active = i;
      metas.forEach((m, k) => m.classList.toggle('is-on', k === i));
      ths.forEach((b, k) => { b.classList.toggle('is-on', k === i); if (k === i) b.scrollIntoView({ block: 'nearest', inline: 'center', behavior: 'smooth' }); });
      fb.forEach((im, k) => im.classList.toggle('is-on', k === i));
      if (cnt) cnt.textContent = String(i + 1).padStart(2, '0');
    };
    let engine = null;           // { go(i) } cuando hay WebGL
    const goTo = i => { i = (i % n + n) % n; if (i === active && !engine?.busy) return; show(i); engine && engine.go(i); };
    const step = d => goTo(active + d);

    root.querySelector('.gm-prev')?.addEventListener('click', () => step(-1));
    root.querySelector('.gm-next')?.addEventListener('click', () => step(1));
    ths.forEach(b => b.addEventListener('click', () => goTo(+b.dataset.i)));
    root.addEventListener('keydown', e => { if (e.key === 'ArrowRight') { e.preventDefault(); step(1); } else if (e.key === 'ArrowLeft') { e.preventDefault(); step(-1); } });
    root.addEventListener('mouseenter', () => paused = true); root.addEventListener('mouseleave', () => paused = false);
    root.addEventListener('focusin', () => paused = true); root.addEventListener('focusout', () => paused = false);
    document.addEventListener('visibilitychange', () => paused = document.hidden);

    /* Deslizar para cambiar; un toque o clic sin arrastre abre el lightbox con la foto actual. */
    let sx = null, sy = 0;
    root.addEventListener('pointerdown', e => { if (e.target.closest('button')) return; sx = e.clientX; sy = e.clientY; });
    root.addEventListener('pointerup', e => {
      if (sx === null) return; const dx = e.clientX - sx, dy = e.clientY - sy; sx = null;
      if (Math.abs(dx) > 48 && Math.abs(dx) > Math.abs(dy)) step(dx < 0 ? 1 : -1);
      else if (Math.abs(dx) < 6 && Math.abs(dy) < 6 && !e.target.closest('.gm-m a,.gm-m .pcats')) anchors[active]?.click();
    });
    root.addEventListener('pointercancel', () => sx = null);
    if (AUTO && n > 1) setInterval(() => { if (!paused && !(engine && engine.busy) && !window.LightboxApi?.open) step(1); }, Math.max(AUTO, 2500));

    /* ---- WebGL ---- */
    const gl = cv.getContext('webgl', { alpha: false, antialias: false }) || cv.getContext('experimental-webgl');
    if (!gl) return;
    const sh = (t, s) => { const o = gl.createShader(t); gl.shaderSource(o, s); gl.compileShader(o); if (!gl.getShaderParameter(o, gl.COMPILE_STATUS)) throw new Error(gl.getShaderInfoLog(o)); return o; };
    let prog;
    try {
      prog = gl.createProgram(); gl.attachShader(prog, sh(gl.VERTEX_SHADER, VERT)); gl.attachShader(prog, sh(gl.FRAGMENT_SHADER, FRAG)); gl.linkProgram(prog);
      if (!gl.getProgramParameter(prog, gl.LINK_STATUS)) throw new Error('link');
    } catch (err) { return; }
    gl.useProgram(prog);
    const buf = gl.createBuffer(); gl.bindBuffer(gl.ARRAY_BUFFER, buf);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 1, -1, -1, 1, 1, 1]), gl.STATIC_DRAW);
    const loc = gl.getAttribLocation(prog, 'a'); gl.enableVertexAttribArray(loc); gl.vertexAttribPointer(loc, 2, gl.FLOAT, false, 0, 0);
    const U = {}; ['from', 'to', 'progress', 'res', 'fa', 'ta', 'scale', 'dir', 'edge', 'drift'].forEach(k => U[k] = gl.getUniformLocation(prog, 'u_' + k));
    gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, true);
    const MAXT = Math.min(gl.getParameter(gl.MAX_TEXTURE_SIZE) || 2048, 3072);

    const tex = new Array(n).fill(null), asp = new Array(n).fill(1), loading = new Array(n).fill(null);
    const load = i => loading[i] || (loading[i] = new Promise(res => {
      const im = new Image(); im.decoding = 'async';
      im.onload = () => {
        let src = im; const big = Math.max(im.naturalWidth, im.naturalHeight);
        if (big > MAXT) { const k = MAXT / big, c = document.createElement('canvas'); c.width = Math.round(im.naturalWidth * k); c.height = Math.round(im.naturalHeight * k); c.getContext('2d').drawImage(im, 0, 0, c.width, c.height); src = c; }
        const t = gl.createTexture(); gl.bindTexture(gl.TEXTURE_2D, t);
        gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, gl.RGBA, gl.UNSIGNED_BYTE, src);
        [[gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE], [gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE], [gl.TEXTURE_MIN_FILTER, gl.LINEAR], [gl.TEXTURE_MAG_FILTER, gl.LINEAR]].forEach(([p, v]) => gl.texParameteri(gl.TEXTURE_2D, p, v));
        tex[i] = t; asp[i] = im.naturalWidth / Math.max(im.naturalHeight, 1); res(true);
      };
      im.onerror = () => res(false); im.src = items[i].src;
    }));

    let from = 0, to = 0, progress = 1, t0 = 0, dir = 1, raf = 0, queued = null, ok = true;
    const resize = () => {
      const d = Math.min(devicePixelRatio || 1, 2), w = Math.round(cv.clientWidth * d), h = Math.round(cv.clientHeight * d);
      if (!w || !h || (cv.width === w && cv.height === h)) return false; cv.width = w; cv.height = h; gl.viewport(0, 0, w, h); return true;
    };
    const draw = () => {
      const a = tex[from] || tex[to], b = tex[to] || tex[from]; if (!a || !b) return;
      gl.activeTexture(gl.TEXTURE0); gl.bindTexture(gl.TEXTURE_2D, a); gl.uniform1i(U.from, 0);
      gl.activeTexture(gl.TEXTURE1); gl.bindTexture(gl.TEXTURE_2D, b); gl.uniform1i(U.to, 1);
      gl.uniform1f(U.progress, progress); gl.uniform2f(U.res, cv.width, cv.height); gl.uniform1f(U.fa, asp[from]); gl.uniform1f(U.ta, asp[to]);
      gl.uniform1f(U.scale, SC); gl.uniform1f(U.dir, dir); gl.uniform1f(U.edge, .15); gl.uniform1f(U.drift, .5);
      gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4);
    };
    const tick = () => {
      raf = 0;
      if (progress < 1) {
        progress = DUR ? ease((performance.now() - t0) / DUR) : 1;
        if (progress >= 1) { progress = 1; from = to; if (queued !== null) { const q = queued; queued = null; run(q); } }
      }
      draw(); engine.busy = progress < 1;
      if (progress < 1) raf = requestAnimationFrame(tick);
    };
    const kick = () => { if (!raf) raf = requestAnimationFrame(tick); };
    const run = async i => {
      await load(i); if (!tex[i]) return;
      if (progress < 1) { queued = i; return; }
      if (i === to && from === to) { draw(); return; }
      from = to; to = i; progress = 0; t0 = performance.now(); engine.busy = true;
      const fw = (to - from + n) % n; dir = fw * 2 <= n ? 1 : -1; kick();
    };
    engine = { busy: false, go(i) { if (progress < 1) { queued = i; return; } run(i); } };

    cv.addEventListener('webglcontextlost', e => { e.preventDefault(); root.classList.remove('is-gl'); engine = null; });
    new ResizeObserver(() => { if (resize()) { draw(); } }).observe(cv);
    resize();
    load(0).then(ok0 => {
      if (!ok0) { engine = null; return; }
      resize(); draw(); root.classList.add('is-gl');
      for (let k = 1; k < n; k++) setTimeout(() => load(k), 400 + k * 250);   // el resto se sube poco a poco
    });
  });
})();
