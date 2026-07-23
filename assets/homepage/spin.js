/* 360° spin viewer — ported from stingray-homepage/index.html.
   Drag/scroll-driven vanilla JS, preserved as-is except:
   - assetBase comes from window.SC_SPIN_BASE (set by functions.php) so the
     frame URLs are absolute wherever this renders.
   - Frame sets lazy-load: only the currently-selected paint loads on page
     view. The next paint loads on demand as rotation approaches a color
     change (addRotation already defers the switch until its set is ready);
     the source's preloadRestDeferred() eager-load of every paint is removed.
   - The mobile-drawer toggle moved to assets/js/nav.js (the drawer is part
     of the shared topbar, not the homepage). */
(function () {
  'use strict';

  /* =========================================================
     CONFIG — edit these to change behavior (no other code changes needed)
     ========================================================= */
  var CONFIG = {
    spinBehavior: 'Scroll to spin',   // 'Continuous loop' | 'Spin once, then rest' | 'Interact to spin' | 'Scroll to spin'
    spinDirection: 'Clockwise',       // 'Clockwise' | 'Counter-clockwise'
    spinSpeed: 3,                     // seconds per revolution (auto modes only)
    scrollPerRev: 600,                // px of sticky page scroll per full revolution
    dragToSpin: true,                 // allow grab + drag on the car
    autoCycle: true,                  // advance paint color after N revolutions
    revsPerColor: 1,                  // revolutions before the paint changes
    colorLeadFrames: 2,               // fire the paint change this many frames before the revolution completes
    manualColor: 'Torch Red',         // used when autoCycle is false
    assetBase: window.SC_SPIN_BASE || 'assets/spin/', // absolute theme URL to the frame folders
    webpDirSuffix: '-webp/',
    webpFileSuffix: '-cmp.webp',
    pngFileSuffix: '.png',
    maxConcurrent: 4
  };

  // Each paint: frame folder + matching accent palette for the whole page.
  var COLORS = [
    { key:'red',    name:'Torch Red',         dir:'gkz-red/',    prefix:'gkz-ext.', count:30, a:'#d0425a', d:'#b22234', on:'#ffffff', glow:'rgba(208,66,90,.42)' },
    { key:'yellow', name:'Accelerate Yellow', dir:'gbk-yellow/', prefix:'gbk-ext.', count:30, a:'#ffc81f', d:'#e0a400', on:'#1c1606', glow:'rgba(255,200,31,.36)' },
    { key:'blue',   name:'Riptide Blue',      dir:'gtr-blue/',   prefix:'gtr-ext.', count:30, a:'#3f73ff', d:'#1d4ed8', on:'#ffffff', glow:'rgba(63,115,255,.40)' },
    { key:'orange', name:'Sebring Orange',    dir:'g26-orange/', prefix:'g26-ext.', count:30, a:'#ff6a1a', d:'#d8480a', on:'#1c0d04', glow:'rgba(255,106,26,.38)' },
    { key:'green',  name:'Cacti Green',       dir:'g4z-green/',  prefix:'g4z-ext.', count:30, a:'#35b85d', d:'#1f7a4d', on:'#06210f', glow:'rgba(53,184,93,.36)' }
  ];

  /* ---------- 360 spin viewer — car paint + page accents cycle together ---------- */
  var canvas = document.getElementById('spinCanvas');
  var loader = document.getElementById('spinLoader');
  var hint = document.getElementById('spinHint');
  if (!canvas) return;
  var hero = canvas.closest ? canvas.closest('.sc-hero') : null;
  var topbar = document.querySelector('.sc-topbar');
  var ctx = canvas.getContext('2d');
  var root = document.documentElement;

  var reduce = !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
  var saveData = !!(navigator.connection && navigator.connection.saveData);
  var constrained = reduce || saveData;
  var dragEnabled = CONFIG.dragToSpin && !constrained;
  var dir = (CONFIG.spinDirection === 'Clockwise') ? -1 : 1;
  var behavior = CONFIG.spinBehavior;
  var pxPerRev = Math.max(100, CONFIG.scrollPerRev);
  var revsPerColor = Math.max(1, Math.round(CONFIG.revsPerColor));

  var frameSets = {};
  var rev = 0;           // rotation in revolutions — frame-count independent
  var revAccum = 0;
  var spun = 0;
  var dragging = false;
  var fade = null;       // paint cross-fade state
  var accentRaf = 0, spinRaf = 0;
  var live = null;
  var scrollQuery = window.matchMedia
    ? window.matchMedia('(min-width: 900px) and (min-height: 700px) and (prefers-reduced-motion: no-preference)')
    : null;
  var scrollEligible = false;
  var lastScrollProgress = 0;
  var nextPaintRequested = false;

  if (hero) hero.style.setProperty('--spin-scroll-distance', pxPerRev + 'px');

  function scrollIsEligible() {
    return !constrained && behavior === 'Scroll to spin' && !!hero && !!scrollQuery && scrollQuery.matches;
  }

  function getScrollProgress() {
    if (!hero) return 0;
    var anchor = topbar ? topbar.getBoundingClientRect().bottom : 0;
    var traveled = anchor - hero.getBoundingClientRect().top;
    return Math.max(0, Math.min(1, traveled / pxPerRev));
  }

  function updateHint() {
    if (!hint) return;
    hint.textContent = scrollEligible
      ? '360° · Scroll to spin'
      : (dragEnabled ? '360° · Drag to spin' : '360° View');
  }

  function syncScrollMode(options) {
    var wasEligible = scrollEligible;
    scrollEligible = scrollIsEligible();
    var progress = scrollEligible ? getScrollProgress() : 0;
    if (options && options.applyInitialProgress && scrollEligible) {
      var initialDelta = dir * progress;
      rev += initialDelta;
      addRotation(Math.abs(initialDelta));
    }
    lastScrollProgress = progress;
    if (!scrollEligible) {
      nextPaintRequested = false;
    } else if ((!wasEligible || (options && options.applyInitialProgress)) && progress >= 0.7) {
      nextPaintRequested = true;
      preloadColor((colorIdx + 1) % COLORS.length);
    }
    updateHint();
    drawFrame();
  }

  var colorIdx = 0;
  if (!CONFIG.autoCycle) {
    for (var i = 0; i < COLORS.length; i++) if (COLORS[i].name === CONFIG.manualColor) colorIdx = i;
  }

  canvas.style.cursor = dragEnabled ? 'grab' : 'default';

  function frameStem(color, i) {
    return color.prefix + String(i + 1).padStart(3, '0');
  }

  function webpFrameUrl(color, i) {
    return CONFIG.assetBase +
      color.dir.replace(/\/$/, '') +
      CONFIG.webpDirSuffix +
      frameStem(color, i) +
      CONFIG.webpFileSuffix;
  }

  function pngFrameUrl(color, i) {
    return CONFIG.assetBase + color.dir + frameStem(color, i) + CONFIG.pngFileSuffix;
  }

  function prioritizedFrameIndexes(count) {
    var order = [0];
    for (var offset = 1; offset <= Math.floor(count / 2); offset++) {
      order.push(offset);
      if (count - offset !== offset) order.push(count - offset);
    }
    return order.slice(0, count);
  }

  var activeLoads = 0;
  var frameLoadQueue = [];

  function pumpFrameLoadQueue() {
    while (activeLoads < CONFIG.maxConcurrent && frameLoadQueue.length) {
      activeLoads++;
      frameLoadQueue.shift()();
    }
  }

  function scheduleFrameLoad(start) {
    frameLoadQueue.push(start);
    pumpFrameLoadQueue();
  }

  function finishFrame(arr, frameIndex, idx) {
    activeLoads--;
    arr.loadedCount++;
    if (idx === colorIdx && frameIndex === 0) {
      if (loader) loader.style.display = 'none';
      sizeCanvas();
    }
    pumpFrameLoadQueue();
  }

  function preloadColor(idx) {
    var color = COLORS[idx];
    if (frameSets[color.key]) return frameSets[color.key];
    var arr = new Array(color.count);
    var queue = prioritizedFrameIndexes(color.count);
    arr.loadedCount = 0;

    function startFrame(frameIndex) {
      var im = new Image();
      var triedPng = false;
      im.onload = function () {
        finishFrame(arr, frameIndex, idx);
      };
      im.onerror = function () {
        if (!triedPng) {
          triedPng = true;
          im.src = pngFrameUrl(color, frameIndex);
          return;
        }
        finishFrame(arr, frameIndex, idx);
      };
      arr[frameIndex] = im;
      im.src = webpFrameUrl(color, frameIndex);
    }

    frameSets[color.key] = arr;
    queue.forEach(function (frameIndex) {
      scheduleFrameLoad(function () { startFrame(frameIndex); });
    });
    return arr;
  }

  function loadStaticFrame(idx) {
    var color = COLORS[idx];
    var arr = new Array(color.count);
    var triedPng = false;
    var im = new Image();
    arr.loadedCount = 0;
    im.onload = function () {
      arr.loadedCount = 1;
      if (loader) loader.style.display = 'none';
      sizeCanvas();
    };
    im.onerror = function () {
      if (!triedPng) {
        triedPng = true;
        im.src = pngFrameUrl(color, 0);
        return;
      }
      arr.loadedCount = 1;
    };
    arr[0] = im;
    frameSets[color.key] = arr;
    im.src = webpFrameUrl(color, 0);
    return arr;
  }

  function isReady(idx) {
    var arr = frameSets[COLORS[idx].key];
    return !!(arr && arr.loadedCount >= arr.length);
  }

  /* ---------- Accent palette (CSS variables) ---------- */
  function parseColor(c) {
    c = (c || '').trim();
    if (c[0] === '#') {
      var h = c.slice(1);
      if (h.length === 3) h = h.split('').map(function (x) { return x + x; }).join('');
      var n = parseInt(h, 16);
      return [(n >> 16) & 255, (n >> 8) & 255, n & 255, 1];
    }
    var m = c.match(/[\d.]+/g);
    if (m) return [+m[0], +m[1], +m[2], m[3] !== undefined ? +m[3] : 1];
    return [0, 0, 0, 1];
  }

  function applyLive(L) {
    function rgba(v) { return 'rgba(' + Math.round(v[0]) + ',' + Math.round(v[1]) + ',' + Math.round(v[2]) + ',' + (+v[3]).toFixed(3) + ')'; }
    root.style.setProperty('--accent', rgba(L.a));
    root.style.setProperty('--accent-deep', rgba(L.d));
    root.style.setProperty('--on-accent', rgba(L.on));
    root.style.setProperty('--accent-glow', rgba(L.glow));
  }

  function setAccentInstant(t) {
    cancelAnimationFrame(accentRaf);
    live = { a: parseColor(t.a), d: parseColor(t.d), on: parseColor(t.on), glow: parseColor(t.glow) };
    applyLive(live);
  }

  function animateAccent(t) {
    if (constrained) { setAccentInstant(t); return; }
    if (!live) { setAccentInstant(t); return; }
    var from = { a: live.a.slice(), d: live.d.slice(), on: live.on.slice(), glow: live.glow.slice() };
    var to = { a: parseColor(t.a), d: parseColor(t.d), on: parseColor(t.on), glow: parseColor(t.glow) };
    cancelAnimationFrame(accentRaf);
    var dur = 1200, t0 = performance.now();
    function ease(x) { return x < 0.5 ? 2 * x * x : 1 - Math.pow(-2 * x + 2, 2) / 2; }
    function lerp(f, g, k) { return [f[0] + (g[0] - f[0]) * k, f[1] + (g[1] - f[1]) * k, f[2] + (g[2] - f[2]) * k, f[3] + (g[3] - f[3]) * k]; }
    function step(now) {
      var k = Math.min(1, (now - t0) / dur), e = ease(k);
      live = { a: lerp(from.a, to.a, e), d: lerp(from.d, to.d, e), on: lerp(from.on, to.on, e), glow: lerp(from.glow, to.glow, e) };
      applyLive(live);
      drawFrame(); // keeps the paint cross-fade rendering even when the spin loop is idle
      if (k < 1) accentRaf = requestAnimationFrame(step);
    }
    accentRaf = requestAnimationFrame(step);
  }

  /* ---------- Rotation → color advance ---------- */
  // The paint change fires colorLeadFrames short of a full revolution: the
  // bounded hero scroll tops out at exactly revsPerColor, so a threshold of
  // revsPerColor itself is only reached at the last scroll pixel (and float
  // accumulation can leave revAccum fractionally under it). Leading by a
  // couple of frames guarantees the change lands within the first
  // scroll-through. Subtracting the full revsPerColor afterwards keeps
  // later changes on the same once-per-revolution cadence.
  var colorLeadRevs = Math.max(0, CONFIG.colorLeadFrames || 0) / COLORS[0].count;
  function addRotation(revs) {
    if (constrained || !CONFIG.autoCycle) return;
    revAccum += revs;
    var threshold = revsPerColor - colorLeadRevs;
    if (revAccum < threshold) return;
    var next = (colorIdx + 1) % COLORS.length;
    if (isReady(next)) {
      revAccum -= revsPerColor;
      fade = { fromKey: COLORS[colorIdx].key, t0: performance.now(), dur: 1200 };
      colorIdx = next;
      animateAccent(COLORS[next]);
      preloadColor((next + 1) % COLORS.length);
    } else {
      revAccum = threshold;
      preloadColor(next);
    }
  }

  /* ---------- Canvas ---------- */
  function sizeCanvas() {
    var rect = canvas.getBoundingClientRect();
    if (!rect.width || !rect.height) return;
    var dpr = Math.min(2, window.devicePixelRatio || 1);
    var w = Math.max(1, Math.round(rect.width * dpr));
    var h = Math.max(1, Math.round(rect.height * dpr));
    if (canvas.width !== w || canvas.height !== h) { canvas.width = w; canvas.height = h; }
    drawFrame();
  }

  function drawFrame() {
    var frames = frameSets[COLORS[colorIdx].key];
    if (!frames) return;
    ctx.imageSmoothingEnabled = true;
    ctx.imageSmoothingQuality = 'high';
    function idxOf(arr) { return (((Math.round(rev * arr.length) % arr.length) + arr.length) % arr.length); }
    var fadeK = 1, fromArr = null;
    if (fade) {
      var k = Math.min(1, (performance.now() - fade.t0) / fade.dur);
      fadeK = k < 0.5 ? 2 * k * k : 1 - Math.pow(-2 * k + 2, 2) / 2;
      fromArr = frameSets[fade.fromKey] || null;
      if (k >= 1) { fade = null; fromArr = null; fadeK = 1; }
    }
    var cw = canvas.width, ch = canvas.height;
    ctx.clearRect(0, 0, cw, ch);
    function draw(im, alpha) {
      if (!im || !im.complete || !im.naturalWidth || alpha <= 0.004) return;
      var ir = im.naturalWidth / im.naturalHeight, cr = cw / ch;
      var dw, dh;
      if (ir > cr) { dw = cw; dh = cw / ir; } else { dh = ch; dw = ch * ir; }
      ctx.globalAlpha = Math.min(1, alpha);
      ctx.drawImage(im, (cw - dw) / 2, (ch - dh) / 2, dw, dh);
    }
    if (fromArr) draw(fromArr[idxOf(fromArr)], 1);
    draw(frames[idxOf(frames)], fromArr ? fadeK : 1);
    ctx.globalAlpha = 1;
  }

  /* ---------- Bounded hero scroll to spin ---------- */
  window.addEventListener('scroll', function () {
    if (!scrollEligible) return;
    var progress = getScrollProgress();
    if (dragging) {
      lastScrollProgress = progress;
      return;
    }
    var delta = progress - lastScrollProgress;
    lastScrollProgress = progress;
    if (!delta) return;
    if (progress < 0.7) nextPaintRequested = false;
    if (!nextPaintRequested && progress >= 0.7) {
      nextPaintRequested = true;
      preloadColor((colorIdx + 1) % COLORS.length);
    }
    var df = dir * delta;
    rev += df;
    addRotation(Math.abs(df));
    drawFrame();
  }, { passive: true });

  /* ---------- Auto-spin loop (unused in scroll mode) ---------- */
  function startLoop() {
    cancelAnimationFrame(spinRaf);
    if (constrained || behavior === 'Interact to spin' || behavior === 'Scroll to spin') { drawFrame(); return; }
    var revsPerMs = 1 / (Math.max(1, CONFIG.spinSpeed) * 1000);
    var last = performance.now();
    function loop(now) {
      var dt = Math.min(64, now - last); last = now;
      if (!dragging) {
        var adv = dir * revsPerMs * dt;
        rev += adv;
        spun += Math.abs(adv);
        addRotation(Math.abs(adv));
        if (behavior === 'Spin once, then rest' && spun >= 1) { rev = Math.round(rev); drawFrame(); return; }
      }
      drawFrame();
      spinRaf = requestAnimationFrame(loop);
    }
    spinRaf = requestAnimationFrame(loop);
  }

  /* ---------- Drag to spin ---------- */
  var lastX = 0;
  canvas.addEventListener('pointerdown', function (e) {
    if (!dragEnabled) return;
    dragging = true;
    lastX = e.clientX;
    canvas.style.cursor = 'grabbing';
  });
  window.addEventListener('pointermove', function (e) {
    if (!dragging) return;
    var dx = e.clientX - lastX; lastX = e.clientX;
    var rect = canvas.getBoundingClientRect();
    var df = dx / Math.max(1, rect.width); // one full drag across = one revolution
    rev += df;
    addRotation(Math.abs(df));
    drawFrame();
    if (e.cancelable) e.preventDefault();
  }, { passive: false });
  window.addEventListener('pointerup', function () {
    if (!dragging) return;
    dragging = false;
    canvas.style.cursor = dragEnabled ? 'grab' : 'default';
  });

  /* ---------- Boot: only the selected paint's frames load here ---------- */
  setAccentInstant(COLORS[colorIdx]);
  if (constrained) loadStaticFrame(colorIdx);
  else preloadColor(colorIdx);
  sizeCanvas();
  syncScrollMode({ applyInitialProgress: true });
  window.addEventListener('resize', function () {
    sizeCanvas();
    syncScrollMode();
  });
  if (scrollQuery && scrollQuery.addEventListener) {
    scrollQuery.addEventListener('change', function () { syncScrollMode(); });
  } else if (scrollQuery && scrollQuery.addListener) {
    scrollQuery.addListener(function () { syncScrollMode(); });
  }
  if (typeof ResizeObserver !== 'undefined') new ResizeObserver(sizeCanvas).observe(canvas);
  startLoop();
})();
