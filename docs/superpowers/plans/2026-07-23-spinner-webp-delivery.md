# Spinner WebP Delivery Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Serve the homepage 360 viewer from the committed 1500×750 WebP sequence with four-request concurrency, constrained-data behavior, and one-time PNG fallback.

**Architecture:** Keep the existing canvas, paint model, absolute `window.SC_SPIN_BASE`, lazy paint loading, and later `colorLeadFrames` behavior. Replace the burst loader with a bounded queue that requests the parallel `*-webp/*-cmp.webp` asset first and retries the matching legacy PNG once before advancing the queue.

**Tech Stack:** Vanilla JavaScript, Node built-in test runner utilities (`fs`, `path`, `vm`), transparent WebP/PNG assets, classic WordPress theme.

## Global Constraints

- No dependencies or build system.
- Preserve the current paint order, canvas, drag behavior, scroll behavior, accent transitions, and absolute theme asset URLs.
- Keep the PNG files as fallback and rollback material; do not rename or delete source images.
- Keep no more than four active frame loads.
- Reduced-motion or Save-Data mode loads one static frame and disables interactive/animated preloading.
- Do not deploy, SFTP, call WordPress APIs, purge caches, or modify the live site.

---

## File Structure

- `assets/homepage/spin.js`: owns WebP/PNG URL construction, bounded loading, fallback, and constrained mode.
- `tests/spin-loading.test.js`: owns asset-contract and executable loader regression coverage.
- `README.md`: records the WebP derivative naming and runtime fallback boundary.
- `style.css`: bumps the theme version so the changed JavaScript is cache-busted.

### Task 1: Test and Implement the Bounded WebP Loader

**Files:**
- Create: `tests/spin-loading.test.js`
- Modify: `assets/homepage/spin.js:17-143`
- Modify: `assets/homepage/spin.js:173-215`
- Modify: `assets/homepage/spin.js:279-339`

**Interfaces:**
- Consumes: `window.SC_SPIN_BASE: string`, existing `COLORS` entries with `dir`, `prefix`, and `count`, browser `Image`.
- Produces: `webpFrameUrl(color, index): string`, `pngFrameUrl(color, index): string`, `prioritizedFrameIndexes(count): number[]`, `preloadColor(index): Image[]`, `loadStaticFrame(index): Image[]`.

- [ ] **Step 1: Add an executable failing loader test**

Create `tests/spin-loading.test.js` with a VM browser harness that records image requests:

```js
/* Run: node tests/spin-loading.test.js */
'use strict';

var fs = require('fs');
var path = require('path');
var vm = require('vm');

var root = path.join(__dirname, '..');
var spinRoot = path.join(root, 'assets', 'homepage', 'spin');
var source = fs.readFileSync(path.join(root, 'assets', 'homepage', 'spin.js'), 'utf8');
var failures = [];

function assert(condition, message) {
	if (!condition) failures.push(message);
}

function collect(directory, suffix) {
	var files = [];
	fs.readdirSync(directory, { withFileTypes: true }).forEach(function (entry) {
		var target = path.join(directory, entry.name);
		if (entry.isDirectory()) files = files.concat(collect(target, suffix));
		else if (target.endsWith(suffix)) files.push(target);
	});
	return files;
}

function fakeNode() {
	return {
		style: { setProperty: function () {} },
		closest: function () { return null; },
		getBoundingClientRect: function () { return { width: 750, height: 375, top: 0, bottom: 0 }; },
		addEventListener: function () {},
		getContext: function () {
			return {
				clearRect: function () {},
				drawImage: function () {},
				globalAlpha: 1
			};
		},
		width: 750,
		height: 375
	};
}

function runViewer(options) {
	options = options || {};
	var requests = [];
	var images = [];
	var canvas = fakeNode();
	var loader = fakeNode();
	var hint = fakeNode();

	function FakeImage() {
		this.complete = false;
		this.naturalWidth = 0;
		this.naturalHeight = 0;
		images.push(this);
	}
	Object.defineProperty(FakeImage.prototype, 'src', {
		set: function (value) {
			this.currentSrc = value;
			requests.push(value);
		}
	});
	FakeImage.prototype.succeed = function () {
		this.complete = true;
		this.naturalWidth = 1500;
		this.naturalHeight = 750;
		if (this.onload) this.onload();
	};
	FakeImage.prototype.fail = function () {
		if (this.onerror) this.onerror();
	};

	var document = {
		documentElement: fakeNode(),
		querySelector: function () { return null; },
		getElementById: function (id) {
			if ('spinCanvas' === id) return canvas;
			if ('spinLoader' === id) return loader;
			if ('spinHint' === id) return hint;
			return null;
		}
	};
	var window = {
		SC_SPIN_BASE: 'https://example.test/theme/spin/',
		devicePixelRatio: 1,
		matchMedia: function (query) {
			return {
				matches: query.indexOf('prefers-reduced-motion: reduce') !== -1 ? !!options.reduce : false,
				addEventListener: function () {},
				addListener: function () {}
			};
		},
		addEventListener: function () {}
	};
	var context = {
		window: window,
		document: document,
		navigator: { connection: { saveData: !!options.saveData } },
		Image: FakeImage,
		ResizeObserver: function () { this.observe = function () {}; },
		requestAnimationFrame: function () { return 1; },
		cancelAnimationFrame: function () {},
		performance: { now: function () { return 0; } },
		Math: Math,
		Array: Array,
		String: String
	};
	vm.runInNewContext(source, context);
	return { requests: requests, images: images };
}

var webpFrames = collect(spinRoot, '.webp');
var pngFrames = collect(spinRoot, '.png');
assert(150 === webpFrames.length, 'Exactly 150 WebP frames must exist.');
assert(150 === pngFrames.length, 'Exactly 150 PNG fallback frames must remain.');

webpFrames.forEach(function (webp) {
	var png = webp
		.replace(/-webp([/\\])/, '$1')
		.replace(/-cmp\.webp$/, '.png');
	assert(fs.existsSync(png), 'Missing PNG fallback for ' + path.relative(root, webp));
});

var normal = runViewer();
assert(4 === normal.requests.length, 'Normal boot must start exactly four requests.');
assert(normal.requests.every(function (url) {
	return /gkz-red-webp\/gkz-ext\.\d{3}-cmp\.webp$/.test(url);
}), 'Normal boot must request Torch Red WebP frames first.');

normal.images[0].fail();
assert(5 === normal.requests.length, 'A failed WebP must issue one fallback request.');
assert(/gkz-red\/gkz-ext\.001\.png$/.test(normal.requests[4]), 'The first WebP must fall back to its matching PNG.');
assert(4 === normal.images.length, 'PNG fallback must reuse the same request slot and Image object.');

normal.images[0].succeed();
assert(6 === normal.requests.length, 'Completing a fallback must advance the bounded queue once.');

var saveData = runViewer({ saveData: true });
assert(1 === saveData.requests.length, 'Save-Data mode must request one static frame.');
assert(/gkz-red-webp\/gkz-ext\.001-cmp\.webp$/.test(saveData.requests[0]), 'Save-Data mode must use frame one WebP.');

var reduced = runViewer({ reduce: true });
assert(1 === reduced.requests.length, 'Reduced-motion mode must request one static frame.');

[
	"maxConcurrent: 4",
	'colorLeadFrames: 2',
	'navigator.connection && navigator.connection.saveData',
	'loadStaticFrame(colorIdx)'
].forEach(function (marker) {
	assert(source.indexOf(marker) !== -1, 'Missing runtime marker: ' + marker);
});

if (failures.length) {
	console.error(failures.join('\n'));
	process.exit(1);
}

console.log('Homepage spin-loading regression tests passed.');
```

- [ ] **Step 2: Run the new test and verify the old loader fails**

Run:

```bash
node tests/spin-loading.test.js
```

Expected: exit 1 with failures including `Normal boot must start exactly four requests`, `A failed WebP must issue one fallback request`, and constrained mode requesting more than one frame.

- [ ] **Step 3: Add WebP, fallback, and constrained-mode configuration**

In `assets/homepage/spin.js`, extend `CONFIG` and the motion state:

```js
    assetBase: window.SC_SPIN_BASE || 'assets/spin/',
    webpDirSuffix: '-webp/',
    webpFileSuffix: '-cmp.webp',
    pngFileSuffix: '.png',
    maxConcurrent: 4
```

```js
  var reduce = !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
  var saveData = !!(navigator.connection && navigator.connection.saveData);
  var constrained = reduce || saveData;
  var dragEnabled = CONFIG.dragToSpin && !constrained;
```

Update the existing guards consistently:

```js
  function scrollIsEligible() {
    return !constrained && behavior === 'Scroll to spin' && !!hero && !!scrollQuery && scrollQuery.matches;
  }
```

```js
  canvas.style.cursor = dragEnabled ? 'grab' : 'default';
```

At the start of the existing `animateAccent(t)` function, before its current
`if (!live)` guard, add:

```js
    if (constrained) { setAccentInstant(t); return; }
```

Replace the first line inside `addRotation(revs)`:

```js
    if (!CONFIG.autoCycle) return;
```

with:

```js
    if (constrained || !CONFIG.autoCycle) return;
```

Replace the current early-return condition inside `startLoop()`:

```js
    if (reduce || behavior === 'Interact to spin' || behavior === 'Scroll to spin') { drawFrame(); return; }
```

with:

```js
    if (constrained || behavior === 'Interact to spin' || behavior === 'Scroll to spin') { drawFrame(); return; }
```

Use `dragEnabled` in pointer-down, pointer-up, and hint text instead of `CONFIG.dragToSpin`.

- [ ] **Step 4: Replace URL construction and burst loading with the bounded queue**

Replace `frameUrl()` and `preloadColor()` with:

```js
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

  function finishFrame(arr, frameIndex, idx, activeState, loadNext) {
    activeState.count--;
    arr.loadedCount++;
    if (idx === colorIdx && frameIndex === 0) {
      if (loader) loader.style.display = 'none';
      sizeCanvas();
    }
    loadNext();
  }

  function preloadColor(idx) {
    var color = COLORS[idx];
    if (frameSets[color.key]) return frameSets[color.key];
    var arr = new Array(color.count);
    var queue = prioritizedFrameIndexes(color.count);
    var cursor = 0;
    var activeState = { count: 0 };
    arr.loadedCount = 0;

    function loadNext() {
      while (activeState.count < CONFIG.maxConcurrent && cursor < queue.length) {
        startFrame(queue[cursor++]);
      }
    }

    function startFrame(frameIndex) {
      var im = new Image();
      var triedPng = false;
      activeState.count++;
      im.onload = function () {
        finishFrame(arr, frameIndex, idx, activeState, loadNext);
      };
      im.onerror = function () {
        if (!triedPng) {
          triedPng = true;
          im.src = pngFrameUrl(color, frameIndex);
          return;
        }
        finishFrame(arr, frameIndex, idx, activeState, loadNext);
      };
      arr[frameIndex] = im;
      im.src = webpFrameUrl(color, frameIndex);
    }

    frameSets[color.key] = arr;
    loadNext();
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
```

At boot, replace the unconditional preload with:

```js
  if (constrained) loadStaticFrame(colorIdx);
  else preloadColor(colorIdx);
```

- [ ] **Step 5: Run the focused loader test**

Run:

```bash
node tests/spin-loading.test.js
```

Expected: `Homepage spin-loading regression tests passed.`

- [ ] **Step 6: Run the asset dimension and alpha gate**

Run:

```bash
test "$(find assets/homepage/spin -type f -path '*-webp/*-cmp.webp' | wc -l | tr -d ' ')" = "150"
identify -format '%wx%h %[channels]\n' assets/homepage/spin/*-webp/*.webp | sort | uniq -c
```

Expected:

```text
    150 1500x750 srgba 4.0
```

- [ ] **Step 7: Commit the independently testable loader**

```bash
git add assets/homepage/spin.js tests/spin-loading.test.js
git commit -m "perf: serve bounded WebP spinner frames"
```

### Task 2: Document the Asset Contract and Cache-Bust the Runtime

**Files:**
- Modify: `README.md:38-48`
- Modify: `style.css:7`

**Interfaces:**
- Consumes: the WebP loader and committed `*-webp/*-cmp.webp` assets from Task 1.
- Produces: documented derivative/fallback ownership and a new WordPress theme version.

- [ ] **Step 1: Update the homepage asset documentation**

Replace the PNG-only homepage bullet in `README.md` with:

```markdown
- `spin/<paint>/*.png` — canonical theme-vendored fallback frames, five paints ×
  30 frames.
- `spin/<paint>-webp/*-cmp.webp` — 1500×750 transparent delivery derivatives,
  generated from the matching PNG stems. The viewer requests WebP first, retries
  the matching PNG once on failure, caps loading at four concurrent requests,
  and loads only one static frame for reduced-motion or Save-Data users.
```

- [ ] **Step 2: Bump the theme cache version**

Change the `Version:` header in `style.css` from `0.1.26` to `0.1.27`.

- [ ] **Step 3: Run all proportional local gates**

Run:

```bash
node tests/spin-loading.test.js
node tests/factory-table.test.js
php tests/legacy-redirects.php
php -l functions.php
git diff --check
```

Expected:

- spinner test prints `Homepage spin-loading regression tests passed.`;
- Factory test prints `Factory table row-preparation regression tests passed.`;
- legacy redirects pass;
- PHP reports no syntax errors;
- `git diff --check` prints nothing.

- [ ] **Step 4: Perform local browser verification**

Serve a local homepage fixture using the real `front-page.php`-equivalent
markup, `homepage.css`, `spin.js`, and frame tree. Verify:

- first Torch Red frame paints without a blank canvas;
- no more than four frame requests are pending at once;
- WebP responses have `image/webp`;
- scroll completes the current revolution and advances paint using the retained
  two-frame lead;
- drag remains functional outside constrained mode;
- next paint remains lazy until requested;
- reduced-motion and emulated Save-Data each request exactly one frame;
- a deliberately missing WebP retries the matching PNG once;
- desktop and mobile layouts do not overlap.

Do not deploy during this step.

- [ ] **Step 5: Commit documentation and version**

```bash
git add README.md style.css
git commit -m "docs: record spinner WebP delivery contract"
```

## Completion Gate

The spinner plan is complete only when the automated gates pass, local browser
verification passes, `git status --short` contains no unintended changes, and
no live deployment or cache operation has occurred.
