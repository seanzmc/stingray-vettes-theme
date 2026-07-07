# Factory Surface Completion Spec

Objective

Complete the `/factory/` surface in the Stingray Corvette WordPress theme by rebuilding the existing Orders in Production page on the shared design system and embedding the existing wpDataTables factory table.

Relevant files / areas

Already present and should be preserved unless verification proves otherwise:

- `functions.php`
  - Already conditionally enqueues `assets/css/embeds.css` on `is_page( array( 'deposit', 'build-and-price', 'factory' ) )`.
  - No enqueue change should be required for this pass.
- `assets/css/surfaces.css`
  - Already provides the page components this pass should use:
    - `.sc-page-eyebrow`
    - `.sc-page-lede`
    - `.sc-section`
    - `.sc-section-title`
    - `.sc-section-lede`
    - `.sc-grid`
    - `.sc-card-panel`
    - `.sc-link-card`
    - `.sc-note`
    - `.sc-accordion`
    - `.sc-code`
    - `.sc-fact-row`
- `assets/css/embeds.css`
  - Already includes wpDataTables dark-skin selectors.
  - May be patched only if real WordPress preview shows wpDataTables markup is not covered or readable.
- Create: `page-factory.php`
  - Missing at the time this spec was written.
  - This is the primary deliverable.
- Update after implementation:
  - `README.md`
  - `docs/superpowers/plans/2026-07-03-surface-integration.md`

Source/reference content

Use the existing live page as content reference:

- Live URL: `https://stingraychevroletcorvette.com/orders-in-production/`
- Planned local slug: `/factory/`
- Existing table shortcode: `[wpdatatable id=7 table_view=regular]`

Live-page copy and structure observed on 2026-07-06:

Intro headline:

- `Track Your 2026 Corvette Order Through The Production Process. Please Note, This Page Only Shows Orders Placed By Stingray Chevrolet In Plant City, FL.`

Important bullets:

- `All orders displayed below are at the factory and CANNOT be MODIFIED.`
- `Email or call your sales rep with questions regarding your order.`
- `When your order reaches 3800 status, contact us to get your VIN number.`
- `While you wait for your car to come in, head over to the Learning Center.`

How to read the table:

- Type your `Order #` into the search box.
- Click anywhere on the table row to see all details in a popup.
- Data is a snapshot extracted directly from GM’s Order Workbench.
- Every order listed in this table will be produced within the current model year; exact timing of production and delivery is unknown and dictated by the Corvette Factory.
- If you do not know your order number or have other questions, contact Stingray Chevrolet at `813-359-5000` or `assistance@stingraychevrolet.com`.

Status-code content:

- `1100: Preliminary Order`
  - When you place a preliminary order with Stingray Chevrolet, you receive a unique 6-character order number to track your Corvette as it is built. For each order cycle, orders are organized based on deposit date and special requirements.
- `2000: Order Placed`
  - When Stingray Chevrolet has an allocation slot available, the order can be placed for production scheduling. The order must pass all constraints for the current order cycle.
- `3000: Accepted by Production Control`
  - Order accepted by Production Control; the Corvette order is officially in the production queue.
- `3100: Available to Sequence`
  - Generally used as a placeholder to indicate the order is past initial order acceptance and a target production week will soon be posted. Not all orders will see this event status.
- `3300: Scheduled for Production`
  - The order is scheduled for production and a target production week (TPW) is posted.
- `3400: Broadcast for Production`
  - The factory is building the car at this stage.
- `3800: Produced`
  - The car has been built and is in final production stages. Quality control checks are completed and accounting documents are produced. A VIN is available; customers should contact Stingray to get it for insurance, accessories, etc. Museum Delivery (`R8C`) transactions may be completed at this stage so documentation is ready for pickup.
- `4200: Shipped`
  - The car is in transit to the dealership. Once Stingray gets the shipping notice, arrival usually takes 2–4 days. Jack Cooper Transport trucks Corvettes directly to Stingray; cars are not transported to Stingray by rail.
- `5000: Delivered to Dealer`
  - The car has been delivered to Stingray or the delivery location indicated at ordering. Corvettes go through pre-delivery inspection (PDI); components and dealer-installed options (LPO) are installed. The rep will set a final delivery appointment.
- `6000: Delivered to Customer`
  - Customer has the car. Point to Learning Center owner-manual resources.
- Other codes / abbreviations:
  - `TPW` = Target Production Week
  - `DFC` = Destination Freight Charge
  - `1YC` = Stingray
  - `1YH` = Z06
  - `1YG` = E-Ray
  - `07` = Coupe
  - `67` = Convertible
  - Example: `1YC07` = Stingray Coupe
  - `255691` = Stingray Chevrolet BAC code
  - `184590` = National Corvette Museum BAC code in Bowling Green, Kentucky

Context and constraints

- This theme has no build step. Use PHP templates, vanilla CSS, and vanilla JS only.
- Do not add dependencies.
- Do not deploy, SFTP, call WordPress APIs, or modify the live site in this pass.
- Preserve the planned public slug: `/factory/`.
- Use `get_header()` and `get_footer()` so the surface uses shared topbar/footer from `inc/topbar.php` and `inc/site-footer.php`.
- Use WordPress escaping APIs for URLs and text:
  - `home_url()`
  - `esc_url()`
  - `esc_html_e()` / `esc_html__()` / `esc_html()`
  - `esc_attr_e()` when needed
- Keep compatible with `Requires PHP: 7.4` in `style.css`.
- Preserve design-system invariants:
  - selection/focus = outline treatment, not filled where it would change meaning
  - hover lift = hard offset shadow, not blur/scale
  - transitions about 140ms ease
  - hit targets at least 42px
  - visible focus states
  - no emoji or new icon libraries
- Do not modify data-source, sheet, wpDataTables configuration, or table business logic.
- Do not connect this theme pass to the CarSales Cloudflare Worker or `sales.stingraychevroletcorvette.com`; that project/subdomain is separate and should not be attached to the public WordPress site.

Proposed approach

Build `page-factory.php` as a DS-styled explainer page with the existing wpDataTables shortcode near the bottom.

High-level structure:

1. Shared shell:
   - `get_header();`
   - `<main class="sc-page">`
   - `<div class="sc-page-inner">`
   - page header:
     - eyebrow: `Orders @ Factory`
     - title: `Orders in Production`
     - lede: concise customer-friendly version of the live-page headline
   - `get_footer();`

2. Intro / important notes section:
   - Section title: `Track your factory order`
   - `sc-grid` containing either one `sc-card-panel` with list items or several cards.
   - Include the four live-page bullets.
   - Use a `sc-note` for the strongest non-editability warning:
     - Orders shown here are already at the factory and cannot be modified.

3. How to read the table section:
   - Section title: `How to read the table`
   - Use `sc-card-panel` or `sc-step-list`.
   - Preserve the table-use instructions:
     - search by `Order #`
     - click a row for full detail popup
     - data is a GM Order Workbench snapshot
     - timing is factory-controlled
   - Add contact line with phone and email link:
     - phone text: `813-359-5000`
     - email link: `mailto:assistance@stingraychevrolet.com`
   - Add Learning Center link card:
     - URL: `home_url( '/learning-center/' )` unless a new local replacement exists
     - label: `Learning Center`

4. Status-code accordion section:
   - Section title: `Order status codes explained`
   - Use native `<details>` inside `.sc-accordion`.
   - One `<details>` per status code:
     - `1100`
     - `2000`
     - `3000`
     - `3100`
     - `3300`
     - `3400`
     - `3800`
     - `4200`
     - `5000`
     - `6000`
     - `Other codes`
   - Summary layout should use `.sc-code` for code chips and readable text for the label.
   - Do not use JS tabs or custom accordion scripts.

5. Table embed section:
   - Section title: `Factory order table`
   - Short lede: explain that rows are current factory orders from Stingray and can be searched by order number.
   - Wrap shortcode in `.sc-embed`:
     - `<?php echo do_shortcode( '[wpdatatable id=7 table_view=regular]' ); ?>`
   - Keep the table below the explainer so customers understand statuses before searching.

Implementation tasks

Task 1: Create `page-factory.php`

Files:

- Create: `page-factory.php`

Steps:

1. Read `page-deposit.php` and `page-build-and-price.php` for shell/style conventions.
2. Create `page-factory.php` with:
   - file docblock naming `/factory/`
   - `get_header();`
   - `main.sc-page > .sc-page-inner`
   - page header with `sc-page-eyebrow`, `sc-page-title`, and `sc-page-lede`
   - `get_footer();`
3. Add intro/notes section using existing surface classes.
4. Add how-to-read section and contact line.
5. Add native details/summary status-code accordion.
6. Add wpDataTables shortcode inside `.sc-embed`.

Acceptance criteria:

- `page-factory.php` exists.
- `/factory/` will bind by slug in WordPress.
- Shared topbar/footer render through existing theme chrome.
- Table shortcode is exactly:
  - `[wpdatatable id=7 table_view=regular]`
- No new CSS/JS dependencies are introduced.
- No live-site/data-source changes are made.

Task 2: Static content and contract audit

Files:

- Inspect: `page-factory.php`

Checks:

- Confirm the page includes:
  - `Orders @ Factory`
  - `Orders in Production`
  - `Order #`
  - `3800`
  - `6000`
  - `assistance@stingraychevrolet.com`
  - `[wpdatatable id=7 table_view=regular]`
- Confirm `.sc-embed` wraps the table shortcode.
- Confirm accordion uses native `<details>` and `.sc-accordion`.
- Confirm no `wpdatatable` shortcode appears outside `.sc-embed`.

Acceptance criteria:

- Static content audit passes.
- No missing required copy or shortcode contract is known.

Task 3: CSS integration check

Files:

- Inspect/possibly modify: `assets/css/embeds.css`
- Inspect/possibly modify: `assets/css/surfaces.css`

Steps:

1. Do not patch CSS before preview unless a static class mismatch is obvious.
2. Confirm `functions.php` already enqueues `stingray-embeds` for `factory`.
3. In WordPress preview, verify wpDataTables search/filter inputs, table header, rows, pagination, and detail popup are readable on the dark DS surface.
4. If wpDataTables markup is not covered, patch only `assets/css/embeds.css` with narrow selectors under `.sc-embed`.
5. Do not change Formidable styling while working on the Factory table unless it is part of the same selector family and harmless.

Acceptance criteria:

- Table embed is readable and usable in WordPress preview.
- Search box and pagination remain visible.
- Row hover/focus states are visible.
- Any popup/modal row detail remains readable.

Task 4: README and plan status update

Files:

- Modify: `README.md`
- Modify: `docs/superpowers/plans/2026-07-03-surface-integration.md`

Steps:

1. Update README `## Surfaces`:
   - add `page-factory.php` as implemented for `/factory/`
   - leave `/process/` as the remaining planned dedicated template
2. In the integration plan, mark Task 7 complete only after the template exists and `php -l` passes.
3. Do not mark Task 8 or Task 9 complete.

Acceptance criteria:

- README accurately describes factory status.
- Integration plan reflects only completed factory work.

Task 5: Verification pass

Commands:

- `php -l page-factory.php`
- `php -l functions.php`
- `git status --short`
- Optional static grep:
  - `grep -n "wpdatatable id=7\|sc-accordion\|assistance@stingraychevrolet.com" page-factory.php`

Manual WordPress preview checklist:

- Open `/factory/` in a local or WordPress preview environment.
- Confirm shared topbar/footer render once.
- Confirm the factory table renders via wpDataTables.
- Confirm no console errors related to wpDataTables assets.
- Confirm search by order number UI is visible.
- Confirm row-click detail behavior still works if the plugin/table supports it in preview.
- Confirm status-code accordion opens/closes with mouse and keyboard.
- Confirm desktop around 1440px is readable.
- Confirm mobile around 390px does not create unusable horizontal overflow outside the table itself. If the table must scroll horizontally, contain it inside the embed/table wrapper rather than letting the whole page overflow.

Acceptance criteria:

- PHP lint passes.
- Static shortcode/content audit passes.
- WordPress preview confirms the table renders and is usable.
- Any unverified browser/plugin checks are explicitly reported as pending; do not claim them complete if not run.

Commit recommendation

After verification, commit the Factory pass separately:

- Include:
  - `page-factory.php`
  - `README.md`
  - `docs/superpowers/plans/2026-07-03-surface-integration.md`
  - `assets/css/embeds.css` only if a real table styling fix was required
- Do not include unrelated files.

Suggested commit subject:

- `feat: add factory orders page`

Risks / assumptions

- The live page currently shows a `Service Unavailable` table error in the browser snapshot; this appears to be the existing wpDataTables/data service, not a theme-template concern. Do not try to solve the data-service problem in this pass unless explicitly asked.
- wpDataTables markup can only be fully verified in WordPress; a static harness cannot prove plugin behavior.
- The table is linked to external/sheet data through wpDataTables. This pass embeds the existing shortcode only and does not modify the data source.
- The live page references 2026/2025 in some copy. Use a year-light phrasing in the new template where possible to avoid stale text while preserving meaning.
- The status-code descriptions are customer-facing and should stay concise, but do not change the meaning of GM status codes.
- This pass does not deploy or replace the live site.

Non-goals

- Do not rebuild the wpDataTables table.
- Do not edit Google Sheets or other table data sources.
- Do not connect to or modify the CarSales Cloudflare Worker / `sales.stingraychevroletcorvette.com`.
- Do not build `/process/` in this pass.
- Do not change global design-system tokens.
- Do not introduce new libraries, build tooling, or JavaScript accordion behavior.
- Do not push or deploy.

Recommended reasoning level

Medium. The implementation is mostly a careful template/content port, but plugin/table rendering must be verified in WordPress because wpDataTables behavior cannot be proven statically.
