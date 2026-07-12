# Quick Pencil Alignment and Factory Column Visibility Design

## Goal

Align every Quick Pencil control in one consistent right-hand column and make
every wpDataTables column marked visible in the plugin appear in the Factory
table on the front end.

## Diagnosis

The Quick Pencil markup already has the correct label-and-control structure,
but `assets/calculator/calculator.css` limits the label track to 190 pixels and
lets the control track begin immediately after it. Because the form spans a
much wider panel, the controls remain clustered toward the left instead of
aligning along a shared right edge like the supplied reference image.

The Factory table configuration is not responsible for showing only three
columns. Theme CSS in `assets/css/embeds.css` deliberately applies
`display: none !important` to the fourth and subsequent header and data cells.
It also assigns fixed percentage widths to the first three columns. Those
rules override the plugin's visible-column configuration. The hidden cells
remain in the DOM so `assets/js/factory-table.js` can display the complete row
in its existing details dialog.

## Approved Experience

### Quick Pencil

At desktop and tablet widths, each row remains a two-column grid. Labels stay
left-aligned in the first column. Text inputs, number inputs, selects, and the
checkbox occupy the same right-hand grid column, use one consistent control
width, and align evenly at the right side of the form. The visual result should
match the structure of the supplied light-theme reference without changing the
theme's dark design-system skin.

At 600 pixels and below, the existing single-column layout remains: the label
appears above its control and controls may use the available width.

### Factory table

The theme will no longer hide columns four onward or force widths for only the
first three columns. wpDataTables remains the authority for which columns are
visible. When all configured columns exceed the available content width, the
existing responsive table container may scroll horizontally rather than
discarding columns.

The existing row-details dialog remains available as a secondary way to read a
complete row. Its JavaScript behavior, keyboard access, modal focus handling,
and row validation will not change.

## Files and Changes

- `assets/calculator/calculator.css`: adjust only the Quick Pencil desktop grid
  and control alignment rules; preserve the current mobile breakpoint.
- `assets/css/embeds.css`: remove the Factory-specific fourth-column hiding and
  three-column fixed-width rules; retain the table skin, responsive container,
  row focus treatment, and details-dialog styles.
- `tests/factory-table.test.js`: replace the assertions that require the theme
  to hide columns with assertions that prohibit theme-owned column hiding;
  preserve the dialog and row-validation coverage.

No PHP templates, calculator JavaScript, wpDataTables configuration, Google
Sheet data, plugin files, dependencies, or live-site settings will change.

## Constraints and Preserved Behavior

- Preserve all Quick Pencil IDs, names, selectors, field order, calculations,
  New/Used switching, validation, decimal steps, and results rendering.
- Preserve the dark carbon visual language, existing typography, focus states,
  and minimum hit targets.
- Preserve wpDataTables filtering, sorting, pagination, responsive wrapper, and
  plugin-controlled visibility.
- Preserve the Factory row-details dialog and its accessible keyboard behavior.
- Do not deploy, use SFTP, call WordPress APIs, or alter the live site.
- Do not add dependencies, create a build step, or refactor unrelated code.

## Validation

1. Run `node tests/factory-table.test.js` and confirm all Factory table dialog
   and visibility-boundary assertions pass.
2. Run a static selector check confirming `assets/css/embeds.css` no longer
   hides columns by `nth-child` position.
3. Run a focused browser check of `/calculator/` at desktop and mobile widths:
   confirm the Quick Pencil controls share one right-hand alignment on desktop,
   collapse cleanly on mobile, and representative New and Used calculations
   still render.
4. Run a focused browser check of `/factory/`: confirm every column configured
   visible is present, overflow remains usable, and clicking or keyboard-
   activating a valid row still opens the complete details dialog.

Browser checks require an environment serving the theme with WordPress and the
relevant plugins. If that environment is unavailable, report those checks as
pending rather than treating static inspection as proof of live behavior.

## Risks and Non-Goals

The main risk is horizontal density when many Factory columns are visible.
Horizontal overflow is preferred to silently hiding plugin-visible data. The
change will not redesign column content, abbreviate values, alter the Google
Sheet, or replace wpDataTables responsive behavior.

The Quick Pencil change is layout-only. It will not restyle the calculator to
the light reference theme or change pricing and calculation logic.
