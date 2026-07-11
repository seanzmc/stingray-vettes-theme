# Current Readiness Report

Updated: 2026-07-11 after the controlled production activation attempt, immediate rollback, read-only redirect-precedence diagnosis, and authorization of a target-only Cloudflare redirect correction and second activation attempt.

## Release decision

**Current decision: NO-GO — production remains on Hello Elementor `3.4.9` after the `0.1.16` candidate failed the `/process-links/` legacy-redirect hard gate and was rolled back.**

The redirect-precedence diagnosis is complete. On `2026-07-11`, Sean explicitly authorized a second controlled activation attempt and a target-only update to the existing Cloudflare rule. That authorization does not change the current release decision: production stays on Hello Elementor and the release remains NO-GO until every controlled-retry gate below passes. This documentation update performs no production, Cloudflare, or theme mutation.

## Release identity and ownership

- Theme repository branch: `main`
- Theme release commit: `a60a10c46f6d029c562ba1492565966d29306871`
- Theme version: `0.1.16`
- Staging active theme: `stingray-corvette` version `0.1.16`
- Staging base URL: `https://staging-427b-stingraychevroletcorvette.wpcomstaging.com/`
- Canonical order repository commit: `702ef1cc468315a20a397310b98e2eeb3d49bdde`
- Canonical order runtime: `https://order.stingraychevroletcorvette.com/`

The 27vette deployment is live at the canonical runtime. Its crossed-flags/wordmark link points to `https://stingraychevroletcorvette.com/`, exposes the accessible label `Return to Stingray Corvette home`, and retains the deployed visible-focus rule. When active, the Stingray theme keeps public links on `/order/`, which redirects to that single canonical runtime; the theme is currently inactive in production. `page-order.php` and `assets/order/` remain dormant rollback material, not a second maintained order runtime.

## Production activation attempt and rollback

The controlled production attempt ran on `2026-07-11` within the recorded `01:13:20–01:22:28 EDT` window: production key attachment was recorded at `01:13:20`, candidate commit `a60a10c46f6d029c562ba1492565966d29306871` / theme version `0.1.16` was uploaded and activated after backup/checksum gates, and rollback verification completed at `01:22:28`.

- Sean's existing account key `seanzmc9613-default` was attached to the **Production** environment only. Staging remained unattached, and the stale SFTP password was not reset.
- The active Hello Elementor theme was downloaded outside the repository before activation. Its 116-file remote/local SHA-256 manifests matched exactly.
- The complete 202-file candidate was uploaded inactive to `/srv/htdocs/wp-content/themes/stingray-corvette/`. The approved, remote, and fresh round-trip SHA-256 manifests matched exactly at `b2d900f18b768ed1b760407b337267f34e2a2ad7d3724e9eef96c09f3f3c8e6b`.
- All six published production page records, the three exact embed values, and wpDataTable 12 were reconfirmed before activation.
- Production WP-CLI activated `stingray-corvette` `0.1.16`. `wp cache flush` succeeded, and WordPress.com Hosting Configuration `Clear all` reported both object and global edge caches cleared.
- The candidate returned the required status for the homepage and six primary routes checked, and six legacy routes returned their required direct `302` first hop.
- `/process-links/` failed the hard gate: it returned `301` to `/corvette-process-guide/` instead of the required direct `302` to `/process/`.
- Gate 1's asset-version subcheck was not completed, and Gate 2 received only the first required queryless `/order/` `302`; the required immediate second queryless redirect check was not completed. Neither gate is claimed as fully passed.
- Task 9 rollback started immediately. Production WP-CLI reactivated Hello Elementor `3.4.9`; object-cache flush and WordPress.com object/global-edge `Clear all` were repeated successfully.
- Post-rollback evidence showed Hello Elementor `3.4.9` active, `stingray-corvette` `0.1.16` retained inactive, homepage `200` with the `wp-theme-hello-elementor` marker, Hello Elementor CSS `200`, and the prior `/order-landing-page/`, `/order/`, `/deposit/`, and `/process-links/` behavior restored.
- No order, deposit, lead, or customer form was submitted. No page record, embed, plugin, table, front-page, or posts-page setting was changed during activation or rollback.

The retained inactive candidate and verified Hello Elementor backup remain diagnostic and rollback evidence; the new activation authorization does not waive re-verification or any controlled-retry gate.

## `/process-links/` root cause

The failed `301` was caused by the enabled Cloudflare Dynamic Redirect named `process-links to process guide` (`ruleset_id=1423769adf114f2287d9a0280bb26599`, `rule_id=c0e8d48ecfda4ca4ac070dfc7012d0ae`), not by WordPress or the candidate theme. Its exact queryless full-URI match is `(http.request.full_uri wildcard r"https://stingraychevroletcorvette.com/process-links/")`; its `301` target is `https://stingraychevroletcorvette.com/corvette-process-guide/`. The Cloudflare redirect phase executes before WordPress, so the queryless request never reached the Stingray theme's priority-0 redirect callback. A unique query changes the full URI, does not match the queryless expression, and therefore reaches the published WordPress page instead.

The candidate theme code was not the root cause and requires no correction. Its `/process-links/` mapping emits the approved direct `302` to `/process/` when WordPress receives the request. The other six legacy redirects were also not the root cause and passed because no equivalent Cloudflare rule intercepted them.

The approved correction is to keep the Cloudflare rule `process-links to process guide` enabled and change only its target from `https://stingraychevroletcorvette.com/corvette-process-guide/` to `https://stingraychevroletcorvette.com/process/`. Its identity, position, exact full-URI source match, `301` status, `preserve_query_string=true` behavior, and all other configuration remain unchanged. Cloudflare therefore remains the production owner of the exact queryless `/process-links/` request. The candidate theme's `/process-links/` `302` remains a fallback if the request reaches WordPress; it is not the accepted first hop while the edge rule is enabled. No Cloudflare or production mutation was performed during the diagnosis or this report update.

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

## Retained production configuration

Production is restored to Hello Elementor `3.4.9`. The replacement records remain published, parentless, use the default template, and have empty editor content. They are retained for diagnosis; their presence does not waive any gate for the authorized second activation attempt.

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

Formidable form 8 (`Deposit Form`, key `deposit-form`) and form 30 (`Chevy Build and Price Link Share`, key `chevbp23`) were verified as the intended production records. Both forms rendered with submit controls during `0.1.16` staging QA without submission. Production rendering was not reached before rollback; no production Formidable definition was changed.

Production wpDataTable 12 (`Orders_v2`) retains the reviewed Factory binding configuration against the Google Sheet source. It has all 14 expected columns, plugin responsive mode disabled, pagination enabled at 10 rows per page, and a credible 23-row preview (`Showing 1 to 10 of 23 entries`). Its source, ID, columns, and pagination were preserved; only the approved responsive toggle was disabled. The theme's PHP and JavaScript regressions explicitly exercise table 12, and active runtime CSS/JavaScript has no dependency on the former staging table ID.

## Performance and delivery evidence

- Representative versioned `0.1.16` CSS and JavaScript assets returned `200` with gzip transfer compression, ETag validators, and `Cache-Control: max-age=315360000` on staging.
- Theme version `0.1.16` remains the cache-busting version attached to enqueued assets.
- Homepage, Order, Factory, Calculator, and embed assets retain their conditional page ownership; surface assets were not made global.
- Homepage frame loading remains lazy: mobile loaded only the selected paint's 30 frames at rest, while desktop loaded the next 30-frame paint set only near the scroll threshold.
- The measured CSS/JavaScript/PHP source total is 266,283 bytes, 5,319 bytes below the approved ceiling.
- No compiler, minifier, package manifest, dependency, lockfile, or build step was introduced.

## Pre-attempt validation summary

The following passed against release commit `a60a10c` before the production attempt. This evidence does not override the current NO-GO decision:

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

## Controlled retry boundary

The second activation attempt and target-only Cloudflare correction were explicitly authorized by Sean on `2026-07-11`, but the release remains NO-GO until the controlled attempt succeeds.

**Read-only preflight — gates 1–3:** Before changing the Cloudflare rule, active theme, or either cache, the operator must execute these three read-only gates. A failure in any of gates 1–3 stops the attempt, retains NO-GO, and performs no Cloudflare, theme, or cache mutation.

1. Recompute the retained inactive production `stingray-corvette` `0.1.16` remote manifest and verify it still matches the approved 202-file manifest whose SHA-256 is `b2d900f18b768ed1b760407b337267f34e2a2ad7d3724e9eef96c09f3f3c8e6b`.
2. Recompute the active production Hello Elementor 116-file remote manifest and the retained local backup manifest; require the per-file SHA-256 manifests to match exactly and the manifest SHA-256 to remain `887e92b513f4d5549828c0cb0af3420f810172d9467ed842e648dca56fc33c0a`. Verify the checksum-matched backup remains available for rollback.
3. Capture the exact current Cloudflare rule identity, ruleset, position, enabled state, source expression, target expression, query-string behavior, `301` status, and complete configuration needed to restore it exactly. Confirm that it is still enabled and still targets `https://stingraychevroletcorvette.com/corvette-process-guide/` before mutation.

**First authorized external mutation — gate 4:** Change only the target of the enabled Cloudflare Dynamic Redirect `process-links to process guide` to `https://stingraychevroletcorvette.com/process/`. Preserve its rule and ruleset IDs, position, enabled state, exact source/full-URI match, `301` status, and existing query-string behavior. Re-read the rule after mutation and require those invariants plus the new target to match before theme activation.

If any failure occurs after gate 4 mutates the rule but before theme activation, restore the captured exact original Cloudflare rule configuration and verify the prior queryless `/process-links/` `301` to `https://stingraychevroletcorvette.com/corvette-process-guide/`. Do not reactivate or otherwise change the already-active Hello Elementor theme, and do not perform the later full rollback sequence because theme activation has not occurred. The release remains NO-GO.

Only after all four pre-activation gates pass may theme activation proceed, in this strict order:

1. Activate only the retained, manifest-matched Stingray `0.1.16` candidate.
2. Clear the WordPress object cache and the global edge cache.
3. Require the homepage to return `200` and load only theme asset URLs versioned `0.1.16`.
4. Require two immediate queryless `/order/` requests to return direct `302` responses to `https://order.stingraychevroletcorvette.com/`.
5. Require the remaining five local content routes — `/deposit/`, `/build-and-price/`, `/calculator/`, `/factory/`, and `/process/` — to return `200`.
6. Require `/process-links/` to return a direct Cloudflare-owned `301` first hop to `https://stingraychevroletcorvette.com/process/`. Require the other six legacy paths to return direct theme-owned `302` first hops to their approved local destinations, with no intermediate legacy destination. Confirm that the theme's `/process-links/` `302` remains an origin fallback behind the enabled edge rule, not the accepted production first hop.
7. Verify all required assets load and that the Formidable and wpDataTables shortcode values render rather than appearing as raw shortcode text; do not submit a form.
8. Complete desktop and mobile browser acceptance for the approved navigation, homepage, form, Factory, calculator, Process, redirect, responsive, console, and required-resource checks.
9. Verify transfer compression, cache headers, validators, conditional asset ownership, lazy frame loading, and the approved source-size/performance constraints.

After theme activation begins, any failed hard gate requires immediate full rollback in this order: reactivate Hello Elementor `3.4.9`; restore the Cloudflare rule's exact recorded original configuration, including its enabled state, position, source expression, `301` status, query-string behavior, and original `https://stingraychevroletcorvette.com/corvette-process-guide/` target; clear both the WordPress object and global edge caches; then verify the prior queryless `/process-links/` `301` to `/corvette-process-guide/`, the prior `200` behavior for `/order-landing-page/`, `/order/`, and `/deposit/`, and the homepage `wp-theme-hello-elementor` marker.

If every retry gate passes, leave the updated Cloudflare rule enabled with its new `/process/` target. Deleting or disabling that rule, changing its source match, status, or query-string behavior, or converting any of the other six theme-owned redirects requires separate explicit approval.

## Remaining risk

- The `/process-links/` redirect owner is resolved, and the target-only rule correction plus second controlled activation attempt are authorized. Execution evidence does not yet exist, so production remains on Hello Elementor and the release remains NO-GO.
- The attempt must prove that only the Cloudflare target changed and that production ownership is split as approved: Cloudflare `301` for `/process-links/`, theme `302` for the other six legacy routes, and the theme's `/process-links/` `302` retained only as fallback.
- Gate 1 asset-version evidence, Gate 2's second immediate queryless Order redirect, and later browser/performance gates were not completed before rollback.
- The canonical order runtime requests an optional production favicon URL that returns `404`; Chromium blocks it via ORB. Required order-form resources and customer flows passed, so this is non-blocking follow-up work in 27vette.
- Browser QA covered Chromium at the approved desktop/mobile viewports, not a full browser or physical-device lab.
