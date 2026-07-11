# Production Theme Cutover Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Clear the remaining launch blockers, activate the exact Stingray Corvette theme in production, and leave one canonical high-performance order runtime with verified redirects, page configuration, documentation, and rollback.

**Architecture:** The WordPress theme owns the homepage, six local page records, shared chrome, and temporary legacy redirects. The 27vette `form-app/` directory remains the only active order-form source and continues to serve from Cloudflare; WordPress `/order/` redirects to it. Production WordPress owns Formidable forms 8 and 30, wpDataTable 12, page records, and page-level `stingray_embed_shortcode` configuration.

**Tech Stack:** Classic WordPress theme, PHP 7.4-compatible WordPress APIs, vanilla CSS/JS, WordPress.com Business staging/production, Formidable Forms, wpDataTables 7.5.1, Cloudflare static order runtime, SFTP, browser QA.

## Global Constraints

- Do not add a CSS/JavaScript compiler, minifier, dependency, package manifest, or build step.
- Preserve the canonical design-system enqueue order in `functions.php`.
- Preserve conditional asset loading and the homepage 360-frame lazy-loading strategy.
- Use wpDataTable 12 exactly; do not create, import, replace, or renumber the production Factory table.
- Treat Formidable form 8 as verification-only unless a current customer-visible defect is reproduced.
- Do not change pricing, RPO rules, calculator logic, Formidable workflows, Turnstile configuration, or the order submission endpoint.
- Do not submit a real order or customer form during routine QA.
- Do not activate the dormant theme order form as a second runtime.
- Do not delete legacy production pages during the reversible launch.
- Do not change unrelated public pages.
- Do not mutate production until the controller has obtained explicit approval for the named production task.
- Preserve unrelated work in both repositories.

---

### Task 1: Freeze the Two-Repository Baseline and Deployment Evidence

**Files:**
- Inspect: `/Users/seandm/Projects/stingray-vettes-theme`
- Inspect: `/Users/seandm/Projects/27vette`
- Record evidence in the controller handoff; do not create a repo file in this task.

**Interfaces:**
- Consumes: approved design `docs/superpowers/specs/2026-07-10-production-cutover-design.md` and current Git remotes.
- Produces: exact theme base commit/version, clean 27vette `origin/main` commit, and confirmed production-mutation boundaries used by every following task.

- [ ] **Step 1: Verify the theme repository baseline**

Run:

```bash
cd /Users/seandm/Projects/stingray-vettes-theme
git status --short
git branch --show-current
git rev-list --left-right --count origin/main...HEAD
git log -3 --oneline --decorate
awk -F': ' '/^Version:/{print $2; exit}' style.css
```

Expected: branch `main`; no unexplained working-tree changes; local commits are identified; version is `0.1.15` before runtime changes.

- [ ] **Step 2: Verify 27vette without touching its current branch**

Run:

```bash
git -C /Users/seandm/Projects/27vette status --short
git -C /Users/seandm/Projects/27vette branch --show-current
git -C /Users/seandm/Projects/27vette fetch origin main
git -C /Users/seandm/Projects/27vette rev-parse origin/main
git -C /Users/seandm/Projects/27vette show origin/main:form-app/index.html | sed -n '14,28p'
```

Expected: the current unrelated branch and any local changes are recorded but untouched; `origin/main` contains the unlinked `.brand-block` markup.

- [ ] **Step 3: Confirm production remains read-only**

Run the public checks only:

```bash
curl -sS -o /dev/null -w '%{http_code}\n' https://stingraychevroletcorvette.com/
curl -sS -o /dev/null -w '%{http_code}\n' https://order.stingraychevroletcorvette.com/
curl -sS 'https://stingraychevroletcorvette.com/wp-json/wp/v2/pages?per_page=100&_fields=id,slug,status,link' \
  | jq -r '.[] | [.id,.slug,.status,.link] | @tsv' | sort
```

Expected: both public roots return `200`; no production data is changed; existing replacement-page state is captured before mutation.

---

### Task 2: Add the Canonical Order-Form Homepage Link

**Files:**
- Modify in a clean 27vette worktree: `form-app/index.html:18-20`
- Modify in the same worktree: `form-app/styles.css:99-115`
- Test temporarily: `/tmp/order-home-link-contract.js`

**Interfaces:**
- Consumes: clean `origin/main` and the existing `.brand-block`, `.brand-crossflags`, and `.brand-wordmark` contracts.
- Produces: `.brand-home-link` pointing to `https://stingraychevroletcorvette.com/` with visible keyboard focus and unchanged order-form behavior.

- [ ] **Step 1: Create an isolated 27vette worktree from `origin/main`**

Use the `superpowers:using-git-worktrees` skill. Create branch `codex/order-home-link` from the fetched `origin/main`; do not switch or edit the existing `/Users/seandm/Projects/27vette` checkout.

Expected: a clean worktree path is recorded as `ORDER_WORKTREE` and `git -C "$ORDER_WORKTREE" status --short` is empty.

- [ ] **Step 2: Add the failing dependency-free contract check**

Create `/tmp/order-home-link-contract.js` with `apply_patch`:

```js
'use strict';

const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');

const root = process.argv[2];
const html = fs.readFileSync(path.join(root, 'form-app/index.html'), 'utf8');
const css = fs.readFileSync(path.join(root, 'form-app/styles.css'), 'utf8');

assert.match(
  html,
  /<a class="brand-home-link" href="https:\/\/stingraychevroletcorvette\.com\/" aria-label="Return to Stingray Corvette home">[\s\S]*?brand-crossflags[\s\S]*?brand-wordmark[\s\S]*?<\/a>/
);
assert.match(css, /\.brand-home-link\s*\{/);
assert.match(css, /\.brand-home-link:focus-visible\s*\{/);
assert.doesNotMatch(html, /<a[^>]*brand-home-link[\s\S]*?<h1 id="appTitle"/);

console.log('order homepage-link contract passed');
```

- [ ] **Step 3: Run the contract and confirm the pre-change failure**

Run:

```bash
node /tmp/order-home-link-contract.js "$ORDER_WORKTREE"
```

Expected: failure on the missing `brand-home-link` anchor.

- [ ] **Step 4: Wrap only the two brand images**

Replace the two image lines inside `.brand-block` with:

```html
<a class="brand-home-link" href="https://stingraychevroletcorvette.com/" aria-label="Return to Stingray Corvette home">
  <img class="brand-crossflags" src="./assets/logo/crossflags-white.png" alt="">
  <img class="brand-wordmark" src="./assets/logo/stingray-wordmark-white.png" alt="">
</a>
```

Keep `.brand-divider`, eyebrow, title, and toolbar outside the link. The empty image alt attributes prevent duplicate accessible names because the anchor supplies the complete label.

- [ ] **Step 5: Add layout-preserving focus styling**

Insert immediately after `.brand-block`:

```css
.brand-home-link {
  display: inline-flex;
  align-items: center;
  gap: 14px;
  flex: 0 0 auto;
  border-radius: 4px;
}

.brand-home-link:focus-visible {
  outline: 2px solid var(--accent);
  outline-offset: 4px;
}
```

Do not change the existing image-height rules or mobile selectors.

- [ ] **Step 6: Run static and scope gates**

Run:

```bash
node /tmp/order-home-link-contract.js "$ORDER_WORKTREE"
node --check "$ORDER_WORKTREE/form-app/app.js"
git -C "$ORDER_WORKTREE" diff --check
git -C "$ORDER_WORKTREE" diff -- form-app/index.html form-app/styles.css
```

Expected: contract passes; JavaScript syntax passes; only the approved anchor and CSS rules appear.

- [ ] **Step 7: Run browser QA before publishing**

Serve `form-app/` from the worktree with a temporary local static server and verify at 1440×900 and 390×844:

- the two logos remain aligned;
- the order title and toolbar do not move into the link;
- Tab reaches the brand link with a visible outline;
- activation opens `https://stingraychevroletcorvette.com/`;
- step navigation, summary controls, and vehicle selection still operate; and
- there is no page-level overflow or console error.

- [ ] **Step 8: Commit and publish through the canonical source**

Run:

```bash
git -C "$ORDER_WORKTREE" add form-app/index.html form-app/styles.css
git -C "$ORDER_WORKTREE" commit -m "feat: link order form branding to homepage"
git -C "$ORDER_WORKTREE" push origin HEAD:main
```

Expected: `origin/main` advances with only the order-branding change. If branch protection rejects the push, stop and use the repository's normal pull-request path; do not bypass protection.

- [ ] **Step 9: Verify the canonical deployment**

Poll the public runtime until the source update appears, then verify:

```bash
curl -fsSL https://order.stingraychevroletcorvette.com/ | rg 'brand-home-link|Return to Stingray Corvette home'
curl -fsSL https://order.stingraychevroletcorvette.com/styles.css | rg 'brand-home-link:focus-visible'
curl -sS --compressed -D - -o /dev/null https://order.stingraychevroletcorvette.com/styles.css \
  | rg -i '^(HTTP|content-encoding|cache-control|cf-cache-status)'
```

Expected: public HTML/CSS contains the link and focus rule; response is `200` and compressed. If the public runtime does not update after the normal deployment window, stop and report the missing 27vette-to-Cloudflare deployment connector as an external blocker.

---

### Task 3: Add Theme-Owned Temporary Legacy Redirects

**Files:**
- Create: `inc/legacy-redirects.php`
- Create: `tests/legacy-redirects.php`
- Modify: `functions.php:28-30`
- Modify: `style.css:7`

**Interfaces:**
- Consumes: WordPress `template_redirect`, `home_url()`, `wp_parse_url()`, `wp_unslash()`, and `wp_safe_redirect()`.
- Produces: `stingray_corvette_legacy_redirect_map()`, `stingray_corvette_normalize_legacy_path($request_uri)`, `stingray_corvette_get_legacy_redirect_path($request_uri)`, and `stingray_corvette_redirect_legacy_path()`.

- [ ] **Step 1: Write the failing PHP contract test**

Create `tests/legacy-redirects.php` with:

```php
<?php
/** Run: php tests/legacy-redirects.php */

function add_action() {}
function wp_parse_url( $url, $component = -1 ) { return parse_url( $url, $component ); }
function trailingslashit( $value ) { return rtrim( $value, '/' ) . '/'; }
function wp_unslash( $value ) { return $value; }
function home_url( $path = '' ) { return 'https://stingraychevroletcorvette.com' . $path; }
function wp_safe_redirect() { return true; }

require dirname( __DIR__ ) . '/inc/legacy-redirects.php';

$expected = array(
	'/order-landing-page/'                            => '/order/',
	'/order-and-production-update/'                  => '/order/',
	'/order-landing-page/deposit-form/'               => '/deposit/',
	'/order-landing-page/build-and-price-link-share/' => '/build-and-price/',
	'/orders-in-production/'                          => '/factory/',
	'/corvette-process-guide/'                        => '/process/',
	'/process-links/'                                  => '/process/',
);
$failures = array();

if ( $expected !== stingray_corvette_legacy_redirect_map() ) {
	$failures[] = 'The legacy redirect map must match the approved paths exactly.';
}

foreach ( $expected as $source => $destination ) {
	if ( $destination !== stingray_corvette_get_legacy_redirect_path( $source . '?source=test' ) ) {
		$failures[] = "Failed mapping: {$source}";
	}
	if ( isset( $expected[ $destination ] ) ) {
		$failures[] = "Redirect destination is also a legacy source: {$destination}";
	}
}

if ( '/factory/' !== stingray_corvette_get_legacy_redirect_path( '/orders-in-production' ) ) {
	$failures[] = 'A missing trailing slash must still normalize safely.';
}

if ( '' !== stingray_corvette_get_legacy_redirect_path( '/learning-center/' ) ) {
	$failures[] = 'Unmapped public pages must remain unchanged.';
}

if ( $failures ) {
	fwrite( STDERR, implode( "\n", $failures ) . "\n" );
	exit( 1 );
}

echo "Legacy redirect regression tests passed.\n";
```

- [ ] **Step 2: Run the test and confirm the pre-change failure**

Run:

```bash
php tests/legacy-redirects.php
```

Expected: failure because `inc/legacy-redirects.php` does not exist.

- [ ] **Step 3: Implement the isolated redirect component**

Create `inc/legacy-redirects.php` with:

```php
<?php
/**
 * Temporary, reversible redirects from replaced legacy surfaces.
 *
 * @package Stingray_Corvette
 */

function stingray_corvette_legacy_redirect_map() {
	return array(
		'/order-landing-page/'                            => '/order/',
		'/order-and-production-update/'                  => '/order/',
		'/order-landing-page/deposit-form/'               => '/deposit/',
		'/order-landing-page/build-and-price-link-share/' => '/build-and-price/',
		'/orders-in-production/'                          => '/factory/',
		'/corvette-process-guide/'                        => '/process/',
		'/process-links/'                                  => '/process/',
	);
}

function stingray_corvette_normalize_legacy_path( $request_uri ) {
	$path = wp_parse_url( $request_uri, PHP_URL_PATH );
	if ( ! is_string( $path ) || '' === $path ) {
		return '';
	}

	return trailingslashit( '/' . ltrim( $path, '/' ) );
}

function stingray_corvette_get_legacy_redirect_path( $request_uri ) {
	$path = stingray_corvette_normalize_legacy_path( $request_uri );
	$map  = stingray_corvette_legacy_redirect_map();

	return isset( $map[ $path ] ) ? $map[ $path ] : '';
}

function stingray_corvette_redirect_legacy_path() {
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$destination = stingray_corvette_get_legacy_redirect_path( $request_uri );

	if ( '' === $destination ) {
		return;
	}

	wp_safe_redirect( home_url( $destination ), 302, 'Stingray Corvette' );
	exit;
}
add_action( 'template_redirect', 'stingray_corvette_redirect_legacy_path', 0 );
```

- [ ] **Step 4: Load the redirect component from the theme bootstrap**

Insert after `add_action( 'after_setup_theme', 'stingray_corvette_setup' );` in `functions.php`:

```php
require_once get_template_directory() . '/inc/legacy-redirects.php';
```

- [ ] **Step 5: Advance the cache-busting theme version**

Change the `style.css` header from:

```css
Version: 0.1.15
```

to:

```css
Version: 0.1.16
```

- [ ] **Step 6: Run the focused test and lint gates**

Run:

```bash
php tests/legacy-redirects.php
php -l inc/legacy-redirects.php
php -l functions.php
git diff --check
```

Expected: regression test passes and all lint/whitespace checks pass.

- [ ] **Step 7: Commit the redirect component**

Run:

```bash
git add inc/legacy-redirects.php tests/legacy-redirects.php functions.php style.css
git commit -m "feat: redirect replaced legacy surfaces"
```

Expected: one focused theme commit containing only the redirect component, test, bootstrap require, and version bump.

---

### Task 4: Run the Full Local and Performance Gate

**Files:**
- Verify: all root PHP files, `inc/*.php`, `tests/*.php`, theme JavaScript, and theme asset enqueues.
- Do not modify runtime files unless a failure is caused by Tasks 2 or 3.

**Interfaces:**
- Consumes: theme `0.1.16` and canonical order-link commit.
- Produces: a release candidate proven syntactically clean, regression-safe, conditionally loaded, compressed in deployment environments, and not dependent on a new compiler.

- [ ] **Step 1: Run every local executable gate**

Run:

```bash
set -e
for file in *.php inc/*.php tests/*.php; do php -l "$file" >/dev/null; done
for file in assets/homepage/spin.js assets/js/nav.js assets/js/factory-table.js \
  assets/calculator/script.js assets/calculator/qp-new.js tests/factory-table.test.js; do
  node --check "$file"
done
php tests/factory-sheet-filter.php
php tests/legacy-redirects.php
node tests/factory-table.test.js
git diff --check
```

Expected: exit 0; both PHP regression messages and the Factory JavaScript regression message report passed.

- [ ] **Step 2: Verify conditional asset ownership statically**

Run:

```bash
rg -n "is_front_page|is_page\( 'order' \)|is_page\( 'factory' \)|is_page\( 'calculator' \)|is_page\( array\( 'deposit', 'build-and-price', 'factory' \) \)" functions.php
```

Expected: homepage, dormant Order, Factory, Calculator, and embed assets remain scoped to their current surfaces; no compiler-generated bundle is introduced.

- [ ] **Step 3: Record the source-size budget**

Run:

```bash
find . -type f \( -name '*.css' -o -name '*.js' -o -name '*.php' \) \
  -not -path './assets/homepage/spin/*' -not -path './assets/order/*' -print0 \
  | xargs -0 wc -c | tail -n 1
```

Expected: the non-frame, non-dormant-order total remains within 10 KB of the pre-plan baseline of 261,362 bytes. The redirect PHP and test explain the increase; unexplained asset growth blocks release.

- [ ] **Step 4: Confirm no build tooling was introduced**

Run:

```bash
git status --short
git diff --name-only 15e938a..HEAD | rg '(^|/)(package-lock\.json|package\.json|vite\.config|webpack|rollup)' && exit 1 || true
```

Expected: no build-tool or package-manager files appear.

---

### Task 5: Deploy and Verify Theme 0.1.16 on Staging

**Files:**
- Deploy exact changed runtime files: `functions.php`, `inc/legacy-redirects.php`, `style.css`
- Verify the complete staging theme already present under `/srv/htdocs/wp-content/themes/stingray-corvette/`.

**Interfaces:**
- Consumes: committed theme `0.1.16`, staging SFTP credentials, and the existing staging page records.
- Produces: checksum-matched staging runtime with reliable queryless `/order/` redirect behavior and passing legacy mappings.

- [ ] **Step 1: Push the committed theme documentation and runtime commits**

Run:

```bash
git status --short
git push origin main
git rev-list --left-right --count origin/main...HEAD
```

Expected: clean tree and `0 0` alignment.

- [ ] **Step 2: Back up the three remote staging files**

Using the ignored staging credentials without printing them, download the existing remote files to a timestamped directory under `/tmp`. Record SHA-256 values before upload.

Expected: three readable backup files exist outside the repository.

- [ ] **Step 3: Upload only the changed runtime files**

Upload:

```text
functions.php
inc/legacy-redirects.php
style.css
```

to:

```text
/srv/htdocs/wp-content/themes/stingray-corvette/
```

Download them again and compare SHA-256 against the committed local files. Any mismatch triggers immediate restoration from the staging backup.

- [ ] **Step 4: Purge staging caches and prove queryless Order behavior**

Use WordPress.com staging cache controls, then run twice without query parameters:

```bash
curl -sS -D - -o /tmp/staging-order-body \
  https://staging-427b-stingraychevroletcorvette.wpcomstaging.com/order/ \
  | rg -i '^(HTTP|location|cache-control|x-redirect-by)'
test ! -s /tmp/staging-order-body
```

Expected on both cold and warm requests: HTTP 302; canonical `Location`; `X-Redirect-By: Stingray Corvette`; no dormant local body.

- [ ] **Step 5: Verify the seven staging routes and legacy map**

Run exact HTTP checks for the homepage, six local routes, and every legacy mapping. Expected: six local content routes plus homepage return `200`; `/order/` returns `302`; every legacy route returns `302` to the approved local replacement.

- [ ] **Step 6: Verify delivery performance without a compiler**

Run:

```bash
for asset in \
  assets/homepage/homepage.css \
  assets/homepage/spin.js \
  assets/css/theme.css; do
  curl -sS --compressed -D - -o /dev/null \
    "https://staging-427b-stingraychevroletcorvette.wpcomstaging.com/wp-content/themes/stingray-corvette/$asset?ver=0.1.16" \
    | rg -i '^(HTTP|content-encoding|cache-control|etag)'
done
```

Expected: each returns `200`, uses gzip or Brotli transfer compression, carries a cache validator, and retains long-lived caching keyed by `?ver=0.1.16`.

- [ ] **Step 7: Run staged browser QA**

At 1440×900 and 390×844 verify:

- homepage 360 interaction and lazy frame behavior;
- navigation and compact footer;
- forms 8 and 30 render without submission;
- Factory table interaction;
- Calculator primary flows;
- queryless `/order/` reaches the canonical runtime;
- the canonical logo link returns home;
- no page-level overflow, console error, or required-resource failure.

---

### Task 6: Prepare Production WordPress Records and Table Binding

**Files:**
- Production WordPress database records only; no repository files.
- Record created page IDs and prior values in the controller handoff.

**Interfaces:**
- Consumes: explicit approval for production page/configuration mutation, forms 8 and 30, and wpDataTable 12.
- Produces: six exact production page records and three exact `stingray_embed_shortcode` values ready for theme activation.

- [ ] **Step 1: Obtain the production-configuration approval gate**

The controller must state the exact intended mutations: create/update six page records and set three page custom fields. Do not combine this approval with theme activation.

Expected: explicit user approval is recorded before login or mutation.

- [ ] **Step 2: Back up affected production records**

Using the authenticated WordPress admin session, export or record:

- existing pages whose paths overlap the six new slugs;
- forms 8 and 30 identifiers without changing definitions;
- wpDataTable 12 configuration and source;
- current front-page setting; and
- currently active theme.

Expected: page IDs/statuses, table configuration evidence, active theme, and rollback values are saved outside the repository.

- [ ] **Step 3: Validate wpDataTable 12 before page binding**

In wpDataTables admin confirm the approved Google worksheet, fourteen columns, Order # and Current filters, ten-row pagination, and plugin responsive mode disabled. Preview credible rows and confirm no malformed placeholder.

Expected: table 12 passes without creating or editing another table. If it fails, stop and correct only table 12 after describing the specific mismatch.

- [ ] **Step 4: Create or update the six exact pages**

Create the following published page records with empty editor content unless WordPress requires a minimal title block:

```text
Order              /order/
Deposit            /deposit/
Build & Price      /build-and-price/
Payment Calculator /calculator/
Orders @ Factory   /factory/
Process Guidelines /process/
```

Do not choose a custom Page Template in the editor; classic WordPress slug resolution must select `page-{slug}.php` after activation.

- [ ] **Step 5: Set exact page custom fields**

Set:

```text
/deposit/         stingray_embed_shortcode = [formidable id=8]
/build-and-price/ stingray_embed_shortcode = [formidable id=30]
/factory/         stingray_embed_shortcode = [wpdatatable id=12 table_view=regular]
```

Expected: re-opening each page shows the exact stored value with no smart quotes or extra markup.

- [ ] **Step 6: Record page IDs and verify public pre-activation behavior**

Record each new page ID. Confirm the old theme remains active and its homepage is unaffected. Do not treat new page rendering under the old theme as launch QA.

---

### Task 7: Refresh the Pre-Production Readiness Report

**Files:**
- Modify: `docs/Current-readiness-report.md`
- Modify: `README.md:84-121`

**Interfaces:**
- Consumes: theme `0.1.16`, staging QA evidence, production page IDs/configuration, table 12, and canonical order commit/deployment evidence.
- Produces: an accurate readiness assessment and current operating documentation before any approved activation attempt.

- [ ] **Step 1: Rewrite the readiness report from current evidence**

The report must state:

- exact theme commit and version;
- exact staging version and route results;
- queryless cold/warm `/order/` results;
- canonical 27vette order commit and homepage-link deployment result;
- six production page IDs and statuses;
- forms 8 and 30 verification status;
- Factory shortcode `[wpdatatable id=12 table_view=regular]` and validation status;
- legacy redirect matrix results;
- performance evidence: compressed CSS/JS, versioned long-lived theme caching, conditional loading preserved, no compiler introduced;
- production deployment status remains not activated; and
- explicit readiness-check status with any failed gate named; the release decision remains `NO-GO` until successful Task 10 closure.

Delete superseded claims that `0.1.13` is uncommitted or staging is on `0.1.12`.

- [ ] **Step 2: Correct active README deployment facts**

Update the Surfaces and Embed Shortcode sections so they state:

```text
/factory/: [wpdatatable id=12 table_view=regular]
```

Replace the stale statement that final WordPress preview/versioning is pending with the actual staging-tested `0.1.16` state while keeping production activation explicitly pending.

- [ ] **Step 3: Review documentation scope**

Run:

```bash
rg -n '0\.1\.12|0\.1\.13|NEW[_]ID|wpdatatable id=7|local, uncommitted' docs/Current-readiness-report.md README.md
git diff --check
git diff -- docs/Current-readiness-report.md README.md
```

Expected: no stale active facts remain; historical plan/spec files are untouched.

- [ ] **Step 4: Commit the readiness update**

Run:

```bash
git add docs/Current-readiness-report.md README.md
git commit -m "docs: update production cutover readiness"
git push origin main
```

Expected: documentation is current. Task 8 may begin only when its read-only readiness checks pass, this corrected runbook has passed independent review, and new explicit approval exists for a future activation attempt; the release remains `NO-GO` until Task 10 records a successful cutover.

---

### Task 8: Activate the Replacement Theme in Production

**Files:**
- No repository file change during cutover.
- The complete manifest-matched candidate is already installed inactive at `/wp-content/themes/stingray-corvette/`.
- Production Elementor conditions and the active-theme setting change only after new explicit approval.

**Interfaces:**
- Consumes: passing read-only readiness checks, exact candidate manifest, production page records, and new explicit Elementor-save/activation approval.
- Produces: checksum-matched production theme activation with immediate smoke-test evidence or a completed rollback.

- [ ] **Step 1: Obtain new explicit approval for a future activation attempt**

All three `2026-07-11` activation attempts rolled back safely. Attempt 3 approval is exhausted. Before requesting another activation, obtain independent review of this corrected runbook, including its direct-output acceptance gate and evidence sequence. State the exact commit/version, current Hello `3.4.9` and inactive Stingray `0.1.16` state, the five proposed exclusions, the five-minute exposure bound, and phase-aware rollback. Then wait for new explicit approval covering the Elementor Display Conditions save and reactivation.

- [ ] **Step 2: Complete all read-only readiness and rollback-operator checks**

Reconfirm the exact Stingray 202-file candidate manifest, Hello 116-file backup, six page records, three embed values, table 12, forms 8 and 30, current theme state, both cache-clear paths, and every rollback operator. Any failure stops NO-GO with no mutation.

- [ ] **Step 3: Update and prove only the Cloudflare target**

Capture and retain the complete existing enabled `/process-links/` rule readback, then change only its target from `https://stingraychevroletcorvette.com/corvette-process-guide/` to `https://stingraychevroletcorvette.com/process/`. Preserve its identity, position, exact source, enabled state, `301`, and query behavior. Retain the complete post-update field readback, raw response headers, and two consecutive exact queryless direct Cloudflare-owned `301` first hops to `/process/`, with timestamps proving propagation completed. Do not treat a partial field summary as replayable evidence.

- [ ] **Step 4: Capture Elementor template `12977` baseline**

Only after the Cloudflare propagation proof is complete, and immediately before the supported-UI save, retain the exact raw serialized and normalized original conditions `include/singular/page` and `exclude/singular/front_page`. Retain sanitized supported-UI before evidence and the public Hello/Elementor baseline. Prove effective matching for pages `68291`, `68294`, `68297`, `68300`, and `68303`; match for at least two named legacy non-front Page controls; and exclusion of front page `5` under a real frontend query. Timestamp every capture so the retained record proves this baseline occurred strictly after Cloudflare propagation and immediately before the save; an earlier preflight baseline is insufficient.

- [ ] **Step 5: Save exactly five exclusions through Elementor's supported UI**

Retain both original conditions and add only:

```text
exclude/singular/page/68291
exclude/singular/page/68294
exclude/singular/page/68297
exclude/singular/page/68300
exclude/singular/page/68303
```

Do not edit `_elementor_conditions` directly. Do not exclude page `68288` (`/order/`): the candidate's priority-1 `template_redirect` exits before Elementor Pro's priority-11 `template_include` callback.

Retain sanitized supported-UI before, save-confirmation, and after evidence without cookies, nonces, credentials, or other secrets. The UI success state alone is insufficient: retain the exact raw serialized and normalized conditions after the save and the effective target, legacy-control, and front-page results.

- [ ] **Step 6: Prove the save and activate within five minutes**

Start a visible timer at confirmed save and retain its timestamp. Within at most five minutes, require exact raw/normalized readback, effective non-matching for all five target IDs, continuing match for both legacy controls, continuing front-page exclusion, and no unrelated change. Retain timestamps for readback completion, activation start/completion, and each cache clear so the sequence and five-minute save-to-activation bound are independently provable. Then activate only the manifest-matched Stingray `0.1.16` candidate. If activation cannot begin within the bound, execute Task 9 rollback.

- [ ] **Step 7: Clear caches and run every strict gate**

Clear object cache first, then WordPress.com global edge cache. Run the full strict route/template/embed/browser/performance sequence. Retain raw response headers and sanitized HTML/marker extracts for every gate, including failed gates. Require homepage `200` and only `0.1.16` assets; two exact `/order/` first hops; all five local pages through candidate output rather than document `12977`; the exact Cloudflare/theme redirect split; all required assets `200`; desktop/mobile browser acceptance; and delivery/performance gates.

For each of the five local pages, require the correct page ID in the response and the active `wp-theme-stingray-corvette` marker, and prove Elementor document `12977` is absent. Then require these direct candidate markers:

- Build & Price `68291`: `Build & Price Share` candidate content, `.sc-embed`, and rendered Formidable form `30` / key `chevbp23`.
- Deposit `68294`: `Deposit Form` plus `How the deposit works` candidate instructions, `.sc-embed`, and rendered Formidable form `8` / key `deposit-form`.
- Calculator `68297`: `Payment Calculator` candidate content, `#payment-form`, and successful `200` responses for the required calculator CSS and JavaScript assets.
- Factory `68300`: `Orders in Production` candidate content, `.sc-embed`, and rendered wpDataTable `12` (`data-wpdatatable_id="12"`).
- Process `68303`: candidate `Corvette Order Process Guide` content.

For every page, reject raw shortcode text and the admin-only notices `Embed shortcode missing.` and `Embed shortcode did not render.` Require `200` for every required local CSS, JavaScript, image, and font URL extracted from the accepted HTML. Body classes are not template-ownership evidence here: these hierarchy-bound `page-{slug}.php` files have no `Template Name` headers and the records retain default/empty `_wp_page_template`, so WordPress core correctly emits `page-template-default`. Do not require `page-template-page-{slug}` and do not reject `page-template-default`. Any hard failure triggers Task 9 immediately.

- [ ] **Step 8: Retain the successful final Cloudflare rule readback**

After all activation gates pass and before declaring cutover success, retain a complete authenticated final readback of the enabled `/process-links/` rule. Record and compare every field that defines the approved contract: rule and ruleset identity, rule position, exact full-URI source expression, enabled state, `301` status, query-string behavior, and direct target. Prove every field equals the original baseline except the direct target, which must be exactly `https://stingraychevroletcorvette.com/process/`. Retain the raw sanitized readback and timestamp; a target-only summary or the earlier post-update readback is insufficient final evidence.

---

### Task 9: Execute Rollback if a Production Hard Gate Fails

**Files:**
- Restore only the prior production theme, Elementor conditions, Cloudflare rule, and cache/public state recorded in Task 8.

**Interfaces:**
- Consumes: a named failed hard gate and the verified backup.
- Produces: restored prior site behavior, cache purge, and failure evidence.

- [ ] **Step 1: Select rollback by mutation phase**

- Before any mutation: stop NO-GO; perform no rollback mutation.
- After Cloudflare changes but before the Elementor save: restore only the exact original Cloudflare configuration and prove two consecutive original direct first hops to `/corvette-process-guide/`.
- After the Elementor save but before activation: keep Hello active and continue at Step 2.
- After activation begins: reactivate Hello Elementor `3.4.9` first, then continue at Step 2.

- [ ] **Step 2: Restore Elementor template `12977` exactly when its save occurred**

Through Elementor's supported Display Conditions UI, remove exactly the five ID-specific exclusions and restore only `include/singular/page` and `exclude/singular/front_page`. Require exact raw and normalized readback, effective matching for all five target pages and both legacy controls, continuing exclusion of front page `5` under a real frontend query, and no unrelated Theme Builder change. Metadata readback or a UI success message alone is insufficient.

- [ ] **Step 3: Restore Cloudflare, clear caches, and prove baseline**

Restore the complete captured Cloudflare rule including its original `/corvette-process-guide/` target. Retain the complete final rule field readback and timestamps, not only the restored target. Clear WordPress object cache first, then WordPress.com global edge cache. Repeat the effective Elementor checks after cache clearing and retain exact raw plus normalized restored conditions, sanitized supported-UI after evidence, raw headers, and sanitized marker extracts. Require two consecutive original direct Cloudflare first hops plus the complete public Hello baseline: homepage/Hello assets, all five target page IDs/titles with document `12977`, and previously accepted baseline routes.

- [ ] **Step 4: Record the failed gate**

Update `docs/Current-readiness-report.md` with the exact failure, rollback time, restored theme, Elementor conditions, Cloudflare state, cache confirmations, and public evidence. Mark production status NO-GO. Do not commit or push unless separately instructed.

---

### Task 10: Close the Successful Cutover

**Files:**
- Modify: `docs/Current-readiness-report.md`
- Modify: `README.md` only if the production status statement still says activation is pending.

**Interfaces:**
- Consumes: passing production hard gates, browser acceptance, performance checks, and final page/table IDs.
- Produces: an accurate deployed GO record and clean repository handoff.

- [ ] **Step 1: Record the deployed state**

Update the report with:

- deployed theme commit/version;
- activation date/time;
- all six page IDs;
- forms 8 and 30 result;
- table 12 result;
- cold/warm queryless Order redirect result;
- legacy redirect matrix;
- canonical order commit and homepage-link result;
- compression/cache evidence;
- production browser acceptance;
- rollback backup location and retained prior-theme slug; and
- release decision `GO — deployed`.

- [ ] **Step 2: Run the final repository gate**

Run:

```bash
git diff --check
git status --short
git rev-list --left-right --count origin/main...HEAD
```

Expected: only the final documentation change is pending; no runtime drift exists.

- [ ] **Step 3: Commit and push closure documentation**

Run:

```bash
git add docs/Current-readiness-report.md README.md
git commit -m "docs: record production theme cutover"
git push origin main
```

Expected: `main` and `origin/main` align and active documentation reports the actual deployed state.

- [ ] **Step 4: Remove temporary local test artifacts**

Delete `/tmp/order-home-link-contract.js` and close/remove the isolated 27vette worktree only after its commit is confirmed on `origin/main`. Preserve production and staging backups for the monitoring period.

## Commit Strategy

Expected focused commits:

```text
27vette: feat: link order form branding to homepage
theme:   feat: redirect replaced legacy surfaces
theme:   docs: update production cutover readiness
theme:   docs: record production theme cutover
```

Do not squash the pre-deployment readiness record into the runtime redirect commit. Do not include production exports, credentials, browser session data, or backups in Git.
