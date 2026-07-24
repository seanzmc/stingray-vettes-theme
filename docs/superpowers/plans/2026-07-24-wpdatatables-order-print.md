# wpDataTables Order Print Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Print only the selected wpDataTables row's `plaintextbuildsummary` value on exactly one US Letter portrait sheet with a visible `0.5in` margin.

**Architecture:** Keep the workflow entirely inside the authenticated `/configurator-view/` document. The existing modal observer adds one print button; clicking it clones only `[data-key="plaintextbuildsummary"]`, removes plugin attributes/classes from that clone, measures it against a `7.5in × 10in` content box, and invokes the existing one-sheet print lifecycle without a Formidable shortcode or network request.

**Tech Stack:** Classic WordPress theme, vanilla JavaScript, CSS print rules, Node.js `vm` regression harness, PHP 7.4-compatible theme enqueue code.

## Global Constraints

- The page remains private and admin-only.
- The print sheet contains only `plaintextbuildsummary`; do not prepend the modal title, name, variant, entry date, or entry ID.
- Use one US Letter portrait sheet with a visible `0.5in` inset and a `7.5in × 10in` usable content area.
- Never upscale print content above 100%.
- Do not add a Formidable shortcode, AJAX/REST endpoint, dependency, build step, or plugin modification.
- Do not change public pages, the Factory modal, wpDataTables configuration, Formidable entries, or submission data.
- Do not deploy, SFTP, call WordPress APIs, or purge caches.
- Preserve the existing modal lifecycle, one-button injection, print cleanup, and failure behavior.

---

### Task 1: Print only the selected plaintext build summary

**Files:**
- Modify: `tests/configurator-view.test.js`
- Modify: `assets/js/configurator-view.js`

**Interfaces:**
- Consumes: the open wpDataTables `.modal-content` and its descendant `[data-key="plaintextbuildsummary"]`.
- Produces: `buildPrintSheet(modal) -> Element|null`, where the sheet contains one `.sc-configurator-print-body` clone of the summary field and no other modal content.

- [ ] **Step 1: Update the DOM harness to model wpDataTables detail fields**

Add exact attribute-selector support to `Element.prototype.matches`:

```js
Element.prototype.matches = function (selector) {
	var dataKeyMatch = /^\[data-key="([^"]+)"\]$/.exec(selector);
	if (dataKeyMatch) return this.getAttribute('data-key') === dataKeyMatch[1];
	if ('.' === selector.charAt(0)) return this.classList.contains(selector.slice(1));
	if ('#' === selector.charAt(0)) return this.getAttribute('id') === selector.slice(1);
	return this.tagName.toLowerCase() === selector.toLowerCase();
};
```

Replace the generic modal-body fixture with representative detail fields:

```js
var customerName = new Element('div');
var summary = new Element('div');
var entryId = new Element('div');

customerName.setAttribute('data-key', 'name');
customerName.textContent = 'Tim Hottel';

summary.setAttribute('data-key', 'plaintextbuildsummary');
summary.setAttribute('class', 'detailColumn plugin-summary');
summary.setAttribute('style', 'display: block; max-height: 320px; overflow: auto;');
summary.textContent = [
	'Tim Hottel',
	'Submitted: 2026-06-26',
	'Variant',
	'• Corvette Stingray Convertible 2LT',
	'Total MSRP: $101,715'
].join('\n');
summary._scrollHeight = bodyHeight;
summary._scrollWidth = bodyWidth;

entryId.setAttribute('data-key', 'id');
entryId.textContent = '243746';

modalBody.appendChild(customerName);
modalBody.appendChild(summary);
modalBody.appendChild(entryId);
```

Keep the modal title fixture so the test can prove it is excluded.

- [ ] **Step 2: Write failing summary-only print assertions**

Replace the existing title/body-clone assertions with:

```js
var longSheet = longHarness.document.querySelector('#sc-configurator-print-sheet');
var longInner = longSheet && longSheet.children[0];
var summaryClone = longInner && longInner.children[0];
var expectedSummary = [
	'Tim Hottel',
	'Submitted: 2026-06-26',
	'Variant',
	'• Corvette Stingray Convertible 2LT',
	'Total MSRP: $101,715'
].join('\n');

assert(
	longInner && 1 === longInner.children.length,
	'Print sheet must contain only the plaintext build summary.'
);
assert(
	summaryClone && expectedSummary === summaryClone.textContent,
	'Print sheet must preserve the complete selected plaintext build summary.'
);
assert(
	summaryClone && 'sc-configurator-print-body' === summaryClone.getAttribute('class'),
	'Summary clone must replace plugin classes with the print-body class.'
);
assert(
	summaryClone && summaryClone.getAttributeNames().sort().join('|') === 'class',
	'Summary clone must discard plugin attributes and inline constraints.'
);
assert(
	longInner.textContent.indexOf('Order 123') === -1 &&
	longInner.textContent.indexOf('243746') === -1,
	'Print sheet must not add the modal title or entry ID.'
);
```

Add a missing-summary failure case:

```js
var missingSummaryHarness = createHarness({ omitSummary: true });
var missingSummaryModal = missingSummaryHarness.makeModal(300, 600);
missingSummaryHarness.modals.push(missingSummaryModal);
missingSummaryHarness.triggerMutation();
clickPrint(missingSummaryModal);

assert(
	0 === missingSummaryHarness.getPrinted(),
	'Missing plaintextbuildsummary must abort without opening the print dialog.'
);
assert(
	!missingSummaryHarness.document.querySelector('#sc-configurator-print-sheet'),
	'Missing plaintextbuildsummary must leave no temporary print sheet.'
);
```

Update `makeModal` so `options.omitSummary` skips appending the summary fixture.

- [ ] **Step 3: Run the focused test and verify RED**

Run:

```bash
node tests/configurator-view.test.js
```

Expected: FAIL because the current implementation clones the modal title and entire modal body instead of only `[data-key="plaintextbuildsummary"]`.

- [ ] **Step 4: Implement summary-only cloning**

Change `buildPrintSheet` in `assets/js/configurator-view.js` to:

```js
function buildPrintSheet(modal) {
  var summary = modal.querySelector('[data-key="plaintextbuildsummary"]');
  if (!summary) return null;

  cleanupPrintSheet();

  var sheet = document.createElement('section');
  var inner = document.createElement('div');
  var summaryClone = summary.cloneNode(true);

  sheet.setAttribute('id', sheetId);
  sheet.setAttribute('aria-hidden', 'true');
  inner.classList.add('sc-configurator-print-inner');
  resetPrintNode(summaryClone, 'sc-configurator-print-body');

  inner.appendChild(summaryClone);
  sheet.appendChild(inner);
  document.body.appendChild(sheet);

  var scale = calculateScale(
    inner.scrollWidth,
    inner.scrollHeight,
    sheet.clientWidth,
    sheet.clientHeight
  );
  if (null === scale) {
    cleanupPrintSheet();
    return null;
  }

  var serializedScale = String(scale);
  inner.style.transform = 'scale(' + serializedScale + ')';
  sheet.setAttribute('data-print-scale', serializedScale);
  return sheet;
}
```

Do not add a title fallback or query any other detail field.

- [ ] **Step 5: Run the focused test and verify GREEN**

Run:

```bash
node tests/configurator-view.test.js
```

Expected:

```text
Configurator modal and one-sheet print regression tests passed.
```

- [ ] **Step 6: Commit the summary-only behavior**

```bash
git add assets/js/configurator-view.js tests/configurator-view.test.js
git commit -m "fix: print selected plaintext order summary"
```

---

### Task 2: Add a reliable half-inch Letter-page inset

**Files:**
- Modify: `tests/configurator-view.test.js`
- Modify: `assets/css/configurator-view.css`
- Modify: `style.css`

**Interfaces:**
- Consumes: `#sc-configurator-print-sheet`, `.sc-configurator-print-inner`, and `.sc-configurator-print-body` created by Task 1.
- Produces: a CSS Letter portrait content box measuring `7.5in × 10in` inside `@page` margins of `0.5in`.

- [ ] **Step 1: Write failing print-geometry CSS contracts**

Replace the old print markers in `tests/configurator-view.test.js`:

```js
'margin: 0.25in',
'width: 8in',
'height: 10.5in',
```

with:

```js
'margin: 0.5in',
'width: 7.5in',
'height: 10in',
'white-space: pre-wrap',
'overflow-wrap: anywhere',
```

Remove the temporary `.wpdt-c .wdt-md-modal.fade.show` and `opacity: 1`
markers. The live page now loads only wpDataTables' Bootstrap modal runtime and
opens with `.in`; the Formidable conflict that motivated the prototype rule is
gone.

Set the harness defaults to the CSS-pixel equivalents of the usable content
box so scale tests exercise the correct dimensions:

```js
this.clientWidth = 720;
this.clientHeight = 960;
this._scrollWidth = 720;
this._scrollHeight = 960;
```

- [ ] **Step 2: Run the focused test and verify RED**

Run:

```bash
node tests/configurator-view.test.js
```

Expected: FAIL with missing CSS contracts for the `0.5in` margin,
`7.5in × 10in` sheet, and preserved plaintext formatting.

- [ ] **Step 3: Implement the Letter content geometry**

Update `assets/css/configurator-view.css`:

```css
#sc-configurator-print-sheet {
  position: fixed;
  left: -10000px;
  top: 0;
  width: 7.5in;
  height: 10in;
  overflow: hidden;
  background: #fff;
}

.sc-configurator-print-inner {
  width: 7.5in;
  color: #000;
  background: #fff;
  transform-origin: top left;
}

.sc-configurator-print-body {
  height: auto !important;
  min-height: 0 !important;
  max-height: none !important;
  overflow: visible !important;
  position: static !important;
  padding: 0;
  white-space: pre-wrap;
  overflow-wrap: anywhere;
  font: 10pt/1.18 Arial, sans-serif;
}

@page {
  size: letter portrait;
  margin: 0.5in;
}
```

Inside `@media print`, change both the `html, body` and print-sheet dimensions
from `8in × 10.5in` to:

```css
width: 7.5in !important;
height: 10in !important;
```

and:

```css
width: 7.5in;
height: 10in;
```

Delete the uncommitted `.wpdt-c .wdt-md-modal.fade.show { opacity: 1; }`
prototype rule because the verified live modal now opens with `.in` after the
Formidable View was removed.

- [ ] **Step 4: Keep the cache-busting version at `0.1.31`**

Verify `style.css` contains:

```css
Version: 0.1.31
```

Do not increment again; `0.1.31` is the currently prepared, uncommitted version
for this configurator print delivery.

- [ ] **Step 5: Run the focused test and verify GREEN**

Run:

```bash
node tests/configurator-view.test.js
```

Expected:

```text
Configurator modal and one-sheet print regression tests passed.
```

- [ ] **Step 6: Commit the print geometry**

```bash
git add assets/css/configurator-view.css tests/configurator-view.test.js style.css
git commit -m "fix: add margins to one-sheet order print"
```

---

### Task 3: Regression and delivery verification

**Files:**
- Verify: `assets/js/configurator-view.js`
- Verify: `assets/css/configurator-view.css`
- Verify: `tests/configurator-view.test.js`
- Verify: `functions.php`
- Verify: `style.css`

**Interfaces:**
- Consumes: the two committed implementation tasks.
- Produces: evidence that the private-page print path is isolated, regression-safe, and ready to push without performing a live deployment.

- [ ] **Step 1: Run all JavaScript regression tests**

Run:

```bash
for test_file in tests/*.test.js; do node "$test_file" || exit 1; done
```

Expected:

```text
Quick Pencil layout regression tests passed.
Configurator modal and one-sheet print regression tests passed.
Factory table row-preparation regression tests passed.
Homepage spin-loading regression tests passed.
```

- [ ] **Step 2: Lint the touched PHP enqueue path**

Run:

```bash
php -l functions.php
```

Expected:

```text
No syntax errors detected in functions.php
```

- [ ] **Step 3: Check whitespace and repository state**

Run:

```bash
git diff --check
git status --short
git log -5 --oneline
```

Expected: no whitespace errors; only intentional commits/changes are present.

- [ ] **Step 4: Inspect the committed diff**

Run:

```bash
git show --stat --oneline HEAD~1..HEAD
git diff HEAD~2..HEAD -- assets/js/configurator-view.js assets/css/configurator-view.css tests/configurator-view.test.js style.css
```

Confirm:

- `buildPrintSheet` queries only `[data-key="plaintextbuildsummary"]`;
- one summary clone is appended;
- plugin attributes and inline constraints are removed from the clone;
- the content box is `7.5in × 10in`;
- `@page` uses `0.5in`;
- the version is `0.1.31`;
- no public-page or plugin files changed.

- [ ] **Step 5: Push only after all gates pass**

```bash
git push origin main
```

Expected: `main` advances successfully on `origin`.

Pushing is repository delivery only. Do not deploy, edit WordPress, purge
caches, or claim production behavior until the updated theme is actually
served.

- [ ] **Step 6: Perform authenticated browser verification when deployment is externally available**

On `https://stingraychevroletcorvette.com/configurator-view/`:

1. Click two different wpDataTables rows.
2. Confirm each modal opens at full opacity.
3. Confirm exactly one `Print order` button appears.
4. Print a short and a long order to PDF.
5. Confirm each PDF has exactly one Letter portrait page.
6. Confirm the printed content is only the selected plaintext summary.
7. Confirm the complete summary is present with preserved line breaks.
8. Confirm the page has a visible half-inch inset on every edge.
9. Confirm the entry ID and modal title are absent.

If the repository push does not automatically publish the theme, report this
browser gate as pending rather than modifying production.
