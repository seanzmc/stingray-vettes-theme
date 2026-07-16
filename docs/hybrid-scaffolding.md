# Hybrid Scaffolding — templates own layout, pages own copy

Since v0.1.25 every surface template renders one editable notes region from
the WordPress page editor, in addition to its hardcoded chrome. (README.md
§ prose covers the vendoring/structure notes; this file is the working
guide.)

## Ownership boundaries

| Owner | What it owns |
| --- | --- |
| PHP templates | Page chrome (eyebrow, `.sc-page-title`, lede), `.sc-embed` mount points, composed widgets (step lists, card grids, accordions, link cards), section order |
| WordPress page content | One free-form copy region per page — notes, announcements, extra paragraphs, tables, images, buttons |
| CSS (`assets/css/prose.css`) | How editor output looks: headings, lists, tables, buttons, images map to DS tokens. Spacing/hierarchy stay theme-controlled |

Embeds are unchanged: Formidable / wpDataTables shortcodes still live in the
`stingray_embed_shortcode` page custom field, rendered inside `.sc-embed`.

## How it works

- `stingray_corvette_render_editable_notes()` (functions.php) renders the
  bound page's `the_content()` inside a `.sc-prose` section titled
  "Additional Information" (title/heading id overridable via `$args`).
  Empty page content → renders nothing → the surface is unchanged.
- `assets/css/prose.css` is enqueued site-wide after `surfaces.css` (handle
  `stingray-prose`) and styles only elements inside `.sc-prose`.
- Region placement (one per page, at the END of the page body):
  - `page-deposit.php`, `page-build-and-price.php`, `page-factory.php`,
    `page-process.php`, `page-calculator.php` — last block inside
    `.sc-page-inner`, after the final template section.
  - `front-page.php` — between the external-destinations section and the
    footer, inside a `.sc-page-inner` gutter wrapper; hidden entirely while
    the front page's content is empty.
  - `page.php` / `index.php` — the whole body is the prose region
    (`.sc-page-content.sc-prose`), so legacy pages pick up the full skin.
- `page-order.php` is dormant rollback material (see AGENTS.md) and has no
  region.

## Editing guidance (wp-admin)

- The section renders under an automatic "Additional Information" heading —
  start your own headings at **H2** for subsections within it.
- Paragraphs, lists, quotes, tables, separators, images, and the Buttons
  block are all skinned by prose.css.
- Don't paste embed shortcodes into the content region; those stay in the
  `stingray_embed_shortcode` custom field.

## Changing things later

- Copy inside the notes region: wp-admin page editor, publish. No deploy.
- Template-owned copy, placement, or styling: theme file change + version
  bump in `style.css` + deploy, as usual.
