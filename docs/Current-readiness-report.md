# Current Readiness Report

Updated: 2026-07-09 after approval and local verification of the canonical order-runtime redirect candidate `0.1.13`.

## Release decision

**Current decision: NO-GO for production pending staging deployment and redirect QA of candidate `0.1.13`, followed by separate production-deployment approval.**

The four previously documented implementation/content blockers are cleared on staging:

1. Cloudflare now authorizes the staging hostname and renders the Turnstile challenge instead of error `110200`.
2. wpDataTables table 7 reads the requested Google worksheet and renders real factory-order rows.
3. Formidable form 8 uses the valid ZR1 image and no longer applies the identified legacy inline colors.
4. `/process/` copy is approved and its cancellation/refund language has been clarified.

The order form is now explicitly owned by the 27vette repository and its existing customer-facing runtime at `https://order.stingraychevroletcorvette.com/`. Candidate `0.1.13` keeps all theme links on `/order/` and adds a temporary redirect to that canonical runtime, eliminating the stale duplicate as a launch dependency. The redirect has passed a local WordPress-function harness but has not yet been deployed to staging. No production deployment or real order submission was performed.

## Candidate and staging state

- Repo branch: `main`
- Base candidate commit: `61404c7`
- Canonical-order redirect and documentation diff: local, uncommitted
- Candidate version: `style.css` `Version: 0.1.13`
- Staging theme: active, version `0.1.12`
- Staging base URL: `https://staging-427b-stingraychevroletcorvette.wpcomstaging.com/`
- Candidate `0.1.13` has not been deployed; the seven-route and rendered-asset staging results below still describe `0.1.12`.
- Required staging page custom fields remain populated:
  - `/deposit/`: `[formidable id=8]`
  - `/build-and-price/`: `[formidable id=30]`
  - `/factory/`: `[wpdatatable id=7 table_view=regular]`
- Formidable, wpDataTables, and the order-submit plugin remain active on staging.

## Candidate changes

### Canonical order runtime

- `functions.php` redirects the local `/order/` page to `https://order.stingraychevroletcorvette.com/` with HTTP 302 during the reversible launch phase.
- Existing homepage, navigation, footer, and Process links remain on the planned local `/order/` slug.
- The authoritative runtime's `app.js`, `data.js`, and `styles.css` match 27vette `origin/main` byte-for-byte by SHA-256.
- `page-order.php` and `assets/order/` remain dormant rollback material; they are no longer an independently maintained runtime or a launch source of truth.

### Google Sheet / wpDataTables integration

- `functions.php` adds a table-7-scoped `wpdatatables_filter_google_sheet_array` filter.
- wpDataTables 7.5.1 drops the worksheet `gid` when adapting a published `2PACX` URL. The theme filter preserves the configured publication URL, fetches its requested worksheet as CSV, normalizes multiline headers, and fails back to the plugin-provided rows on invalid host, URL, response, or schema.
- Successful parsed responses, including a valid empty worksheet, are cached for five minutes; the uncached request timeout is eight seconds.
- CSV records with omitted trailing empty fields are padded to the header count instead of discarded.
- Table 7 metadata now matches all 14 source columns.
- Identifier-like values such as Ship-to, Model Year, and MSRP remain source strings instead of receiving inappropriate numeric formatting.
- Public filters are limited to Order # and Current.
- The plugin's responsive row mutation is disabled for table 7; the theme owns the compact responsive presentation and details interaction.

### Factory table presentation

- The public table presents Order #, Last Updated @ Factory, and Current at desktop width.
- At 390px it presents Order # and Current without page overflow.
- The complete 14-column row remains in the DOM and opens in an accessible theme-owned dialog.
- Rows are mouse- and keyboard-operable; Enter/Space opens the dialog, Escape closes it, focus moves to the close control, and the dialog traps Tab focus.
- Empty source values are omitted from the detail list rather than shown as blank rows.

### Formidable form 8

- Field 696 now uses:
  `https://stingraychevroletcorvette.com/wp-content/uploads/pictures/deposit-form/zr1-closed.png`
- Legacy inline `color` declarations were removed from Formidable fields 696, 699, 1729, and 2221 while preserving non-color emphasis/layout declarations.
- The form content now inherits the theme's dark-surface typography and link colors.

### Process copy

- Refund eligibility now states directly that a customer may cancel and request a full refund before the order is submitted to the factory.
- The non-refundable point is consistently described as factory submission for production.
- Refund request, address-confirmation, check-payee, E-Ray transfer, list-move, and refund instructions are separated into single-purpose bullets.
- The approved business policy itself was not changed.

## Static and rendered gates

Passed locally against candidate `0.1.13`:

- PHP lint: all 14 root/include PHP files plus the Factory Sheet regression harness
- JavaScript syntax: all seven theme scripts plus the Factory table regression test
- Factory Sheet regression test: valid empty worksheet, trailing-empty-field padding, and short-lived response caching
- Factory table regression test: real rows remain operable; rows that start invalid or later become empty/child/group/malformed are unprepared and non-interactive
- Redirect harness: `/order/` returns the canonical HTTPS location with status 302; a non-Order page does not redirect
- Canonical runtime parity: `app.js`, `data.js`, and `styles.css` each return HTTP 200 and SHA-256-match 27vette `origin/main`
- `git diff --check`

Previously passed on staging against `0.1.12`; this is the historical pre-redirect baseline, not the expected `/order/` status after deploying `0.1.13`:

- Seven-route HTTP gate: `/`, `/order/`, `/deposit/`, `/build-and-price/`, `/calculator/`, `/factory/`, `/process/`
- Version alignment: every rendered route reports only `0.1.12` theme asset versions
- Rendered asset gate: 21 of 21 local theme assets return HTTP 200
- Staging database gate:
  - table 7 has 14 synchronized columns
  - `responsive=0`
  - `display_length=10`
  - Formidable field backup set contains all four expected records
  - old ZR1 URL absent
  - new ZR1 URL present
  - identified inline color styles absent

## Affected browser QA

### Factory

- Google publication ID and worksheet `gid=520639850` are preserved.
- The source currently produces 25 rows.
- Public headers are Order #, Last Updated @ Factory, and Current.
- Pagination reports `Showing 1 to 10 of 25 entries`.
- The prior malformed `Group"` / `Model` row is absent.
- Desktop rows are 44px high instead of expanding to the height of the Ordered Options field.
- Filtering to zero results leaves the DataTables placeholder non-focusable and non-interactive; clicking it does not open a dialog.
- Clearing the filter restores ten prepared rows and `Showing 1 to 10 of 25 entries`.
- A staging DOM mutation that converted a prepared row into a DataTables child row removed its trigger class, tabindex, and dialog ARIA attributes; a subsequent click did not open the dialog.
- The row dialog opened by keyboard and a synthetic browser mouse event, displayed 13 populated fields for the sampled row, and rendered with the dark theme.
- At an asserted 390px iframe width:
  - no page-level horizontal overflow
  - visible headers are Order # and Current
  - all 14 source cells remain available to the dialog
  - the sampled dialog displayed correctly with aligned labels/values
- No JavaScript errors were reported.

### Order / Turnstile

- The canonical order runtime currently serves Stingray, Grand Sport, and Z06 from the 27vette deployment.
- Its `app.js`, `data.js`, and `styles.css` match current 27vette `origin/main` exactly.
- The prior staging-hosted `0.1.12` non-submitting Turnstile checks are superseded by the canonical-runtime ownership decision.
- Browser verification that staging `/order/` redirects to the canonical runtime remains pending deployment of `0.1.13`.

### Deposit / Formidable

- Selecting Yes revealed the conditional model fields.
- The new ZR1 image loaded completely at natural size 1000×1000.
- The old `zr1-closed-150x150.png` URL is absent from rendered form HTML.
- The new `/pictures/deposit-form/zr1-closed.png` URL is present.
- Rendered Formidable content contains zero inline color declarations.
- No JavaScript errors were reported.

### Process

- All four revised refund-policy sentences are present in staging HTML.
- E-Ray transfer, list-move, and refund instructions are present as separate items.
- Superseded cancellation/refund wording is absent.
- The page has no horizontal overflow at the tested desktop viewport.
- The route serves candidate version `0.1.12`; the Process code did not change in the review follow-up.

## Independent review follow-up

The independent reviews found no XSS, SSRF, enqueue/version, or process-copy defect. Their edge cases are corrected and covered by regression tests in the `0.1.12` staging deployment:

- a valid header-only worksheet no longer falls back to the wrong/default worksheet;
- CSV records with omitted trailing fields are retained and padded;
- parsed responses are cached for five minutes and the uncached request is bounded to eight seconds;
- DataTables empty-result, child, group, and malformed rows no longer become order-dialog triggers.
- a previously prepared row is actively unprepared if a later DataTables redraw turns it into an invalid row, and event handling revalidates the row before opening a dialog.

The final focused re-review passed with no remaining logic or security errors. It independently reran both Factory regression suites, JavaScript syntax validation, and `git diff --check`.

Staging cache verification returned the same 25 rows on both calls: the uncached request completed in 253ms and the immediate cached call completed in less than 1ms.

## Cleared blockers

### Turnstile hostname authorization — cleared

The staging hostname is accepted by the configured widget. The challenge now renders normally and no longer emits Cloudflare error `110200`.

### wpDataTables worksheet selection and row details — cleared

Table 7 now renders the requested worksheet's current rows, has synchronized metadata, retains all details, paginates normally, and provides a usable compact/mobile presentation.

### Formidable image and inline colors — cleared

The replacement image loads and the identified inline colors are absent from both the stored field definitions and rendered form.

### Process business-copy approval — cleared

The approved copy is deployed with the requested cancellation/refund refinements.

## Remaining production gate

### Canonical redirect staging QA — not run

Before production GO:

1. Deploy exact candidate `0.1.13` to staging under the existing staging-only approval boundary.
2. Verify staging `/order/` returns HTTP 302 with `Location: https://order.stingraychevroletcorvette.com/`.
3. Verify the redirect occurs before the dormant local order CSS, JavaScript, Turnstile, or page template is emitted.
4. Follow the redirect on desktop and mobile and verify the canonical runtime loads without console or required-resource errors.
5. Re-run the remaining six local staging routes and version/asset gates to confirm the redirect change did not affect them.

A controlled submission on the canonical runtime is not part of this redirect pass. It can create a real dealer record and still requires separate explicit approval for test identity, recipient, and cleanup.

## Gates intentionally not run

- No automated CAPTCHA/Turnstile solving.
- No real order-form POST or lead creation.
- No staging upload or redirect browser QA for `0.1.13`.
- No production upload, activation, cache flush, or live smoke test.

## Required path to GO

1. Deploy `0.1.13` to staging and complete the redirect plus unaffected-route QA above.
2. Update this report with the staging result and issue GO if it passes.
3. Obtain separate explicit approval before production deployment.