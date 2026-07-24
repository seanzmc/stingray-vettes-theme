# wpDataTables Order Print Design

**Date:** 2026-07-24  
**Status:** Approved design; implementation pending

## Goal

Keep one-sheet order printing on the private `/configurator-view/` page after
removing the legacy Formidable View, using the selected wpDataTables row as the
data source and providing a consistent visible margin on the printed sheet.

## Verified Current State

- The page now contains only the wpDataTables submission table.
- Removing the Formidable View also removed the Formidable modal assets that
  conflicted with wpDataTables' Bootstrap modal.
- A wpDataTables row now opens `#wdt-md-modal` with the expected `.in` state,
  `display: block`, and full opacity.
- The selected-row modal contains stable detail fields for `name`, `variant`,
  `plaintextbuildsummary`, `createdat`, and the newly added `id`.
- The existing theme script injects one `Print order` button into the
  wpDataTables modal.
- Field `6611` is already represented by the selected row's
  `plaintextbuildsummary` value, so a second Formidable lookup is unnecessary.

## Approaches Considered

### Selected: print the selected wpDataTables row

Read the current modal's detail fields in the authenticated browser and build
the print sheet from those values. This guarantees that the printed data comes
from the row the administrator selected and requires no additional request.

### Rejected: dynamic Formidable field-value request

Formidable supports
`[frm-field-value field_id=6611 entry="ENTRY_ID"]`, but using it here would
require a protected AJAX or REST endpoint, nonce validation, authorization,
Formidable availability checks, and additional failure handling. It would
return the same value already present in the selected wpDataTables row.

### Rejected: static Formidable field-value shortcode

A shortcode without a selected entry ID can return the most recent entry rather
than the row the administrator clicked. That behavior is not safe for an order
printing workflow.

## Data and Print Flow

1. The administrator clicks a wpDataTables row.
2. wpDataTables opens and populates its existing detail modal.
3. The theme-owned `Print order` button reads the open modal.
4. The print sheet includes the customer name, variant, build summary, and
   entry date.
5. The technical entry ID remains available for row identity but is omitted
   from the printed document.
6. The temporary print document is measured against the usable Letter content
   area and proportionally reduced when necessary. Content is never enlarged.
7. The browser print dialog opens.
8. The temporary print document and printing state are removed after printing
   or cancellation.

The print action does not request, persist, or mutate submission data.

## Letter-Sheet Geometry

The supported output is one US Letter portrait page.

- Physical page: `8.5in × 11in`.
- Visible inset: `0.5in` on all four sides.
- Usable content area: `7.5in × 10in`.
- Print typography and list spacing remain compact and black on white.
- The scale calculation uses the usable content area, including the explicit
  inset, so long orders stay within one page.
- The CSS controls the inset rather than depending on the user's print-dialog
  margin setting.

## Files

Expected implementation changes:

- `assets/js/configurator-view.js`
- `assets/css/configurator-view.css`
- `tests/configurator-view.test.js`
- `style.css` for cache busting

No plugin, WordPress page, wpDataTables configuration, Formidable entry, or
submission data will be changed by the theme implementation.

## Validation

### Automated

- Prove that the print source targets the wpDataTables detail modal.
- Prove that selected-row fields are copied while the entry ID is omitted.
- Prove that hidden or plugin-constrained source markup cannot suppress the
  printed build summary.
- Prove that scale is calculated against `7.5in × 10in` and never exceeds 1.
- Prove that failed measurement cleans up without opening the print dialog.
- Run `node tests/configurator-view.test.js`.
- Run all existing theme JavaScript regression tests.
- Run `php -l functions.php`.
- Run `git diff --check`.

### Authenticated browser

- Click multiple rows and confirm each modal opens normally.
- Confirm each modal receives exactly one `Print order` button.
- Print representative short and long orders to PDF.
- Confirm each PDF is one Letter portrait page, has a visible `0.5in` inset,
  contains the complete selected build summary, and omits the entry ID.

## Preserved Boundaries

- The page remains private and admin-only.
- No public page or Factory modal behavior changes.
- No new endpoint, dependency, build step, or plugin modification.
- No live deployment, WordPress API call, SFTP action, or cache purge is part
  of this implementation.
