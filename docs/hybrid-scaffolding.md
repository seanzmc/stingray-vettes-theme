# Hybrid Scaffolding — templates own layout, pages own copy

Since v0.1.24 every interior surface template renders one editable prose
region from the WordPress page editor, in addition to its hardcoded chrome.

## Ownership boundaries

| Owner | What it owns |
| --- | --- |
| PHP templates | Page chrome (eyebrow, `.sc-page-title`, lede), `.sc-embed` mount points, composed widgets (step lists, card grids, accordions, link cards), section order |
| WordPress page content | One free-form copy region per page — notes, announcements, extra paragraphs, tables, images, buttons |
| CSS (`assets/css/prose.css`) | How editor output looks: headings, lists, tables, buttons, images map to DS tokens. Spacing/hierarchy stay theme-controlled |

Embeds are unchanged: Formidable / wpDataTables shortcodes still live in the
`stingray_embed_shortcode` page custom field, rendered inside `.sc-embed`.

## How it works

- `stingray_corvette_render_page_prose()` (functions.php) runs the loop,
  renders `the_content()` through the normal filters, and wraps it in
  `<div class="sc-prose">`. Empty page content → empty string → the surface
  renders exactly as before.
- `assets/css/prose.css` is enqueued globally after `surfaces.css` (handle
  `stingray-prose`) and styles only elements inside `.sc-prose`.
- Region placement per template (one region each):
  - `page-deposit.php`, `page-build-and-price.php`, `page-factory.php`,
    `page-process.php`, `page-calculator.php` — directly after the page
    header, before the first structural section.
  - `front-page.php` — after the external-destinations section, before the
    footer; hidden entirely while the front page's content is empty.
  - `page.php` / `index.php` — the whole body is the prose region
    (`.sc-page-content.sc-prose`), so legacy pages pick up the full skin.
- `page-order.php` is dormant rollback material (see AGENTS.md) and was
  intentionally not touched.

## Editing guidance (wp-admin)

- Start headings at **H2** — H1 belongs to the template title. An H2 renders
  like a template section title, accent bar included.
- Paragraphs, lists, quotes, tables, separators, images, and the Buttons
  block (default = primary red, outline style = ghost) are all skinned.
- Don't paste embed shortcodes into the content region; those stay in the
  `stingray_embed_shortcode` custom field.

## Rollout checklist for a live site

Before deploying, check each mapped page's content in wp-admin
(Deposit, Build & Price, Factory, Process, Calculator, front page):
leftover legacy content in the editor will start rendering in the new
prose region. Clear it or keep it deliberately.
