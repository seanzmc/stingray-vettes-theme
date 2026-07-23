# Stingray Corvette — WordPress Theme

Standalone classic theme for `stingraychevroletcorvette.com` (WordPress.com Business).
Repo root = the theme folder; deploys to `/wp-content/themes/stingray-corvette/`.
The global style layer is the **Stingray Corvette Design System**, vendored from
the canonical copy in the 27vette repo — **the DS stays canonical there; this
theme holds a copy, not the source of truth.**

## What was vendored, and from where

Canonical source: `/Users/seandm/Projects/27vette/export-page/StingrayCorvetteDesignSystem_v1/`
(vendored 2026-07-03).

### Global CSS (load order is normative — from `_ds_manifest.json` → `globalCssPaths`)

| Theme file | DS source | Delta from source |
|---|---|---|
| `assets/css/tokens/colors.css` | `tokens/colors.css` | none (verbatim) |
| `assets/css/tokens/typography.css` | `tokens/typography.css` | `@font-face` block removed — moved to `assets/css/fonts.css` (see below) |
| `assets/css/tokens/spacing.css` | `tokens/spacing.css` | none (verbatim) |
| `assets/css/tokens/base.css` | `tokens/base.css` | none (verbatim) |
| `assets/css/styles.css` | `styles.css` | `@import` lines removed — each file is enqueued as its own handle in `functions.php`, and Inter has its own handle, so the imports would double-load |

### Fonts (verbatim copies)

| Theme file | DS source |
|---|---|
| `assets/fonts/ChevySans-Regular.woff2` | `assets/fonts/ChevySans-Regular.woff2` |
| `assets/fonts/ChevySans-Demi.woff2` | `assets/fonts/ChevySans-Demi.woff2` |
| `assets/fonts/ChevySans-NarrowRegular.woff2` | `assets/fonts/ChevySans-NarrowRegular.woff2` |

`assets/css/fonts.css` is theme-authored: it holds the three `@font-face` rules
extracted from the DS `tokens/typography.css`, with `url()`s rewritten from the
DS-relative `../assets/fonts/` to the theme-relative `../fonts/`. Weight-range
mapping is unchanged: ChevySans Regular = 100–549, Demi = 550–900, Narrow =
100–900; Inter remains the fallback stack (`--font-fallback`).

### Homepage assets

Vendored from `/Users/seandm/Projects/27vette/export-page/stingray-homepage/assets/`
into `assets/homepage/`:

- `crossflags-white.png`, `wordmark-white.png` — logo lockup.
- `spin/<paint>/*.png` — canonical theme-vendored fallback frames, five paints ×
  30 frames.
- `spin/<paint>-webp/*-cmp.webp` — 1500×750 transparent delivery derivatives,
  generated from the matching PNG stems. The viewer requests WebP first, retries
  the matching PNG once on failure, caps loading at four concurrent requests,
  and loads only one static frame for reduced-motion or Save-Data users.

### Calculator assets

Vendored from `/Users/seandm/Projects/Stingcalc/` into `assets/calculator/`:

- `script.js`, `qp-new.js` — calculator logic copied verbatim from the Stingcalc
  source (`script.js` and `qp/qp-new.js`).
- `calculator.css` — theme-authored DS skin replacing the source `styles.css` and
  `qp/qp.css` while preserving the JS selector contracts used by the vendored
  scripts.

## How to re-sync when the DS changes

1. Re-copy the five CSS files from the DS paths in the table above.
2. Re-apply the two deltas: strip the `@font-face` block from
   `tokens/typography.css` (diff it against `assets/css/fonts.css` in case the
   DS changed weights/files) and strip the `@import` lines from `styles.css`.
3. Re-copy the three woff2 files if they changed.
4. If `_ds_manifest.json` → `globalCssPaths` changed order or membership,
   mirror that in `stingray_corvette_enqueue_styles()` in `functions.php`.
5. Bump `Version:` in `style.css` (it cache-busts every enqueued file).

## Theme-authored styles (not from the DS)

- `assets/css/theme.css` — shared chrome (topbar, drawer, buttons, footer,
  interior-page shell), ported from the homepage's inline CSS onto DS tokens.
- `assets/css/surfaces.css` — interior-page component vocabulary owned by PHP
  templates: page headers, panel cards, link cards, step lists, pills,
  accordions, `.sc-embed`.
- `assets/css/prose.css` — maps DS tokens onto plain wp-admin editor output
  (headings, paragraphs, lists, tables, buttons, images) for the `.sc-prose`
  region. Enqueued globally, after `surfaces.css`. See "Editable content
  regions" below.
- `assets/homepage/homepage.css` — homepage-only layer (hero, spin viewer,
  quick-action cards), same provenance.
- `assets/calculator/calculator.css` — calculator-only DS skin for the vendored
  Stingcalc markup.

## Editable content regions (hybrid scaffolding)

Every surface template keeps its page chrome (hero, eyebrow, title),
`.sc-embed` mount points, and composed widgets (step lists, card grids,
accordions) as hard-coded PHP — that hierarchy is not wp-admin editable by
design. Each surface additionally calls
`stingray_corvette_render_editable_notes()` (defined in `functions.php`) once,
near the end of its content, which prints the bound WordPress page's normal
`the_content()` inside a `.sc-prose`-wrapped `<section>` titled "Additional
Information." The block renders nothing when that page's content field is
empty, so nothing changes on a surface until an editor adds copy in wp-admin.

`page.php` and `index.php` (generic content pages with no dedicated template)
already render their entire body from `the_content()`; their wrapper carries
both `sc-page-content` (existing width/spacing) and `sc-prose` (the new
heading/table/button/image mapping).

`front-page.php` (full-bleed, no `get_header()`/`get_footer()`) and
`page-order.php` (dormant rollback material) are intentionally excluded from
this rollout; see their own header comments for why.

## Surfaces

- `front-page.php` — the ported marketing homepage (full-bleed, own document,
  shares the topbar/footer partials in `inc/`).
- `/order/` — temporary redirect to the canonical 27vette runtime at
  `https://order.stingraychevroletcorvette.com/`; `page-order.php` and
  `assets/order/` remain dormant rollback material and are not maintained as a
  second order-form runtime.
- `page-calculator.php` — mounted payment calculator at `/calculator/`, using
  the vendored Stingcalc logic in `assets/calculator/`.
- `page-deposit.php` — DS-styled deposit instructions and Formidable form embed
  at `/deposit/` inside `.sc-embed`.
- `page-build-and-price.php` — configurator links, build-code guidance, and
  Formidable link-share embed at `/build-and-price/`.
- `page-factory.php` — DS-styled Orders @ Factory explainer and wpDataTables
  embed at `/factory/`.
- `page-process.php` — DS-styled Corvette order process guide at `/process/`,
  rebuilt from the live policy/process page content.
- `page.php` — generic DS-styled interior wrapper for pages without a dedicated
  template yet.
- All planned dedicated public surface templates are present. Theme `0.1.16` at
  commit `a60a10c` passed version-aligned desktop/mobile staging QA; production
  upload and activation remain pending.

### Order form ownership

The 27vette repository is the only source of truth for order-form markup, data,
behavior, styling, vehicle renders, Turnstile, and dealer submission. Its
`main` branch deploys `order.stingraychevroletcorvette.com`. Keep theme links on
the local `/order/` slug; WordPress redirects that route to the canonical app so
27vette releases do not require a theme sync or WordPress deployment.

### Embed shortcode configuration

The plugin-backed surfaces read their embed from the page custom field
`stingray_embed_shortcode`. Store the complete shortcode there so staging and
production can use different plugin IDs without editing the theme.

Suggested current values:

- `/deposit/`: `[formidable id=8]`
- `/build-and-price/`: `[formidable id=30]`
- `/factory/`: `[wpdatatable id=12 table_view=regular]`
