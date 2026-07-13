# WordPress Performance, Compatibility, and Cache Hardening Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Fix the highest-priority performance, WordPress integration, navigation-accessibility, and Factory reliability findings, then prove every public surface on WordPress 7.0.1 and PHP 8.4 without allowing cache uncertainty to consume the validation cycle.

**Architecture:** Keep the existing classic-theme structure, design-system load order, public slugs, plugin-backed embeds, calculator contracts, canonical external order runtime, and no-build deployment model. Make four bounded runtime changes: valid/accessible shared navigation, normal WordPress stylesheet enqueueing, failure-resistant Factory data caching, and a substantially lighter 360-viewer asset pipeline. Cache invalidation is an explicit deployment operation with independent WordPress.com and Cloudflare steps, versioned assets, bounded UI observation, and queryless public proof.

**Tech Stack:** WordPress 7.0.1 classic theme, PHP 8.4, vanilla JavaScript, vanilla CSS, Node's built-in test runner, WordPress.com Business hosting, WordPress.com object/global-edge cache, Cloudflare APO/cache, Formidable Forms, wpDataTables, `cwebp` for transparent WebP conversion.

## Global Constraints

- Keep the repository as a classic WordPress theme with no build system and no new runtime dependencies.
- Do not deploy, SFTP, call live WordPress APIs, purge production caches, or modify the live site without a separate explicit production-deployment approval.
- Preserve the canonical order runtime at `https://order.stingraychevroletcorvette.com/` and the local `/order/` redirect.
- Preserve the public slugs `/order/`, `/deposit/`, `/build-and-price/`, `/calculator/`, `/factory/`, and `/process/`.
- Preserve Formidable form IDs through the page-owned `stingray_embed_shortcode` field and preserve wpDataTable `12` configuration.
- Preserve calculator DOM selectors and vendored Stingcalc behavior.
- Do not edit the canonical design-system repository or casually modify vendored design-system token files.
- Keep selection outline-only, hard-offset hover shadows, short transitions, visible focus, at least 42px hit targets, and reduced-motion handling.
- Treat `style.css` version `0.1.19` as the single cache-busting identity for this pass; do not bump it until code, asset, and staging gates pass.
- Do not use cache-busting query noise to validate HTML or redirects. Public acceptance requests must use the exact queryless URLs customers use.
- Never wait indefinitely for a cache UI message. WordPress.com cache-control observation is bounded to 90 seconds; public freshness evidence decides whether the deployed result is accepted.

## Source and Documentation Basis

- WordPress.com cache clearing: <https://wordpress.com/support/clear-your-sites-cache/>
- Cloudflare URL purge API: <https://developers.cloudflare.com/api/resources/cache/methods/purge/>
- WordPress asset enqueueing: <https://developer.wordpress.org/themes/core-concepts/including-assets/>
- WordPress accessibility: <https://developer.wordpress.org/themes/classic-themes/functionality/accessibility/>
- WordPress/PHP compatibility: <https://make.wordpress.org/core/handbook/references/php-compatibility-and-wordpress-versions/>
- PHP 8.4 CSV behavior: <https://www.php.net/fgetcsv>

---

## File Map

### Create

- `tests/nav.test.js` — shared desktop/mobile navigation semantics and keyboard-state regression.
- `tests/spin-loading.test.js` — verifies WebP frame ownership, constrained-mode loading, and bounded preload concurrency.
- `tests/theme-assets.test.js` — verifies conditional styles, local font policy, logo dimensions/formats, and versioned handles.
- `docs/cache-invalidation-runbook.md` — durable WordPress.com + Cloudflare purge and freshness-proof procedure.

### Modify

- `inc/topbar.php` — valid menu list markup, dialog-like drawer state, intrinsic logo dimensions.
- `inc/site-footer.php` — optimized logo and intrinsic dimensions.
- `assets/js/nav.js` — Escape close, focus entry/return, focus trap, inert/background and scroll state.
- `assets/css/theme.css` — list reset, hidden drawer behavior, and dialog-open scroll handling.
- `functions.php` — normal Factory stylesheet enqueue, conditional surface CSS, local/system font fallback, Factory stale/failure caching, and PHP-future-safe CSV parsing.
- `tests/factory-sheet-filter.php` — success, stale, and failure-cache coverage.
- `assets/homepage/spin.js` — WebP frame URLs, bounded image concurrency, static constrained-data mode.
- `assets/homepage/spin/*.png` — replace with same-stem transparent WebP frames after visual comparison.
- `assets/homepage/crossflags-white.png` — replace with optimized `crossflags-white.webp`.
- `assets/homepage/wordmark-white.png` — replace with optimized `wordmark-white.webp`.
- `README.md` — record the new theme-owned asset deltas and cache runbook.
- `style.css` — update `Tested up to` to `7.0.1` and bump version from `0.1.18` to `0.1.19` only at the release gate.

### Preserve Without Modification

- `page-order.php` and `assets/order/` remain dormant rollback material.
- `assets/calculator/script.js` and `assets/calculator/qp-new.js` remain selector-compatible vendored logic.
- `assets/css/tokens/*` remain vendored design-system files.
- Formidable and wpDataTables records remain WordPress-owned configuration.

---

### Task 1: Establish Failing Regression Gates and Baseline Measurements

**Files:**
- Create: `tests/nav.test.js`
- Create: `tests/spin-loading.test.js`
- Create: `tests/theme-assets.test.js`
- Modify: `tests/factory-sheet-filter.php`

**Interfaces:**
- Consumes: current theme markup, JavaScript, enqueue declarations, Factory filter, and asset tree.
- Produces: regression commands used by every later task and a recorded before-size baseline.

- [ ] **Step 1: Record the clean baseline and current asset sizes**

Run:

```bash
git status --short
php -v
node --version
du -sk assets/homepage/spin
find assets/homepage/spin -type f -name '*.png' | wc -l
find assets/homepage/spin -type f -name '*.png' -exec stat -f '%z' {} + | awk '{ total += $1 } END { print total }'
```

Expected:

```text
git status prints nothing
PHP reports 8.4.x for the compatibility gate
150 PNG spin frames exist
the current spin directory is approximately 84 MB
```

- [ ] **Step 2: Add a navigation regression that fails on the current markup and behavior**

Create `tests/nav.test.js` with assertions that:

```js
var php = fs.readFileSync(path.join(__dirname, '..', 'inc', 'topbar.php'), 'utf8');
var js = fs.readFileSync(path.join(__dirname, '..', 'assets', 'js', 'nav.js'), 'utf8');

assert(php.includes("'items_wrap'     => '<ul id=\"%1$s\" class=\"sc-nav-menu %2$s\">%3$s</ul>'"), 'Primary menu must retain a valid ul wrapper.');
assert(php.includes("'fallback_cb'    => false"), 'The assigned menu render must not silently fall back to an unrelated menu.');
assert(php.includes('hidden aria-hidden="true"'), 'The closed mobile drawer must be absent from interaction and accessibility trees.');
assert(js.includes("'Escape' === event.key"), 'Escape must close the drawer.');
assert(js.includes('previousFocus'), 'Drawer close must restore the prior focus target.');
assert(js.includes("document.body.classList.add('sc-drawer-open')"), 'Open drawer must lock background scrolling.');
```

Use the same `failures`/`assert`/exit structure already used by `tests/calculator-layout.test.js`.

- [ ] **Step 3: Add a spin-loading regression that fails on PNG and unlimited preload**

Create `tests/spin-loading.test.js` with these exact ownership assertions:

```js
assert(source.includes("extension: '.webp'"), 'Spin frames must use the converted WebP sequence.');
assert(source.includes('maxConcurrent: 4'), 'Spin frame fetching must use a four-request concurrency ceiling.');
assert(source.includes('navigator.connection && navigator.connection.saveData'), 'Data-saver mode must be recognized.');
assert(source.includes('loadStaticFrame(colorIdx)'), 'Reduced-motion or data-saver mode must load only a static frame at boot.');
assert(0 === pngFrames.length, 'No PNG spin frames may remain after the conversion pass.');
assert(150 === webpFrames.length, 'Every one of the 150 spin frames must have a WebP replacement.');
```

- [ ] **Step 4: Add a theme-asset regression that fails on current global loading and oversized PNG logos**

Create `tests/theme-assets.test.js` and assert:

```js
assert(!functionsPhp.includes('fonts.googleapis.com'), 'The theme must not block on Google Fonts for a fallback face.');
assert(functionsPhp.includes("if ( ! is_front_page() ) {\n\t\twp_enqueue_style( 'stingray-surfaces'"), 'Interior component CSS must not load on the homepage.');
assert(!functionsPhp.includes("wp_print_styles( 'stingray-embeds' )"), 'Factory CSS must stay in the normal enqueue lifecycle.');
assert(topbarPhp.includes('crossflags-white.webp'), 'The header must use the optimized flags asset.');
assert(topbarPhp.includes('wordmark-white.webp'), 'The header must use the optimized wordmark asset.');
assert(topbarPhp.includes('width="76" height="72"'), 'Flags must declare intrinsic dimensions.');
assert(topbarPhp.includes('width="125" height="72"'), 'Wordmark must declare intrinsic dimensions.');
```

- [ ] **Step 5: Extend Factory tests for failure caching and stale fallback**

Change the HTTP stub in `tests/factory-sheet-filter.php` to read `$GLOBALS['stingray_http_code']`, record transient expiration values, and add these cases:

```php
stingray_test_reset( $headers . "FMNPZX,10/13/2025,5000,\n" );
$fresh = stingray_corvette_filter_factory_sheet_rows( $fallback, 12, $source_url );
$GLOBALS['stingray_http_code'] = 503;
$GLOBALS['stingray_transients'] = array();
$stale_key = 'stingray_factory_sheet_stale_' . md5( $expected_csv_url );
$GLOBALS['stingray_transients'][ $stale_key ] = array( 'rows' => $fresh );
$rows = stingray_corvette_filter_factory_sheet_rows( $fallback, 12, $source_url );
stingray_test_same( $fresh, $rows, 'A failed refresh must return the last successful Factory rows.' );

$calls_after_failure = $GLOBALS['stingray_remote_calls'];
$rows = stingray_corvette_filter_factory_sheet_rows( $fallback, 12, $source_url );
stingray_test_same( $calls_after_failure, $GLOBALS['stingray_remote_calls'], 'A failure transient must suppress repeated remote requests.' );
stingray_test_same( $fresh, $rows, 'The failure window must continue serving stale successful rows.' );
```

- [ ] **Step 6: Prove the new gates fail for the intended reasons**

Run:

```bash
node tests/nav.test.js
node tests/spin-loading.test.js
node tests/theme-assets.test.js
php -d error_reporting=E_ALL tests/factory-sheet-filter.php
```

Expected: failures name the missing menu wrapper/state, PNG/unbounded loading, Google Font/global CSS/footer printing, and missing Factory failure/stale-cache behavior. No test may fail because of a syntax error in the test itself.

- [ ] **Step 7: Commit the test-only baseline**

```bash
git add tests/nav.test.js tests/spin-loading.test.js tests/theme-assets.test.js tests/factory-sheet-filter.php
git commit -m "test: define theme hardening gates"
```

---

### Task 2: Make Shared Navigation Semantically Valid and Keyboard Complete

**Files:**
- Modify: `inc/topbar.php:33-79`
- Modify: `functions.php:365-375`
- Modify: `assets/js/nav.js`
- Modify: `assets/css/theme.css:20-75`
- Test: `tests/nav.test.js`

**Interfaces:**
- Consumes: registered `primary` menu and existing `.sc-*` styling contracts.
- Produces: valid desktop navigation and a mobile drawer with explicit open/closed and focus behavior.

- [ ] **Step 1: Restore valid WordPress menu markup**

Use:

```php
wp_nav_menu(
	array(
		'theme_location' => 'primary',
		'container'      => false,
		'items_wrap'     => '<ul id="%1$s" class="sc-nav-menu %2$s">%3$s</ul>',
		'depth'          => 1,
		'fallback_cb'    => false,
	)
);
```

Render the hard-coded fallback links inside `<ul class="sc-nav-menu sc-nav-menu--fallback">` and wrap each link in `<li>`.

- [ ] **Step 2: Preserve third-party link classes**

Replace the overwrite in `stingray_corvette_nav_link_class()` with:

```php
$existing      = isset( $atts['class'] ) ? trim( $atts['class'] ) : '';
$atts['class'] = trim( $existing . ' sc-nav-link' );
```

- [ ] **Step 3: Give the drawer a real initial hidden state**

Use:

```html
<div class="sc-drawer" id="drawer" hidden aria-hidden="true">
```

Set `type="button"` on both drawer buttons and add `aria-label="Primary navigation"` to the desktop `<nav>`.

- [ ] **Step 4: Implement bounded drawer focus behavior**

In `assets/js/nav.js`, implement these named functions:

```js
function getFocusable() {
	return drawer.querySelectorAll('a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])');
}
function open() {
	previousFocus = document.activeElement;
	drawer.hidden = false;
	drawer.setAttribute('aria-hidden', 'false');
	drawer.classList.add('open');
	document.body.classList.add('sc-drawer-open');
	menuBtn.setAttribute('aria-expanded', 'true');
	closeBtn.focus();
}
function close() {
	drawer.classList.remove('open');
	drawer.hidden = true;
	drawer.setAttribute('aria-hidden', 'true');
	document.body.classList.remove('sc-drawer-open');
	menuBtn.setAttribute('aria-expanded', 'false');
	if (previousFocus && previousFocus.focus) previousFocus.focus();
}
```

Add document-level key handling for Escape and Tab wrapping. Do not set `inert` on `<body>`; the drawer is a body child. If supported, set `inert` only on the shared topbar's non-drawer sibling and the page/footer siblings, restoring their prior state on close.

- [ ] **Step 5: Update styles without changing the visual direction**

Replace `.sc-nav-links li` with `.sc-nav-menu` and `.sc-nav-menu > li` resets, retain the current flex layout, add:

```css
.sc-drawer[hidden] { display: none !important; }
body.sc-drawer-open { overflow: hidden; }
```

- [ ] **Step 6: Run navigation and PHP gates**

```bash
node tests/nav.test.js
php -l inc/topbar.php
php -l functions.php
```

Expected: all pass.

- [ ] **Step 7: Manually verify keyboard navigation on staging/local WordPress**

Verify at 390×844 and 1440×900:

1. Tab reaches the menu button.
2. Enter/Space opens the drawer and focuses Close.
3. Tab and Shift+Tab stay inside the drawer.
4. Escape closes and returns focus to Menu.
5. Link activation closes the drawer.
6. Background does not scroll while open.
7. Desktop assigned-menu and fallback-menu markup both contain `ul > li > a`.

- [ ] **Step 8: Commit**

```bash
git add inc/topbar.php functions.php assets/js/nav.js assets/css/theme.css tests/nav.test.js
git commit -m "fix: harden shared navigation accessibility"
```

---

### Task 3: Normalize Factory CSS Loading and Make Remote Data Failure-Resistant

**Files:**
- Modify: `functions.php:133-313`
- Modify: `tests/factory-sheet-filter.php`
- Test: `tests/theme-assets.test.js`

**Interfaces:**
- Consumes: wpDataTables filter arguments and page-owned Google publication URL.
- Produces: normal head-enqueued Factory CSS and `rows` arrays with fresh, stale, and failure-cache behavior.

- [ ] **Step 1: Enqueue embed CSS normally on all three embed pages**

Change `stingray_corvette_enqueue_embed_styles()` to accept:

```php
if ( ! is_page( array( 'deposit', 'build-and-price', 'factory' ) ) ) {
	return;
}
```

Keep `stingray-surfaces` as the only required dependency. Delete `stingray_corvette_print_factory_embed_styles()`, its `wp_footer` action, its runtime plugin-handle probing, and the direct `wp_print_styles()` call.

- [ ] **Step 2: Define separate fresh, stale, and failure keys**

Immediately after `$csv_url`:

```php
$cache_hash  = md5( $csv_url );
$fresh_key   = 'stingray_factory_sheet_' . $cache_hash;
$stale_key   = 'stingray_factory_sheet_stale_' . $cache_hash;
$failure_key = 'stingray_factory_sheet_failure_' . $cache_hash;
$fresh       = get_transient( $fresh_key );
$stale       = get_transient( $stale_key );
```

Return fresh rows first. If the failure transient exists, return stale rows when available, otherwise return the wpDataTables fallback.

- [ ] **Step 3: Bound remote failure cost**

Use:

```php
$response = wp_safe_remote_get(
	$csv_url,
	array(
		'timeout'     => 4,
		'redirection' => 3,
	)
);
```

On transport error, non-200 response, empty body, stream failure, invalid header, or parse failure, set:

```php
set_transient( $failure_key, 1, MINUTE_IN_SECONDS );
return is_array( $stale ) && isset( $stale['rows'] ) && is_array( $stale['rows'] )
	? $stale['rows']
	: $sheet_rows;
```

Factor that repeated return into one private prefixed helper only if it removes at least three identical branches; do not introduce a class.

- [ ] **Step 4: Cache successful data at two lifetimes**

After successful parsing:

```php
$payload = array( 'rows' => $factory_rows );
set_transient( $fresh_key, $payload, 5 * MINUTE_IN_SECONDS );
set_transient( $stale_key, $payload, 6 * HOUR_IN_SECONDS );
delete_transient( $failure_key );
```

Add `HOUR_IN_SECONDS` and `delete_transient()` stubs to the regression test.

- [ ] **Step 5: Use PHP-future-safe CSV escaping**

Change both `fgetcsv()` calls from a backslash escape to an empty escape string:

```php
fgetcsv( $stream, 0, ',', '"', '' )
```

This remains valid on PHP 7.4+ and avoids PHP's proprietary escape behavior.

- [ ] **Step 6: Run Factory, asset, and PHP 8.4 gates**

```bash
php -d error_reporting=E_ALL tests/factory-sheet-filter.php
node tests/factory-table.test.js
node tests/theme-assets.test.js
php -l functions.php
```

Expected: all pass; a simulated repeated 503 performs only one remote call and serves stale rows.

- [ ] **Step 7: Verify the rendered Factory page**

On a WordPress 7.0.1/PHP 8.4 staging page, require:

- embed CSS link appears in `<head>`, not after page content;
- table `12` renders and filters;
- 14-column rows become dialog triggers;
- a forced remote failure returns the plugin fallback or retained stale rows within approximately four seconds, followed by immediate failure-cache responses;
- no raw shortcode or setup notice appears to logged-out visitors.

- [ ] **Step 8: Commit**

```bash
git add functions.php tests/factory-sheet-filter.php tests/theme-assets.test.js
git commit -m "fix: harden factory asset and data loading"
```

---

### Task 4: Convert and Throttle the Homepage 360-Viewer Assets

**Files:**
- Modify: `assets/homepage/spin.js`
- Replace: `assets/homepage/spin/**/*.png` with same-stem `.webp`
- Modify: `README.md`
- Test: `tests/spin-loading.test.js`

**Interfaces:**
- Consumes: five paints, 30 frames each, same folder/prefix naming, `window.SC_SPIN_BASE`.
- Produces: 150 transparent WebP frames, four-request maximum concurrency, static constrained-data mode, unchanged visual interaction for normal connections.

- [ ] **Step 1: Convert one representative frame and approve visual quality**

Run:

```bash
cwebp -quiet -q 82 -alpha_q 90 -m 6 -mt \
  assets/homepage/spin/gkz-red/gkz-ext.001.png \
  -o /tmp/gkz-ext.001.webp
```

Compare transparency, edge quality, paint gradients, shadow, and body-panel detail at 100% and at the actual canvas size. Expected representative output is roughly 40–80 KB instead of approximately 550 KB. If the visual comparison fails, stop this task; do not bulk-convert at a lower quality.

- [ ] **Step 2: Convert all frames with deterministic settings**

Run from the repository root:

```bash
find assets/homepage/spin -type f -name '*.png' -print0 | while IFS= read -r -d '' source; do
  target="${source%.png}.webp"
  cwebp -quiet -q 82 -alpha_q 90 -m 6 -mt "$source" -o "$target" || exit 1
done
```

Require exactly 150 WebP files before removing any PNG:

```bash
test "$(find assets/homepage/spin -type f -name '*.webp' | wc -l | tr -d ' ')" = "150"
```

- [ ] **Step 3: Change frame ownership to WebP and add bounded concurrency**

Add to `CONFIG`:

```js
extension: '.webp',
maxConcurrent: 4
```

Make `frameUrl()` append `CONFIG.extension`. Replace the 30-request loop with a queue ordered `0, 1, 29, 2, 28, ...`; start at most `CONFIG.maxConcurrent` image requests and start the next only from the preceding image's `load` or `error` handler.

- [ ] **Step 4: Add constrained-data static mode**

Define:

```js
var saveData = !!(navigator.connection && navigator.connection.saveData);
var constrained = reduce || saveData;
```

Implement `loadStaticFrame(idx)` to create only frame zero for that color. At boot call `loadStaticFrame(colorIdx)` when constrained; otherwise call `preloadColor(colorIdx)`. Disable drag, scroll-spin, paint preloading, and accent animation when constrained while retaining the visible first vehicle frame and the text label `360° View`.

- [ ] **Step 5: Remove PNG only after the WebP regression passes**

Run:

```bash
node tests/spin-loading.test.js
find assets/homepage/spin -type f -name '*.png' -delete
node tests/spin-loading.test.js
```

Expected: first run fails only because PNGs coexist; second run passes with 150 WebP and zero PNG.

- [ ] **Step 6: Enforce a size ceiling**

```bash
bytes=$(find assets/homepage/spin -type f -name '*.webp' -exec stat -f '%z' {} + | awk '{ total += $1 } END { print total }')
test "$bytes" -le 12000000
```

Expected: the full five-color sequence is no more than 12 MB, down from approximately 84 MB. Record actual before/after totals in the README.

- [ ] **Step 7: Browser-test every viewer mode**

Verify:

- desktop normal connection: first frame paints promptly, scroll completes one revolution, next color becomes available without blank frames;
- mobile normal connection: drag works, scrolling remains vertical, no horizontal overflow;
- reduced motion: one static frame loads, no animated accent or scroll-driven spin occurs;
- emulated Save-Data: one static frame loads and no additional paint/frame requests occur;
- all five paints retain transparency and correct framing;
- canvas resize at 390×844 and 1440×900 does not blur or crop the vehicle.

- [ ] **Step 8: Document the new vendored delta and commit**

Update README homepage assets to state that the theme converts canonical PNG frames to transparent WebP using the exact command/settings above; canonical source remains the 27vette PNG set.

```bash
git add assets/homepage/spin assets/homepage/spin.js tests/spin-loading.test.js README.md
git commit -m "perf: reduce homepage spin payload"
```

---

### Task 5: Reduce Global Asset Cost and Layout Shift

**Files:**
- Modify: `functions.php:60-95`
- Replace: `assets/homepage/crossflags-white.png` with `crossflags-white.webp`
- Replace: `assets/homepage/wordmark-white.png` with `wordmark-white.webp`
- Modify: `inc/topbar.php`
- Modify: `inc/site-footer.php`
- Modify: `README.md`
- Test: `tests/theme-assets.test.js`

**Interfaces:**
- Consumes: existing theme CSS hierarchy and shared logo classes.
- Produces: one fewer external stylesheet/origin, interior-only component CSS, intrinsic logo geometry, and optimized transparent logo assets.

- [ ] **Step 1: Remove the Google Fonts stylesheet dependency**

Delete the `stingray-inter` enqueue. Change `stingray-ds-colors` to depend on `stingray-fonts`. Do not modify the design-system token: `Inter` may remain first in the fallback string for devices that already have it locally; all other devices continue to the system stack.

- [ ] **Step 2: Make interior component CSS conditional**

Use:

```php
if ( ! is_front_page() ) {
	wp_enqueue_style( 'stingray-surfaces', $uri . '/assets/css/surfaces.css', array( 'stingray-theme' ), $ver );
}
```

Homepage CSS continues depending only on `stingray-theme`. Dedicated interior assets continue depending on `stingray-surfaces`.

- [ ] **Step 3: Generate correctly sized transparent logos**

Run:

```bash
cwebp -quiet -q 90 -alpha_q 100 -resize 76 72 -m 6 -mt assets/homepage/crossflags-white.png -o assets/homepage/crossflags-white.webp
cwebp -quiet -q 90 -alpha_q 100 -resize 125 72 -m 6 -mt assets/homepage/wordmark-white.png -o assets/homepage/wordmark-white.webp
```

Visually compare against the originals on dark carbon at header and footer sizes. Then update references and remove the two PNG originals.

- [ ] **Step 4: Add intrinsic image dimensions and decoding policy**

Use `width="76" height="72" decoding="async"` on crossflags and `width="125" height="72" decoding="async"` on wordmarks. The first header wordmark and flags are above the fold, so do not add `loading="lazy"`. Add `loading="lazy"` to the drawer wordmark and footer wordmark.

- [ ] **Step 5: Run asset and template gates**

```bash
node tests/theme-assets.test.js
php -l functions.php
php -l inc/topbar.php
php -l inc/site-footer.php
```

Expected: all pass; no `fonts.googleapis.com`, PNG logo reference, or global homepage `surfaces.css` enqueue remains.

- [ ] **Step 6: Verify no visual or layout regressions**

At 390×844 and 1440×900, require header/footer logos to retain the same apparent size and alignment, no topbar layout shift, no missing interior card/accordion styling, and no missing homepage card styling.

- [ ] **Step 7: Document and commit**

Update README to record optimized WebP logo deltas and the removal of the remote Inter request.

```bash
git add functions.php inc/topbar.php inc/site-footer.php assets/homepage/crossflags-white.webp assets/homepage/wordmark-white.webp README.md tests/theme-assets.test.js
git add -u assets/homepage/crossflags-white.png assets/homepage/wordmark-white.png
git commit -m "perf: reduce shared theme assets"
```

---

### Task 6: Create the Cache Invalidation Runbook and Automated Freshness Evidence

**Files:**
- Create: `docs/cache-invalidation-runbook.md`
- Modify: `README.md`

**Interfaces:**
- Consumes: release version, deployed public URLs, WordPress.com Hosting Dashboard access, production SSH/WP-CLI access, Cloudflare zone/token access.
- Produces: bounded cache-clear operation and replayable queryless evidence for each public surface.

- [ ] **Step 1: Document the cache layers and their owners**

The runbook must name these distinct layers:

1. Browser cache — controlled by a fresh browser context or DevTools Disable Cache during QA; never treated as proof of public freshness.
2. WordPress object cache — Memcached-backed database/object results; clear through production `wp cache flush` or WordPress.com `Clear all`.
3. WordPress.com global edge cache — HTML/static delivery; clear through Hosting Dashboard → site → Settings → Server → Caching → Clear all.
4. Cloudflare APO/cache — public HTML edge entries in front of WordPress.com; purge exact affected URLs through Cloudflare.
5. Versioned theme assets — `?ver=0.1.19`; freshness comes from a new URL/cache key, not waiting for the prior asset to expire.

- [ ] **Step 2: Define the exact deployment purge order**

Document this order:

```text
1. Upload checksum-matched release files.
2. Confirm active style.css reports Version 0.1.19.
3. Run wp cache flush once in the correct WordPress.com production environment.
4. Click WordPress.com Production Clear all once.
5. Observe the WordPress.com control for at most 90 seconds; do not click again.
6. Purge only the affected queryless public URLs in Cloudflare.
7. Run exact queryless cold and immediate warm verification twice.
8. Open a new browser context and run functional QA.
```

WordPress.com's current documentation says the buttons may remain disabled for one minute. Therefore 90 seconds is a sufficient bounded operator check. Both success messages are useful evidence, but missing transient UI text is not by itself a production failure if the authenticated action was issued once and the subsequent public response proves the new release.

- [ ] **Step 3: Define a targeted Cloudflare purge request**

Store no token or zone ID in the repository. Use environment variables and `/tmp`:

```bash
purge_payload=$(jq -nc '{files: [
  "https://stingraychevroletcorvette.com/",
  "https://stingraychevroletcorvette.com/deposit/",
  "https://stingraychevroletcorvette.com/build-and-price/",
  "https://stingraychevroletcorvette.com/calculator/",
  "https://stingraychevroletcorvette.com/factory/",
  "https://stingraychevroletcorvette.com/process/",
  "https://stingraychevroletcorvette.com/order/"
]}')

curl --fail-with-body -sS \
  -X POST "https://api.cloudflare.com/client/v4/zones/${CLOUDFLARE_ZONE_ID}/purge_cache" \
  -H "Authorization: Bearer ${CLOUDFLARE_API_TOKEN}" \
  -H 'Content-Type: application/json' \
  --data "$purge_payload" \
  | tee /tmp/stingray-purge-response.json
```

Require JSON `success: true`. Do not use `purge_everything` for a theme release.

- [ ] **Step 4: Define exact queryless public verification**

The runbook must provide:

```bash
evidence_dir="/tmp/stingray-cache-proof-$(date -u +%Y%m%dT%H%M%SZ)"
mkdir -p "$evidence_dir"

for path in '' deposit/ build-and-price/ calculator/ factory/ process/; do
  name=${path:-home}
  name=${name%/}
  for pass in cold warm; do
    curl --compressed --fail-with-body -sS \
      -D "$evidence_dir/${name}.${pass}.headers" \
      -o "$evidence_dir/${name}.${pass}.html" \
      "https://stingraychevroletcorvette.com/${path}"
  done
done

for pass in cold warm; do
  curl --compressed -sS \
    -D "$evidence_dir/order.${pass}.headers" \
    -o "$evidence_dir/order.${pass}.body" \
    https://stingraychevroletcorvette.com/order/
done
```

Do not append timestamps, random query parameters, or fragments.

- [ ] **Step 5: Define freshness acceptance, not waiting**

For both cold and warm responses require:

- `/`, `/deposit/`, `/build-and-price/`, `/calculator/`, `/factory/`, `/process/` return `200`;
- HTML contains `wp-theme-stingray-corvette`, `sc-topbar`, and `sc-site-footer`;
- homepage contains `sc-hero`;
- every local theme CSS/JS URL extracted from HTML uses `ver=0.1.19`;
- `/order/` returns an empty-body `302` with exact `Location: https://order.stingraychevroletcorvette.com/`;
- no Elementor IDs `58074`, `32458`, or `data-elementor-id` appear;
- no raw shortcode, `Embed shortcode missing.`, `Embed shortcode did not render.`, PHP warning, fatal error, or critical-error marker appears;
- every extracted required local CSS, JavaScript, font, and image returns `200`.

Capture `age`, `cf-cache-status`, `cache-control`, `etag`, and WordPress.com cache headers when present, but do not require a particular `CF-Cache-Status` value. A new release marker and versioned URLs in two queryless responses are the freshness proof.

- [ ] **Step 6: Define what happens when the WordPress.com UI confirmation is absent**

The runbook must say:

```text
- Do not wait beyond 90 seconds.
- Do not click Clear all twice.
- Record “UI confirmation not retained; purge action issued once.”
- Continue with targeted Cloudflare purge and public cold/warm proof.
- If public proof shows 0.1.19 consistently, cache validation passes.
- If public proof still shows the prior version or prior HTML markers, stop and diagnose the owning cache layer; do not sleep and hope.
```

- [ ] **Step 7: Link the runbook from README and commit**

```bash
git add docs/cache-invalidation-runbook.md README.md
git commit -m "docs: make cache invalidation deterministic"
```

---

### Task 7: Run Full WordPress 7.0.1 / PHP 8.4 Staging Acceptance

**Files:**
- Modify only if a concrete staging failure is attributable to this pass.
- Evidence: create a timestamped ignored directory under `.superpowers/sdd/`.

**Interfaces:**
- Consumes: completed Tasks 1–6 and a staging WordPress 7.0.1/PHP 8.4 environment.
- Produces: complete cross-surface acceptance evidence before the release version bump.

- [ ] **Step 1: Run the complete local gate**

```bash
find . -path './assets/order' -prune -o -name '*.php' -print0 | xargs -0 -n1 "$(brew --prefix php@8.4)/bin/php" -l
"$(brew --prefix php@8.4)/bin/php" -d error_reporting=E_ALL tests/legacy-redirects.php
"$(brew --prefix php@8.4)/bin/php" -d error_reporting=E_ALL tests/factory-sheet-filter.php
node --test tests/*.test.js
git diff --check
```

Expected: all pass with no warnings from theme code.

- [ ] **Step 2: Upload the candidate to staging without syncing the staging database to production**

Upload only the theme files. Confirm remote/local checksums for every changed PHP, CSS, JS, WebP, README, and style header file. Do not use WordPress.com staging-to-production database sync because production forms, table configuration, page records, and activation state must remain authoritative.

- [ ] **Step 3: Clear staging caches using the new runbook**

Use staging `wp cache flush`, staging WordPress.com `Clear all`, and any staging Cloudflare layer only if that hostname is proxied. Apply the same 90-second maximum UI observation and queryless public proof. Do not use production cache controls.

- [ ] **Step 4: Verify all seven routes and redirect ownership**

Require:

| Route | Expected |
|---|---|
| `/` | `200`, homepage markers, working 360 viewer |
| `/deposit/` | `200`, Formidable form `8` |
| `/build-and-price/` | `200`, Formidable form `30` |
| `/calculator/` | `200`, both calculator modes work |
| `/factory/` | `200`, wpDataTable `12`, row dialog works |
| `/process/` | `200`, process content |
| `/order/` | direct `302` to canonical order runtime |

Also run all seven legacy redirect first hops and confirm `/process-links/` ownership separately from the theme when Cloudflare intercepts it.

- [ ] **Step 5: Verify browser, device, and accessibility coverage**

In fresh browser contexts, test Chromium desktop 1440×900 and mobile 390×844, plus one WebKit/Safari-equivalent pass where available:

- topbar and drawer keyboard behavior;
- homepage viewer normal, reduced-motion, and Save-Data modes;
- logo loading and no layout shift;
- Formidable labels, required states, validation presentation, and submit controls without submitting customer data;
- Factory search/filter/pagination, row keyboard activation, modal focus trap/return;
- calculator primary flow and Quick Pencil decimal input;
- process accordions and all internal/external links;
- order redirect and canonical app return link;
- no horizontal overflow or console errors.

- [ ] **Step 6: Measure delivery and enforce regressions**

Record Lighthouse or equivalent mobile/desktop results and compare:

- initial homepage spin transfer target: no more than 2.5 MB before interaction on a normal cold load;
- all 150 spin assets: no more than 12 MB total in the package;
- no request to `fonts.googleapis.com` or `fonts.gstatic.com`;
- no `surfaces.css` request on homepage;
- Factory CSS appears in `<head>`;
- no failed required resource;
- no new render-blocking third-party request.

- [ ] **Step 7: Correct only failures caused by this plan and rerun the owning task gate**

Do not fix unrelated plugin or content issues. Any changed code returns to its task-specific automated and browser gate before this full gate is rerun.

---

### Task 8: Set Release Identity and Prepare a No-Deploy Handoff

**Files:**
- Modify: `style.css:7-10`
- Modify: `README.md`
- Modify: `docs/Current-readiness-report.md`

**Interfaces:**
- Consumes: passing local and staging evidence.
- Produces: cache-busted `0.1.19` candidate and an explicit production runbook; does not deploy.

- [ ] **Step 1: Update compatibility metadata and release version**

Set:

```text
Version: 0.1.19
Tested up to: 7.0.1
Requires PHP: 7.4
```

Keep `Requires PHP: 7.4`; it declares the supported minimum, while PHP 8.4 is the tested production target.

- [ ] **Step 2: Rerun the full local release gate after the version bump**

```bash
find . -path './assets/order' -prune -o -name '*.php' -print0 | xargs -0 -n1 "$(brew --prefix php@8.4)/bin/php" -l
"$(brew --prefix php@8.4)/bin/php" -d error_reporting=E_ALL tests/legacy-redirects.php
"$(brew --prefix php@8.4)/bin/php" -d error_reporting=E_ALL tests/factory-sheet-filter.php
node --test tests/*.test.js
git diff --check
git status --short
```

- [ ] **Step 3: Update readiness documentation with exact measured results**

Record:

- before/after spin package and initial-transfer sizes;
- navigation accessibility result;
- Factory failure/stale-cache timing;
- WordPress 7.0.1/PHP 8.4 staging result;
- all route, embed, calculator, Factory, redirect, resource, and browser gates;
- `0.1.19` release identity;
- explicit statement that production was not deployed or purged;
- production cache sequence copied from `docs/cache-invalidation-runbook.md`.

- [ ] **Step 4: Commit the release candidate**

```bash
git add style.css README.md docs/Current-readiness-report.md
git commit -m "chore: prepare theme 0.1.19 hardening release"
```

- [ ] **Step 5: Stop for explicit production-deployment approval**

The handoff must name the exact commit, version, changed files, staging proof, rollback version, and the seven Cloudflare purge URLs. Production upload, activation changes, cache controls, and Cloudflare API calls require the next explicit approval.

---

## Production Execution Gate After Separate Approval

When production deployment is separately approved, execute only this sequence:

1. Confirm production still runs the expected prior version and capture public cold/warm baseline.
2. Upload checksum-matched `0.1.19` theme files; do not change page records, embeds, forms, wpDataTable configuration, Elementor state, or Cloudflare redirect rules.
3. Confirm remote `style.css` and representative changed files match the release checksums.
4. Run production `wp cache flush` once.
5. Click WordPress.com Production `Clear all` once and observe for no more than 90 seconds.
6. Purge the seven exact Cloudflare URLs from the runbook; require API `success: true`.
7. Run queryless cold/warm route and asset proof immediately; do not add query parameters.
8. Run fresh-context desktop/mobile browser acceptance across every surface.
9. If the new version and markers are stable twice, leave live. If the prior version persists, identify the response/cache owner from headers and purge only that layer; do not wait blindly.
10. Roll back only for a concrete runtime, render, asset, form, calculator, Factory, accessibility, or redirect failure—not because a transient cache toast was missed while public proof passes.

## Non-Goals and Follow-Up Boundary

- Do not move redirect or Factory integration code into a site plugin in this pass. That is a separate architecture/deployment project because the current repository deploys only the theme root.
- Do not reactivate the dormant local order form.
- Do not refactor calculator logic.
- Do not redesign the site or change business copy.
- Do not add a caching plugin; WordPress.com already supplies object and edge caching.
- Do not use staging-to-production database sync for these theme-file changes.

## Final Acceptance Checklist

- [ ] PHP 8.4 lint and regressions pass with `E_ALL`.
- [ ] All Node regressions pass.
- [ ] Primary menu output is valid `ul > li > a` markup.
- [ ] Mobile drawer passes keyboard/focus/background-interaction checks.
- [ ] Factory CSS loads through the normal enqueue lifecycle in `<head>`.
- [ ] Factory failure cache prevents repeated remote waits and serves stale successful data.
- [ ] All 150 viewer frames are WebP, total no more than 12 MB.
- [ ] Initial homepage viewer transfer is no more than 2.5 MB before interaction.
- [ ] Reduced-motion and Save-Data modes load one static frame.
- [ ] Google Fonts request is removed.
- [ ] Homepage does not request `surfaces.css`.
- [ ] Shared logos are optimized WebP with intrinsic dimensions.
- [ ] Every public route, embed, calculator, Factory interaction, redirect, and required asset passes on WordPress 7.0.1/PHP 8.4 staging.
- [ ] Cache runbook is complete, bounded, and linked from README.
- [ ] `0.1.19` is applied only after staging acceptance.
- [ ] No production deployment or cache purge occurs without separate approval.
