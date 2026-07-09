# Current Readiness Report

Updated: 2026-07-09 after staging deployment and browser QA of theme candidate `0.1.5`.

## Release decision

**Current decision: NO-GO for production.**

The theme-owned implementation is now a viable staging candidate: static gates pass, all public routes serve version `0.1.5`, Formidable controls inherit the dark theme, and wpDataTables no longer shows the white/Mojito stock skin. Production deployment remains blocked by three plugin/integration defects and one business-content approval gate documented below.

No production deployment, live-site mutation, or real order submission was performed.

## Candidate and staging state

- Repo branch: `main`
- Candidate version: `style.css` `Version: 0.1.5`
- Staging theme: active, version `0.1.5`
- Staging base URL: `https://staging-427b-stingraychevroletcorvette.wpcomstaging.com/`
- Every versioned theme CSS/JS asset on all seven public routes uses `?ver=0.1.5`.
- All seven routes return HTTP 200.
- Required page custom fields are populated on staging:
  - `/deposit/`: `[formidable id=8]`
  - `/build-and-price/`: `[formidable id=30]`
  - `/factory/`: `[wpdatatable id=7 table_view=regular]`
- Formidable, wpDataTables, and the order-submit plugin are active on staging.
- The old local WP Studio install remains non-authoritative because its plugins are inactive.

## Theme changes in the candidate

### Embed loading

- `functions.php` loads `assets/css/embeds.css` normally for `/deposit/` and `/build-and-price/`.
- wpDataTables registers page-specific styles only after rendering its shortcode. `/factory/` therefore prints the theme embed skin from `wp_footer` after the registered Mojito/core styles.
- Staging HTML proves all wpDataTables 7.5.1 styles load before one `embeds.css?ver=0.1.5` occurrence.

### Formidable

- Existing field, label, radio, checkbox, submit, validation, and message styles were retained.
- HTML-container images are responsive.
- Radio/checkbox labels, upload buttons, and submit buttons have visible keyboard focus treatment.
- No field-ID-specific theme overrides or broad inline-style normalization were added.

### wpDataTables

- The outer wrapper now uses the dark carbon surface, theme border/radius, inherited ChevySans typography, and contained horizontal overflow.
- Filter sections wrap responsively and preserve DOM/tab order.
- Filter labels and inputs are readable and theme-aligned.
- The clear-filter control is a 42px dark button with hover/focus treatment.
- Headers and body cells no longer use Mojito teal/white surfaces or dark-on-dark text.
- Table headers, body cells, pagination, and modal surfaces use scoped theme selectors.
- Narrow layouts keep the 680px table inside the plugin wrapper's horizontal scroller instead of creating page-level overflow.

## Static gates

Passed after the final changes:

- PHP lint: all 14 root/include PHP files
- JavaScript syntax: six theme scripts
- Enqueued local asset existence: 18 paths
- Homepage spin inventory: five paints with 30 frames each
- Embed CSS brace/token validation: all 46 referenced tokens resolve
- Mock enqueue harness:
  - Formidable routes enqueue normally
  - Factory prefers registered `wdt-skin-mojito`
  - Factory falls back to registered `wdt-wpdatatables`
  - Factory remains safe when neither handle is registered
  - Unrelated routes do not enqueue or print the embed skin
- `git diff --check`
- Independent final review: pass

## Browser QA completed

### Responsive matrix

Checked at 390px, 768px, and 1440px:

- `/`
- `/order/`
- `/deposit/`
- `/build-and-price/`
- `/calculator/`
- `/factory/`
- `/process/`

Results:

- No page-level horizontal overflow on any tested route/width.
- All tested pages served theme version `0.1.5`.
- Shared navigation, page shell, and footer rendered on all routes.
- The only failed image was the known Formidable-owned ZR1 image described under blockers.

### Homepage

- `#spinCanvas` rendered at the mobile viewport.
- The correct theme spin base was present.
- All 30 current-paint frames loaded.
- A synthetic pointer drag changed the canvas frame.

### Order form

- Twelve-step app rendered without page overflow.
- Representative required path completed with Arctic White and Jet Black selections.
- Download and Submit controls enabled after required selections.
- Dealer modal opened.
- Turnstile attempted to render; the domain-authorization blocker below prevented a valid challenge.
- No POST was sent.

### Calculator

- Mobile layout had no page overflow.
- Monthly payment test `70000 / 72 / 6.9` returned `$1,194.24`.

### Formidable

- Deposit and Build & Price forms render beneath `.sc-embed`.
- Inputs, radios, labels, buttons, and validation errors are readable on the dark theme.
- Build & Price blank-submit validation rendered 18 themed error/blank-field states and did not submit.
- Build & Price images stay within the 390px layout and none failed.
- Formidable's stock frontend stylesheet is not required for the current presentation.

### wpDataTables

- Desktop computed styles confirm a dark wrapper, dark headers, muted readable cells, ChevySans inheritance, 42px controls, and no page overflow.
- At 390px, filters wrap in DOM order and the wide table remains inside the plugin's horizontal scroller.
- Keyboard traversal exposes visible focus glow on filter controls, the clear button, and sortable headers.

## Production blockers

### 1. Turnstile staging hostname is not authorized

Opening the order submission modal produces Cloudflare Turnstile error `110200`.

Cloudflare's current documentation defines `110200` as **Domain not authorized** and directs the owner to add the current domain in Hostname Management.

Required action:

1. Add `staging-427b-stingraychevroletcorvette.wpcomstaging.com` to the Turnstile widget's allowed hostnames.
2. Re-open the staging order modal and obtain a valid challenge/token.
3. Approve a test lead identity, recipient, and cleanup procedure.
4. Perform one controlled end-to-end POST and verify delivery plus server-side Turnstile validation.

This cannot be cleared by theme CSS or a browser-only test.

### 2. wpDataTables table 7 points at a Google `pubhtml` page

Table `7` (`Orders at Factory_v0`) is configured as `google_spreadsheet`, but its source returns a Google HTML document rather than spreadsheet rows. wpDataTables consequently renders one malformed row:

- `Group"`
- `Model`

A non-mutating test of the corresponding CSV export endpoint returned HTTP 200, `text/csv`, 29 data rows, and these headers:

- `Order #`
- `Last Updated @ Factory`
- `Current`

Required action: after plugin-configuration approval, back up table 7 and change only its source from the published HTML endpoint to the equivalent CSV export endpoint. Then verify filters, real rows, pagination, and row-detail modal behavior.

### 3. Deposit form 8 contains a broken ZR1 image and legacy inline colors

Formidable field `696` (`ZR1 LIST CLOSED`) references:

`https://stingraychevroletcorvette.com/wp-content/uploads/pictures/zr1-closed-150x150.png`

That URL returns HTTP 404. The existing attachment resolves at:

`/wp-content/uploads/pictures/deposit-form/zr1-closed.png`

The deposit form also contains six inline-color declarations, including a stock-blue `Instructions:` link that is visually inconsistent and low-emphasis on the dark surface. Relevant HTML fields include `696`, `699`, `1729`, and `2221`.

Required action: after Formidable-content approval, replace the broken image URL and remove legacy inline color formatting so theme styles can control presentation. CSS should not hide the failed asset or hard-code Formidable field IDs.

### 4. `/process/` business and policy copy needs human approval

The route is structurally and responsively sound, but production still requires Sean's acceptance of:

- deposit/refund rules
- open and closed model lists
- dealer fee and tag-agency fee
- restricted-state/customer language
- pricing and timing disclaimers
- external ZR1 process link

## Gates intentionally not run

- No real order-form POST or lead creation.
- No success-confirmation/payment-link submission path.
- No wpDataTables real-row modal test because table 7 currently has malformed source data.
- No production upload, activation, cache flush, or live smoke test.

## Required path to GO

1. Authorize the staging hostname in Turnstile and complete one approved, removable test submission.
2. Correct wpDataTables table 7's source to CSV and verify real-row behavior.
3. Correct Formidable form 8's broken image and inline presentation content.
4. Obtain business approval for `/process/` copy.
5. Re-run the affected staging checks and update this report to GO.
6. Obtain separate explicit approval before any production deployment.