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
- `spin/<paint>/*.png` — the 360° frame sets, all five paints × 30 frames
  (`gkz-red`, `gbk-yellow`, `gtr-blue`, `g26-orange`, `g4z-green`), vendored
  per the build brief so the theme is self-contained. Only the currently
  selected paint's set loads on page view; the next paint's set loads
  on demand as rotation approaches a color change (see `assets/homepage/spin.js`).

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
- `assets/homepage/homepage.css` — homepage-only layer (hero, spin viewer,
  quick-action cards), same provenance.
- `assets/calculator/calculator.css` — calculator-only DS skin for the vendored
  Stingcalc markup.

## Surfaces

- `front-page.php` — the ported marketing homepage (full-bleed, own document,
  shares the topbar/footer partials in `inc/`).
- `page-order.php` — mounted 2027 Corvette order form at `/order/`, using the
  vendored form-app assets in `assets/order/`.
- `page-calculator.php` — mounted payment calculator at `/calculator/`, using
  the vendored Stingcalc logic in `assets/calculator/`.
- `page.php` — generic DS-styled interior wrapper for pages without a dedicated
  template yet.
- Planned dedicated templates still remaining: `/deposit/`,
  `/build-and-price/`, `/factory/`, and `/process/`.
