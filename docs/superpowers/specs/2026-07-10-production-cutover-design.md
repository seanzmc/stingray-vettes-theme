# Production Theme Cutover Design

**Date:** 2026-07-10

**Status:** Approved design; implementation requires a separate executable plan and explicit production-deployment approval.

## Goal

Replace the current production WordPress theme with the committed Stingray Corvette theme, preserve customer intent through temporary legacy redirects, keep the 27vette order form as one high-performance canonical runtime, and leave a verified rollback path.

## Current State

- Theme repository: `/Users/seandm/Projects/stingray-vettes-theme`
- Candidate branch: `main`
- Candidate commit at design approval: `15e938a`
- Candidate version: `0.1.15`
- Staging serves the candidate on the six local surfaces plus the homepage.
- Production does not yet have the replacement page records at the planned slugs.
- Production Formidable forms 8 and 30 already exist.
- Sean rebuilt the production Factory table as wpDataTable 12.
- The canonical order runtime is owned by `/Users/seandm/Projects/27vette/form-app/` and served at `https://order.stingraychevroletcorvette.com/`.
- The normal WordPress `/order/` path is intended to redirect to that canonical runtime, but stale staging edge output has served the dormant local form on a queryless request.
- The active readiness report still describes older candidate and staging versions and must be refreshed after the current gates are rerun.

## Architecture Decision

Keep the order form as a standalone Cloudflare-served runtime sourced only from 27vette.

WordPress owns the discoverable local entry point `/order/`; the theme redirects that path to the canonical order subdomain. The order runtime visually reconnects to the main site by making the crossflags and Stingray wordmark a link to `https://stingraychevroletcorvette.com/`.

This design is preferred over reverse-proxying the app through WordPress because it preserves the current fast static delivery path and avoids extra cache, asset-path, and routing failure modes. It is preferred over vendoring an active theme copy because a second maintained runtime would drift from 27vette.

## Source-of-Truth Boundaries

- Theme templates, theme redirects, shared chrome, and theme documentation live in `stingray-vettes-theme`.
- Order-form markup, styling, behavior, data, Turnstile integration, and submission wiring live in `27vette/form-app/`.
- Production page records and `stingray_embed_shortcode` values live in the production WordPress database.
- Formidable form definitions remain plugin-owned production data.
- wpDataTable 12 remains plugin-owned production data and is not recreated by the theme.
- Historical specs and completed plans remain historical; only active readiness and operating documentation is refreshed.

## Production Page Records

Create and publish these exact WordPress pages:

| Title | Slug | Runtime owner |
|---|---|---|
| Order | `order` | `page-order.php`, intercepted by the canonical redirect |
| Deposit | `deposit` | `page-deposit.php` |
| Build & Price | `build-and-price` | `page-build-and-price.php` |
| Payment Calculator | `calculator` | `page-calculator.php` |
| Orders @ Factory | `factory` | `page-factory.php` |
| Process Guidelines | `process` | `page-process.php` |

The plugin-backed pages receive the following exact custom field values:

| Page | Custom field | Value |
|---|---|---|
| Deposit | `stingray_embed_shortcode` | `[formidable id=8]` |
| Build & Price | `stingray_embed_shortcode` | `[formidable id=30]` |
| Factory | `stingray_embed_shortcode` | `[wpdatatable id=12 table_view=regular]` |

The pages may be prepared before theme activation, but they must not be treated as ready until their dedicated templates render under the replacement theme.

## Legacy Redirect Policy

The replacement theme owns an exact, path-based temporary redirect map:

| Legacy path | Destination |
|---|---|
| `/order-landing-page/` | `/order/` |
| `/order-and-production-update/` | `/order/` |
| `/order-landing-page/deposit-form/` | `/deposit/` |
| `/order-landing-page/build-and-price-link-share/` | `/build-and-price/` |
| `/orders-in-production/` | `/factory/` |
| `/corvette-process-guide/` | `/process/` |
| `/process-links/` | `/process/` |

Initial status is HTTP 302 so the cutover remains reversible while production behavior is monitored. The redirect implementation must:

- match only the listed normalized paths;
- avoid redirect loops;
- use local `home_url()` destinations;
- run before legacy templates emit content;
- leave unrelated public pages unchanged; and
- disappear automatically if the prior theme is reactivated.

After a stable monitoring period and separate approval, the mappings may become HTTP 301 and the corresponding legacy page records may be retired.

## Formidable Form 8 Policy

Formidable form 8 is verification-only during cutover. Sean has visually reviewed the production form and considers it clean. The implementation must not rewrite form 8 merely because cached or responsive HTML contains an older thumbnail reference.

The new `/deposit/` surface must be checked for:

- successful form rendering;
- current customer-visible imagery;
- readable inherited theme colors;
- conditional field behavior;
- mobile containment;
- validation presentation without a real submission; and
- absence of a visible broken-image state.

Production Formidable data changes require a concrete rendered defect and separate approval. Form 30 receives the same non-submitting render and layout verification.

## Factory Table 12 Acceptance

The implementation consumes the production table Sean already rebuilt; it does not create, import, replace, or renumber the table.

Table 12 must be verified through the new `/factory/` page for:

- intended Google worksheet and worksheet identifier;
- credible current rows;
- fourteen-column detail availability;
- public Order # and Current filters;
- ten-row pagination;
- plugin responsive mutation disabled so the theme owns compact presentation;
- keyboard- and mouse-operable row details;
- no malformed placeholder row;
- no page-level horizontal overflow at mobile width; and
- no console or required-resource errors.

If table 12 fails one of these gates, stop the production cutover and correct the table configuration without creating a second replacement table.

## Canonical Order Runtime Change

Implement the homepage return link from a clean 27vette worktree based on `origin/main`, not from the current unrelated local branch.

In `form-app/index.html`, wrap the crossflags and Stingray wordmark in one link to `https://stingraychevroletcorvette.com/` with an accessible name equivalent to `Return to Stingray Corvette home`. The link must preserve the existing brand-block dimensions and must not wrap the order-form title or toolbar actions.

In `form-app/styles.css`, preserve the current layout while adding a visible `:focus-visible` state for the brand link. No order logic, pricing data, selector contract, Turnstile configuration, or submission endpoint changes are part of this pass.

Validation covers desktop and mobile layout, keyboard focus, destination URL, existing form navigation, build-summary controls, required assets, Turnstile loading, and unchanged production submission wiring without creating a real dealer record.

The order change deploys only through the existing canonical 27vette-to-Cloudflare path. The dormant theme copy remains rollback material and is not synchronized or reactivated.

## `/order/` Cache Clearance

The staging and production `/order/` paths must be validated without cache-busting query parameters.

For staging before production cutover:

1. Purge the stale `/order/` edge response.
2. Request the ordinary queryless URL in a clean browser session.
3. Confirm HTTP 302 to `https://order.stingraychevroletcorvette.com/`.
4. Repeat the request after warming the route.
5. Confirm the dormant local order CSS, JavaScript, Turnstile initialization, and page body are not emitted by WordPress.
6. Follow the redirect at desktop and mobile widths and confirm the canonical runtime loads without console or required-resource errors.

Repeat the same queryless cold/warm checks after production activation and cache purge. A single cache-busted success is not sufficient evidence.

## Cutover Sequence

1. Record the clean theme candidate commit and version.
2. Back up the active production theme and the affected WordPress page/plugin configuration.
3. Verify production wpDataTable 12 before binding it to a public page.
4. Create and configure the six production page records.
5. Implement, review, and deploy the canonical order-runtime homepage link from 27vette `origin/main`.
6. Implement and test the theme-owned temporary legacy redirect map.
7. Advance the theme version for cache busting and rerun the complete local gate.
8. Deploy the exact candidate to staging and clear the `/order/` cache split.
9. Run version-aligned staging QA across all seven public surfaces and the redirect matrix.
10. Update the active readiness report with the exact candidate and issue a production GO or NO-GO.
11. Obtain explicit approval for production upload and activation.
12. Upload the exact candidate as an inactive production theme and verify remote checksums.
13. Publish or confirm the six prepared page records and custom fields.
14. Activate the replacement theme during a controlled cutover window.
15. Purge WordPress.com caches.
16. Run the production smoke tests, queryless redirect checks, and legacy redirect matrix.
17. Update active documentation with the actual production result.

## Validation Gates

### Theme repository

- Clean working tree and known relation to `origin/main`.
- PHP lint for every root, include, and PHP regression file.
- JavaScript syntax validation for every theme script and JavaScript regression file.
- Factory Sheet and Factory row-preparation regression suites pass.
- Redirect contract proves every exact legacy mapping, a non-mapped path, and no loop through the new destination.
- Every referenced local asset exists.
- `git diff --check` passes.

### Staging

- Homepage, Deposit, Build & Price, Calculator, Factory, and Process return HTTP 200.
- `/order/` consistently returns HTTP 302 to the canonical runtime on cold and warm queryless requests.
- All rendered theme assets use the release version.
- Homepage, footer, forms, calculator, Factory table, navigation, and order return link pass desktop/mobile browser checks.
- No required local asset returns a non-200 response.

### Production

- All seven public entry points behave as designed.
- Every legacy path reaches its mapped replacement without a loop.
- Formidable forms 8 and 30 render without submission.
- Factory table 12 renders and passes its acceptance checks.
- The canonical order form links home and retains its existing build behavior.
- Production caches serve the current release version.
- No uncontrolled dealer record is created.

## Rollback

If any production hard gate fails:

1. Reactivate the previously active theme.
2. Purge WordPress.com caches.
3. Confirm the prior homepage and critical legacy paths render.
4. Leave the canonical order-form homepage link in place because it is backward-compatible and independent of theme activation.
5. Preserve the six new page records and table 12 for diagnosis, but unpublish a new page if it conflicts with restored legacy routing.
6. Record the failed gate and evidence in the readiness report before another attempt.

Because legacy redirects live in the replacement theme, reactivating the prior theme removes those mappings without a separate redirect rollback.

## Documentation Closure

Update:

- `docs/Current-readiness-report.md`
- `README.md`
- `style.css` version when the final theme change requires cache busting

Do not rewrite completed historical plans or specs. The final readiness report records the exact theme commit/version, production page IDs, embed values, table 12, redirect results, cache state, canonical order-runtime commit, validation evidence, deployment status, and rollback state.

## Constraints and Non-Goals

- Do not modify production before the implementation plan is approved and production mutation is explicitly authorized.
- Do not add dependencies or a build system.
- Do not change pricing, RPO rules, calculator logic, Formidable workflows, Turnstile configuration, or the submission endpoint.
- Do not submit a real order or form during routine QA.
- Do not attach the unrelated CarSales Worker or `sales.stingraychevroletcorvette.com` to this site.
- Do not make the dormant theme order form a second active runtime.
- Do not delete legacy production pages during the initial reversible cutover.
- Do not change unrelated public pages.

## Definition of Done

The job is complete when the exact approved theme is active in production; the six page records bind to their dedicated templates; forms 8 and 30 and table 12 render correctly; all legacy mappings reach their intended replacements; `/order/` consistently reaches the one canonical Cloudflare runtime; the order-form logo links home; cache and asset versions are current; every production smoke gate passes; the readiness report records GO and deployed status; and the verified rollback path remains available.
