# Current Readiness Report

Updated: 2026-07-11 after the user-directed production publish and final launch verification.

## Release decision

**Current decision: GO / LIVE FOR USER REVIEW — Stingray Corvette `0.1.16` is active in production and the user-directed minimal launch gate passed.**

After Attempt 6's verified rollback, the user explicitly overrode the former acceptance boundary and directed publication as built. The final publish left Stingray `0.1.16` active, Hello Elementor inactive, the exact five Elementor exclusions in place, and the Cloudflare `/process-links/` target set to `/process/`. Homepage and the five primary local pages returned `200` with the Stingray marker; `/order/` and `/process-links/` reached their intended destinations in one hop; and the required theme assets in the retained launch sweep returned `200`. The known Factory row-detail dialog defect is now a post-launch follow-up, not a launch blocker.

## Release identity and ownership

- Theme repository branch: `main`
- Theme release commit: `a60a10c46f6d029c562ba1492565966d29306871`
- Theme version: `0.1.16`
- Production active theme: `stingray-corvette` version `0.1.16`
- Production inactive fallback theme: `hello-elementor` version `3.4.9`
- Staging active theme: `stingray-corvette` version `0.1.16`
- Staging base URL: `https://staging-427b-stingraychevroletcorvette.wpcomstaging.com/`
- Canonical order repository commit: `702ef1cc468315a20a397310b98e2eeb3d49bdde`
- Canonical order runtime: `https://order.stingraychevroletcorvette.com/`

The 27vette deployment is live at the canonical runtime. Its crossed-flags/wordmark link points to `https://stingraychevroletcorvette.com/`, exposes the accessible label `Return to Stingray Corvette home`, and retains the deployed visible-focus rule. The active Stingray theme keeps public links on `/order/`, which redirects to that single canonical runtime. `page-order.php` and `assets/order/` remain dormant rollback material, not a second maintained order runtime.

## Production activation attempts and rollbacks

### User-directed final publish: live

The final publish applied the approved Cloudflare public-target correction and exact five Elementor exclusions, activated Stingray `0.1.16`, flushed the WordPress object cache, and performed one WordPress.com Production `Clear all` action. Final authenticated WordPress readback showed Stingray active, Hello inactive, and exactly seven Elementor conditions: the original all-pages include and front-page exclusion plus exclusions for page IDs `68291`, `68294`, `68297`, `68300`, and `68303`. Task 15 separately proved the public Cloudflare `301` target and no-loop behavior; it did not retain a fresh authenticated full-field Cloudflare readback.

The minimal launch gate passed at `2026-07-11T20:05:29Z`: all six local surfaces returned `200` with the Stingray marker and no detected fatal marker; `/order/` reached the canonical order runtime in one redirect; `/process-links/` reached `/process/` in one redirect without a loop; all 16 extracted required theme assets returned `200`; and a fresh Chromium homepage read captured no console error. Production was intentionally left live for user review. The Factory row-detail dialog remains a known non-blocking follow-up.

Six controlled activation attempts ran on `2026-07-11`; each stopped at a hard gate and restored the prior production baseline.

### Attempt 6: Factory row-detail dialog hard failure; complete rollback

Attempt 6 passed fresh manifests/state, the target-only Cloudflare update, the immediate Elementor baseline, exact supported-UI exclusions, activation within 77 seconds, object-cache flush, and the single authorized Production `Clear all`. Under the explicit override, missing transient activation toast capture was not treated as failure; actual public results governed acceptance.

- Homepage and all five candidate pages returned `200`, used Stingray `0.1.16`, rendered the required direct template/embed markers, omitted Elementor document `12977`, and contained no raw shortcode or missing-embed notice.
- Both redirect owners and all approved first hops passed. All 122 extracted local CSS/JavaScript/image/font assets returned `200`. Desktop/mobile layouts had no horizontal overflow and browser console capture was clean. Forms rendered without submission and the calculator produced a valid test result.
- Factory table 12 rendered 10 rows plus search/filter controls, but clicking a row did not open the required detail dialog. The live table had 28 `thead th` nodes (labels plus filters) and 14 cells per data row. `assets/js/factory-table.js` requires equality, so every row was rejected and never received its interactive class, keyboard focus, accessible label, or dialog behavior.
- The substantive Gate 6 failure triggered immediate rollback. Hello `3.4.9`, the exact original two Elementor conditions, the complete original Cloudflare `/corvette-process-guide/` target, object cache, the single Production rollback cache clear, and the public Hello/document-`12977` baseline all passed final proof. The 202-file Stingray candidate remains installed inactive and unchanged.

Evidence and report: `.superpowers/sdd/task-13-attempt6-*`. No form/order submission, plugin deactivation, unrelated production change, commit, push, or deletion occurred.

### Attempt 5: activation and rollback global-edge confirmation failures; purge outcomes unknown

The explicitly approved fifth attempt followed the corrected five-minute/five-second cache protocol and replayable-evidence contract. Fresh preflight passed with zero mutation: the 202-file Stingray manifest and 116-file Hello remote/backup manifests matched, Hello `3.4.9` was active, Stingray `0.1.16` inactive, all production records/embeds/forms/table checks passed, the original two Elementor conditions/effective state passed, and the exact Production cache control was ready.

- The Cloudflare rule update changed only the wildcard replacement target from `/corvette-process-guide/` to `/process/`. Complete raw authenticated before/after JSON and normalized semantic diffs were retained. Two consecutive direct Cloudflare `301` first hops to `/process/` passed.
- Strictly after propagation, a fresh Elementor baseline retained raw/normalized conditions, eight effective probes, public Hello headers/HTML/markers, and supported-UI proof. The UI saved exactly the five exclusions `68291`, `68294`, `68297`, `68300`, and `68303`; page `68288` remained unexcluded. Raw/normalized/effective and reopened-row evidence passed.
- Save confirmation was retained at `18:15:35.815Z`. Stingray activation began at `18:17:08Z`, completed at `18:17:17Z`, and `wp cache flush` completed at `18:17:21Z`, inside the five-minute bound.
- The activation Production `Clear all` control was clicked exactly once at `18:19:52.194Z`. `Object cache cleared.` appeared, but `Global edge cache cleared.` did not appear through `18:25:03.470Z` / `311.276s`; the control remained disabled and no retry occurred. This is a confirmation-gate failure with purge outcome unknown. Gates 1-7 were not run.
- Phase-aware rollback reactivated Hello first, restored Elementor template `12977` through the supported UI to exactly `include/singular/page` and `exclude/singular/front_page`, restored the complete original Cloudflare rule and `/corvette-process-guide/` target, and completed rollback `wp cache flush`.
- The rollback Production `Clear all` control was clicked exactly once at `18:29:13.765Z`. The object confirmation appeared, but the global-edge confirmation did not appear through `18:34:17.556Z` / `303.791s`; the control remained disabled and no retry occurred. Rollback global-edge purge outcome is unknown.
- Read-only restoration proof passed after that timeout: Hello `3.4.9` active, Stingray `0.1.16` inactive, exact two-condition raw/normalized/effective and reopened-row state, complete original Cloudflare readback plus two original direct `301` first hops, public Hello/document-`12977` baseline, all extracted Hello assets `200`, and the exact retained 202-file candidate hash `b2d900f18b768ed1b760407b337267f34e2a2ad7d3724e9eef96c09f3f3c8e6b`.

Evidence and the report are retained under `.superpowers/sdd/task-12-attempt5-*`. No form/order submission, plugin deactivation, unrelated production change, commit, push, or deletion occurred. Attempt 5 approval is exhausted.

### Attempt 4: activation global-edge confirmation gate failure; purge outcome unknown

The explicitly approved fourth attempt used the corrected direct-output runbook and strengthened evidence sequence. Fresh read-only readiness passed before mutation: Hello Elementor `3.4.9` was active, Stingray `0.1.16` inactive, the exact 202-file candidate and 116-file Hello backup matched their approved manifests, all six page records/three embeds/forms `8` and `30`/wpDataTable `12` passed, and both authenticated cache and rollback operators were ready.

- The enabled Cloudflare `/process-links/` rule was changed only to target `/process/`. The retained synthesized summaries and screenshots report that its identity, first position, exact full-URI wildcard source, enabled state, `301`, and preserved-query behavior were unchanged. Two consecutive direct Cloudflare-owned `301` first hops to `/process/` passed at `15:59:36Z`. The package does not contain the raw complete authenticated rule readbacks needed to replay every exact field claim independently.
- Strictly after propagation, the immediate Elementor baseline ran from `15:59:54Z` through `16:00:44Z`. It retained the exact raw two-condition value, effective matches for all five targets and two named legacy controls, the real front-page exclusion, raw public headers/HTML, and supported-UI before evidence.
- Elementor template `12977` was saved through the supported Display Conditions UI with exactly the original two rows plus exclusions for `68291`, `68294`, `68297`, `68300`, and `68303`. Save confirmation was retained at `16:03:58.722Z`; exact raw/effective readback completed at `16:04:47Z` and passed.
- Stingray activation began at `16:05:48Z`, completed at `16:05:56Z`, and `wp cache flush` completed at `16:06:00Z`, within the five-minute bound.
- The authenticated Production `Clear all` action was clicked once at `16:06:39.682Z`. `Object cache cleared.` appeared, but the required `Global edge cache cleared.` confirmation did not appear during the retained observation window of more than 18 seconds and the controls remained disabled. That establishes a **global-edge confirmation gate failure with purge outcome unknown**; it does not prove that the global edge purge itself failed. No retry occurred. This was treated as a hard gate before public acceptance Gates 1–7, so none of those gates is claimed.
- Full phase-aware rollback passed. Hello was active again at `16:09:40Z`; the supported Elementor UI restored exactly `include/singular/page` and `exclude/singular/front_page` at `16:11:17Z`; effective target/legacy/front checks passed; the complete original Cloudflare `/corvette-process-guide/` rule was restored and read back; rollback `wp cache flush` passed at `16:12:48Z`; and the authorized rollback `Clear all` at `16:14:01.320Z` displayed both object and global-edge success confirmations.
- Post-clear proof passed: exact raw/effective Elementor state, two consecutive original direct Cloudflare first hops, the public Hello/document-`12977` baseline, Hello active/Stingray inactive, and the exact retained 202-file candidate hash.

Attempt 4 evidence and the final report are retained under `.superpowers/sdd/task-11-attempt4-*`. The operator record and read-only requests show no form/order submission; no separate submission audit was performed. No unrelated setting, page, embed, form, table, plugin, theme file, dependency, commit, push, or deployment changed. Attempt 4 approval is exhausted.

Independent review found that the rollback is operationally convincing and the restored public baseline passed, but not every exact full-spec field/UI claim is independently replayable from the retained Attempt 4 package:

- Synthesized Cloudflare field summaries and screenshots were retained instead of raw complete authenticated machine-readable rule readbacks and normalized semantic diffs.
- Raw Elementor condition values and effective checks were retained, but normalized ordered lists and supported-UI proof of the exact post-save/reopened rows were not retained for every required phase.
- The cache report records the authenticated UI observations, but raw sanitized cache UI screenshot/DOM files were not retained under the attempt evidence directory.
- The no-submission boundary remains operator/read-only-request evidence, not a separate submission audit.

No future attempt may inherit an evidence waiver from Attempt 4.

### Attempt 1: Cloudflare redirect conflict

Attempt 1 ran within the recorded `01:13:20–01:22:28 EDT` window: production key attachment was recorded at `01:13:20`, candidate commit `a60a10c46f6d029c562ba1492565966d29306871` / theme version `0.1.16` was uploaded and activated after backup/checksum gates, and rollback verification completed at `01:22:28`.

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

### Attempt 2: Elementor template-selection failure

The explicitly authorized second attempt began from a fresh read-only preflight. It reconfirmed Hello Elementor `3.4.9` active, Stingray `0.1.16` inactive, the exact 202-file Stingray candidate manifest, the exact 116-file Hello backup, the six page records, the three embed values, table 12, and the complete original Cloudflare rule.

- The existing enabled Cloudflare `/process-links/` rule was changed only from target `/corvette-process-guide/` to `/process/`. Authenticated readback preserved its identity, source, enabled state, `301`, and query behavior, and two consecutive exact direct Cloudflare-owned `301` first hops to `/process/` passed.
- Stingray `0.1.16` was activated. The object cache was cleared first, then WordPress.com Production `Clear all` confirmed both object and global edge caches cleared.
- Strict Gates 1–4 passed: homepage/versioned assets, two exact `/order/` first hops, all five local route statuses, and the Cloudflare/theme legacy-redirect split.
- Gate 5 failed. All five interior pages rendered through Elementor Pro Theme Builder document `12977`, so the three required embeds, Calculator markup, and Process theme content were absent. Gates 6–7 were not run.
- Rollback reactivated Hello Elementor `3.4.9`, restored the enabled Cloudflare rule's original `/corvette-process-guide/` target and configuration, cleared object then global edge caches, and verified the prior public baseline plus two consecutive original direct Cloudflare first hops.

### Attempt 3: literal body-class acceptance mismatch

Attempt 3 ran under new explicit approval for the five Elementor exclusions and third activation. A fresh preflight reconfirmed the exact 202-file Stingray candidate, 116-file Hello backup, six page records, three embed values, forms `8` and `30`, wpDataTable `12`, rollback operators, cache operator, original Cloudflare rule, and exact Elementor/public baseline.

- The enabled Cloudflare rule was changed only to target `/process/`; authenticated readback and two consecutive direct Cloudflare-owned `301` first hops passed.
- Elementor template `12977` was saved through the supported Display Conditions UI with exactly the original two conditions plus exclusions for IDs `68291`, `68294`, `68297`, `68300`, and `68303`. Exact raw/normalized readback passed. The five targets no longer matched `12977`; two named legacy controls still matched; front page `5` remained excluded. No unrelated page, embed, theme, candidate, or Theme Builder condition changed.
- The save was confirmed at `15:05:33Z`; Stingray activation began at `15:07:13Z`, completed at `15:07:15Z`, and the object-cache flush completed at `15:07:17Z`, all within the maximum five-minute window. WordPress.com Production `Clear all` then confirmed both object and global edge caches cleared.
- Strict Gates 1–4 passed: homepage `200` with 11 Stingray assets all at `0.1.16`; two exact empty-body `/order/` `302` first hops; all five local pages `200`; and the exact Cloudflare/theme legacy-redirect split.
- Gate 5 proved that every target bypassed document `12977` and executed candidate output: Build & Price rendered `.sc-embed` and Formidable form key `chevbp23` (form `30`); Deposit rendered `.sc-embed` and Formidable form key `deposit-form` (form `8`); Calculator rendered `#payment-form`; Factory rendered `.sc-embed` and `data-wpdatatable_id="12"`; Process rendered `Corvette Order Process Guide`.
- The same HTML reported `page-template-default` on all five pages. Because the authoritative strict gate required a `page-template-page-{slug}` class rather than `page-template-default`, the operator failed closed before the asset sweep, browser gate, or delivery/performance gate and triggered rollback.
- Rollback reactivated Hello first; restored template `12977` through the supported UI to exactly `include/singular/page` and `exclude/singular/front_page`; restored the original Cloudflare `/corvette-process-guide/` target/configuration; cleared object then global edge caches; and repeated all effective/public baseline checks. Final proof showed Hello active, Stingray inactive, the five targets again using document `12977`, front page excluded, two consecutive original Cloudflare first hops, Hello CSS `200`, prior `/order-landing-page/` and `/order/` behavior, and the exact retained 202-file candidate.

Independent review found evidence and sequence defects that prevent the retained Attempt 3 material from serving as independently replayable approval evidence, even though the rollback itself is verified:

- The retained Elementor baseline predates the Cloudflare mutation. It does not prove the required baseline timing strictly after Cloudflare propagation and immediately before the supported-UI save.
- Supported-UI before/save-confirmation/after evidence, normalized readback, raw Gates 1–5 responses, and the complete final Cloudflare field readback were not all retained in independently replayable form.
- Timestamps do not establish the complete required evidence order. A future attempt must retain raw headers and sanitized HTML/marker extracts for every gate, exact raw plus normalized Elementor conditions before and after, supported-UI evidence without secrets, complete Cloudflare rule readbacks before/update/final-or-rollback, and timestamps tying the sequence together.

At the completion of Attempt 3, the retained state was Hello Elementor `3.4.9` active, Stingray `0.1.16` installed inactive, template `12977` restored to its original two conditions, and the original enabled Cloudflare `/process-links/` target restored. The fourth-attempt authorization was exhausted. Task 15 later superseded that historical state with the live cutover recorded at the top of this report.

## Proven production root causes

### Attempt 1: `/process-links/` ownership

During Attempt 1, the failed `301` was caused by the then-enabled Cloudflare Dynamic Redirect named `process-links to process guide` (`ruleset_id=1423769adf114f2287d9a0280bb26599`, `rule_id=c0e8d48ecfda4ca4ac070dfc7012d0ae`), not by WordPress or the candidate theme. At that time its exact queryless full-URI match was `(http.request.full_uri wildcard r"https://stingraychevroletcorvette.com/process-links/")`, and its `301` target was `https://stingraychevroletcorvette.com/corvette-process-guide/`. The Cloudflare redirect phase executed before WordPress, so the queryless request did not reach the Stingray theme's priority-0 redirect callback. A unique query changed the full URI, did not match the queryless expression, and therefore reached the published WordPress page instead.

The candidate theme code was not the Attempt 1 root cause and required no correction for that failure. Its `/process-links/` mapping emitted the approved direct `302` to `/process/` when WordPress received the request. The other six legacy redirects were also not the root cause and passed because no equivalent Cloudflare rule intercepted them.

The approved cutover correction was to change the Cloudflare target from `https://stingraychevroletcorvette.com/corvette-process-guide/` to `https://stingraychevroletcorvette.com/process/`. Task 15 now freshly proves that the exact queryless `/process-links/` request receives a Cloudflare-owned `301` to `/process/` and reaches that destination without a loop. Task 15 did not retain a fresh authenticated full-field rule readback, so no present-tense claim is made here that the stored rule identity, position, source expression, query setting, or every other non-target field remains unchanged. The candidate theme's `/process-links/` `302` remains the code-path fallback if a request reaches WordPress; it is not the observed public first hop.

### Attempt 2: Elementor Pro template override

Task 9D proved that published Elementor Theme Builder document `12977` then had exactly these conditions:

```text
include/singular/page
exclude/singular/front_page
```

Those conditions match every ordinary Page except the static front page. Elementor Pro's priority-11 `template_include` callback then replaces the slug-specific theme template with document `12977`. During Attempt 2 this occurred on all five target pages: `68291` Build & Price, `68294` Deposit, `68297` Calculator, `68300` Factory, and `68303` Process.

The page records, slugs, default-template metadata, empty page bodies, and three embed values are correct. All five installed candidate files exist, their remote/local SHA-256 values match, and the active candidate recognized the intended page queries. This is a production Elementor configuration conflict, not a theme-code, upload, page, shortcode, wpDataTables, Cloudflare, or cache defect.

The smallest reviewed correction was to retain the two existing conditions and add exactly five page-specific exclusions through Elementor's supported Display Conditions UI. Attempt 3 proved that correction removes document `12977` from only the five targets while preserving the named legacy and front-page controls. Task 15 applied and retained that correction in production.

### Attempt 3: body-class gate predicate

Attempt 3 proved the five slug-owned candidate files executed and rendered their required content while each page still had default/empty `_wp_page_template` state. Installed WordPress core explains the result exactly: `get_page_template()` adds `page-{pagename}.php` to the page hierarchy independently of page-template metadata, while `get_body_class()` bases its template class on `is_page_template()` and `get_page_template_slug()`. `get_page_template_slug()` returns an empty string for missing or `default` `_wp_page_template`, so `is_page_template()` is false and core emits `page-template-default`.

That class is expected here. The five files are hierarchy-bound `page-{slug}.php` templates without `Template Name` headers, and all five page records intentionally retain default/empty `_wp_page_template` metadata. Neither the presence of `page-template-default` nor the absence of `page-template-page-{slug}` can accept or reject candidate ownership. The corrected gate instead requires the correct page ID, active `wp-theme-stingray-corvette` marker, absence of Elementor document `12977`, and the direct candidate content, embed, selector, shortcode-rendering, and asset evidence specified in the runbook.

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

## Current production configuration

Production now runs Stingray `0.1.16`; Hello Elementor `3.4.9` is installed inactive. Elementor document `12977` retains its original all-pages include and front-page exclusion plus the exact five exclusions for `68291`, `68294`, `68297`, `68300`, and `68303`. The replacement records remain published, parentless, use the default template, and have empty editor content. No new activation approval is pending because the approved cutover is already live; further changes require a separate user request.

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

Formidable form 8 (`Deposit Form`, key `deposit-form`) and form 30 (`Chevy Build and Price Link Share`, key `chevbp23`) were verified as the intended production records. Both forms rendered with submit controls during `0.1.16` staging QA without submission. Attempt 2 reached production rendering, but Elementor document `12977` bypassed the candidate slug templates before their embed output could render. No production Formidable definition was changed.

Production wpDataTable 12 (`Orders_v2`) retains the reviewed Factory binding configuration against the Google Sheet source. It has all 14 expected columns, plugin responsive mode disabled, pagination enabled at 10 rows per page, and a credible 23-row preview (`Showing 1 to 10 of 23 entries`). Its source, ID, columns, and pagination were preserved; only the approved responsive toggle was disabled. The theme's PHP and JavaScript regressions explicitly exercise table 12, and active runtime CSS/JavaScript has no dependency on the former staging table ID.

## Performance and delivery evidence

- Representative versioned `0.1.16` CSS and JavaScript assets returned `200` with gzip transfer compression, ETag validators, and `Cache-Control: max-age=315360000` on staging.
- Theme version `0.1.16` remains the cache-busting version attached to enqueued assets.
- Homepage, Order, Factory, Calculator, and embed assets retain their conditional page ownership; surface assets were not made global.
- Homepage frame loading remains lazy: mobile loaded only the selected paint's 30 frames at rest, while desktop loaded the next 30-frame paint set only near the scroll threshold.
- The measured CSS/JavaScript/PHP source total is 266,283 bytes, 5,319 bytes below the approved ceiling.
- No compiler, minifier, package manifest, dependency, lockfile, or build step was introduced.

## Pre-attempt validation summary

The following passed against release commit `a60a10c` before the production attempts. This historical pre-attempt evidence supplements, but does not replace, the Task 15 live launch verification:

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

## Current user-review and follow-up boundary

**The approved cutover is complete and no new activation is pending.** Stingray `0.1.16` remains live for user review, Hello remains inactive, and the exact five Elementor exclusions remain applied. The concrete Factory row-detail defect is a known post-launch follow-up and does not revoke the current GO decision. Any correction or new production mutation requires a separate user request.

The live public cutover state is a direct Cloudflare-owned `301` from `/process-links/` to `/process/`, proven twice after publication and again by the final verifier without a loop. Task 15 did not retain a fresh authenticated full-field Cloudflare rule readback, so this report does not claim that every non-target rule field was independently reverified during Task 15. The retained Elementor readback does prove document `12977` keeps its existing all-pages include and front-page exclusion plus exactly five page-specific exclusions for IDs `68291`, `68294`, `68297`, `68300`, and `68303`. Page `68288` (`/order/`) remains intentionally outside the exclusions.

## Remaining risk

- The live public Cloudflare target and no-loop behavior are proven. Task 15 lacks a fresh authenticated full-field rule readback, so exact preservation of every non-target Cloudflare field is not newly claimed for the final publish.
- The Elementor correction is live and retained: the original two conditions plus the exact five approved page exclusions. Any later edit remains a live routing mutation requiring a separate user request and proportionate rollback planning.
- Attempt 3's literal body-class predicate was technically invalid for hierarchy-bound `page-{slug}.php` files with default/empty page-template metadata. The corrected direct-output gate and strengthened evidence sequence require independent review before another production attempt.
- Attempt 3 rollback is verified, but its retained evidence does not independently replay every mutation, readback, response, and required timing transition. No future attempt may inherit an evidence waiver from Attempt 3.
- Attempt 4 failed the activation confirmation gate because both required messages were not observed from the single `Clear all` action; the global edge purge outcome is unknown, not proven failed. Future activation and rollback `Clear all` actions require the bounded five-minute protocol in the operative runbook, with no retry.
- Attempt 4 rollback and public restoration are operationally convincing, but raw complete Cloudflare readbacks, normalized/reopened-row Elementor proof, and raw cache UI captures were not all retained. The operative evidence contract must pass its pre-mutation and completion index checks on any future attempt.
- Gates 6–7 were not run after Attempt 3's Gate 5 body-class failure. No browser or delivery/performance result from that production attempt is claimed.
- Attempt 6 reached Gate 6 and proved that the Factory row-detail dialog is broken against the live two-row wpDataTables header: the script counts 28 headers against 14 data cells and rejects every row. Gate 7 was not completed after this hard failure.
- The five Elementor exclusions are already saved and Stingray is already active. A new explicit request is required only for a further production mutation, including a Factory-dialog fix or another theme/configuration change.
- The canonical order runtime requests an optional production favicon URL that returns `404`; Chromium blocks it via ORB. Required order-form resources and customer flows passed, so this is non-blocking follow-up work in 27vette.
- Browser QA covered Chromium at the approved desktop/mobile viewports, not a full browser or physical-device lab.
