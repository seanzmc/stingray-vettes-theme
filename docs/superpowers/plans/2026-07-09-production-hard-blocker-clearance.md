# Production Hard-Blocker Clearance Spec

> **For Hermes:** Use the `subagent-driven-development` skill only after Sean approves this spec. Keep staging deployment, deterministic browser checks, and the final go/no-go decision in the controller session.

**Goal:** Produce one version-aligned staging candidate that resolves the current wpDataTables theme conflict, proves the plugin-backed and interactive customer flows, removes known broken staging content, and leaves a documented production go/no-go decision.

**Architecture:** Keep all theme-owned embed styling in the existing `assets/css/embeds.css` layer. Do not add a second plugin override stylesheet. Enqueue that layer after the plugin styles it must override, extend it only with selectors confirmed against the current staging DOM, and keep Formidable or wpDataTables configuration/data changes separate from theme code.

**Tech Stack:** Classic WordPress theme, PHP 7.4-compatible WordPress APIs, vanilla CSS/JS, Formidable Forms, wpDataTables 7.5.1, WordPress.com Business staging.

---

## Approval gate

This document is a spec only. Do not edit theme code, change Formidable forms, change wpDataTables configuration/data, deploy to staging, submit a test lead, or touch production until Sean approves the relevant task.

Theme implementation and staging deployment may be approved together. Formidable form-definition changes, wpDataTables configuration/data-source changes, and an actual order-form submission each require explicit scope approval because they affect plugin-owned business surfaces or external records.

## Current verified state — 2026-07-09

### Repository

- Root: `/Users/seandm/Projects/stingray-vettes-theme`
- Branch: `main`
- Working tree before this spec: clean
- `origin/main...HEAD`: `0 0`
- Repo theme version: `style.css` `Version: 0.1.2`
- PHP lint: all root, dedicated page, and `inc/` templates pass
- JS syntax: all 6 theme/vendored scripts pass `node --check`
- Local enqueue references: all files exist

### Staging

Base URL:

- `https://staging-427b-stingraychevroletcorvette.wpcomstaging.com/`

All 7 public routes return HTTP 200 and show the Stingray theme. Staging currently emits theme assets with `?ver=0.1.1`, so staging is not running the same artifact as repo HEAD.

Observed plugin versions/markup:

- Formidable forms render on `/deposit/` and `/build-and-price/`.
- No literal Formidable shortcodes appear.
- No external Formidable stylesheet was found on those pages; the theme appears to be the active styling source for stock Formidable markup.
- wpDataTables 7.5.1 renders table ID 7 on `/factory/` with the Mojito skin.
- wpDataTables plugin styles load after `stingray-embeds-css`.

### Embed-style conclusion

No new stylesheet is required.

`assets/css/embeds.css` already successfully styles the core Formidable DOM:

- `.frm_forms`
- `.frm_form_field`
- `.frm_primary_label`
- text/email/tel/date controls
- radio option labels and images
- section headings
- submit button
- validation/success classes

Computed staging styles confirm dark backgrounds, warm-white text, ChevySans, token borders, and the Torch Red submit action. The Build & Price form is already theme-integrated at the control level.

The wpDataTables section is incomplete in practice. The current rules target several correct classes, but the plugin styles load later and the stylesheet does not cover the current filter wrapper/clear-button classes. Current staging output has:

- white `.wpDataTablesWrapper` background
- teal Mojito table headers
- near-black table cell text on dark cells
- a stock white clear-filter icon button
- theme-styled filter inputs and pagination mixed with plugin defaults

This is a production hard blocker because table content is not reliably readable.

### Plugin-owned content findings

Formidable `/deposit/` contains legacy inline color styles that bypass normal theme declarations:

- `#frm_field_2221_container`: blue underlined “Instructions:” span and red `SALES` span
- `#frm_field_696_container`: red closed-list copy
- `#frm_field_699_container`: red Z07 wait-time copy
- `#frm_field_1729_container`: orange consent warning

The deposit form also references a broken production image:

- `https://stingraychevroletcorvette.com/wp-content/uploads/pictures/zr1-closed-150x150.png` → HTTP 404

The other checked Formidable images returned HTTP 200. The 128 production-hosted option images referenced by `assets/order/data.js` also returned HTTP 200 and are an intentional production media dependency, not a blocker.

The current factory table exposes one apparent placeholder/malformed row (`Group"` / `Model`) rather than a credible factory-order snapshot. Treat that as a plugin/data-source acceptance blocker separate from theme CSS.

## Hard constraints and non-goals

- Do not deploy to or modify production during this pass.
- Do not attach the CarSales Worker or `sales.stingraychevroletcorvette.com` to the WordPress site.
- Do not change pricing, RPO rules, order-form generated data, calculator logic, Turnstile configuration, Formidable workflows, wpDataTables configuration, or linked data sources without explicit approval.
- Preserve local public slugs: `/order/`, `/deposit/`, `/build-and-price/`, `/calculator/`, `/factory/`, `/process/`.
- Preserve `.sc-embed` as the stable theme boundary.
- Keep Formidable and wpDataTables rules in `assets/css/embeds.css`; do not create plugin-specific stylesheet sprawl.
- Use scoped `!important` only where the plugin stylesheet demonstrably wins.
- Do not hide failed assets or malformed table data with CSS.
- Do not submit real customer data as QA data.
- Do not refactor unrelated theme chrome, vendored order logic, or calculator logic.

## Risk summary

1. Moving embed CSS later can unintentionally override plugin layout, so selectors must stay under `.sc-embed` except for the wpDataTables modal, which is mounted outside the wrapper.
2. Depending unconditionally on an unregistered plugin style handle can prevent WordPress from printing the theme stylesheet. Any plugin-handle dependency must be conditional.
3. Field-ID CSS for Formidable content is brittle. Fix source form HTML/inline formatting when approved; use field-ID overrides only as an explicit fallback.
4. A staging submission currently posts to the production REST endpoint. It can create a real record and must not be exercised without an approved test identity and cleanup plan.
5. wpDataTables data problems may belong to its linked source rather than this theme. CSS completion must not be reported as table-data completion.

---

## Task 1: Establish the deploy-candidate baseline

**Objective:** Record the exact pre-change staging artifact and avoid testing 0.1.1 as though it were repo 0.1.2.

**Files:**

- Inspect: `style.css:1-14`
- Inspect: `functions.php:40-111`
- Inspect: current staging HTML and stylesheet URLs
- No code changes in this task

**Steps:**

1. Re-run:

   ```bash
   git status --short
   git branch --show-current
   git rev-list --left-right --count origin/main...HEAD
   git log --oneline -5
   ```

2. Fetch all 7 staging routes and record HTTP status, final URL, expected theme marker, literal-shortcode status, and emitted `stingray-*.css/js?ver=` values.
3. Record `0.1.1` as the observed staging baseline and `0.1.2` as the repo baseline.
4. Do not deploy 0.1.2 separately if Tasks 2–3 will create a newer candidate; deploy once after the scoped fixes and version bump.

**Acceptance criteria:**

- Working tree and branch are known.
- Staging/repo version mismatch is explicit.
- No staging or production mutation occurred.

## Task 2: Make embed CSS load after wpDataTables

**Objective:** Ensure the existing theme-authored embed layer wins the cascade without broad selector escalation.

**Files:**

- Modify: `functions.php:106-111`
- Verify: `assets/css/embeds.css`

**Required approach:**

1. Remove the current `stingray-embeds` enqueue from `stingray_corvette_enqueue_styles()`.
2. Add a dedicated `stingray_corvette_enqueue_embed_styles()` callback hooked to `wp_enqueue_scripts` at a late priority such as `100`.
3. Keep the route guard exactly scoped to `deposit`, `build-and-price`, and `factory`.
4. Keep `stingray-surfaces` as the required dependency.
5. On `/factory/`, conditionally add the currently registered/queued final wpDataTables skin handle as a dependency. Staging currently exposes:
   - `wdt-wpdatatables`
   - `wdt-skin-mojito`
6. Never add an unregistered handle as an unconditional dependency. Prefer the final skin handle when registered; fall back to the core wpDataTables handle when that is the only registered handle.
7. Do not alter plugin files or dequeue plugin styles.

**Static verification:**

```bash
php -l functions.php
```

Expected: `No syntax errors detected in functions.php`.

**Staging verification after deployment:**

- `stingray-embeds-css` appears after `wdt-skin-mojito-css` on `/factory/`.
- `stingray-embeds-css` remains present on `/deposit/` and `/build-and-price/`.
- It remains absent from `/`, `/order/`, `/calculator/`, and `/process/`.

**Acceptance criteria:**

- The theme override layer is last among relevant embed styles.
- No route gains unnecessary plugin CSS.
- Formidable controls remain styled after the enqueue change.

## Task 3: Complete current wpDataTables selector coverage

**Objective:** Make wpDataTables 7.5.1 readable and visually coherent with the dark design system.

**Files:**

- Modify: `assets/css/embeds.css:292-434`
- Do not modify: wpDataTables plugin CSS or table configuration

**Confirmed current DOM contracts to support:**

- `.sc-embed .wpDataTablesWrapper.wdt-skin-mojito`
- `.sc-embed .wpDataTableFilterBox`
- `.sc-embed .wpDataTableFilterSection`
- `.sc-embed .wdt-filter-control`
- `.sc-embed .wdt-clear-filters-button`
- `.sc-embed table.wpDataTable`
- `.sc-embed table.wpDataTable thead th.wdtheader`
- `.sc-embed table.wpDataTable tbody td`
- `.sc-embed .dataTables_paginate .paginate_button`
- `.sc-embed .dataTables_info`
- `.wdt-frontend-modal .modal-content`

**Required declarations:**

1. Wrapper/filter region:
   - carbon background, not white
   - token border/radius
   - responsive padding
   - inherited theme font and ink colors
2. Filter labels:
   - target `.wpDataTableFilterSection > label` in addition to legacy DataTables labels
   - preserve readable muted text and normal wrapping
3. Filter controls:
   - retain current dark control styling
   - add visible `:focus-visible`
   - prevent overflow at mobile widths
4. Clear filters:
   - target `.wdt-clear-filters-button`
   - dark/ghost button treatment
   - minimum 42px hit target
   - visible hover and focus states
   - preserve its existing accessible name, “Clear filters”
5. Table:
   - tokenized dark table surface
   - warm-white headers, not Mojito teal
   - muted readable body cells
   - tabular numerals for status/date/order comparisons where appropriate
   - visible hover/focus without scale or blur
   - responsive horizontal containment rather than clipping columns
6. Pagination/info:
   - retain the existing token treatment
   - ensure current/disabled/hover/focus states remain readable after load-order correction
7. Row details/modal:
   - preserve existing dark modal selectors
   - add selectors only if the actual opened row-detail DOM differs
   - do not guess modal classes without observing them
8. Use scoped `!important` only for properties the later/plugin rules still win after Task 2.

**Do not solve with:**

- a new stylesheet
- plugin file edits
- global `table`, `th`, `td`, `.button`, or `.modal` overrides
- hiding table controls
- changing the Mojito skin setting merely to make the theme CSS easier

**Acceptance criteria on staging:**

- No white wrapper remains.
- No teal plugin header remains.
- Every visible cell passes a basic contrast check against its background.
- Filter inputs and clear button belong visually to the theme.
- Keyboard focus is visible on filters, sortable headers, clear button, pagination, and any row-detail action.
- No horizontal page overflow at approximately 390px, 768px, or 1440px.

## Task 4: Harden Formidable styling without duplicating CSS layers

**Objective:** Preserve the successful Formidable theme integration and close only observed accessibility/cascade gaps.

**Files:**

- Modify only if needed after Task 2: `assets/css/embeds.css:13-290`
- Inspect on staging: `/deposit/`, `/build-and-price/`
- Plugin form-definition changes require separate approval

**Required theme checks:**

1. Verify computed dark styles for text, email, tel, date, textarea, radio labels, required marks, descriptions, submit button, errors, and success messages.
2. Add explicit focus treatment for:
   - radio/checkbox controls or their compound labels via `:focus-within`
   - submit buttons via `:focus-visible`
   - file/upload controls if present
3. Ensure `.frm_html_container img` remains responsive with `max-width: 100%` and `height: auto`; do not force small option-logo dimensions onto instructional screenshots.
4. Preserve Formidable conditional logic, validation, form actions, and submit behavior.

**Content-owned exception:**

Theme CSS should not broadly normalize every inline-colored span. Preferred resolution, if Sean approves Formidable form edits:

- remove legacy inline blue/red/orange formatting from the affected HTML fields
- use semantic Formidable classes or plain content so `embeds.css` controls presentation
- replace or remove the broken `zr1-closed-150x150.png` reference

If form edits are not approved, document the remaining inline colors. A field-ID-specific `!important` override may be used only after explicit approval, because those selectors couple the theme to form ID 8 internals.

**Acceptance criteria:**

- Core Formidable controls remain visually integrated; no second stylesheet is introduced.
- All keyboard focus states are visible.
- No broken image icon appears in the deposit form.
- No new Formidable field IDs, workflows, notifications, or confirmations are changed unintentionally.

## Task 5: Resolve plugin-owned staging content blockers

**Objective:** Ensure plugin surfaces contain credible production-candidate content before visual sign-off.

**Scope:** WordPress staging admin/plugin configuration; no theme file is the source of truth for these fixes.

### Formidable deposit form

1. Replace or remove the 404 image URL:
   - `https://stingraychevroletcorvette.com/wp-content/uploads/pictures/zr1-closed-150x150.png`
2. If approved, remove legacy inline color formatting from the affected HTML fields.
3. Preview the entire conditional form path without submission.
4. Confirm labels, required fields, terms, confirmation copy, email/payment-link behavior, and closed-list messaging are current.

### wpDataTables factory table

1. Confirm table ID 7 is the intended table.
2. Confirm the linked source/configuration is the intended staging equivalent of the production factory snapshot.
3. Resolve or explicitly approve the current apparent placeholder/malformed row (`Group"` / `Model`).
4. Confirm credible rows render before testing row details, search, sorting, pagination, and mobile behavior.
5. Do not change the linked sheet/data source without explicit approval.

**Acceptance criteria:**

- Deposit form has no broken media.
- Factory table contains approved/credible test or staging data rather than malformed placeholder output.
- Plugin/data issues are not misreported as theme fixes.

## Task 6: Run the order-form functional gate safely

**Objective:** Prove the customer order path without creating an uncontrolled production record.

**Files:**

- Inspect only: `page-order.php`
- Inspect only: `assets/order/app.js`
- Inspect only: `assets/order/data.js`
- Do not change vendored business logic in this pass

**Steps:**

1. On staging, complete representative required selections through the customer modal without submitting.
2. Verify:
   - desktop step rail
   - mobile step drawer
   - summary drawer
   - required-selection gating
   - reset confirmation
   - build download
   - Turnstile widget rendering
   - no console/resource errors
3. Record that the client currently posts to:
   - `https://stingraychevroletcorvette.com/wp-json/corvette-build/v1/submit`
4. Before a real POST, obtain explicit approval for:
   - test name/email/phone values
   - expected recipient/notification behavior
   - how the test entry will be identified and removed
5. Submit exactly one approved test record.
6. Verify one success response, confirmation ID if returned, notification/entry creation, duplicate-submit prevention, and cleanup.

**Acceptance criteria:**

- Non-submitting flow passes.
- Turnstile works on the staging hostname.
- One approved end-to-end test passes and is cleaned up, or Sean explicitly accepts submission QA as deferred.
- No real customer is contacted.

## Task 7: Complete business-content approval

**Objective:** Close the non-technical blocker on policy/legal-adjacent copy.

**Files:**

- Review: `page-process.php`
- Review: `page-deposit.php`
- Review rendered Formidable deposit content

**Approval checklist:**

- open/closed model lists
- deposit amounts and refund timing
- dealer and tag agency fees
- credit-card surcharge copy
- MSRP/pricing language
- restricted states/customers
- contact email and phone
- ZR1/ZR1X and Grand Sport/Grand Sport X status
- external ZR1 process link

**Acceptance criteria:**

- Sean approves the copy or supplies exact corrections.
- Any correction is scoped to its actual source: theme template versus Formidable form content.

## Task 8: Version, package, and deploy one staging candidate

**Objective:** Put the exact reviewed artifact on staging with deterministic cache busting.

**Files:**

- Modify: `style.css:7`
- Update after QA: `docs/Current-readiness-report.md`

**Steps:**

1. After Tasks 2–4 pass static checks, bump `Version:` from `0.1.2` to `0.1.3` unless a different release version is approved.
2. Re-run PHP lint and JS syntax checks.
3. Package from tracked Git content so `.env.local`, `.DS_Store`, and other untracked files are excluded.
4. Deploy to the WordPress.com staging theme directory only after explicit approval.
5. Verify staging emits `?ver=0.1.3` for local CSS/JS.
6. Purge/refresh staging caches if old query versions remain.
7. Confirm staging `style.css` and rendered asset query versions agree.

**Acceptance criteria:**

- Staging and repo candidate versions match.
- Deployed files are traceable to the reviewed diff.
- No production deployment occurred.

## Task 9: Full staging browser matrix and go/no-go report

**Objective:** Decide production candidacy from the actual version-aligned staging artifact.

**Routes:**

- `/`
- `/order/`
- `/deposit/`
- `/build-and-price/`
- `/calculator/`
- `/factory/`
- `/process/`

**Viewports:**

- mobile: approximately 390px
- tablet: approximately 768px
- desktop: approximately 1440px

**Checks:**

- expected template marker and no duplicate Elementor/theme chrome
- no literal shortcodes
- no console exceptions or failed required resources
- no horizontal page overflow
- visible focus states and usable keyboard order
- homepage spin loads, spins, and changes paint without overlap
- order flow from Task 6
- Formidable conditional fields, validation, submit controls, and confirmation readiness
- wpDataTables filters, clear button, sorting, pagination, row hover/focus, and row details/modal
- calculator tabs, representative monthly payment, income, and quick-pencil results
- process copy matches the approved version
- production-hosted order images continue to return successfully

**Final report must state:**

- exact candidate version
- deployed to staging: yes/no
- deployed to production: no
- files changed and diff stat
- static gate results
- routes/viewports checked
- Formidable styling and content status
- wpDataTables styling and data status
- order submission status
- remaining risks or accepted deferrals
- explicit go/no-go recommendation

**Production candidate acceptance:**

The theme is a production deployment candidate only when:

1. staging serves the same candidate version as the repo;
2. wpDataTables is readable and functionally verified with credible data;
3. Formidable forms have no broken required media and pass conditional/validation checks;
4. the order path passes the approved functional gate or has an explicit accepted deferral;
5. policy/business copy is approved;
6. all 7 routes pass the responsive browser matrix without critical console/resource errors.

## Suggested implementation commits

Keep theme and plugin-admin work separate:

1. `fix: load embed styles after plugin css`
2. `fix: complete wpdatatables dark theme`
3. `fix: harden embed focus states`
4. `chore: bump theme version to 0.1.3`
5. `docs: update production readiness report`

Do not commit WordPress database/plugin-admin changes into the theme repo. Record those actions in the readiness report instead.
