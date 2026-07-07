# Final Live-Readiness QA Spec

> **For Hermes:** Use `software-development:subagent-driven-development` only if splitting this into independent review/check tasks. Keep deterministic validation and final reporting in the controller session.

**Goal:** Verify the completed Stingray Corvette theme as an integrated replacement site before any live deployment: all public surfaces, enqueue/load order, plugin-backed embeds, vendored JS contracts, visual responsiveness, cache-busting versioning, and deployment readiness.

**Architecture:** The theme is a classic WordPress theme with one full-bleed homepage and six dedicated page templates bound by local slugs. This pass should not build new surfaces. It should audit, preview, validate, record issues, make only small scoped fixes if required, bump the theme version for cache busting when ready, and produce a clear deployment checklist/handoff.

**Tech Stack:** Classic WordPress theme, PHP 7.4-compatible templates, vanilla CSS/JS, WordPress.com Business target, Formidable Forms, wpDataTables, vendored order-form assets, vendored Stingcalc calculator assets.

---

## Current repo state at spec time

Date checked: 2026-07-07.

Branch / remote:

- Branch: `main`
- `origin/main...HEAD`: `0 0` at inspection time, so local and origin were in sync.
- Working tree: clean at inspection time.

Latest relevant commits:

- `38ccaca Add Corvette Order Process Guide page with detailed deposit and ordering information`
- `4c3520d feat: add factory orders page with status codes and wpDataTables embed`
- `1a63712 feat: add build-and-price page with configurator links and Formidable embed`
- `acbb764 feat: add deposit form page with instructions and Formidable embed`
- `ccab308 feat: complete calculator surface integration with page template and JS contracts`
- `51ea15b Mount the 1a order form at /order/ + shared surface/embed style layers`

Dedicated public templates now present:

- `front-page.php` → homepage
- `page-order.php` → `/order/`
- `page-deposit.php` → `/deposit/`
- `page-build-and-price.php` → `/build-and-price/`
- `page-calculator.php` → `/calculator/`
- `page-factory.php` → `/factory/`
- `page-process.php` → `/process/`

Theme version at inspection time:

- `style.css` has `Version: 0.1.0`
- All enqueued local assets use `STINGRAY_CORVETTE_VERSION`, so the version is the cache-busting key.

Known enqueue graph from `functions.php`:

Global styles:

1. `assets/css/fonts.css`
2. Google Inter fallback
3. `assets/css/tokens/colors.css`
4. `assets/css/tokens/typography.css`
5. `assets/css/tokens/spacing.css`
6. `assets/css/tokens/base.css`
7. `assets/css/styles.css`
8. `assets/css/theme.css`
9. `assets/css/surfaces.css`

Conditional assets:

- Homepage: `assets/homepage/homepage.css`, `assets/homepage/spin.js`, `window.SC_SPIN_BASE`
- `/order/`: `assets/order/order.css`, Cloudflare Turnstile script, `assets/order/data.js`, `assets/order/app.js`, `window.SC_FORM_ASSET_BASE`
- `/calculator/`: `assets/calculator/calculator.css`, `assets/calculator/script.js`, `assets/calculator/qp-new.js`
- `/deposit/`, `/build-and-price/`, `/factory/`: `assets/css/embeds.css`

## Hard constraints

- Do not deploy, push, SFTP, or modify the live site unless Sean explicitly approves a deployment step after QA.
- Do not attach or route the CarSales Cloudflare Worker / `sales.stingraychevroletcorvette.com` to the public WordPress site. That Worker/subdomain is separate and must stay separate from `stingraychevroletcorvette.com` and `www`.
- Do not modify Formidable form definitions, wpDataTables table configuration, Google Sheets/data sources, order-form pricing/RPO logic, calculator JS logic, Turnstile configuration, or live WordPress settings unless explicitly approved.
- Do not introduce dependencies or a build step.
- Do not refactor unrelated code.
- Keep all public links on planned local slugs:
  - `/order/`
  - `/deposit/`
  - `/build-and-price/`
  - `/calculator/`
  - `/factory/`
  - `/process/`
- Keep WordPress escaping and URL helpers where code changes are required.
- Preserve visual direction: dark carbon surfaces, ChevySans, Torch Red accent, hard offset hover shadow, short transitions, accessible focus states, no emoji/new icon libraries.

## Goal-state acceptance criteria

The pass is complete when:

- All PHP templates lint clean.
- Enqueue graph matches the intended load order and page-specific assets are scoped to the right slugs.
- All seven public surfaces render in WordPress preview or a staging/live-equivalent preview:
  - homepage
  - `/order/`
  - `/deposit/`
  - `/build-and-price/`
  - `/calculator/`
  - `/factory/`
  - `/process/`
- No obvious missing CSS, JS, image, font, or shortcode assets are present on previewed pages.
- Homepage 360 viewer loads, spins, and lazy-loads paint frame sets without overlap on mobile/desktop.
- Order-form primary path remains functional and preserves existing business logic and selector/data contracts.
- Calculator primary flows work:
  - tabs
  - monthly payment calculator
  - income calculator
  - quick pencil summary
- Formidable-backed surfaces render forms inside `.sc-embed` and remain readable with the chosen Formidable styling setting.
- wpDataTables-backed `/factory/` renders, or any existing service/data error is documented separately from theme-template readiness.
- `/process/` is reviewed for policy/content correctness by Sean before live deployment.
- `style.css` version is bumped only after QA fixes are complete and the theme is ready for cache-busting.
- Final handoff explicitly states deploy/no-deploy status, gates run, pending risks, and any issues found.

## Task 1: Pre-flight inventory and clean working tree

**Objective:** Confirm the repo is in a known state before QA or edits.

**Files:**

- Inspect only unless status reveals intentional uncommitted files.

**Commands:**

```bash
git status --short
git branch --show-current
git log --oneline -8
git rev-list --left-right --count origin/main...HEAD
find . -maxdepth 1 -name 'page-*.php' -print | sort
```

Use `search_files`, not shell `find`, if following tool rules strictly in Hermes.

**Acceptance criteria:**

- Branch is `main` unless Sean intentionally switches branches.
- Working tree is clean before QA starts, or every local change is explicitly accounted for.
- Six dedicated templates plus `front-page.php` are present.
- Local and origin relation is known.

## Task 2: Static PHP lint and template contract audit

**Objective:** Prove all theme PHP entry points are syntactically valid and key template contracts exist.

**Files:**

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

**Commands:**

```bash
php -l front-page.php
php -l page-order.php
php -l page-deposit.php
php -l page-build-and-price.php
php -l page-calculator.php
php -l page-factory.php
php -l page-process.php
php -l page.php
php -l header.php
php -l footer.php
php -l functions.php
php -l inc/topbar.php
php -l inc/site-footer.php
php -l index.php
```

Static contract checks:

```bash
grep -n "formidable id=8\|formidable id=30\|wpdatatable id=7\|SC_FORM_ASSET_BASE\|SC_SPIN_BASE\|SC\|qp-results\|payment-form" functions.php page-*.php front-page.php
```

Recommended specific checks:

- `page-deposit.php` contains `[formidable id=8]` and `.sc-embed`.
- `page-build-and-price.php` contains `[formidable id=30]`, configurator links, and screenshot URLs.
- `page-factory.php` contains `[wpdatatable id=7 table_view=regular]`, `.sc-embed`, `3800`, `6000`, and `assistance@stingraychevrolet.com`.
- `page-calculator.php` contains required calculator IDs/classes:
  - `payment-form`
  - `loan-amount`
  - `quick-pencil`
  - `qp-results`
  - `tab-btn`
  - `tab-pane`
- `page-process.php` contains policy-critical content:
  - `Deposit Lists`
  - `Grand Sport X`
  - `ZR1X`
  - `Dealer Fee`
  - `Tag Agency Fee`
  - `smccann@stingraychevrolet.com`
  - `813-359-5000`

**Acceptance criteria:**

- All PHP lint commands pass.
- Static contracts are present.
- Any missing contract is fixed or explicitly logged as a blocker.

## Task 3: Enqueue graph and asset existence audit

**Objective:** Ensure every enqueued local asset exists and only loads on intended surfaces.

**Files:**

- `functions.php`
- local assets referenced by `functions.php`

**Checks:**

Confirm these files exist:

- `assets/css/fonts.css`
- `assets/css/tokens/colors.css`
- `assets/css/tokens/typography.css`
- `assets/css/tokens/spacing.css`
- `assets/css/tokens/base.css`
- `assets/css/styles.css`
- `assets/css/theme.css`
- `assets/css/surfaces.css`
- `assets/css/embeds.css`
- `assets/js/nav.js`
- `assets/homepage/homepage.css`
- `assets/homepage/spin.js`
- `assets/order/order.css`
- `assets/order/data.js`
- `assets/order/app.js`
- `assets/calculator/calculator.css`
- `assets/calculator/script.js`
- `assets/calculator/qp-new.js`

Confirm conditional scopes:

- `stingray-homepage` and `stingray-spin` only on `is_front_page()`.
- `stingray-order` / `stingray-order-data` / `stingray-order-app` only on `is_page( 'order' )`.
- `stingray-calculator` / `stingray-calculator-qp` only on `is_page( 'calculator' )`.
- `stingray-embeds` only on `deposit`, `build-and-price`, `factory`.
- `stingray-nav` globally.

**Acceptance criteria:**

- No missing local files.
- No surface-specific asset loads globally by accident.
- `STINGRAY_CORVETTE_VERSION` remains the version source for local assets.

## Task 4: Vendored JS syntax and source-boundary audit

**Objective:** Verify vendored JS is syntactically valid and has not drifted unexpectedly from source where source-of-truth boundaries matter.

**Files:**

- `assets/homepage/spin.js`
- `assets/order/app.js`
- `assets/order/data.js`
- `assets/calculator/script.js`
- `assets/calculator/qp-new.js`

**Commands:**

```bash
node --check assets/homepage/spin.js
node --check assets/order/app.js
node --check assets/order/data.js
node --check assets/calculator/script.js
node --check assets/calculator/qp-new.js
```

Optional provenance checks:

- Compare calculator JS hashes against `/Users/seandm/Projects/Stingcalc/script.js` and `/Users/seandm/Projects/Stingcalc/qp/qp-new.js` if those source files are available.
- Compare order assets against documented 27vette vendored source if needed, but do not alter order logic in this pass.

**Acceptance criteria:**

- JS syntax checks pass.
- Any vendored-source drift is documented before any fix.

## Task 5: WordPress preview environment setup

**Objective:** Establish a preview environment that can exercise WordPress template binding, shortcode rendering, enqueues, plugins, and actual routes.

**Preferred preview target:**

- WordPress.com Business preview/staging/live-equivalent environment where this theme can be activated or previewed safely.

**If no WP preview is available:**

- Do not claim plugin-backed QA complete.
- Run static/lint/harness checks only.
- Report WordPress preview as blocked/pending with the exact missing access/environment.

**Acceptance criteria:**

- Preview URL or blocker is recorded.
- The preview environment uses the correct theme code.
- Formidable Forms and wpDataTables are available if plugin-backed checks are to be marked complete.

## Task 6: Homepage QA

**Objective:** Confirm the replacement homepage is ready as the site’s front door.

**Route:** `/`

**Manual checks:**

- Topbar renders once.
- Hero lockup is readable and does not overlap at desktop and mobile widths.
- Primary CTA routes to `/order/`.
- Secondary/process CTA routes to `/process/`.
- Six quick-action cards route to planned local slugs.
- 360 viewer loads the initial paint frame set.
- Viewer spins/drags correctly.
- Paint changes lazy-load the next frame set as intended.
- No missing logo/spin images.
- No console errors.
- Mobile drawer opens/closes and links work.

**Viewport minimums:**

- Desktop around 1440px.
- Mobile around 390px.

**Acceptance criteria:**

- Homepage is usable and visually coherent on desktop/mobile.
- 360 viewer works.
- No broken internal links among planned surfaces.

## Task 7: Order form QA

**Objective:** Verify `/order/` remains functional after being mounted into theme chrome.

**Route:** `/order/`

**Manual checks:**

- Shared topbar/footer render once.
- Form app loads with DS/order styling.
- `window.SC_FORM_ASSET_BASE` resolves images/logos from `assets/order/assets/`.
- Vehicle stage images load for paint choices.
- Paint selection changes accent/vehicle image if that feature is present.
- Step navigation works.
- Required customer fields and validation are still visible/usable.
- Summary drawer/mobile summary works.
- Turnstile script loads only where expected.
- Submission flow is not altered in this pass.
- No JS console errors.

**Do not:**

- Submit real customer/order data unless Sean explicitly approves a test submission path.
- Change pricing/RPO/order logic in this theme pass.

**Acceptance criteria:**

- Primary non-submitting customer flow is usable.
- Selector/data contracts and image paths work.
- Any submission/Turnstile limitation is documented honestly.

## Task 8: Calculator QA

**Objective:** Verify the mounted calculator behaves like the source app inside the theme.

**Route:** `/calculator/`

**Manual checks:**

- Shared topbar/footer render once.
- Top-level tabs switch.
- Monthly payment calculator accepts values and shows a plausible result.
- Income calculator accepts values and shows monthly/annual values.
- Quick Pencil accepts values and populates `#qp-results`.
- Required classes/IDs used by JS are present.
- No console errors.
- Mobile layout is usable.

**Static harness fallback:**

If WordPress preview is unavailable, a static harness may verify DOM/JS behavior, but mark WP preview as pending.

**Acceptance criteria:**

- Calculator flows work in WordPress preview, or static-only verification is clearly labeled as incomplete.

## Task 9: Deposit and Build & Price Formidable QA

**Objective:** Verify Formidable-backed pages render and adopt the theme style.

**Routes:**

- `/deposit/`
- `/build-and-price/`

**Plugin styling decision:**

Sean is considering deactivating Formidable’s built-in styling so forms adopt theme style. QA should explicitly record which setting was used:

- Formidable styling enabled
- Formidable styling disabled
- Unknown/not checked

**Manual checks for `/deposit/`:**

- Deposit instructions are readable.
- Payment portal field callouts are clear:
  - `RO/Stock#` = `C8 Deposit`
  - `Department` = `SALES`
  - total = `$500.00`
- `[formidable id=8]` renders inside `.sc-embed`.
- Fields, labels, required markers, validation states, and submit button are readable.
- Confirmation/payment-link behavior is not changed by the theme.

**Manual checks for `/build-and-price/`:**

- Four configurator links open correct Chevrolet URLs.
- Screenshot images load.
- `[formidable id=30]` renders inside `.sc-embed`.
- Build code field and buyer information fields are readable.
- Submit button uses/aligns with DS styling.

**Acceptance criteria:**

- Both forms render in WP preview.
- Theme form skin is readable with chosen Formidable styling setting.
- No form configuration is modified.

## Task 10: Factory wpDataTables QA

**Objective:** Verify the Factory explainer and table embed are usable.

**Route:** `/factory/`

**Manual checks:**

- Shared topbar/footer render once.
- Status-code accordion opens/closes with mouse and keyboard.
- `[wpdatatable id=7 table_view=regular]` renders inside `.sc-embed`.
- Search/filter input is visible and readable.
- Table header/rows/pagination are readable.
- Row hover/focus states are visible.
- Row detail popup/modal is readable if the plugin supports it in preview.
- If table shows `Service Unavailable`, determine whether the same error exists on the current live/source page before treating it as a theme regression.

**Acceptance criteria:**

- Factory page content is ready.
- Table plugin behavior is verified, or an existing plugin/data-source error is documented separately from theme readiness.

## Task 11: Process page content and visual QA

**Objective:** Verify the policy-heavy process page is accurate, readable, and not over-boxed.

**Route:** `/process/`

**Manual checks:**

- Quick action cards route to local slugs.
- Deposit list statuses are present:
  - Stingray OPEN
  - Z06 OPEN
  - Grand Sport OPEN
  - Grand Sport X OPEN
  - ZR1 CLOSED
  - ZR1X CLOSED
  - E-Ray DISCONTINUED
- Fees are present:
  - Dealer Fee `$999`
  - Tag Agency Fee `$362`
- Contact info is present and linked:
  - `smccann@stingraychevrolet.com`
  - `813-359-5000`
- Restrictions preserve meaning.
- Accordion opens/closes with mouse and keyboard.
- Mobile layout remains readable and not excessively nested.
- Sean performs or explicitly defers business-content review of policy/legal-adjacent content before deployment.

**Acceptance criteria:**

- No obvious policy/content omissions.
- Business-content review status is explicitly recorded.

## Task 12: Link and route audit

**Objective:** Confirm the new site does not accidentally route users back to obsolete legacy surfaces where local replacements exist.

**Search targets:**

Look for legacy links in theme PHP/JS/CSS/docs where relevant:

- `order-landing-page/deposit-form`
- `order-landing-page/build-and-price-link-share`
- `orders-in-production`
- `corvette-process-guide`
- `process-links`

**Expected handling:**

- Public templates should use local slugs for replaced surfaces.
- Docs/specs may mention source URLs as provenance; do not rewrite those unless they are user-facing instructions.
- Keep special pages external where no local replacement exists, e.g. `https://stingraychevroletcorvette.com/zr1-process/`.

**Acceptance criteria:**

- No unintended legacy links remain in public templates.
- Intentional external/source/provenance links are documented.

## Task 13: Responsive visual QA and issue log

**Objective:** Catch surface-level visual regressions before deployment.

**Viewports:**

- Mobile: ~390px wide
- Tablet: ~768px wide if time allows
- Desktop: ~1440px wide

**For each public surface, record:**

- Page/route
- Viewport
- Pass/fail
- Issues found
- Screenshot path or brief evidence if available
- Whether fix was applied or deferred

**Visual focus areas:**

- Duplicate chrome or footer.
- Horizontal overflow.
- Overlapping hero/topbar/content.
- Missing focus states.
- Over-boxed sections on long pages.
- Form/table controls unreadable against dark theme.
- Hard-shadow hover and outline selection invariants.

**Acceptance criteria:**

- Visual QA matrix exists in final handoff or a docs report.
- Any must-fix visual issue is fixed before version bump/deployment readiness.

## Task 14: Small scoped fixes, if required

**Objective:** Fix only issues found by QA that are clearly in scope for launch readiness.

**Allowed fixes:**

- Narrow CSS fixes in:
  - `assets/css/theme.css`
  - `assets/css/surfaces.css`
  - `assets/css/embeds.css`
  - surface-specific CSS such as `assets/calculator/calculator.css`
- Narrow PHP copy/link/escaping fixes in the relevant template.
- Asset path fixes in `functions.php` only if a broken enqueue/global is found.

**Avoid:**

- Reworking business logic.
- Rebuilding form/table plugins.
- Refactoring templates wholesale.
- Changing pricing, deposit policy, statuses, or restrictions without Sean’s explicit approval.

**Verification after every fix:**

- `php -l` for touched PHP.
- Browser re-check for the affected route.
- If CSS-only, reload the affected page and confirm visual fix.

**Acceptance criteria:**

- Fixes are minimal and directly tied to QA findings.
- No drive-by refactors.

## Task 15: Version bump for cache busting

**Objective:** Bump the theme version only after QA/fixes indicate the theme is ready for deployment/cache busting.

**File:**

- Modify: `style.css`

**Current version at spec time:**

- `Version: 0.1.0`

**Recommended bump:**

- Use a release-ready value such as `0.2.0` for this integrated surface release, unless Sean wants a different semantic/versioning scheme.

**Acceptance criteria:**

- `style.css` version is bumped after QA fixes, not before.
- README/deployment handoff mentions the version bump.
- Enqueued asset URLs will receive the new version via `STINGRAY_CORVETTE_VERSION`.

## Task 16: Final deployment checklist / no-deploy handoff

**Objective:** Produce the final readiness report without deploying unless explicitly approved.

**Report must include:**

- Commit/branch status.
- Theme version.
- Routes checked:
  - `/`
  - `/order/`
  - `/deposit/`
  - `/build-and-price/`
  - `/calculator/`
  - `/factory/`
  - `/process/`
- Gates run and results.
- Plugin styling setting observed for Formidable.
- wpDataTables status.
- Order form Turnstile/submission status.
- Calculator flow status.
- Homepage 360 status.
- Business-content review status for `/process/`.
- Issues fixed.
- Issues deferred.
- Explicit statement: deployed or not deployed.

**Acceptance criteria:**

- Sean can decide whether to deploy based on the report.
- No deployment action is implied or performed without explicit approval.

## Recommended commit strategy

If the pass is only a spec:

```text
docs: add final live-readiness QA spec
```

If executing the QA/fix pass later:

1. Commit narrow QA fixes separately if any:
   - `fix: polish launch QA issues`
2. Commit version bump and final docs after QA passes:
   - `chore: prepare theme release`

Do not bundle unrelated or unreviewed work.

## Risks / assumptions

- Plugin-backed QA requires a real WordPress environment with Formidable and wpDataTables active. Static checks cannot prove those surfaces fully work.
- The live/source Factory table previously showed a `Service Unavailable` table error during inspection; verify whether that persists before treating it as a theme regression.
- Disabling Formidable built-in styling may improve theme adoption, but it must be checked against actual generated Formidable markup and validation states.
- `/process/` contains policy/legal-adjacent business content; Sean should review it before live deployment.
- This theme is intended to replace the public site experience, so final deployment should include rollback awareness and cache-busting/versioning.
- CarSales/Cloudflare Worker memory/context is intentionally out of scope for the public WordPress deployment path.

## Non-goals

- Do not implement a new surface.
- Do not change form/table plugin configuration.
- Do not modify the CarSales project or its Cloudflare Worker.
- Do not attach `sales.stingraychevroletcorvette.com` to the public site.
- Do not change 27vette workbook/business data.
- Do not deploy without explicit approval.

## Recommended reasoning level

High. The coding is likely small, but the QA surface area is broad: seven routes, plugin-backed embeds, vendored JS, mobile/desktop layout, cache busting, and live-site replacement risk.
