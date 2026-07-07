# Current Readiness Report

Updated: 2026-07-07 after the latest committed pass.

Current readiness: the theme is structurally ready for final plugin-enabled/staging QA, but it is not yet live-ready. The latest pass changed plugin-backed embeds from hard-coded shortcodes in templates to page-owned shortcode custom fields. That is a good deployment-flexibility improvement, but it adds a required launch setup step: the `stingray_embed_shortcode` custom field must be populated on the live/staging WordPress pages before those embeds can render.

No deployment was performed by this report update.

## Repo state

- Branch: `main`
- origin/main relation: `0 0` — local and origin are in sync.
- Working tree before this report update: clean.
- Latest commits:
  - `d3f323d feat: implement dynamic embed shortcode rendering for deposit, build-and-price, and factory pages`
  - `f29854d feat: add current readiness report with QA status and next steps`
  - `60f5e1d feat: update build-and-price page with improved summary and share instructions`
- Theme version: `style.css` `Version: 0.1.0`
- Cache-busting: local enqueued assets still use `STINGRAY_CORVETTE_VERSION`, sourced from `style.css`.

## Material change since prior report

Plugin-backed page templates no longer hard-code these shortcodes:

- `/deposit/`: formerly `[formidable id=8]`
- `/build-and-price/`: formerly `[formidable id=30]`
- `/factory/`: formerly `[wpdatatable id=7 table_view=regular]`

Instead, all three pages now call:

- `stingray_corvette_render_page_embed_shortcode()`

That function reads the current page custom field:

- `stingray_embed_shortcode`

Behavior:

- If the shortcode custom field is present, the function runs `do_shortcode()`.
- If the shortcode is missing, public visitors see an empty embed area.
- If the shortcode is missing and the viewer can edit the post, WordPress shows an admin-only setup note.
- If the shortcode does not render because the plugin is inactive or the shortcode is invalid, public visitors see an empty embed area and editors see an admin-only plugin/shortcode warning.

Launch implication: the live/staging pages must have the `stingray_embed_shortcode` custom field populated:

- `/deposit/`: `[formidable id=8]`
- `/build-and-price/`: `[formidable id=30]`
- `/factory/`: `[wpdatatable id=7 table_view=regular]`

This is now a required launch checklist item, not optional.

## Static gates run after latest pass

PHP lint passed:

- `front-page.php`
- `page-order.php`
- `page-deposit.php`
- `page-build-and-price.php`
- `page-calculator.php`
- `page-factory.php`
- `page-process.php`
- `page.php`
- `header.php`
- `footer.php`
- `functions.php`
- `inc/topbar.php`
- `inc/site-footer.php`
- `index.php`

JS syntax checks passed:

- `assets/js/nav.js`
- `assets/homepage/spin.js`
- `assets/order/app.js`
- `assets/order/data.js`
- `assets/calculator/script.js`
- `assets/calculator/qp-new.js`

## Asset/enqueue audit

- All local assets referenced by `functions.php` exist.
- No missing local enqueue references found.
- Homepage spin frame sets exist:
  - `g26-orange`: 30
  - `g4z-green`: 30
  - `gbk-yellow`: 30
  - `gkz-red`: 30
  - `gtr-blue`: 30
- Order vehicle PNG assets found: 10
- No legacy replaced-surface URL slugs found in public templates.

## Local WP Studio state

Local preview install:

- `/Users/seandm/Projects/WP-Studio/stingraychevroletcorvettecom`

Theme symlink target:

- `/Users/seandm/Projects/stingray-vettes-theme`

Local WordPress options checked:

- `stylesheet=stingray-corvette`
- `template=stingray-corvette`
- `show_on_front=page`
- `page_on_front=5`
- `active_plugins=[]`

Local pages exist:

- `home`: published
- `order`: published
- `deposit`: published
- `build-and-price`: published
- `calculator`: published
- `factory`: published
- `process`: published

Local embed custom field status:

- `deposit`: empty `stingray_embed_shortcode`
- `build-and-price`: empty `stingray_embed_shortcode`
- `factory`: empty `stingray_embed_shortcode`

Because local plugins are inactive and local custom fields are empty, local WP Studio still cannot prove Formidable/wpDataTables rendering. It can only prove the non-plugin static surfaces and theme asset loading.

## Route readiness summary

### `/`

Status: likely ready pending final responsive visual QA.

Previously verified:

- Homepage rendered in local WP preview.
- `#spinCanvas` present.
- `window.SC_SPIN_BASE` set correctly.
- Initial spin frame resources loaded.
- No console/resource errors observed in the prior browser pass.

Still needed:

- Final mobile/desktop visual pass.
- Confirm 360 drag/spin interaction manually in launch/staging context.

### `/order/`

Status: likely ready pending final responsive/manual flow QA.

Previously verified:

- Order app rendered.
- Step rail and initial vehicle setup loaded.
- `window.SC_FORM_ASSET_BASE` set correctly.
- Turnstile script scoped to `/order/`.
- No console/resource errors observed in the prior browser pass.

Still needed:

- Mobile drawer and summary drawer checks.
- Non-submitting primary path through required selections/customer modal.
- Confirm Turnstile/submission behavior only in an approved test path.

### `/deposit/`

Status: blocked until page custom field and Formidable rendering are verified in a plugin-enabled environment.

Current template behavior:

- Static deposit instructions render from `page-deposit.php`.
- Embed is dynamic via `stingray_corvette_render_page_embed_shortcode()`.
- Required live page custom field: `stingray_embed_shortcode = [formidable id=8]`.

Still needed:

- Set/confirm live/staging page custom field.
- Confirm Formidable form renders inside `.sc-embed`.
- Record Formidable styling setting: enabled, disabled, or intentionally unchanged.
- Verify labels, fields, validation, submit button, confirmation/payment-link behavior, and readability.

### `/build-and-price/`

Status: blocked until page custom field and Formidable rendering are verified in a plugin-enabled environment.

Current template behavior:

- Static Build & Price guidance renders from `page-build-and-price.php`.
- Chevrolet configurator URLs remain external and intentional.
- Embed is dynamic via `stingray_corvette_render_page_embed_shortcode()`.
- Required live page custom field: `stingray_embed_shortcode = [formidable id=30]`.

Still needed:

- Set/confirm live/staging page custom field.
- Confirm Formidable form renders inside `.sc-embed`.
- Verify form readability and validation states with the chosen Formidable styling setting.
- Re-check Chevrolet configurator links during final launch QA.

### `/calculator/`

Status: likely ready pending final responsive visual QA.

Previously verified:

- Calculator rendered.
- Monthly payment test: `70000 / 72 / 6.9` returned `$1,194.24`.
- Income calculator produced output when populated.
- Quick Pencil produced an itemized summary for `$90,000` MSRP/selling price.
- No console/resource errors observed in the prior browser pass.

Still needed:

- Mobile layout QA.
- One final staging/live browser pass after deployment target is configured.

### `/factory/`

Status: blocked until page custom field and wpDataTables rendering are verified in a plugin-enabled environment.

Current template behavior:

- Static factory/status-code content renders from `page-factory.php`.
- The earlier local `/learning-center/` link card was removed from this template.
- Embed is dynamic via `stingray_corvette_render_page_embed_shortcode()`.
- Required live page custom field: `stingray_embed_shortcode = [wpdatatable id=7 table_view=regular]`.

Still needed:

- Set/confirm live/staging page custom field.
- Confirm wpDataTables renders inside `.sc-embed`.
- Verify search/filter input, table header/rows, pagination, row hover/focus, and row details/modal readability.
- If the table shows a service/data error, compare against existing live/source behavior before treating it as a theme regression.

### `/process/`

Status: structurally ready, but business-content review is still required before live deployment.

Static contract present:

- `Deposit Lists — Current Status`
- `Grand Sport X`
- `ZR1X`
- `Dealer Fee` / `$999`
- `Tag Agency Fee` / `$362`
- `smccann@stingraychevrolet.com`
- `813-359-5000`

Still needed:

- Sean review/acceptance of policy/legal-adjacent content.
- Final mobile/desktop visual pass.
- Confirm intentional external ZR1 process link remains acceptable.

## Link audit

No legacy replaced-surface slugs were found in public templates:

- `order-landing-page/deposit-form`
- `order-landing-page/build-and-price-link-share`
- `orders-in-production`
- `corvette-process-guide`
- `process-links`

Intentional external links still present:

- Chevrolet configurator links in `page-build-and-price.php`
- Existing ZR1 process page links in `page-process.php`
- Chevrolet.com and StingrayChevrolet.com footer/drawer links

## Necessary actions before going live

1. Populate required page custom fields in the plugin-enabled staging/live-equivalent WordPress environment:
   - `/deposit/`: `stingray_embed_shortcode = [formidable id=8]`
   - `/build-and-price/`: `stingray_embed_shortcode = [formidable id=30]`
   - `/factory/`: `stingray_embed_shortcode = [wpdatatable id=7 table_view=regular]`

2. Run plugin-backed QA with Formidable and wpDataTables active:
   - Confirm embeds render for public visitors.
   - Confirm editor-only setup/error notes do not appear to public visitors.
   - Confirm form/table controls are readable on the dark theme.
   - Record Formidable styling setting.

3. Run final responsive visual QA on all seven routes:
   - Mobile around 390px.
   - Desktop around 1440px.
   - Tablet around 768px if time allows.
   - Check duplicate chrome, horizontal overflow, overlaps, unreadable controls, focus states, and excessive nesting.

4. Complete Sean’s business-content review for `/process/`.

5. After plugin QA and content review are accepted, bump `style.css` from `0.1.0` to a release/cache-busting version such as `0.2.0`.

6. Deploy only after explicit approval, then verify live routes and cache-busted asset URLs.

7. Keep CarSales / `sales.stingraychevroletcorvette.com` out of this WordPress deployment path. That Worker/subdomain remains separate from `stingraychevroletcorvette.com` and `www`.

## Current project status summary

- Static code gates: pass
- Asset existence/enqueue audit: pass
- Local WP route setup: present
- Homepage initial local browser pass: pass from prior report
- Order app initial local browser pass: pass from prior report
- Calculator primary local browser checks: pass from prior report
- Dynamic embed helper: present
- Required embed page custom fields: pending in live/staging, empty in local WP Studio
- Formidable rendering: pending plugin-enabled environment
- wpDataTables rendering: pending plugin-enabled environment
- Mobile/tablet visual QA: pending
- Business-content approval: pending
- Version/cache-bust bump: pending
- Deployment: not performed

## Recommended next commit, if committing this report update

- `docs: update readiness report`
