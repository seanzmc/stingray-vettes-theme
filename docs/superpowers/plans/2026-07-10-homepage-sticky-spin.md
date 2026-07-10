# Homepage Sticky Spin Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Keep the homepage hero visible while 600 pixels of native desktop scrolling produce one reversible Corvette revolution, then release the page into the quick-actions section.

**Architecture:** CSS will add a media-gated 600-pixel sticky segment to the existing hero without changing markup. The existing canvas script will replace unrestricted page deltas with clamped hero progress, share the configured distance through a CSS custom property, and fall back to manual drag on mobile, short screens, and reduced-motion settings.

**Tech Stack:** Classic WordPress theme, vanilla JavaScript, vanilla CSS, Node.js syntax/contract checks, browser QA.

## Global Constraints

- Preserve the current visual design, hero content, page order, and markup.
- Preserve the existing canvas, frame assets, paint palettes, lazy-loading strategy, accent transitions, and manual drag selector contracts.
- Do not intercept or suppress native scrolling.
- Do not add dependencies or introduce a build system.
- Do not refactor unrelated homepage or shared-theme code.
- Do not modify canonical design-system files.
- Do not deploy, use SFTP, call WordPress APIs, or modify the live site.
- Change only `assets/homepage/spin.js`, `assets/homepage/homepage.css`, and `style.css` for the implementation.

---

### Task 1: Add a Failing Sticky-Spin Contract Check

**Files:**
- Test temporarily: `/tmp/homepage-sticky-spin-contract.js`
- Read: `assets/homepage/spin.js`
- Read: `assets/homepage/homepage.css`
- Read: `style.css`

**Interfaces:**
- Consumes: the existing homepage CSS, canvas script, and WordPress theme header.
- Produces: a temporary dependency-free acceptance check for the approved selectors, media conditions, bounded-progress functions, and cache version.

- [ ] **Step 1: Create the temporary contract check outside the repo**

Use `apply_patch` to create `/tmp/homepage-sticky-spin-contract.js` with:

```js
'use strict';

const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');

const root = process.argv[2];
const js = fs.readFileSync(path.join(root, 'assets/homepage/spin.js'), 'utf8');
const css = fs.readFileSync(path.join(root, 'assets/homepage/homepage.css'), 'utf8');
const style = fs.readFileSync(path.join(root, 'style.css'), 'utf8');

assert.match(js, /scrollPerRev:\s*600/);
assert.match(js, /function scrollIsEligible\s*\(/);
assert.match(js, /function getScrollProgress\s*\(/);
assert.match(js, /Math\.max\(0,\s*Math\.min\(1,/);
assert.match(js, /--spin-scroll-distance/);
assert.doesNotMatch(js, /var dy = y - lastScrollY/);
assert.match(css, /--spin-scroll-distance:\s*600px/);
assert.match(css, /min-width:\s*900px/);
assert.match(css, /min-height:\s*700px/);
assert.match(css, /prefers-reduced-motion:\s*no-preference/);
assert.match(css, /position:\s*sticky/);
assert.match(css, /\.sc-hero::after\s*\{[^}]*height:\s*var\(--spin-scroll-distance\)/s);
assert.doesNotMatch(css, /padding-bottom:\s*calc\([^;]*--spin-scroll-distance/);
assert.match(css, /@media[^{}]*min-width:\s*900px[\s\S]*?\.sc-hero\s*\{[^}]*overflow:\s*visible/);
assert.match(css, /@media[^{}]*min-width:\s*900px[\s\S]*?\.sc-hero-glow\s*\{[^}]*right:\s*0;[^}]*width:\s*56%/);
assert.match(style, /Version:\s*0\.1\.14/);

console.log('homepage sticky-spin contract passed');
```

- [ ] **Step 2: Run the contract check and confirm it fails before implementation**

Run:

```bash
node /tmp/homepage-sticky-spin-contract.js /Users/seandm/Projects/stingray-vettes-theme
```

Expected: FAIL on the missing `scrollPerRev: 600` assertion.

### Task 2: Add the Responsive Sticky Hero Segment

**Files:**
- Modify: `assets/homepage/homepage.css:21-32`

**Interfaces:**
- Consumes: `--spin-scroll-distance`, set to `600px` in CSS and synchronized by `spin.js`; existing `.sc-hero`, `.sc-hero-row`, `.sc-topbar`, and `body.admin-bar` selectors.
- Produces: a sticky hero segment active only at a minimum 900-pixel width, minimum 700-pixel height, and `prefers-reduced-motion: no-preference`.

- [ ] **Step 1: Define the scroll-distance fallback on the hero**

Add the property to `.sc-hero` without changing its other visual declarations:

```css
.sc-hero {
  --spin-scroll-distance: 600px;
  position: relative; max-width: var(--content-max); margin: 0 auto;
  padding: clamp(40px, 7vw, 84px) clamp(16px, 4vw, 40px) clamp(20px, 4vw, 48px);
  overflow: hidden;
}
```

- [ ] **Step 2: Add the eligible sticky layout after `.sc-hero-row`**

```css
@media (min-width: 900px) and (min-height: 700px) and (prefers-reduced-motion: no-preference) {
  .sc-hero {
    overflow: visible;
  }

  .sc-hero-glow {
    right: 0;
    width: 56%;
  }

  .sc-hero::after {
    content: "";
    display: block;
    height: var(--spin-scroll-distance);
  }

  .sc-hero-row {
    position: sticky;
    top: calc(75px + clamp(40px, 7vw, 84px));
  }

  body.admin-bar .sc-hero-row {
    top: calc(32px + 75px + clamp(40px, 7vw, 84px));
  }
}
```

The pseudo-element must provide the 600 pixels as in-flow content rather than
padding; sticky positioning cannot travel through bottom padding. Moving the
glow's right edge from `-6%` to `0` while reducing its width from `62%` to
`56%` preserves its left edge and prevents horizontal overflow after the hero
becomes visible for sticky positioning.

The eligible width is above WordPress's 782-pixel admin-bar breakpoint, so the sticky state always uses the 32-pixel desktop admin-bar offset. Mobile's 46-pixel offset remains handled by normal flow because sticky behavior is disabled below 900 pixels.

- [ ] **Step 3: Inspect the CSS diff**

Run:

```bash
git diff -- assets/homepage/homepage.css
```

Expected: only the fallback custom property and media-gated sticky rules appear; existing hero and viewer styles remain unchanged.

### Task 3: Bound Rotation to Hero Scroll Progress

**Files:**
- Modify: `assets/homepage/spin.js:17-27`
- Modify: `assets/homepage/spin.js:39-69`
- Modify: `assets/homepage/spin.js:200-211`
- Modify: `assets/homepage/spin.js:258-264`

**Interfaces:**
- Consumes: `.sc-hero`, `.sc-topbar`, `CONFIG.scrollPerRev`, the existing `rev`, `addRotation(revs)`, `drawFrame()`, `preloadColor(index)`, and drag handlers.
- Produces: `scrollIsEligible() -> boolean`, `getScrollProgress() -> number` in `[0, 1]`, `syncScrollMode(options?) -> void`, and a passive bounded scroll handler.

- [ ] **Step 1: Set the approved distance and capture the hero/topbar**

Change the configuration and element lookup to:

```js
scrollPerRev: 600,                // px of sticky page scroll per full revolution
```

```js
var canvas = document.getElementById('spinCanvas');
var loader = document.getElementById('spinLoader');
var hint = document.getElementById('spinHint');
if (!canvas) return;
var hero = canvas.closest ? canvas.closest('.sc-hero') : null;
var topbar = document.querySelector('.sc-topbar');
```

- [ ] **Step 2: Add scroll eligibility and progress state after the existing viewer state variables**

```js
var scrollQuery = window.matchMedia
  ? window.matchMedia('(min-width: 900px) and (min-height: 700px) and (prefers-reduced-motion: no-preference)')
  : null;
var scrollEligible = false;
var lastScrollProgress = 0;
var nextPaintRequested = false;

if (hero) hero.style.setProperty('--spin-scroll-distance', pxPerRev + 'px');

function scrollIsEligible() {
  return behavior === 'Scroll to spin' && !!hero && !!scrollQuery && scrollQuery.matches;
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
    : (CONFIG.dragToSpin ? '360° · Drag to spin' : '360° View');
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
```

Remove the earlier one-line hint assignment because `updateHint()` now owns all hint states. Preserve the existing cursor assignment.

- [ ] **Step 3: Replace the unrestricted global-delta scroll handler**

Replace lines 200-211 with:

```js
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
  if (!nextPaintRequested && progress >= 0.7) {
    nextPaintRequested = true;
    preloadColor((colorIdx + 1) % COLORS.length);
  }
  var df = dir * delta;
  rev += df;
  addRotation(Math.abs(df));
  drawFrame();
}, { passive: true });
```

This handler never calls `preventDefault()` and never listens for `wheel` or `touchmove`, so native scrolling remains in control.

- [ ] **Step 4: Synchronize boot, resize, and live motion-preference changes**

Replace the boot tail with:

```js
/* ---------- Boot: only the selected paint's frames load here ---------- */
setAccentInstant(COLORS[colorIdx]);
preloadColor(colorIdx);
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
```

The initial call maps restored scroll position to the initial frame. Later eligibility changes reset the progress baseline without changing the current frame, preventing resize jumps.

- [ ] **Step 5: Run JavaScript syntax validation**

Run:

```bash
node --check assets/homepage/spin.js
```

Expected: exit 0 with no output.

### Task 4: Cache-Bust and Pass the Static Gates

**Files:**
- Modify: `style.css:6`
- Test: `/tmp/homepage-sticky-spin-contract.js`

**Interfaces:**
- Consumes: the completed CSS and JavaScript behavior.
- Produces: theme version `0.1.14` and passing static acceptance gates.

- [ ] **Step 1: Increment the theme version**

Change:

```css
Version: 0.1.13
```

to:

```css
Version: 0.1.14
```

- [ ] **Step 2: Run the temporary contract check**

Run:

```bash
node /tmp/homepage-sticky-spin-contract.js /Users/seandm/Projects/stingray-vettes-theme
```

Expected: `homepage sticky-spin contract passed`.

- [ ] **Step 3: Run formatting and scope checks**

Run:

```bash
git diff --check
git status --short
git diff --stat
```

Expected: no whitespace errors; only `assets/homepage/spin.js`, `assets/homepage/homepage.css`, and `style.css` are modified by the implementation.

### Task 5: Verify the Complete Browser Interaction

**Files:**
- Verify: `assets/homepage/spin.js`
- Verify: `assets/homepage/homepage.css`
- Verify: `style.css`

**Interfaces:**
- Consumes: the complete sticky-spin implementation and existing homepage frame assets.
- Produces: evidence that the primary desktop flow and all responsive/accessibility fallbacks match the approved design.

- [ ] **Step 1: Load a local homepage fixture using the real CSS, JavaScript, and frame assets**

Use the repository's existing homepage markup and assets in a temporary local fixture or an available local WordPress environment. Do not edit `front-page.php` and do not call or modify the live site.

- [ ] **Step 2: Verify eligible desktop behavior at 1440 by 900**

Confirm all of the following:

- The hero remains below the 75-pixel topbar during the sticky segment.
- Exactly 600 pixels of forward scroll produce one clockwise revolution.
- The hero releases into `.sc-actions` after the revolution.
- Upward scroll reverses frames without a jump.
- Scrolling below the bounded segment does not continue rotating the car.
- The hint reads `360° · Scroll to spin`.
- Drag-to-spin remains functional.
- Current and next paint loading and accent transitions remain functional.
- No missing assets or console errors appear.

- [ ] **Step 3: Verify responsive and reduced-motion fallbacks**

At 390 by 844, at 1024 by 650, and at 1440 by 900 with reduced motion enabled, confirm:

- The hero remains in normal flow with no extra 600-pixel segment.
- Ordinary vertical scrolling does not rotate the car.
- The hint reads `360° · Drag to spin`.
- Manual drag still rotates the car.
- Hero, topbar, quick actions, and footer do not overlap.

- [ ] **Step 4: Verify restored and resized states**

Reload from the middle and below the desktop sticky segment. Confirm the first rendered frame matches restored progress. Resize across the eligibility thresholds and confirm the page releases or enables sticky behavior without a frame jump.

### Task 6: Final Review, Commit, and Push

**Files:**
- Review: `assets/homepage/spin.js`
- Review: `assets/homepage/homepage.css`
- Review: `style.css`

**Interfaces:**
- Consumes: all passing automated and manual validation evidence.
- Produces: one focused implementation commit pushed to the current `main` branch.

- [ ] **Step 1: Review the final diff against the approved spec**

Run:

```bash
git diff -- assets/homepage/spin.js assets/homepage/homepage.css style.css
git diff --check
node --check assets/homepage/spin.js
node /tmp/homepage-sticky-spin-contract.js /Users/seandm/Projects/stingray-vettes-theme
```

Expected: the diff contains only approved behavior; all gates pass.

- [ ] **Step 2: Remove the temporary contract check**

Delete `/tmp/homepage-sticky-spin-contract.js`. This file is outside the repo and must not be committed.

- [ ] **Step 3: Commit the implementation**

```bash
git add assets/homepage/spin.js assets/homepage/homepage.css style.css
git commit -m "feat: pin homepage hero for one scroll-driven spin"
```

- [ ] **Step 4: Push the current branch**

```bash
git push origin main
```

Expected: `main` pushes successfully to `origin/main` with both the approved documentation and the implementation commits.
