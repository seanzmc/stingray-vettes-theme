# Calculator Surface Completion Spec

Objective

Complete the `/calculator/` surface in the Stingray Corvette WordPress theme by mounting the vendored Stingcalc calculator inside the shared design-system chrome, without changing calculator business logic or introducing a build step.

Relevant files / areas

Already present and should be preserved unless verification proves otherwise:

- `functions.php`
  - Already conditionally enqueues calculator assets on `is_page( 'calculator' )`.
  - Existing handles:
    - `stingray-calculator` stylesheet: `assets/calculator/calculator.css`
    - `stingray-calculator` script: `assets/calculator/script.js`
    - `stingray-calculator-qp` script: `assets/calculator/qp-new.js`
- `assets/calculator/script.js`
  - Untracked at the time this spec was written.
  - SHA-256 matches `/Users/seandm/Projects/Stingcalc/script.js`.
  - Treat as vendored logic; do not edit for this pass.
- `assets/calculator/qp-new.js`
  - Untracked at the time this spec was written.
  - SHA-256 matches `/Users/seandm/Projects/Stingcalc/qp/qp-new.js`.
  - Treat as vendored logic; do not edit for this pass.
- `assets/calculator/calculator.css`
  - Untracked at the time this spec was written.
  - Theme-authored DS skin for the calculator markup.
  - May be adjusted only to fix actual integration/visual issues found during verification.
- `/Users/seandm/Projects/Stingcalc/index.html`
  - Source of truth for calculator body markup and DOM contracts.
  - Port the calculator body markup, not the document head, favicon, old stylesheets, or script tags.
- Create: `page-calculator.php`
  - Missing at the time this spec was written.
  - This is the primary deliverable.

Context and constraints

- This theme has no build step. Use PHP templates, vanilla CSS, and vanilla JS only.
- Do not add dependencies.
- Do not deploy, SFTP, call WordPress APIs, or modify the live site in this pass.
- Preserve the planned public slug: `/calculator/`.
- Use `get_header()` and `get_footer()` so the calculator surface uses the shared topbar/footer from `inc/topbar.php` and `inc/site-footer.php`.
- Use WordPress escaping APIs for theme URLs and text where PHP emits dynamic values.
- Keep the theme compatible with `Requires PHP: 7.4` in `style.css`.
- Preserve design-system invariants:
  - selection/focus = outline treatment, not filled where it would change meaning
  - hover lift = hard offset shadow, not blur/scale
  - transitions about 140ms ease
  - hit targets at least 42px
  - visible focus states
  - no emoji or new icon libraries
- Treat calculator JS as vendored logic. The task is mounting/skin/integration, not calculator math changes.

Proposed approach

Build `page-calculator.php` as a thin WordPress wrapper around the existing Stingcalc markup:

1. Use shared interior chrome:
   - `get_header();`
   - `<main class="sc-page">`
   - `<div class="sc-page-inner">`
   - page eyebrow/title/lede using existing surface classes
   - calculator wrapper around the ported Stingcalc markup
   - `get_footer();`

2. Port the calculator DOM from `/Users/seandm/Projects/Stingcalc/index.html`:
   - Include the contents of the source `.calculator-container` and the nested tab/pane/form markup.
   - Do not include the source `<html>`, `<head>`, `<body>`, `<noscript>` head style block, favicon, old CSS links, old script tags, or source `.container` heading shell.
   - Add a theme-local no-JS message in the page body so non-JS users receive an explanation without hiding the entire page through inline head CSS.

3. Preserve all JS selector contracts exactly.

Required contracts from `assets/calculator/script.js`:

- Top-level tab controls:
  - `.tab-btn[data-tab]`
  - `data-tab="payment-calculators"`
  - `data-tab="income-calc"`
  - `data-tab="quick-pencil"`
  - `.tab-pane`
  - `.tab-pane.active`
- Payment calculator forms/results:
  - `#payment-form`
  - `#payment-result .amount`
  - `#amount-form`
  - `#amount-result .amount`
  - `#income-form`
  - `#income-result .amount`
  - `#income-annual`
  - `#interest-rate-form`
  - `#interest-rate-result .amount`
  - `#disableDocStampPayment`
  - `#disableDocStampAmount`
  - `#payment-calculator-info`
  - `#payment-calculator-heading`
  - `#payment-calculators .payment-subtabs .tab-btn`
  - `#payment-calculators .calculator-pane`
  - `data-calculator="payment-calc"`
  - `data-calculator="amount-calc"`
  - `data-calculator="rate-solver"`
- Input/result IDs used by the script include, but are not limited to:
  - `#loan-amount`
  - `#loan-term`
  - `#interest-rate`
  - `#payment-doc-stamp`
  - `#payment-total-loan`
  - `#payment-total-cost`
  - `#desired-payment`
  - `#amount-term`
  - `#amount-rate`
  - `#amount-doc-stamp`
  - `#amount-total-loan`
  - `#principal-amount`
  - `#interest-term`
  - `#target-payment`
  - `#interest-validation-message`
  - `#ytd-amount`
  - `#check-date`
  - `#hire-date`
- Utility/control classes:
  - `.result.hidden`
  - `.button-group`
  - `.calculate-btn`
  - `.clear-btn`

Required contracts from `assets/calculator/qp-new.js`:

- Quick Pencil root and tabs:
  - `#quick-pencil`
  - `#quick-pencil .tab-btn`
  - `data-sale-type="new"`
  - `data-sale-type="used"`
- Form and rows:
  - `#qp-form`
  - `.qp-row`
  - `data-field="custom-tag-fee"`
  - `data-field="custom-tax-rate"`
  - `data-field="rebates-reduce-taxable"`
  - `data-field="state-dropdown"`
  - `data-field="msrp"`
  - `data-field="discount"`
  - `data-field="rebates"`
  - `data-field="selling-price"`
- Inputs/results used by Quick Pencil:
  - `#tag-type`
  - `#custom-tag-fee`
  - `#tax-outside-fl`
  - `#rebates-reduce-taxable`
  - `#state-select`
  - `#qp-results`

Implementation tasks

Task 1: Create the calculator page template

Files:

- Create: `page-calculator.php`

Steps:

1. Read `/Users/seandm/Projects/Stingcalc/index.html` before editing and identify the complete `.calculator-container` block.
2. Create `page-calculator.php` with standard theme header/footer:
   - file docblock naming `/calculator/`
   - `get_header();`
   - `main.sc-page > .sc-page-inner`
   - page header:
     - eyebrow: `Payment Tools`
     - title: `Payment Calculator`
     - lede: customer-facing explanation that the tool estimates payments, income, and quick-pencil figures before starting an order
   - `<noscript>` body message using `sc-note sc-note--info`
   - `<div class="sc-calc-wrap">` around the ported `.calculator-container`
   - `get_footer();`
3. Paste the source calculator markup inside `.sc-calc-wrap`, preserving IDs/classes/data attributes exactly.
4. Remove source-only wrapper/metadata artifacts:
   - no source `.container` heading wrapper unless needed for JS; it is not a JS contract
   - no old stylesheet links
   - no old script tags
   - no source favicon/meta tags

Acceptance criteria:

- `page-calculator.php` exists.
- `/calculator/` will bind by slug in WordPress.
- The page uses shared theme chrome.
- Calculator markup contains the required JS contract IDs/classes/data attributes.
- No inline script or old Stingcalc stylesheet references are introduced.

Task 2: Static contract audit

Files:

- Inspect: `page-calculator.php`
- Inspect only unless a contract is missing: `assets/calculator/script.js`, `assets/calculator/qp-new.js`

Steps:

1. Search both calculator JS files for `getElementById`, `querySelector`, `querySelectorAll`, `.dataset`, and required class names.
2. Confirm every selector has matching markup in `page-calculator.php`.
3. If a selector is missing, update only `page-calculator.php` markup unless the source Stingcalc markup itself is missing the contract.
4. Do not change vendored JS for styling or naming preferences.

Suggested verification command:

- `php -l page-calculator.php`
- `node --check assets/calculator/script.js`
- `node --check assets/calculator/qp-new.js`

Acceptance criteria:

- PHP lint passes.
- JS syntax checks pass.
- No missing selector contract is known after the audit.

Task 3: CSS integration check

Files:

- Inspect/possibly modify: `assets/calculator/calculator.css`
- Inspect: `assets/css/surfaces.css`
- Inspect: `assets/css/theme.css`

Steps:

1. Verify `calculator.css` styles the actual classes present in `page-calculator.php`.
2. Check that the root wrapper class `.sc-calc-wrap` exists around `.calculator-container`.
3. Check responsive behavior for:
   - top-level tabs
   - payment sub-tabs
   - form rows
   - result panels
   - Quick Pencil rows/results
4. Only patch `calculator.css` if browser/static verification reveals actual layout or accessibility problems.
5. Do not restyle unrelated surfaces.

Acceptance criteria:

- Calculator appears as one DS-styled surface inside the shared page shell.
- No obvious duplicate outer container/heading hierarchy.
- Controls remain readable on dark carbon background.
- Tabs and active states are visually clear without breaking DS invariants.
- Mobile layout does not require horizontal scrolling for normal form controls.

Task 4: README and plan status update

Files:

- Modify: `README.md`
- Modify: `docs/superpowers/plans/2026-07-03-surface-integration.md`

Steps:

1. Update README `## Surfaces` so it no longer says all six content pages are later passes.
2. Document current implemented/in-progress surfaces accurately:
   - `front-page.php` implemented
   - `page-order.php` implemented
   - `page-calculator.php` implemented after this pass
   - deposit/B&P/factory/process remain planned templates unless completed separately
3. In the integration plan, mark Task 3 calculator subtasks complete only after the template and verification pass.
4. Do not mark later tasks complete.

Acceptance criteria:

- README accurately describes calculator and order status.
- The integration plan reflects the calculator task completion status without overstating live/deployment status.

Task 5: Verification pass

Commands:

- `php -l page-calculator.php functions.php header.php footer.php inc/topbar.php inc/site-footer.php`
- `node --check assets/calculator/script.js`
- `node --check assets/calculator/qp-new.js`
- `git status --short`

Manual browser verification:

- Open `/calculator/` in a local or WordPress preview environment.
- Confirm shared topbar/footer render once.
- Confirm no console errors on load.
- Confirm top-level tabs switch:
  - Payment Calculators
  - Income Calculator
  - Quick Pencil
- Confirm payment sub-tabs switch:
  - Monthly Payment
  - Loan Amount
  - Rate Solver
- Confirm at least one monthly payment calculation renders a result.
- Confirm Income Calculator renders monthly and annual values.
- Confirm Quick Pencil can switch New/Used and show a result in `#qp-results`.
- Confirm mobile width around 390px remains usable.
- Confirm desktop width around 1440px does not look underbuilt or over-boxed.

Acceptance criteria:

- All command gates pass.
- Manual browser QA confirms the calculator is usable.
- Any unverified browser checks are explicitly reported as pending; do not claim them complete if not run.

Commit recommendation

After verification, commit the calculator pass separately from later surfaces:

- Include:
  - `page-calculator.php`
  - `assets/calculator/calculator.css`
  - `assets/calculator/script.js`
  - `assets/calculator/qp-new.js`
  - README/plan status edits if made
- Do not include unrelated files unless intentionally approved.
- Decide explicitly whether to include currently untracked `AGENTS.md`; it is outside the calculator deliverable unless Sean wants repo-local agent notes committed.

Suggested commit subject:

- `feat: mount calculator surface`

Risks / assumptions

- The calculator source page is static HTML. WordPress template markup must preserve the same DOM contracts or the existing JS will fail silently.
- The calculator JS focuses fields on tab changes; browser QA should confirm this does not produce annoying scroll jumps inside the themed page.
- `functions.php` already has calculator enqueue wiring. If the WordPress page slug is not exactly `calculator`, conditional assets will not load.
- Existing `assets/calculator/calculator.css` is untracked. Review it as new work before committing.
- Plugin/live-site behavior is not involved in the calculator pass; this is a self-contained static surface.
- This pass does not deploy or replace the live site.

Non-goals

- Do not change calculator formulas, Florida doc stamp logic, Quick Pencil calculations, or validation behavior.
- Do not modify Stingcalc source files in `/Users/seandm/Projects/Stingcalc/`.
- Do not build deposit, B&P, factory, or process pages in this pass.
- Do not change global design-system tokens.
- Do not introduce React, bundlers, npm, or any build tooling.
- Do not push or deploy.

Recommended reasoning level

Medium. This is mostly a careful DOM-port and verification task. The main risk is breaking JS selector contracts, not algorithmic complexity.
