/* Agua: WebGL con hasta 8 ondas simultáneas (ratón/dedo y una gota automática cada pocos segundos). Sin WebGL queda la foto fija. */
(() => {
  const box = document.querySelector('.hxw'); if (!box) return;
  const cv = box.querySelector('canvas'), hero = box.closest('.hero') || box;
  const gl = cv.getContext('webgl', { antialias: false, alpha: false }); if (!gl) return;
  const VS = 'attribute vec2 p;varying vec2 v;void main(){v=p*.5+.5;gl_Position=vec4(p,0.,1.);}';
  const FS = `precision mediump float;varying vec2 v;uniform sampler2D tex;uniform vec2 res,img;uniform float time,str;uniform vec4 drop[8];
  void main(){
    float ra=res.x/res.y, ia=img.x/img.y; vec2 off=vec2(0.); float hl=0.;
    for(int i=0;i<8;i++){
      vec4 d=drop[i]; float t=time-d.z; if(d.w<=0.||t<0.||t>6.) continue;
      vec2 dp=(v-d.xy)*vec2(ra,1.); float r=length(dp), f=t*.34;
      float w=sin((r-f)*34.)*exp(-abs(r-f)*8.)*exp(-t*.75)*d.w;
      off+=normalize(dp+1e-5)*w; hl+=w;
    }
    vec2 s=vec2(1.); if(ra>ia) s.y=ia/ra; else s.x=ra/ia;
    vec2 uv=((v+off*.02*str)-.5)*s+.5;
    vec3 c=texture2D(tex,uv).rgb*.82; c+=hl*.05*str;
    gl_FragColor=vec4(c,1.);
  }`;
  const sh = (t, s) => { const o = gl.createShader(t); gl.shaderSource(o, s); gl.compileShader(o); return gl.getShaderParameter(o, gl.COMPILE_STATUS) ? o : null; };
  const vs = sh(gl.VERTEX_SHADER, VS), fs = sh(gl.FRAGMENT_SHADER, FS); if (!vs || !fs) return;
  const pr = gl.createProgram(); gl.attachShader(pr, vs); gl.attachShader(pr, fs); gl.linkProgram(pr);
  if (!gl.getProgramParameter(pr, gl.LINK_STATUS)) return;
  gl.useProgram(pr);
  const buf = gl.createBuffer(); gl.bindBuffer(gl.ARRAY_BUFFER, buf); gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 3, -1, -1, 3]), gl.STATIC_DRAW);
  const loc = gl.getAttribLocation(pr, 'p'); gl.enableVertexAttribArray(loc); gl.vertexAttribPointer(loc, 2, gl.FLOAT, false, 0, 0);
  const U = (n) => gl.getUniformLocation(pr, n), uRes = U('res'), uImg = U('img'), uTime = U('time'), uStr = U('str'), uDrop = U('drop');
  const str = parseFloat(box.dataset.strength) || 1.2, drops = new Float32Array(32); let di = 0, imgSize = [1, 1], ready = false;
  const addDrop = (x, y, amp) => { drops.set([x, y, (performance.now() - t0) / 1000, amp], (di++ % 8) * 4); };
  const t0 = performance.now();

  const im = new Image(); im.crossOrigin = 'anonymous';
  im.onload = () => {
    const tx = gl.createTexture(); gl.bindTexture(gl.TEXTURE_2D, tx);
    gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, true);
    gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGB, gl.RGB, gl.UNSIGNED_BYTE, im);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR); gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.LINEAR);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE); gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);
    imgSize = [im.naturalWidth, im.naturalHeight]; ready = true; resize(); box.classList.add('is-gl');
    addDrop(.5, .5, .9); requestAnimationFrame(frame);
  };
  im.src = box.dataset.src;

  const resize = () => {
    const d = Math.min(devicePixelRatio || 1, 1.6), w = Math.round(box.clientWidth * d), h = Math.round(box.clientHeight * d);
    if (cv.width !== w || cv.height !== h) { cv.width = w; cv.height = h; }
    gl.viewport(0, 0, w, h);
  };
  new ResizeObserver(() => ready && resize()).observe(box);

  const pos = (e) => { const r = box.getBoundingClientRect(); return [(e.clientX - r.left) / r.width, 1 - (e.clientY - r.top) / r.height]; };
  let lastMove = 0;
  hero.addEventListener('pointermove', (e) => { const n = performance.now(); if (n - lastMove < 90) return; lastMove = n; addDrop(...pos(e), .38); }, { passive: true });
  hero.addEventListener('pointerdown', (e) => addDrop(...pos(e), 1), { passive: true });
  setInterval(() => { if (!document.hidden) addDrop(.2 + Math.random() * .6, .25 + Math.random() * .5, .8); }, 3600);

  let vis = true, running = false;
  new IntersectionObserver(([en]) => { vis = en.isIntersecting; if (vis && !running && ready) requestAnimationFrame(frame); }).observe(box);
  const frame = () => {
    if (!vis) { running = false; return; } running = true;
    gl.uniform2f(uRes, cv.width, cv.height); gl.uniform2f(uImg, imgSize[0], imgSize[1]);
    gl.uniform1f(uTime, (performance.now() - t0) / 1000); gl.uniform1f(uStr, str); gl.uniform4fv(uDrop, drops);
    gl.drawArrays(gl.TRIANGLES, 0, 3); requestAnimationFrame(frame);
  };
})();
