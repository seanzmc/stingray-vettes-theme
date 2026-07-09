# Current Readiness Report

Updated: 2026-07-09 after staging deployment, two independent-review remediation passes, and affected-route QA of theme candidate `0.1.12`.

## Release decision

**Current decision: NO-GO for production pending one controlled staging submission and separate production-deployment approval.**

The four previously documented implementation/content blockers are cleared on staging:

1. Cloudflare now authorizes the staging hostname and renders the Turnstile challenge instead of error `110200`.
2. wpDataTables table 7 reads the requested Google worksheet and renders real factory-order rows.
3. Formidable form 8 uses the valid ZR1 image and no longer applies the identified legacy inline colors.
4. `/process/` copy is approved and its cancellation/refund language has been clarified.

The remaining validation gate is a real end-to-end staging POST. That requires separate approval for a test lead identity, recipient, and cleanup procedure; a human may also need to complete the Turnstile challenge. No production deployment or real order submission was performed.

## Candidate and staging state

- Repo branch: `main`
- Base candidate commit: `7c5c172`
- Review-remediation diff and readiness report: local, uncommitted
- Candidate version: `style.css` `Version: 0.1.12`
- Staging theme: active, version `0.1.12`
- Staging base URL: `https://staging-427b-stingraychevroletcorvette.wpcomstaging.com/`
- All seven public routes return HTTP 200 and emit only `?ver=0.1.12` theme assets.
- All 21 rendered local theme assets return HTTP 200.
- Required staging page custom fields remain populated:
  - `/deposit/`: `[formidable id=8]`
  - `/build-and-price/`: `[formidable id=30]`
  - `/factory/`: `[wpdatatable id=7 table_view=regular]`
- Formidable, wpDataTables, and the order-submit plugin remain active on staging.

## Candidate changes

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

Passed against candidate `0.1.12`:

- PHP lint: all 14 root/include PHP files plus the Factory Sheet regression harness
- JavaScript syntax: all seven theme scripts plus the Factory table regression test
- Factory Sheet regression test: valid empty worksheet, trailing-empty-field padding, and short-lived response caching
- Factory table regression test: real rows remain operable; rows that start invalid or later become empty/child/group/malformed are unprepared and non-interactive
- `git diff --check`
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

- Representative required selections completed with Arctic White and Jet Black.
- Download Build and Submit to Dealer became available.
- The dealer-request modal opened.
- Turnstile rendered a visible `Verify you are human` challenge.
- Error `110200` / `Domain not authorized` did not recur.
- The hidden Turnstile response control was created by the widget.
- No challenge was automated and no POST was sent.
- No JavaScript errors were reported.

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

### Controlled staging submission — not run

A real submission would create an external lead/order record and therefore still requires explicit approval beyond browser QA. Before production GO:

1. Approve a test name, email/phone, destination recipient, and cleanup owner.
2. Complete the Turnstile challenge in staging; a human may need to perform this step.
3. Submit exactly one controlled staging request.
4. Verify the server validates the Turnstile token and the intended recipient receives the complete build.
5. Remove or clearly mark the test record.

## Gates intentionally not run

- No automated CAPTCHA/Turnstile solving.
- No real order-form POST or lead creation.
- No production upload, activation, cache flush, or live smoke test.

## Required path to GO

1. Complete and clean up one explicitly approved staging test submission.
2. Update this report with the delivery/server-validation result and issue GO if it passes.
3. Obtain separate explicit approval before production deployment.