# Current Readiness Report

Updated: 2026-07-11 after four controlled production activation attempts and four complete rollbacks.

## Release decision

**Current decision: NO-GO / ROLLED BACK — production is restored to Hello Elementor `3.4.9`; Stingray Corvette `0.1.16` is installed and inactive after all four controlled activation attempts rolled back completely.**

The first attempt established the Cloudflare `/process-links/` redirect conflict. The second attempt established the Elementor document `12977` conflict. The third attempt proved the supported-UI exclusions and candidate output but failed closed on the former body-class predicate. The explicitly approved fourth attempt used the corrected direct-output gate and strengthened evidence sequence, but the activation `Clear all` action did not confirm the global edge clear. The full symmetric rollback is complete. Attempt 4 approval is exhausted; any future activation requires new explicit approval.

## Release identity and ownership

- Theme repository branch: `main`
- Theme release commit: `a60a10c46f6d029c562ba1492565966d29306871`
- Theme version: `0.1.16`
- Staging active theme: `stingray-corvette` version `0.1.16`
- Staging base URL: `https://staging-427b-stingraychevroletcorvette.wpcomstaging.com/`
- Canonical order repository commit: `702ef1cc468315a20a397310b98e2eeb3d49bdde`
- Canonical order runtime: `https://order.stingraychevroletcorvette.com/`

The 27vette deployment is live at the canonical runtime. Its crossed-flags/wordmark link points to `https://stingraychevroletcorvette.com/`, exposes the accessible label `Return to Stingray Corvette home`, and retains the deployed visible-focus rule. When active, the Stingray theme keeps public links on `/order/`, which redirects to that single canonical runtime; the theme is currently inactive in production. `page-order.php` and `assets/order/` remain dormant rollback material, not a second maintained order runtime.

## Production activation attempts and rollbacks

Four controlled activation attempts ran on `2026-07-11`; each stopped at a hard gate and restored the prior production baseline.

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

The current retained state is therefore Hello Elementor `3.4.9` active, Stingray `0.1.16` installed inactive, template `12977` restored to its original two conditions, and the original enabled Cloudflare `/process-links/` target restored. The fourth-attempt authorization is exhausted.

## Proven production root causes

### Attempt 1: `/process-links/` ownership

The failed `301` was caused by the enabled Cloudflare Dynamic Redirect named `process-links to process guide` (`ruleset_id=1423769adf114f2287d9a0280bb26599`, `rule_id=c0e8d48ecfda4ca4ac070dfc7012d0ae`), not by WordPress or the candidate theme. Its exact queryless full-URI match is `(http.request.full_uri wildcard r"https://stingraychevroletcorvette.com/process-links/")`; its `301` target is `https://stingraychevroletcorvette.com/corvette-process-guide/`. The Cloudflare redirect phase executes before WordPress, so the queryless request never reached the Stingray theme's priority-0 redirect callback. A unique query changes the full URI, does not match the queryless expression, and therefore reaches the published WordPress page instead.

The candidate theme code was not the root cause and requires no correction. Its `/process-links/` mapping emits the approved direct `302` to `/process/` when WordPress receives the request. The other six legacy redirects were also not the root cause and passed because no equivalent Cloudflare rule intercepted them.

The intended cutover correction remains: keep the Cloudflare rule `process-links to process guide` enabled and change only its target from `https://stingraychevroletcorvette.com/corvette-process-guide/` to `https://stingraychevroletcorvette.com/process/`. Its identity, position, exact full-URI source match, `301` status, `preserve_query_string=true` behavior, and all other configuration remain unchanged. Cloudflare therefore remains the production owner of the exact queryless `/process-links/` request. The candidate theme's `/process-links/` `302` remains a fallback if the request reaches WordPress; it is not the accepted first hop while the edge rule is enabled.

### Attempt 2: Elementor Pro template override

Task 9D proved that published Elementor Theme Builder document `12977` has exactly these current conditions:

```text
include/singular/page
exclude/singular/front_page
```

Those conditions match every ordinary Page except the static front page. Elementor Pro's priority-11 `template_include` callback then replaces the slug-specific theme template with document `12977`. During Attempt 2 this occurred on all five target pages: `68291` Build & Price, `68294` Deposit, `68297` Calculator, `68300` Factory, and `68303` Process.

The page records, slugs, default-template metadata, empty page bodies, and three embed values are correct. All five installed candidate files exist, their remote/local SHA-256 values match, and the active candidate recognized the intended page queries. This is a production Elementor configuration conflict, not a theme-code, upload, page, shortcode, wpDataTables, Cloudflare, or cache defect.

The smallest reviewed correction was to retain the two existing conditions and add exactly five page-specific exclusions through Elementor's supported Display Conditions UI. Attempt 3 proved that correction removes document `12977` from only the five targets while preserving the named legacy and front-page controls. It is currently rolled back.

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

## Retained production configuration

Production is restored to Hello Elementor `3.4.9`; Stingray `0.1.16` remains installed inactive. The replacement records remain published, parentless, use the default template, and have empty editor content. They are retained for diagnosis; their presence does not authorize an Elementor mutation or another activation attempt.

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

## Future activation authorization boundary

**No further activation is currently authorized.** The fourth-attempt authorization was consumed and ended in complete rollback. A future attempt requires independent review of the corrected cache-confirmation and replayable-evidence protocol in the operative runbook, followed by new explicit approval for the Elementor save and reactivation of Stingray `0.1.16`.

The intended successful cutover state remains an enabled Cloudflare `/process-links/` rule whose only changed contract field is the direct target `/process/`. The proven Elementor correction retains document `12977`'s existing all-pages include and front-page exclusion while adding exactly five page-specific exclusions for IDs `68291`, `68294`, `68297`, `68300`, and `68303`. Page `68288` (`/order/`) remains intentionally outside the exclusions. None of those cutover changes is currently applied or authorized.

## Remaining risk

- The Cloudflare correction is proven, but its original target is currently restored. A future cutover must again prove that only the target changed and preserve the approved split: Cloudflare `301` for `/process-links/`, theme `302` for the other six legacy routes, and the theme's `/process-links/` `302` only as fallback.
- The Elementor correction is now proven in production but currently rolled back. Its future save remains a live routing mutation with a maximum five-minute save-to-activation window and mandatory symmetric rollback.
- Attempt 3's literal body-class predicate was technically invalid for hierarchy-bound `page-{slug}.php` files with default/empty page-template metadata. The corrected direct-output gate and strengthened evidence sequence require independent review before another production attempt.
- Attempt 3 rollback is verified, but its retained evidence does not independently replay every mutation, readback, response, and required timing transition. No future attempt may inherit an evidence waiver from Attempt 3.
- Attempt 4 failed the activation confirmation gate because both required messages were not observed from the single `Clear all` action; the global edge purge outcome is unknown, not proven failed. Future activation and rollback `Clear all` actions require the bounded five-minute protocol in the operative runbook, with no retry.
- Attempt 4 rollback and public restoration are operationally convincing, but raw complete Cloudflare readbacks, normalized/reopened-row Elementor proof, and raw cache UI captures were not all retained. The operative evidence contract must pass its pre-mutation and completion index checks on any future attempt.
- Gates 6–7 were not run after Attempt 3's Gate 5 body-class failure. No browser or delivery/performance result from that production attempt is claimed.
- A new explicit approval is required before the five Elementor exclusions are saved or Stingray is activated again.
- The canonical order runtime requests an optional production favicon URL that returns `404`; Chromium blocks it via ORB. Required order-form resources and customer flows passed, so this is non-blocking follow-up work in 27vette.
- Browser QA covered Chromium at the approved desktop/mobile viewports, not a full browser or physical-device lab.
