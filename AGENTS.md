# Stingray Vettes Theme Agent Notes

## Project Shape

This repo is the theme root for the custom classic WordPress theme deployed as
`/wp-content/themes/stingray-corvette/` on the WordPress.com Business site
`stingraychevroletcorvette.com`.

There is no build step. Work in PHP templates, vanilla CSS, and vanilla JS.
Keep the theme compatible with the PHP requirement declared in `style.css`.

Primary references:

- `README.md` documents what was vendored and how to re-sync it.
- `THEME_BUILD_BRIEF.md` is the original foundation/homepage brief.
- `SiteWireframe.md` defines the intended public surfaces and slugs.
- `docs/superpowers/plans/2026-07-03-surface-integration.md` captures the
  multi-surface integration plan and design constraints.

## Source of Truth Boundaries

The Stingray Corvette Design System is canonical in
`/Users/seandm/Projects/27vette/export-page/StingrayCorvetteDesignSystem_v1/`.
This theme vendors a copy. Do not treat `assets/css/tokens/*`,
`assets/css/styles.css`, or `assets/fonts/*` as the canonical source. If the
design system changes, re-copy intentionally, preserve the documented deltas in
`README.md`, and mirror any manifest load-order change in `functions.php`.

Order-form assets under `assets/order/` are vendored from the 27vette form app.
Do not alter pricing, RPO rules, generated data, dependencies, or submission
logic in this theme unless the task explicitly asks for that and the source of
truth has been identified.

Calculator assets under `assets/calculator/` are vendored from Stingcalc logic
with a theme-authored skin. Preserve JS selector contracts when changing
calculator markup or styles.

## Working Rules

Follow Sean's spec-first mode for non-trivial work: diagnose first, name exact
files to inspect and change, repeat constraints, list risks/non-goals, and wait
for approval before editing. Keep passes small and reversible.

Default constraints unless the user overrides them:

- Do not deploy, SFTP, call WordPress APIs, or modify the live site.
- Do not add dependencies or introduce a build system.
- Do not refactor unrelated code.
- Preserve the existing visual direction unless the task is explicitly visual.
- Do not overwrite user or untracked work. Check `git status --short` before
  edits and call out unrelated local changes.
- Keep public links on the planned local slugs: `/order/`, `/deposit/`,
  `/build-and-price/`, `/calculator/`, `/factory/`, and `/process/`.

## Theme Architecture

`functions.php` owns theme supports, menu registration, and enqueue order.
The global style order is intentional:

1. `assets/css/fonts.css`
2. Google Inter fallback
3. `assets/css/tokens/colors.css`
4. `assets/css/tokens/typography.css`
5. `assets/css/tokens/spacing.css`
6. `assets/css/tokens/base.css`
7. `assets/css/styles.css`
8. `assets/css/theme.css`
9. `assets/css/surfaces.css`
10. conditional per-surface styles/scripts

`front-page.php` is full-bleed and renders its own document while sharing the
topbar and footer partials. Interior page templates should normally use
`get_header()` and `get_footer()`.

Shared chrome lives in `inc/topbar.php` and `inc/site-footer.php`. Avoid
duplicating nav/footer markup inside surface templates.

Use WordPress APIs for URLs and escaping: `home_url()`,
`get_template_directory_uri()`, `esc_url()`, `esc_html()`, `esc_attr()`, and
the `stingray-corvette` text domain for translatable strings.

## Visual Constraints

Use design-system tokens and existing theme classes before adding new styling.
Respect the established dark carbon surface language, ChevySans typography, and
Torch Red accent system.

Preserve these interaction invariants unless the user explicitly approves a
change:

- Selection is outline-only, not filled.
- Hover lift uses a hard offset shadow, not blur or scale.
- Transitions are short, about 140ms ease.
- Hit targets should stay at least 42px.
- Keep ARIA attributes and visible focus states.
- Avoid emoji and new icon libraries.

Do not edit vendored design-system files casually. Prefer small theme-authored
CSS layers such as `assets/css/theme.css`, `assets/css/surfaces.css`,
`assets/css/embeds.css`, or a surface-specific stylesheet.

## Validation

For PHP changes, lint the touched PHP files with `php -l`; for broader template
work, lint all root and `inc/` PHP templates.

For enqueue or asset-path changes, verify that generated URLs are absolute theme
URLs and that the affected page has no obvious missing CSS, JS, image, or font
assets. Bump `Version:` in `style.css` when cache-busting is needed.

For homepage or 360-viewer changes, manually verify the canvas loads and spins,
the current paint frame set lazy-loads correctly, and mobile/desktop layouts do
not overlap.

For order-form or calculator changes, preserve existing JS contracts and perform
manual browser checks for the primary flow before reporting done.

Every handoff should state:

- What changed and behavior impact.
- What did not change, including live-site/deployment status.
- Gate results or why a gate was not run.
- Manual verification still pending, residual risk, and follow-up work.
