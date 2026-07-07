# Six-Surface Theme Integration Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Integrate the six remaining surfaces (Order form, Calculator, Deposit, B&P share, @Factory, Process guide) into the `stingray-vettes-theme` classic WordPress theme, all on the Stingray Corvette design system.

**Architecture:** Two source repos feed the theme. (1) `~/Projects/27vette/form-app/` gets the form-style-1a "Full Carbon" restyle in place (per SPEC.md, normative reference `1a-reference.html`), then is vendored into the theme under `assets/order/` and mounted by `page-order.php`. (2) `~/Projects/Stingcalc/` is vendored (JS verbatim, markup ported, CSS rewritten onto DS tokens) into `assets/calculator/` + `page-calculator.php`. The four WP-hosted surfaces become `page-{slug}.php` templates: deposit and B&P embed the existing Formidable forms (ids 8 and 30) via shortcode, @Factory embeds `[wpdatatable id=7]`, and the Process guide is rebuilt as DS-styled markup. Two theme-authored stylesheets carry the shared surface styling: `assets/css/surfaces.css` (page components: link cards, accordions, status pills, step lists) and `assets/css/embeds.css` (dark skins for Formidable + wpDataTables).

**Tech Stack:** Classic WP theme (PHP templates), vanilla CSS on DS custom properties, vanilla JS. No build step. WP.com Business (Atomic) target; templates auto-bind by slug (`page-order.php` ↔ `/order/`).

## Global Constraints

- Design system invariants (SPEC §12): selection = outline (2px accent, 3px offset), never fill; hover lift = hard offset shadow `6px 6px 0 rgba(38,44,49,.95)`, never blur/scale; transitions 140ms ease; honor `prefers-reduced-motion`; Inter only, weights 650–900; letter-spacing 0 except uppercase runs; `Included` never `$0`; stroked SVGs + unicode glyphs only, no icon libraries/emoji; hit targets ≥ 42px; keep all aria attributes and focus visibility; never `--accent-dark` text on `--bg`.
- Order form restyle scope (SPEC): only `form-app/styles.css` + small hooks in `index.html`/`app.js`. **No changes** to configurator logic, pricing, RPO rules, `data.js`, or submission flow. When SPEC and `1a-reference.html` disagree, the reference wins.
- Theme token names come from `assets/css/tokens/colors.css` (`--accent-deep`, not `--accent-dark`; the form app keeps its own `--accent-dark` name per SPEC).
- Theme load order is normative (functions.php): fonts → colors → typography → spacing → base → styles → theme.css → (new) surfaces.css → per-page CSS.
- Calculator/order logic JS is copied verbatim — restyle only.
- All new templates use the shared topbar/footer partials (`inc/topbar.php`, `inc/site-footer.php`) via `get_header()`/`get_footer()` or direct `get_template_part`.
- Homepage already routes to `/order/`, `/deposit/`, `/process/`, `/build-and-price/`, `/calculator/`, `/factory/` — templates must bind to exactly those slugs.

---

### Task 1: form-style-1a restyle of the order form (in `~/Projects/27vette/form-app/`)

**Files:**
- Modify: `~/Projects/27vette/form-app/styles.css` (full re-theme)
- Modify: `~/Projects/27vette/form-app/index.html` (accent stripe + brand block hooks)
- Modify: `~/Projects/27vette/form-app/app.js` (accent-follows-paint hook + vehicle stage)
- Create: `~/Projects/27vette/form-app/assets/logo/{crossflags-white.png,stingray-wordmark-white.png}` (copy from `export-page/form-style-1a/reference/assets/logo/`)
- Create: `~/Projects/27vette/form-app/assets/vehicle/{gkz,g8g,gba,gec,gka,g4z,gtr,gbk,g26,gph}.png` (copy from `export-page/form-style-1a/reference/assets/vehicle/`)

**Interfaces:**
- Produces: restyled app whose files Task 2 vendors verbatim into the theme. app.js gains `applyAccentForPaint()` (reads selected paint RPO, sets `--accent/--accent-dark/--on-accent/--accent-glow` on `document.documentElement.style`) and `renderVehicleStage()` (returns the `.vehicle-stage` HTML for the paint step). `window.SC_FORM_ASSET_BASE` (optional global, default `"./assets/"`) prefixes vehicle/logo asset URLs so the theme mount can point at its own copy.

- [ ] **Step 1: Copy assets** — `mkdir -p form-app/assets/{logo,vehicle}` and copy the ten vehicle renders + two logos from `export-page/form-style-1a/reference/assets/`.
- [ ] **Step 2: styles.css §1 token remap** — replace `:root` per SPEC §1 (bg #0c0e0f, panel #101315, panel-strong #14181b, ink #f4f1eb, muted #a9aeb1, +dim #8b9094, line #262c31, +hairline(.07)/+hairline-strong(.12), accent #d0425a, accent-dark #b22234, +on-accent #fff, +accent-glow rgba(208,66,90,.40), ok #35b85d, +ok-bg, warn #e8a13c, disabled #5c6166, choice-bg #14181b, choice-hover #181d20, choice-hover-shadow 6px 6px 0 rgba(38,44,49,.95), control-hover rgba(255,255,255,.10), shadow 0 18px 48px rgba(0,0,0,.55), shell-radius 12px), `color-scheme: dark`, `body{background:#33373a}`.
- [ ] **Step 3: shell/topbar/buttons/rail/panel (SPEC §3–6)** — app-shell border+radius 12+overflow hidden+bg var(--bg); `.accent-stripe` rule; topbar rgba(11,13,14,.92) compact (h1 21px/850/uppercase, eyebrow 11px/.14em accent); primary buttons accent fill + glow + uppercase 12.5px/800, ghost = 1px rgba(255,255,255,.24) border, icon buttons 44px control-bg; step rail per §5 (upcoming dim, complete ✓ ok-bg, active hairline-strong border + accent); choice panel carbon checker texture per §6; step-header h2 30px/850 uppercase, step-meta 12px/800/.12em dim.
- [ ] **Step 4: cards + summary (SPEC §8, §10)** — choice cards 10px radius, hover border #3a4248 + hard shadow only; selected outline accent; media 3:1 swatch ~42px with white sheen gradient handled by app (existing inline bg); price accent / `Included` muted; status pill transparent→accent-filled when selected; summary panel/cards per §10 incl. grand-total row 24px accent, summary-rpo-code transparent pill w/ accent border.
- [ ] **Step 5: everything-else sweep (SPEC §11 + judgment)** — inputs/selects/textareas panel-strong + accent-glow focus ring; alerts warn-tinted; modals panel bg + var(--shadow), backdrop rgba(0,0,0,.6); tooltips keep #172026 + add 1px rgba(255,255,255,.16) border; interior groups / disclosures / relation groups → rgba(255,255,255,.03–.06) fills + hairline borders + inset 0 1px 4px rgba(0,0,0,.4); mobile drawers panel surfaces + rgba(0,0,0,.55) backdrop; mobile summary bar = summary-card styling, total accent. Judgment items not in reference (record for report): toasts, toolbar `Build Summary` drawer button, model picker, vehicle-setup stepper/chips/highlight/facts/equipment disclosure, choice-relation groups/badges, standard-equipment groups, customer form, dealer-submit status colors, mobile progress bar.
- [ ] **Step 6: index.html hooks** — insert `<div class="accent-stripe"></div>` as first child of `.app-shell`; insert brand block (crossflags 26px + wordmark 30px + divider) before the title div in `.topbar`.
- [ ] **Step 7: app.js hooks** — add `PAINT_ACCENTS` map (SPEC §2 table) + `applyAccentForPaint()` called from `render()`; add vehicle stage to the paint step in `renderStepContent()` (uses `assetBase() + 'vehicle/' + rpo.toLowerCase() + '.png'`, pill shows RPO in accent + name); guard with `ACCENT_FOLLOWS_PAINT = true`.
- [ ] **Step 8: QA** — open `form-app/index.html` in browser at 1440px + 390px; click through paints (accent re-themes, stage swaps), steps, summary drawer, submit modal; compare against `1a-reference.html`. Commit in 27vette repo.

### Task 2: mount the order form in the theme (`/order/`)

**Files:**
- Create: `page-order.php` (full-bleed document like front-page.php, theme topbar + form shell + footer)
- Create: `assets/order/{order.css,app.js,data.js}` (vendored from restyled form-app; order.css = styles.css minus `body` desk rule, scoped adjustments for embedding)
- Create: `assets/order/assets/{logo,vehicle}/*` (vendored)
- Modify: `functions.php` (conditional enqueue on `is_page('order')`, Turnstile script, `SC_FORM_ASSET_BASE` inline)
- Modify: `README.md` (vendoring provenance table row)

**Interfaces:**
- Consumes: Task 1 outputs verbatim. Theme topbar carries brand → the in-form brand block is omitted in the mounted markup (the standalone form keeps it).

- [ ] Step 1: vendor files; Step 2: `page-order.php` with form-app `<body>` markup (shell + modals) inside theme chrome; Step 3: enqueue hook + `php -l`; Step 4: commit.

### Task 3: calculator (`/calculator/`)

**Files:**
- Create: `page-calculator.php` (markup ported from Stingcalc index.html body, DS classes)
- Create: `assets/calculator/{calculator.css,script.js,qp-new.js}` (JS verbatim from `~/Projects/Stingcalc/`; calculator.css theme-authored replacing styles.css+qp/qp.css on DS tokens: tabs → segmented control w/ accent active state, forms → panel-strong cards, results → ok-tinted card, disclosure → dim footnote)
- Modify: `functions.php`, `README.md`

- [x] Step 1: vendor JS; Step 2: write calculator.css (keep every selector contract script.js/qp-new.js relies on: `.tab-btn.active`, `.tab-pane.active`, `.calculator-pane.active`, `.result.hidden`, `.qp-row`, `#qp-results`, `.hidden`); Step 3: template + enqueue + `php -l`; Step 4: static/static-contract QA (PHP lint, JS syntax, selector audit) plus static browser harness QA. WordPress preview QA and commit remain pending until run/approved.

### Task 4: shared surface + embed stylesheets

**Files:**
- Create: `assets/css/surfaces.css` (interior-page components: `.sc-panel-card`, `.sc-link-card` grid, `.sc-step-list`, `.sc-accordion` (details/summary), `.sc-pill` status pills, `.sc-note`, `.sc-embed-figure`)
- Create: `assets/css/embeds.css` (dark skin for Formidable `.frm_forms` — fields, labels, radios/checkboxes, file dropzone, submit button = DS primary, validation/error states; dark skin for wpDataTables — table surface panel-strong, header row panel, row hover, search/filter inputs, pagination, popup/modal row detail)
- Modify: `functions.php` (enqueue surfaces.css globally after theme.css; embeds.css on deposit/build-and-price/factory)

- [ ] Step 1: surfaces.css; Step 2: embeds.css; Step 3: enqueue + `php -l`; Step 4: commit.

### Task 5: deposit page (`/deposit/`)

**Files:** Create `page-deposit.php`.
- Hero header (eyebrow "Reserve Your Allocation" / title "Deposit Form"), DS step-list of the 6 payment-portal instructions (incl. the RO/Stock# = "C8 Deposit", Department = "SALES" callout as a warn note), link card to the process guide, then `<?php echo do_shortcode( '[formidable id=8]' ); ?>` inside `.sc-embed`.
- [x] Step 1: template; Step 2: `php -l`; Step 3: commit pending.

### Task 6: B&P link-share page (`/build-and-price/`)

**Files:** Create `page-build-and-price.php`.
- Four configurator link cards (Stingray/E-Ray/Z06/ZR1 → chevrolet.com configurator URLs, external-arrow style like footer `.sc-ext-card`), "how to find your build code" explainer with the two screenshots (existing media URLs), then `[formidable id=30]`.
- [ ] Step 1: template; Step 2: `php -l`; Step 3: commit.

### Task 7: @Factory page (`/factory/`)

**Files:** Create `page-factory.php`.
- Intro (tracking copy, four bullet notes incl. Learning Center link, contact block), "How to read the table" panel, status-code accordion (native `<details>` per code 1100→6000 + "other codes", styled by `.sc-accordion`), then `[wpdatatable id=7 table_view=regular]`.
- [ ] Step 1: template; Step 2: `php -l`; Step 3: commit.

### Task 8: process guide rebuild (`/process/`)

**Files:** Create `page-process.php`.
- Full content rebuilt from live page: deposit lists status grid (cards with `.sc-pill` Open/Closed/Discontinued), deposit details by model, policies, order process, pricing, fees, payment options, delivery, restrictions, staying informed. All DS-typeset; `<hr>` → hairline dividers.
- [ ] Step 1: template; Step 2: `php -l`; Step 3: commit.

### Task 9: QA + report

- [ ] Browser QA: order form standalone + a static preview of calculator; lint all PHP; check enqueue graph.
- [ ] Final report: list every judgment-call style element (styled without explicit SPEC/DS coverage) with location.
