# Configurator One-Sheet Print Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Give the private `/configurator-view/` wpDataTables modal readable compact styling and a button that prints the complete open submission on exactly one US Letter portrait sheet.

**Architecture:** Enqueue one dedicated stylesheet and one dependency-free script only on the private page. The script observes plugin-generated modals, injects one print button, clones the open modal title and complete body into a temporary fixed Letter sheet, measures and proportionally scales it, prints, and removes the clone without sending or storing submission data.

**Tech Stack:** Classic WordPress/PHP enqueueing, vanilla CSS, vanilla JavaScript, Node `fs`/`vm` regression test, Bootstrap-style wpDataTables modal markup.

## Global Constraints

- Scope all new runtime assets to `is_page( 'configurator-view' )`.
- Use a white modal surface and near-black `#161616` content with `--bs-modal-color` defined.
- Use approximately `0.75rem 1rem` modal padding and retain visible section separation.
- Preserve plugin modal lifecycle, close behavior, focus behavior, submission markup, and data.
- Print Letter portrait with `0.25in` margins and proportionally scale all content onto one sheet.
- No network request, analytics, persistence, server-side document generation, plugin edit, dependency, or build system.
- Do not change public Factory modal behavior.
- Do not deploy, SFTP, call WordPress APIs, purge caches, or modify the live site.

---

## File Structure

- `functions.php`: conditionally enqueues the private-page stylesheet and script.
- `assets/css/configurator-view.css`: owns private modal screen styling and the one-sheet print surface.
- `assets/js/configurator-view.js`: owns modal discovery, button injection, cloning, measurement, scaling, printing, and cleanup.
- `tests/configurator-view.test.js`: owns enqueue, CSS-contract, and executable print-helper regressions.
- `README.md`: records the private page asset boundary.
- `style.css`: cache-busts the new assets.

### Task 1: Test and Add Private-Page Asset Ownership

**Files:**
- Create: `tests/configurator-view.test.js`
- Modify: `functions.php:124-134`

**Interfaces:**
- Consumes: WordPress `is_page()`, `wp_enqueue_style()`, `wp_enqueue_script()`, `$uri`, and `$ver` inside `stingray_corvette_enqueue_styles()`.
- Produces: handles `stingray-configurator-view` and `stingray-configurator-view-print`, loaded only on slug `configurator-view`.

- [ ] **Step 1: Create the failing ownership and behavior test**

Create `tests/configurator-view.test.js`:

```js
/* Run: node tests/configurator-view.test.js */
'use strict';

var fs = require('fs');
var path = require('path');
var vm = require('vm');

var root = path.join(__dirname, '..');
var functionsSource = fs.readFileSync(path.join(root, 'functions.php'), 'utf8');
var cssPath = path.join(root, 'assets', 'css', 'configurator-view.css');
var jsPath = path.join(root, 'assets', 'js', 'configurator-view.js');
var failures = [];

function assert(condition, message) {
	if (!condition) failures.push(message);
}

assert(
	functionsSource.indexOf("is_page( 'configurator-view' )") !== -1,
	'Configurator assets must be scoped to the private page slug.'
);
assert(
	functionsSource.indexOf("'stingray-configurator-view'") !== -1 &&
	functionsSource.indexOf("'stingray-configurator-view-print'") !== -1,
	'Configurator stylesheet and script handles must be registered.'
);
assert(fs.existsSync(cssPath), 'Private configurator stylesheet must exist.');
assert(fs.existsSync(jsPath), 'Private configurator script must exist.');

if (!fs.existsSync(cssPath) || !fs.existsSync(jsPath)) {
	console.error(failures.join('\n'));
	process.exit(1);
}

var css = fs.readFileSync(cssPath, 'utf8');
var source = fs.readFileSync(jsPath, 'utf8');

[
	'--bs-modal-color: #161616',
	'padding: 0.75rem 1rem',
	'@media print',
	'@page',
	'size: letter portrait',
	'margin: 0.25in',
	'#sc-configurator-print-sheet'
].forEach(function (marker) {
	assert(css.indexOf(marker) !== -1, 'Missing CSS contract: ' + marker);
});

function ClassList() {
	this.values = [];
}
ClassList.prototype.add = function (name) {
	if (this.values.indexOf(name) === -1) this.values.push(name);
};
ClassList.prototype.remove = function (name) {
	this.values = this.values.filter(function (value) { return value !== name; });
};
ClassList.prototype.contains = function (name) {
	return this.values.indexOf(name) !== -1;
};

function Element(tagName) {
	this.tagName = String(tagName || 'div').toUpperCase();
	this.children = [];
	this.classList = new ClassList();
	this.listeners = {};
	this.style = {};
	this.attributes = {};
	this.textContent = '';
	this.clientWidth = 768;
	this.clientHeight = 1008;
	this.scrollWidth = 768;
	this.scrollHeight = 1008;
}
Element.prototype.appendChild = function (child) {
	this.children.push(child);
	child.parentNode = this;
	this.scrollWidth = Math.max(this.scrollWidth, child.scrollWidth);
	this.scrollHeight = Math.max(this.scrollHeight, child.scrollHeight);
	return child;
};
Element.prototype.remove = function () {
	if (!this.parentNode) return;
	var parent = this.parentNode;
	parent.children = parent.children.filter(function (child) {
		return child !== this;
	}, this);
	this.parentNode = null;
};
Element.prototype.addEventListener = function (name, handler) {
	this.listeners[name] = handler;
};
Element.prototype.setAttribute = function (name, value) {
	this.attributes[name] = String(value);
};
Element.prototype.getAttribute = function (name) {
	return this.attributes[name] || null;
};
Element.prototype.cloneNode = function () {
	var copy = new Element(this.tagName);
	copy.textContent = this.textContent;
	copy.scrollWidth = this.scrollWidth;
	copy.scrollHeight = this.scrollHeight;
	return copy;
};
Element.prototype.querySelector = function (selector) {
	if ('.modal-header' === selector) return this.header || null;
	if ('.modal-title' === selector) return this.title || null;
	if ('.modal-body' === selector) return this.body || null;
	if ('.sc-configurator-print-button' === selector && this.header) {
		return this.header.children.filter(function (child) {
			return child.classList.contains('sc-configurator-print-button');
		})[0] || null;
	}
	return null;
};

var modal = new Element('div');
modal.header = new Element('header');
modal.title = new Element('h2');
modal.title.textContent = 'Tim Hottel';
modal.body = new Element('div');
modal.body.textContent = 'Complete submission content';
modal.body.scrollHeight = 1800;
modal.header.appendChild(modal.title);

var body = new Element('body');
var sheet = null;
var document = {
	body: body,
	createElement: function (tagName) { return new Element(tagName); },
	querySelector: function (selector) {
		if (selector.indexOf('.modal-content') !== -1) return modal;
		if ('#sc-configurator-print-sheet' === selector) {
			return sheet && sheet.parentNode ? sheet : null;
		}
		return null;
	},
	querySelectorAll: function (selector) {
		return selector.indexOf('.modal-content') !== -1 ? [modal] : [];
	}
};
var printed = 0;
var afterPrint = null;
var window = {
	addEventListener: function (name, handler) {
		if ('afterprint' === name) afterPrint = handler;
	},
	print: function () { printed++; },
	requestAnimationFrame: function (handler) { handler(); }
};
function MutationObserver(handler) {
	this.handler = handler;
}
MutationObserver.prototype.observe = function () {};

var originalAppend = body.appendChild;
body.appendChild = function (child) {
	if ('sc-configurator-print-sheet' === child.attributes.id) sheet = child;
	return originalAppend.call(body, child);
};

vm.runInNewContext(source, {
	window: window,
	document: document,
	MutationObserver: MutationObserver,
	setTimeout: function () { return 1; },
	clearTimeout: function () {},
	Math: Math
});

assert(1 === modal.header.children.filter(function (child) {
	return child.classList.contains('sc-configurator-print-button');
}).length, 'Exactly one Print order button must be injected.');

var button = modal.header.children.filter(function (child) {
	return child.classList.contains('sc-configurator-print-button');
})[0];
button.listeners.click();

assert(1 === printed, 'Print button must call window.print once.');
assert(body.classList.contains('sc-configurator-printing'), 'Print state must be active for printing.');
assert(sheet, 'Print action must append a temporary sheet.');
assert(sheet.children[0].style.transform.indexOf('scale(') === 0, 'Print content must receive a measured scale.');
assert(Number(sheet.getAttribute('data-print-scale')) < 1, 'Long modal content must scale below 100%.');

if (afterPrint) afterPrint();
assert(!body.classList.contains('sc-configurator-printing'), 'afterprint must clear print state.');
assert(!document.querySelector('#sc-configurator-print-sheet'), 'afterprint must remove the temporary sheet.');

if (failures.length) {
	console.error(failures.join('\n'));
	process.exit(1);
}

console.log('Configurator modal and one-sheet print regression tests passed.');
```

- [ ] **Step 2: Run the test and verify it fails before assets exist**

Run:

```bash
node tests/configurator-view.test.js
```

Expected: exit 1 naming the missing slug ownership, stylesheet, and script.

- [ ] **Step 3: Add the private-page enqueue block**

Inside `stingray_corvette_enqueue_styles()` in `functions.php`, after the
Factory block and before Calculator, add:

```php
	// Private configurator submission viewer: compact modal + one-sheet print.
	if ( is_page( 'configurator-view' ) ) {
		wp_enqueue_style(
			'stingray-configurator-view',
			$uri . '/assets/css/configurator-view.css',
			array( 'stingray-surfaces' ),
			$ver
		);
		wp_enqueue_script(
			'stingray-configurator-view-print',
			$uri . '/assets/js/configurator-view.js',
			array(),
			$ver,
			true
		);
	}
```

- [ ] **Step 4: Run PHP lint and confirm only the missing assets remain**

Run:

```bash
php -l functions.php
node tests/configurator-view.test.js
```

Expected:

- PHP reports `No syntax errors detected in functions.php`.
- Node still exits 1 only because the CSS and JS files do not exist.

### Task 2: Implement Readable Modal Styling and One-Sheet Printing

**Files:**
- Create: `assets/css/configurator-view.css`
- Create: `assets/js/configurator-view.js`
- Test: `tests/configurator-view.test.js`

**Interfaces:**
- Consumes: plugin `.wpdt-c` / `.wdt-md-modal` `.modal-content`, `.modal-header`, `.modal-title`, and `.modal-body`.
- Produces: `.sc-configurator-print-button`, `#sc-configurator-print-sheet`, `.sc-configurator-print-inner`, body state `.sc-configurator-printing`.

- [ ] **Step 1: Add the dedicated screen and print stylesheet**

Create `assets/css/configurator-view.css`:

```css
/* Private /configurator-view/ utility surface only. */
.wpdt-c .modal-content,
.wdt-md-modal .modal-content {
  --bs-modal-color: #161616;
  background: #fff !important;
  color: #161616 !important;
}

.wpdt-c .modal-header,
.wdt-md-modal .modal-header {
  display: flex;
  gap: 0.75rem;
  align-items: center;
  padding: 0.75rem 1rem !important;
  border-bottom: 1px solid #d8dde3 !important;
  color: #161616 !important;
}

.wpdt-c .modal-title,
.wdt-md-modal .modal-title {
  margin-right: auto;
  color: #161616 !important;
}

.wpdt-c .modal-body,
.wdt-md-modal .modal-body {
  padding: 0.75rem 1rem !important;
  color: #161616 !important;
}

.wpdt-c .modal-body *,
.wdt-md-modal .modal-body * {
  color: inherit !important;
}

.wpdt-c .modal-body p,
.wpdt-c .modal-body ul,
.wpdt-c .modal-body ol,
.wdt-md-modal .modal-body p,
.wdt-md-modal .modal-body ul,
.wdt-md-modal .modal-body ol {
  margin-top: 0.35rem;
  margin-bottom: 0.6rem;
}

.sc-configurator-print-button {
  min-height: 42px;
  padding: 0.45rem 0.8rem;
  border: 1px solid #161616;
  border-radius: 4px;
  background: #fff;
  color: #161616;
  font: inherit;
  font-weight: 700;
  cursor: pointer;
}

.sc-configurator-print-button:hover {
  background: #f1f3f5;
}

.sc-configurator-print-button:focus-visible {
  outline: 3px solid #d22630;
  outline-offset: 2px;
}

#sc-configurator-print-sheet {
  position: fixed;
  left: -10000px;
  top: 0;
  width: 8in;
  height: 10.5in;
  overflow: hidden;
  background: #fff;
}

.sc-configurator-print-inner {
  width: 8in;
  color: #000;
  background: #fff;
  transform-origin: top left;
}

.sc-configurator-print-inner,
.sc-configurator-print-inner * {
  box-sizing: border-box;
  color: #000 !important;
  background: #fff !important;
  box-shadow: none !important;
  text-shadow: none !important;
}

.sc-configurator-print-title {
  margin: 0 0 0.18in;
  padding: 0 0 0.1in;
  border-bottom: 1px solid #cfd4da;
  font: 700 17pt/1.1 Arial, sans-serif;
}

.sc-configurator-print-body {
  padding: 0;
  font: 10pt/1.18 Arial, sans-serif;
}

.sc-configurator-print-body p,
.sc-configurator-print-body ul,
.sc-configurator-print-body ol {
  margin-top: 0.04in;
  margin-bottom: 0.08in;
}

.sc-configurator-print-body h1,
.sc-configurator-print-body h2,
.sc-configurator-print-body h3,
.sc-configurator-print-body h4,
.sc-configurator-print-body strong,
.sc-configurator-print-body b {
  font-weight: 700;
}

.sc-configurator-print-body h1,
.sc-configurator-print-body h2,
.sc-configurator-print-body h3,
.sc-configurator-print-body h4 {
  margin: 0.08in 0 0.03in;
  font-size: 10.5pt;
  line-height: 1.12;
}

.sc-configurator-print-body li {
  margin: 0;
}

@page {
  size: letter portrait;
  margin: 0.25in;
}

@media print {
  html,
  body {
    width: 8in !important;
    height: 10.5in !important;
    margin: 0 !important;
    padding: 0 !important;
    background: #fff !important;
  }

  body.sc-configurator-printing > *:not(#sc-configurator-print-sheet) {
    display: none !important;
  }

  body.sc-configurator-printing #sc-configurator-print-sheet {
    display: block !important;
    position: fixed;
    inset: 0;
    width: 8in;
    height: 10.5in;
    overflow: hidden;
    break-inside: avoid;
    break-after: avoid;
    page-break-inside: avoid;
    page-break-after: avoid;
  }

  .sc-configurator-print-button,
  .modal-close,
  .close,
  .btn-close {
    display: none !important;
  }
}
```

- [ ] **Step 2: Add modal discovery, print cloning, scale, and cleanup**

Create `assets/js/configurator-view.js`:

```js
(function () {
  'use strict';

  var modalSelector = '.wpdt-c .modal-content, .wdt-md-modal .modal-content';
  var printButtonClass = 'sc-configurator-print-button';
  var sheetId = 'sc-configurator-print-sheet';
  var cleanupTimer = 0;

  function cleanupPrintSheet() {
    clearTimeout(cleanupTimer);
    cleanupTimer = 0;
    var oldSheet = document.querySelector('#' + sheetId);
    if (oldSheet) oldSheet.remove();
    document.body.classList.remove('sc-configurator-printing');
  }

  function calculateScale(contentWidth, contentHeight, boxWidth, boxHeight) {
    if (!contentWidth || !contentHeight || !boxWidth || !boxHeight) return 1;
    return Math.min(1, boxWidth / contentWidth, boxHeight / contentHeight);
  }

  function buildPrintSheet(modal) {
    var modalBody = modal.querySelector('.modal-body');
    if (!modalBody) return null;

    cleanupPrintSheet();

    var sheet = document.createElement('section');
    var inner = document.createElement('div');
    var title = document.createElement('h1');
    var bodyClone = modalBody.cloneNode(true);
    var modalTitle = modal.querySelector('.modal-title');

    sheet.setAttribute('id', sheetId);
    sheet.setAttribute('aria-hidden', 'true');
    inner.classList.add('sc-configurator-print-inner');
    title.classList.add('sc-configurator-print-title');
    bodyClone.classList.add('sc-configurator-print-body');
    title.textContent = modalTitle ? modalTitle.textContent.trim() : 'Configurator order';

    inner.appendChild(title);
    inner.appendChild(bodyClone);
    sheet.appendChild(inner);
    document.body.appendChild(sheet);

    var scale = calculateScale(
      inner.scrollWidth,
      inner.scrollHeight,
      sheet.clientWidth,
      sheet.clientHeight
    );
    inner.style.transform = 'scale(' + scale.toFixed(4) + ')';
    sheet.setAttribute('data-print-scale', scale.toFixed(4));
    return sheet;
  }

  function printModal(modal) {
    if (!buildPrintSheet(modal)) return;
    document.body.classList.add('sc-configurator-printing');
    window.requestAnimationFrame(function () {
      window.requestAnimationFrame(function () {
        window.print();
        cleanupTimer = setTimeout(cleanupPrintSheet, 1000);
      });
    });
  }

  function addPrintButton(modal) {
    var header = modal.querySelector('.modal-header');
    if (!header || modal.querySelector('.' + printButtonClass)) return;

    var button = document.createElement('button');
    button.classList.add(printButtonClass);
    button.setAttribute('type', 'button');
    button.textContent = 'Print order';
    button.addEventListener('click', function () {
      printModal(modal);
    });
    header.appendChild(button);
  }

  function scanForModals() {
    Array.prototype.forEach.call(document.querySelectorAll(modalSelector), addPrintButton);
  }

  window.addEventListener('afterprint', cleanupPrintSheet);
  new MutationObserver(scanForModals).observe(document.body, {
    childList: true,
    subtree: true
  });
  scanForModals();
})();
```

- [ ] **Step 3: Run the focused test**

Run:

```bash
node tests/configurator-view.test.js
```

Expected: `Configurator modal and one-sheet print regression tests passed.`

- [ ] **Step 4: Run PHP and existing Factory regressions**

Run:

```bash
php -l functions.php
node tests/factory-table.test.js
node tests/configurator-view.test.js
git diff --check
```

Expected:

- no PHP syntax errors;
- Factory regression passes unchanged;
- configurator regression passes;
- whitespace check is silent.

- [ ] **Step 5: Commit the private-page runtime**

```bash
git add functions.php assets/css/configurator-view.css assets/js/configurator-view.js tests/configurator-view.test.js
git commit -m "feat: add one-sheet configurator printing"
```

### Task 3: Document, Cache-Bust, and Verify One-Sheet Output

**Files:**
- Modify: `README.md:108-145`
- Modify: `style.css:7`

**Interfaces:**
- Consumes: private-page assets from Tasks 1-2.
- Produces: documented ownership and a cache-busted theme version.

- [ ] **Step 1: Document the private page boundary**

Add under `README.md` → `Surfaces`:

```markdown
- `/configurator-view/` — private wpDataTables submission-review page. The theme
  conditionally loads `assets/css/configurator-view.css` and
  `assets/js/configurator-view.js` to provide a compact readable modal and a
  client-only one-Letter-sheet print action. Submission data and table
  configuration remain plugin-owned.
```

- [ ] **Step 2: Bump the cache version without colliding with the spinner plan**

Execute the spinner plan first, then change the `Version:` header in `style.css`
from `0.1.27` to `0.1.28`.

- [ ] **Step 3: Run the complete local regression set**

Run:

```bash
node tests/configurator-view.test.js
node tests/factory-table.test.js
node tests/calculator-layout.test.js
php tests/factory-sheet-filter.php
php tests/legacy-redirects.php
php -l functions.php
git diff --check
```

Expected: every test exits 0, PHP lint reports no syntax errors, and the
whitespace check is silent.

- [ ] **Step 4: Verify a short and long modal in a local browser fixture**

Create a temporary, untracked HTML fixture with the real new CSS/JS and
representative wpDataTables modal markup. Verify:

- white background and `#161616` content;
- title, labels, generated fields, lists, and totals are readable;
- header and body use compact nonzero padding;
- exactly one `Print order` button appears after repeated DOM mutations;
- close control and keyboard focus remain usable;
- the print action includes content below the modal's visible scroll region;
- the browser print preview shows Letter portrait and exactly one page;
- a long submission receives a scale below `1`;
- a short submission is never enlarged beyond `1`;
- cancelling and completing print both restore the original modal.

Save each print to a temporary PDF and verify one page:

```bash
pdfinfo /tmp/configurator-short.pdf | rg '^Pages:\\s+1$'
pdfinfo /tmp/configurator-long.pdf | rg '^Pages:\\s+1$'
```

Expected: each command prints `Pages:           1`.

- [ ] **Step 5: Commit documentation and version**

```bash
git add README.md style.css
git commit -m "docs: record private configurator print surface"
```

## Completion Gate

This plan is complete only when the focused and existing regressions pass, both
representative PDFs contain exactly one Letter page with all modal content, the
private-page assets remain conditional, `git status --short` contains no
unintended changes, and no live deployment or cache operation has occurred.
