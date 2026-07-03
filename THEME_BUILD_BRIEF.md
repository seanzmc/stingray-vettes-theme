# Stingray Corvette WordPress Theme — Build Brief

**Pass 1 of several: theme foundation + homepage port.**
For: Claude Code, running locally on the MacBook. Work in small, reviewable commits.

## Decisions (resolved — proceed, don't ask)

- **Font:** adopt ChevySans now, including on the hero. Adjust hero type sizing/line-height as needed so it reads well at the 0.28em display tracking — don't just drop the font in and leave broken metrics. Use judgment, matching the DS type scale (`--text-display`, `--tracking-display`, `--leading-display`).
- **Spin frames:** vendor them into the theme (`assets/homepage/spin/<paint>/`), not left pointing at the existing `/wp-content/stingray-homepage/assets/spin/` path. There's no cross-origin performance difference either way (same domain) — this is purely about the theme being self-contained rather than depending on a folder outside itself. To manage the resulting file weight (multiple paints × ~30 frames each), **lazy-load only the currently-selected paint's frame set**; don't eager-load every paint's frames on page load.

---

## What we're building

A standalone **classic** WordPress theme (no parent theme) for `stingraychevroletcorvette.com`, hosted on WordPress.com Business. It replaces the current Elementor build. The theme's global style layer *is* the Stingray Corvette Design System.

This pass does three things and nothing else:
1. Scaffold the theme foundation.
2. Wire the design system (tokens + fonts) in as the global style layer.
3. Port the existing static homepage to `front-page.php`.

The order form and the other content pages are **later passes** (see "Out of scope").

---

## Inputs — read-only source of truth

All under `/Users/seandm/Projects/27vette/export-page/`:

- **`StingrayCorvetteDesignSystem_v1/`** — the design system. **Read `SKILL.md` and `readme.md` first.**
  - Global CSS layer, in this **exact load order** (from `_ds_manifest.json` → `globalCssPaths`): `tokens/colors.css`, `tokens/typography.css`, `tokens/spacing.css`, `tokens/base.css`, `styles.css`.
  - Brand fonts (woff2) in `assets/fonts/`: `ChevySans-Regular.woff2`, `ChevySans-Demi.woff2`, `ChevySans-NarrowRegular.woff2`.
  - HTML reference recreations: `ui_kits/homepage/index.html`, `ui_kits/order-form/index.html`; per-component HTML in `components/**/*.card.html`; specimens in `guidelines/`.
  - The `components/**/*.jsx` files are **React, reference only** — do not import them. This is a PHP/classic theme.
- **`stingray-homepage/index.html`** — the live homepage (inline CSS + vanilla-JS 360° spin viewer). Assets in `stingray-homepage/assets/` (`crossflags-white.png`, `wordmark-white.png`, `spin/<paint>/*.png`).
- **`form-style-1a/`** — order-form restyle spec (`SPEC.md`) + normative reference (`reference/1a-reference.html`). **For a later pass.** Do not implement the order form now.
- **`SiteWireframe.md`** — the 7-surface site plan. Use item 1 (Home) for the homepage link fixes.

---

## Target

Git repo at **`/Users/seandm/Projects/stingray-vettes-theme/`** (already created). Repo root = the theme folder (maps 1:1 to `/wp-content/themes/stingray-corvette/` on deploy). **Do not create or duplicate anything inside the 27vette repo** — `27vette/export-page/` is read-only source for this brief, referenced by absolute path; nothing gets copied into it or out of it into this repo except the specific vendored files listed in Task 2.

Suggested structure:

```
stingray-vettes-theme/
  style.css          # theme header comment
  functions.php      # enqueues, theme supports, menu registration
  index.php          # required fallback template
  front-page.php     # the ported homepage
  page.php           # generic interior page (base DS styling wrapper)
  header.php         # shared <head> + wp_head()
  footer.php         # shared close + wp_footer()
  assets/
    css/             # vendored DS: tokens/*.css, styles.css, base.css
    fonts/           # vendored woff2 (3 files)
    homepage/        # homepage CSS/JS + assets (or reference existing live paths)
  inc/               # optional PHP partials
  README.md          # what was vendored from the DS + how to re-sync
```

---

## Tasks (this pass only)

1. **Scaffold** `style.css` (Theme Name: Stingray Corvette, plus standard header fields), `index.php`, `functions.php`, `header.php`, `footer.php`, `page.php`.
2. **Vendor** the 5 global CSS files (in load order) and the 3 woff2 fonts into `assets/`. In `README.md`, list exactly which files were copied and from where, so the DS stays canonical in 27vette and this can be re-synced when it changes.
3. **`@font-face` ChevySans** in the vendored typography layer (or a small `fonts.css`), mapping weight ranges onto the two real cuts: Regular = 100–549, Demi = 550–900, plus ChevySans Narrow. Keep Inter as the fallback stack (already defined as `--font-fallback`).
4. **Enqueue** the global CSS layer via `wp_enqueue_style` on `wp_enqueue_scripts`, in the manifest's exact order, site-wide. Fonts enqueue first. Add `add_theme_support` for `title-tag`, `post-thumbnails`, `html5`; register a primary nav menu.
5. **Port the homepage → `front-page.php`:**
   - Bring over the homepage markup, its inline CSS, and the vanilla-JS 360° viewer.
   - **All asset references must be absolute.** Image assets already are; verify the CSS/JS links and the spin-frame paths. A relative path will break when this renders at `/` instead of at the file's own directory. Either point spin frames at the existing live location (`/wp-content/stingray-homepage/assets/spin/...`, which already hosts every paint's full frame set) or vendor them — pick one and document it.
   - **Fix the homepage's internal nav links** to the new local WordPress pages (Order, Deposit, B&P, Calculator, @Factory, Process), per `SiteWireframe.md` item 1. Where a page doesn't exist yet, link to its intended slug (`/order/`, `/deposit/`, etc.) so routes are ready.
   - Render the homepage **full-bleed**: do not wrap it in generic theme chrome that would inject a duplicate nav/footer. Use the homepage's own markup; only include `wp_head()` and `wp_footer()` hooks inside the document (needed for enqueues and the admin bar).
   - **Font:** the homepage predates the DS extraction and its inline CSS currently renders in Inter with hardcoded values. Per the resolved decision above, adopt ChevySans on the hero (and everywhere else on the page) now. This shifts the hero's type metrics under the 0.28em display tracking — adjust hero sizing/line-height to compensate rather than leaving it looking broken. QA the hero specifically at a few widths before calling this task done.

---

## Guardrails — do not break

- **Local only this pass.** Do not connect to, deploy to, or modify the live WordPress site, its active theme, or any content. No SFTP. No WordPress MCP calls.
- **Do not touch** `form-app/` logic, `data.js`, pricing, RPO rules, or the order-form submission flow. The order form is a later pass.
- **Preserve the 360° viewer exactly** — drag/scroll-driven vanilla JS. Honor `prefers-reduced-motion` (DS `base.css` already handles this).
- **Respect DS invariants** (readme "Visual Foundations" + `form-style-1a/SPEC.md` §12) for any shared components you build: selection is outline-only, never a fill; hover is a hard offset shadow, never blur/scale; 140ms ease transitions; ChevySans, no emoji; hit targets ≥ 42px; keep aria attributes and focus visibility.
- **DS stays canonical in 27vette.** The theme vendors a copy; it is not the source of truth.

---

## Validate before reporting done

- Theme has no PHP fatals; `style.css` header is valid; enqueues resolve in load order.
- `front-page.php` renders at `/` with fonts loading, the 360° viewer spinning, and no broken assets or 404 links.
- Asset paths are absolute; internal nav points at the new local slugs.
- **Report back:** the file tree created, exactly which DS files were vendored (for the re-sync note in README), how the hero type was adjusted for ChevySans, actual file count/weight of the vendored spin frames and how lazy-loading was implemented, and a checklist of remaining surfaces as next passes.

---

## Out of scope this pass (scaffold routes/slugs, build later)

Order form restyle + mount (has its own SPEC), the five content pages, Formidable embeds, the Stingcalc migration, the @Factory sheets table, and anything touching the live site.
