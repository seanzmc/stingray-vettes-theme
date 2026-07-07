Current readiness: codebase is structurally close, but not live-ready until plugin-backed QA, final mobile/visual QA, Sean’s process-content review, and
cache-busting/version bump are completed.

No deployment was performed. No files were changed.

Repo state:
- Branch: main
- origin/main relation: 0 ahead / 0 behind
- Working tree: clean
- Diff stat: empty
- Theme version: style.css Version: 0.1.0
- Local WP Studio preview found at:
  /Users/seandm/Projects/WP-Studio/stingraychevroletcorvettecom
- Theme is symlinked there to:
  /Users/seandm/Projects/stingray-vettes-theme
- Local preview DB currently uses this theme.
- Local preview plugins: none active, active_plugins=[]

Static gates run:
- PHP lint passed:
  front-page.php
  page-order.php
  page-deposit.php
  page-build-and-price.php
  page-calculator.php
  page-factory.php
  page-process.php
  page.php
  header.php
  footer.php
  functions.php
  inc/topbar.php
  inc/site-footer.php
  index.php

- JS syntax checks passed:
  assets/js/nav.js
  assets/homepage/spin.js
  assets/order/app.js
  assets/order/data.js
  assets/calculator/script.js
  assets/calculator/qp-new.js

Asset/enqueue audit:
- All local assets referenced by functions.php exist.
- Rendered local WP routes had no missing theme CSS/JS/image/font assets:
  /: 13 theme assets, all 200
  /order/: 14 theme assets, all 200
  /deposit/: 12 theme assets, all 200
  /build-and-price/: 12 theme assets, all 200
  /calculator/: 14 theme assets, all 200
  /factory/: 12 theme assets, all 200
  /process/: 11 theme assets, all 200

- Homepage spin frame sets exist:
  g26-orange: 30
  g4z-green: 30
  gbk-yellow: 30
  gkz-red: 30
  gtr-blue: 30

- Order vehicle PNG assets found: 10

Browser/local WP checks completed:
- /:
  - Homepage rendered.
  - #spinCanvas present.
  - window.SC_SPIN_BASE set correctly.
  - Initial spin frame resources loaded 200.
  - No console/resource errors observed.

- /order/:
  - Order app rendered.
  - Step rail and initial vehicle setup loaded.
  - window.SC_FORM_ASSET_BASE set correctly.
  - Turnstile script scoped to order page.
  - No console/resource errors observed.
  - No real submission attempted.

- /calculator/:
  - Calculator rendered.
  - Payment test worked: 70000 / 72 / 6.9 returned $1,194.24.
  - Income calculator produced output when populated.
  - Quick Pencil produced itemized summary for $90,000 MSRP/selling price.
  - No console/resource errors observed.

- /deposit/:
  - Static content rendered.
  - Formidable shortcode contract present inside .sc-embed.
  - Because plugins are inactive locally, shortcode rendered literal: [formidable id=8].
  - No console/resource errors observed.

- /build-and-price/:
  - Static content rendered.
  - Chevrolet configurator links present.
  - Formidable shortcode contract present inside .sc-embed.
  - Because plugins are inactive locally, shortcode rendered literal: [formidable id=30].
  - No console/resource errors observed.
  - External configurator URLs returned 200 by HEAD.

- /factory/:
  - Static content rendered.
  - wpDataTables shortcode contract present inside .sc-embed.
  - Because plugins are inactive locally, shortcode rendered literal: [wpdatatable id=7 table_view=regular].
  - 11 details/accordion elements present.
  - No console/resource errors observed.

- /process/:
  - Static page rendered.
  - Key policy/status/fee/contact content present in source and rendered:
    Grand Sport X, ZR1X, Dealer Fee $999, Tag Agency Fee $362, smccann@stingraychevrolet.com, 813-359-5000.
  - No horizontal overflow at the checked desktop viewport.
  - No console/resource errors observed.

Link audit:
- No legacy replaced-surface slugs found in public PHP templates:
  order-landing-page/deposit-form
  order-landing-page/build-and-price-link-share
  orders-in-production
  corvette-process-guide
  process-links

- Intentional/existing external links checked:
  - Chevrolet configurator links: 200
  - https://stingraychevroletcorvette.com/zr1-process/ redirects to live ZR1 process path and returns 200
  - https://stingraychevroletcorvette.com/learning-center/ returns 200
  - https://www.chevrolet.com/ returns 200
  - https://www.stingraychevrolet.com/ returned 403 to this scripted HEAD check, likely bot/WAF behavior; browser/user verification still recommended.

Necessary actions before going live:

1. Run plugin-backed QA in a real WordPress environment with required plugins active.
    Static/theme code is ready enough for this step, but local WP Studio cannot prove it because active_plugins=[].
    Must verify:
    - /deposit/ renders Formidable form id 8 inside .sc-embed.
    - /build-and-price/ renders Formidable form id 30 inside .sc-embed.
    - /factory/ renders wpDataTables id 7 inside .sc-embed.
    - Form labels, fields, validation states, submit buttons, search/filter/table controls, pagination, and modals are readable on the dark theme.
    - Record whether Formidable built-in styling is enabled or disabled.

2. Decide and test the Formidable styling setting.
    Sean is considering disabling Formidable built-in styling. That decision cannot be validated statically.
    Required check:
    - Test deposit and build/share forms with the chosen setting.
    - Confirm required markers, errors, confirmations, and submit/payment-link flow remain readable.
    - Do not change Formidable form definitions unless explicitly approved.

3. Verify wpDataTables live/staging behavior separately from theme readiness.
    Required check:
    - Confirm /factory/ table renders with active plugin.
    - Confirm search/filter input, headers, rows, pagination, row details/modal are readable.
    - If the table shows Service Unavailable, compare against current live/source behavior before treating it as a theme bug.

4. Complete responsive visual QA on real rendered pages.
    I checked local desktop-level render and deterministic JS/resource state. Still needed:
    - Mobile around 390px
    - Tablet around 768px if time allows
    - Desktop around 1440px
    - Check all seven routes for duplicate chrome, horizontal overflow, overlap, unreadable controls, excessive nesting, and focus states.
    - Especially verify /order/ mobile drawers and /process/ long-page readability.

5. Sean should review /process/ business/policy content before deployment.
    The required content is present, but it is policy/legal-adjacent:
    - deposit statuses
    - ZR1/ZR1X closure language
    - E-Ray discontinued/transfer language
    - refund/deposit rules
    - pricing/fee language
    - restrictions
    - contact info

6. Decide what to do with auxiliary live links.
    These are not blockers, but should be intentionally accepted:
    - /factory/ links to local /learning-center/, which is not one of the six rebuilt surfaces. Live URL returns 200, but local WP preview has no
learning-center page.
    - /process/ links to the existing ZR1 process page on stingraychevroletcorvette.com, which resolves live.
    If these pages are intended to remain outside this theme pass, leave them. If the replacement site should avoid any non-rebuilt internal content,
update those links/copy in a scoped pass.

7. After plugin QA and content review are accepted, bump style.css Version.
    Current:
    - Version: 0.1.0
    Recommended release/cache-bust bump:
    - Version: 0.2.0
    Do not bump yet unless Sean accepts plugin/content risk or plugin-backed QA passes, because STINGRAY_CORVETTE_VERSION controls cache keys for local
enqueued assets.

8. Deployment checklist still needed.
    Once the above passes:
    - Commit any narrow QA fixes separately.
    - Commit version bump separately, e.g. chore: prepare theme release.
    - Deploy/upload/activate only after explicit approval.
    - Verify live routes:
      /
      /order/
      /deposit/
      /build-and-price/
      /calculator/
      /factory/
      /process/
    - Check live console/resource errors.
    - Verify cache-busted asset URLs show the new version.
    - Keep CarSales/sales.stingraychevroletcorvette.com out of this deployment path.

Recommended next pass:
1. Use the plugin-enabled WordPress.com Business preview/staging/live-equivalent environment.
2. Run browser QA on all seven routes at desktop and mobile.
3. Fix only scoped launch blockers.
4. Bump style.css to 0.2.0 after acceptance.
5. Prepare deployment handoff/commit.

Recommended commit messages if fixes are needed:
- fix: polish launch QA issues
- chore: prepare theme release

Current project status summary:
- Static code gates: pass
- Local WordPress route render: pass for non-plugin surfaces
- Theme asset loading: pass
- Homepage 360 initial load: pass
- Order app initial load: pass
- Calculator primary checks: pass
- Formidable rendering: pending plugin-enabled environment
- wpDataTables rendering: pending plugin-enabled environment
- Mobile/tablet visual QA: pending
- Business-content approval: pending
- Version/cache-bust bump: pending
- Deployment: not performed