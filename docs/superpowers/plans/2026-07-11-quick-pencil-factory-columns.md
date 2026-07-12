# Quick Pencil Alignment and Factory Column Visibility Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Align Quick Pencil controls in a consistent right-hand column and allow wpDataTables to display every plugin-visible Factory column.

**Architecture:** Keep the change entirely in the two existing theme-authored CSS layers and the focused Factory regression test. Calculator markup and JavaScript remain unchanged; wpDataTables remains responsible for column visibility, while the existing theme-owned row-details dialog remains available.

**Tech Stack:** Classic WordPress theme, vanilla CSS, vanilla JavaScript, Node.js regression script.

## Global Constraints

- Preserve all Quick Pencil IDs, names, selectors, field order, calculations, New/Used switching, validation, decimal steps, and results rendering.
- Preserve the dark carbon visual language, existing typography, focus states, and minimum hit targets.
- Preserve wpDataTables filtering, sorting, pagination, responsive wrapper, plugin-controlled visibility, and the Factory row-details dialog.
- Do not modify PHP templates, calculator JavaScript, wpDataTables configuration, Google Sheet data, plugin files, dependencies, or live-site settings.
- Do not deploy, use SFTP, call WordPress APIs, or alter the live site during implementation.
- Do not add dependencies, create a build step, or refactor unrelated code.

---

### Task 1: Return Factory Column Visibility to wpDataTables

**Files:**
- Modify: `tests/factory-table.test.js:158-166`
- Modify: `assets/css/embeds.css:582-605,738-750`

**Interfaces:**
- Consumes: wpDataTables-generated `.sc-embed table.wpDataTable` markup and the existing horizontally scrollable `.wpDataTablesWrapper`.
- Produces: theme CSS that does not hide or assign fixed widths to columns by position; unchanged `.sc-factory-row` dialog preparation behavior.

- [ ] **Step 1: Replace the regression assertions with the intended visibility boundary**

Replace the two assertions that require desktop and mobile column hiding with:

```js
assert(
	-1 === styles.indexOf('th:nth-child(n + 4)') &&
	-1 === styles.indexOf('td:nth-child(n + 4)'),
	'Factory styles must leave fourth and subsequent column visibility to wpDataTables.'
);
assert(
	-1 === styles.indexOf('table.wpDataTable th:nth-child(2)') &&
	-1 === styles.indexOf('table.wpDataTable td:nth-child(2)'),
	'Factory styles must not hide the last-updated column on mobile.'
);
```

Keep every row-validation and dialog assertion below these checks unchanged.

- [ ] **Step 2: Run the focused regression test and verify the new boundary fails**

Run:

```bash
node tests/factory-table.test.js
```

Expected: FAIL with both new visibility messages because `assets/css/embeds.css` still contains the desktop and mobile `nth-child` hiding rules.

- [ ] **Step 3: Remove only the theme-owned positional column restrictions**

Delete this entire block from `assets/css/embeds.css`:

```css
/* Factory table: the first three columns stay scannable; full row data opens
   in the theme-owned dialog below. The source columns remain in the DOM so the
   live Google Sheet row is available without widening every table row. */
.sc-embed table.wpDataTable {
  min-width: 0 !important;
  table-layout: fixed !important;
}

.sc-embed table.wpDataTable th:nth-child(n + 4),
.sc-embed table.wpDataTable td:nth-child(n + 4) {
  display: none !important;
}

.sc-embed table.wpDataTable th:nth-child(1) {
  width: 30% !important;
}

.sc-embed table.wpDataTable th:nth-child(2) {
  width: 44% !important;
}

.sc-embed table.wpDataTable th:nth-child(3) {
  width: 26% !important;
}
```

Inside `@media (max-width: 640px)`, delete only:

```css
  .sc-embed table.wpDataTable th:nth-child(2),
  .sc-embed table.wpDataTable td:nth-child(2) {
    display: none !important;
  }

  .sc-embed table.wpDataTable th:nth-child(1) {
    width: 60% !important;
  }

  .sc-embed table.wpDataTable th:nth-child(3) {
    width: 40% !important;
  }
```

Do not change the existing `.wpDataTablesWrapper` `overflow-x: auto`, row-focus styles, modal styles, or JavaScript.

- [ ] **Step 4: Run the Factory regression and static selector checks**

Run:

```bash
node tests/factory-table.test.js
rg -n "table\.wpDataTable (th|td):nth-child" assets/css/embeds.css
git diff --check
```

Expected: the Node script prints `Factory table row-preparation regression tests passed.`; `rg` returns no matches; `git diff --check` prints nothing.

- [ ] **Step 5: Commit the Factory visibility fix**

```bash
git add assets/css/embeds.css tests/factory-table.test.js
git commit -m "fix: honor factory table column visibility"
```

---

### Task 2: Align Quick Pencil Controls at the Right

**Files:**
- Modify: `assets/calculator/calculator.css:345-369`

**Interfaces:**
- Consumes: existing `.qp-row` markup containing one label and one input, select, or checkbox.
- Produces: a desktop two-column grid with a shared 280-pixel control track at the right edge and the existing single-column layout at 600 pixels and below.

- [ ] **Step 1: Record the current CSS boundary as the failing static check**

Run:

```bash
rg -n -U "grid-template-columns: minmax\(0, 1fr\) 280px;[\s\S]{0,240}width: 100%;" assets/calculator/calculator.css
```

Expected: no match because the Quick Pencil currently uses `minmax(140px, 190px) minmax(0, 1fr)` and only sets `max-width` on controls.

- [ ] **Step 2: Implement the shared right-hand control track**

Change the desktop Quick Pencil rules to:

```css
#quick-pencil .qp-row {
  display: grid;
  grid-template-columns: minmax(0, 1fr) 280px;
  align-items: center;
  gap: 20px;
  margin-bottom: 10px;
}

#quick-pencil .qp-row label {
  margin: 0;
  justify-self: start;
}

#quick-pencil .qp-row input,
#quick-pencil .qp-row select {
  width: 100%;
  max-width: none;
}

#quick-pencil .qp-row.checkbox-group {
  display: grid;
}

#quick-pencil .qp-row.checkbox-group input[type="checkbox"] {
  width: auto;
  justify-self: center;
}
```

Leave the existing `@media (max-width: 600px)` grid collapse and `max-width: none` rules in place. The checkbox-specific `width: auto` prevents the shared input width from stretching the checkbox.

- [ ] **Step 3: Verify the CSS contract and file integrity**

Run:

```bash
rg -n -U "grid-template-columns: minmax\(0, 1fr\) 280px;[\s\S]{0,240}width: 100%;" assets/calculator/calculator.css
rg -n -U "@media \(max-width: 600px\)[\s\S]*#quick-pencil \.qp-row \{[[:space:]]*grid-template-columns: 1fr;" assets/calculator/calculator.css
git diff --check
```

Expected: both `rg` commands locate the intended desktop and mobile rules; `git diff --check` prints nothing.

- [ ] **Step 4: Run the available calculator and Factory static gates**

Run:

```bash
node --check assets/calculator/script.js
node --check assets/calculator/qp-new.js
node tests/factory-table.test.js
```

Expected: both syntax checks exit successfully and the Factory test prints its pass message.

- [ ] **Step 5: Commit the Quick Pencil alignment**

```bash
git add assets/calculator/calculator.css
git commit -m "fix: align quick pencil controls"
```

---

### Task 3: Browser QA and Release Handoff

**Files:**
- Inspect: `assets/calculator/calculator.css`
- Inspect: `assets/css/embeds.css`
- Inspect: `assets/js/factory-table.js`
- Do not modify or deploy production files in this task.

**Interfaces:**
- Consumes: the completed CSS changes and a WordPress environment containing the Calculator page and production-equivalent wpDataTable configuration.
- Produces: visual evidence and a release decision for a separately approved versioned deployment candidate.

- [ ] **Step 1: Check the repository release boundary**

Run:

```bash
git status --short
git log -3 --oneline
grep '^Version:' style.css
```

Expected: no uncommitted implementation files; both focused fix commits are present; the current theme version is reported without changing it during this implementation pass.

- [ ] **Step 2: Verify Quick Pencil at desktop width**

Open `/calculator/`, select Quick Pencil, and confirm:

- every visible text, number, and select control begins and ends on the same vertical lines;
- labels remain left-aligned;
- the Tax outside FL checkbox sits in the shared control column;
- New and Used tabs reveal their correct conditional fields;
- one representative New calculation and one representative Used calculation render results without a console error.

- [ ] **Step 3: Verify Quick Pencil at mobile width**

At a viewport no wider than 600 pixels, confirm each label stacks above its control, controls fit without horizontal page overflow, the checkbox remains usable, and Calculate/Clear remain reachable.

- [ ] **Step 4: Verify the Factory table and dialog**

Open `/factory/` and confirm:

- all columns marked visible in wpDataTables appear in the rendered header and rows;
- the table wrapper scrolls horizontally when required;
- filtering, sorting, pagination, and the filter header remain usable;
- clicking a valid row opens the details dialog with the complete row;
- Enter and Space open the dialog from a focused row, Escape closes it, and focus returns to the row.

- [ ] **Step 5: Report the deployment boundary**

Report browser results, any unavailable environment checks, the exact implementation commits, and that no staging or production mutation occurred. Propose a separate release pass that bumps `style.css`, uploads the exact candidate to staging, verifies it, requests explicit production approval, uploads the approved candidate to `/wp-content/themes/stingray-corvette/`, verifies remote checksums, clears caches, and runs live smoke checks.
