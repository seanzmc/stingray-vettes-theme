# Current Readiness Report

Updated: 2026-07-10 after staging approval of theme `0.1.16` and review of the prepared production page and plugin configuration.

## Release decision

**Current decision: GO for the controlled production upload and activation of theme `0.1.16` at commit `a60a10c46f6d029c562ba1492565966d29306871`.**

All pre-activation gates are passing. Production activation has **not** occurred: production still runs Hello Elementor, and the production smoke, cache, queryless Order, and legacy-redirect gates remain post-activation checks. Any failure in those gates requires rollback to the prior theme and changes the decision to NO-GO pending diagnosis.

## Release identity and ownership

- Theme repository branch: `main`
- Theme release commit: `a60a10c46f6d029c562ba1492565966d29306871`
- Theme version: `0.1.16`
- Staging active theme: `stingray-corvette` version `0.1.16`
- Staging base URL: `https://staging-427b-stingraychevroletcorvette.wpcomstaging.com/`
- Canonical order repository commit: `702ef1cc468315a20a397310b98e2eeb3d49bdde`
- Canonical order runtime: `https://order.stingraychevroletcorvette.com/`

The 27vette deployment is live at the canonical runtime. Its crossed-flags/wordmark link points to `https://stingraychevroletcorvette.com/`, exposes the accessible label `Return to Stingray Corvette home`, and retains the deployed visible-focus rule. The theme continues to keep public links on `/order/`, which redirects to that single canonical runtime. `page-order.php` and `assets/order/` remain dormant rollback material, not a second maintained order runtime.

## Staging release gate

The three changed runtime files on staging match the committed `0.1.16` files byte-for-byte. After the WordPress object cache and edge cache were purged, the exact queryless `/order/` route produced stable results:

| Request | Status | Location | Body |
|---|---:|---|---:|
| Cold after confirmed purge | `302` | `https://order.stingraychevroletcorvette.com/` | 0 bytes |
| Immediate warm repeat | `302` | `https://order.stingraychevroletcorvette.com/` | 0 bytes |

Both responses identified `Stingray Corvette` as the redirect source and sent private, no-store/no-cache headers. The initial stale edge response was purged and was not accepted as passing evidence.

The version-aligned staging route matrix passed:

| Route | Result |
|---|---:|
| `/` | `200` |
| `/deposit/` | `200` |
| `/build-and-price/` | `200` |
| `/calculator/` | `200` |
| `/factory/` | `200` |
| `/process/` | `200` |
| `/order/` | `302` to the canonical order runtime |

Desktop checks at 1440×900 and mobile checks at 390×844 passed for the homepage 360 viewer, navigation/drawer, footer, forms, Factory table/details, calculator, Process page, and Order redirect/return link. No checked surface had page-level overflow or a console error. A required-resource sweep across the six locally rendered surfaces found no failed request or HTTP 4xx/5xx response.

## Staging legacy redirects

Every approved first hop returned `302` to the exact local replacement:

| Legacy path | Replacement |
|---|---|
| `/order-landing-page/` | `/order/` |
| `/order-and-production-update/` | `/order/` |
| `/order-landing-page/deposit-form/` | `/deposit/` |
| `/order-landing-page/build-and-price-link-share/` | `/build-and-price/` |
| `/orders-in-production/` | `/factory/` |
| `/corvette-process-guide/` | `/process/` |
| `/process-links/` | `/process/` |

The local redirect regression also verifies the exact map, query-string handling, missing-trailing-slash normalization, unchanged unmapped paths, and no destination-to-legacy-source loop.

## Prepared production configuration

Production remains on Hello Elementor. The replacement records are published, parentless, use the default template, and have empty editor content so the Stingray theme's dedicated templates can own their rendering after activation.

| Page ID | Title | Path | Status |
|---:|---|---|---|
| `68288` | Order | `/order/` | Published |
| `68294` | Deposit | `/deposit/` | Published |
| `68291` | Build & Price | `/build-and-price/` | Published |
| `68297` | Payment Calculator | `/calculator/` | Published |
| `68300` | Orders @ Factory | `/factory/` | Published |
| `68303` | Process Guidelines | `/process/` | Published |

The production embed configuration was reopened and verified with these exact raw values:

- Page `68294`, Deposit: `[formidable id=8]`
- Page `68291`, Build & Price: `[formidable id=30]`
- Page `68300`, Orders @ Factory: `[wpdatatable id=12 table_view=regular]`

Formidable form 8 (`Deposit Form`, key `deposit-form`) and form 30 (`Chevy Build and Price Link Share`, key `chevbp23`) were verified as the intended production records. Both forms rendered with submit controls during `0.1.16` staging QA without submission. Their production rendering remains an explicit post-activation smoke gate; no production Formidable definition was changed.

Production wpDataTable 12 (`Orders_v2`) was reviewed against the Google Sheet source and is ready for the Factory binding. It has all 14 expected columns, plugin responsive mode disabled, pagination enabled at 10 rows per page, and a credible 23-row preview (`Showing 1 to 10 of 23 entries`). Its source, ID, columns, and pagination were preserved; only the approved responsive toggle was disabled. The theme's PHP and JavaScript regressions explicitly exercise table 12, and active runtime CSS/JavaScript has no dependency on the former staging table ID.

## Performance and delivery evidence

- Representative versioned `0.1.16` CSS and JavaScript assets returned `200` with gzip transfer compression, ETag validators, and `Cache-Control: max-age=315360000` on staging.
- Theme version `0.1.16` remains the cache-busting version attached to enqueued assets.
- Homepage, Order, Factory, Calculator, and embed assets retain their conditional page ownership; surface assets were not made global.
- Homepage frame loading remains lazy: mobile loaded only the selected paint's 30 frames at rest, while desktop loaded the next 30-frame paint set only near the scroll threshold.
- The measured CSS/JavaScript/PHP source total is 266,283 bytes, 5,319 bytes below the approved ceiling.
- No compiler, minifier, package manifest, dependency, lockfile, or build step was introduced.

## Validation summary

Passed against release commit `a60a10c`:

- PHP lint: 17 of 17 root, include, and regression PHP files
- JavaScript syntax: 6 of 6 theme/regression scripts
- Factory Sheet, Factory row-preparation, and legacy redirect regression suites
- Production table-12 PHP/JavaScript contract and no-active-table-7 runtime scan
- Conditional-loading, source-size, no-build-tool, asset-reference, and whitespace gates
- Exact staging upload round-trip checksums
- Queryless cold/warm Order redirect, seven-route matrix, and seven-path legacy redirect matrix
- Desktop/mobile functional browser QA and required-resource sweep
- Canonical 27vette remote commit alignment and live homepage-link deployment

No order, lead, deposit, or customer form was submitted during validation.

## Production activation boundary and rollback

The GO authorizes the separate controlled production task; it does not record production as deployed. Before activation, back up the active production theme and affected configuration, upload the exact candidate as inactive, and verify remote checksums. After activation, purge caches and require all seven public entry points, both queryless cold/warm `/order/` checks, the full legacy redirect matrix, forms 8 and 30, Factory table 12, calculator, homepage, shared chrome, asset versions, and required resources to pass.

If any hard gate fails, reactivate Hello Elementor, purge caches, confirm the prior homepage and critical legacy paths, and record the failed gate before another attempt. The six prepared page records and table 12 remain available for diagnosis unless a new page conflicts with restored routing.

## Remaining risk

- Production rendering under the Stingray theme cannot be verified until the separately approved activation occurs.
- The canonical order runtime requests an optional production favicon URL that returns `404`; Chromium blocks it via ORB. Required order-form resources and customer flows passed, so this is non-blocking follow-up work in 27vette.
- Browser QA covered Chromium at the approved desktop/mobile viewports, not a full browser or physical-device lab.
