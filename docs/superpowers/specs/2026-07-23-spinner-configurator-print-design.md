# Spinner WebP and Configurator Print Design

**Date:** 2026-07-23  
**Status:** Approved design; implementation pending

## Goal

Make two bounded theme improvements:

1. Serve the homepage 360 viewer from the new transparent WebP frame set with
   controlled loading and PNG fallback.
2. Make the private `/configurator-view/` wpDataTables submission modal readable
   on screen and printable as one scaled US Letter sheet.

## Current State

### Homepage spinner

- `assets/homepage/spin.js` still requests 1500×750 PNG frames from the five
  existing paint folders.
- Commit `c5a633b` added five parallel `*-webp` folders with 30 frames each.
- Each new filename retains the PNG stem and adds `-cmp.webp`.
- The complete replacement set has been verified as 150 files, all 1500×750
  with alpha, totaling 12,409,000 bytes (11.83 MiB).
- The current PNG set remains available for reversible fallback.

### Configurator submissions

- `/configurator-view/` is a private WordPress page. An unauthenticated request
  returns a private, no-store 404.
- The page uses a plugin-generated Bootstrap-style wpDataTables modal.
- The modal currently has a white background with inherited light text because
  `--bs-modal-color` is unset on this page.
- Existing `assets/css/embeds.css` modal rules are intentionally loaded for
  public Formidable and Factory surfaces, not this private utility page.
- The desired print result is a compact black-on-white order summary scaled
  onto exactly one US Letter portrait sheet.

## Architecture

Keep the two changes isolated.

### Spinner

Continue to use the theme-owned `window.SC_SPIN_BASE` URL and the existing
paint/frame model. Extend the loader so each primary request resolves to:

`<paint>-webp/<prefix><frame>-cmp.webp`

If a WebP request fails, retry that frame once from the existing PNG path:

`<paint>/<prefix><frame>.png`

Restore the previously proven bounded loader behavior without reverting later
homepage work:

- maximum four active frame requests;
- frame-zero-first loading, followed by a near-neighbor sequence;
- one static frame for Save-Data or reduced-motion users;
- no constrained-mode drag, scroll animation, paint preloading, or accent
  animation;
- preserve `colorLeadFrames`, current paint order, drag behavior, scroll
  behavior, accent transitions, and absolute theme asset URLs.

The PNG files remain in this pass as rollback/fallback material. No source
images are renamed or deleted.

### Private configurator page

Add a dedicated stylesheet and script, loaded only when
`is_page( 'configurator-view' )` is true:

- `assets/css/configurator-view.css`
- `assets/js/configurator-view.js`

Do not make the private-page CSS global and do not extend the public Factory
runtime script. The private page has a different white utility presentation and
a distinct print workflow.

## Screen Modal Design

Target the stable wpDataTables/Bootstrap modal boundary while keeping selectors
under a configurator-specific body class or page-scoped asset.

- Set `--bs-modal-color: #161616`.
- Use a white modal surface and near-black body text.
- Force titles, labels, strong text, list content, and generated detail fields
  to inherit or use the readable foreground.
- Use approximately `0.75rem 1rem` header/body padding.
- Retain visible separation between the header and body.
- Preserve the close control, focus behavior, plugin modal lifecycle, and
  existing submission markup.
- Add a theme-authored `Print order` button with a visible focus state and a
  minimum 42px hit target.

The script observes the plugin-generated modal and injects at most one print
button into its header. It must tolerate the modal being created after page
load or reused for later rows.

## One-Sheet Print Flow

The print action operates entirely in the authenticated browser:

1. Read the currently open modal.
2. Clone its title and complete body content, including content outside the
   visible modal viewport.
3. Exclude close controls, buttons, backdrop, table, and other page chrome.
4. Place the clone in a temporary print-only sheet.
5. Apply compact black-on-white typography and section/list spacing modeled on
   the supplied readable order example.
6. Measure the rendered clone against a fixed Letter portrait content box.
7. Calculate one proportional scale factor using the available width and
   height, never upscale above 100%, and apply it from the top-left origin.
8. Add a printing state class and call `window.print()`.
9. Remove the temporary clone and printing state after `afterprint`, with a
   bounded cleanup fallback in case that event is not emitted.

Print CSS sets:

- `@page { size: letter portrait; margin: 0.25in; }`
- one fixed print sheet;
- hidden overflow and no page breaks;
- white background and black text;
- no modal chrome or unrelated document content.

The theme can guarantee that its generated content fits within one CSS Letter
page box. The browser print dialog still controls the physical printer and
paper choice; Letter portrait at 100% scale is the supported output.

## Privacy and Failure Handling

- Do not add network requests, analytics, persistence, server-side generation,
  or submission-data mutation.
- The print clone exists only in the current authenticated document and is
  removed after printing.
- If no open compatible modal is found, do not open an empty print dialog.
- If measurement fails, clean up the temporary document state and leave the
  original modal usable.
- If WebP loading fails for a frame, retry only the corresponding PNG once.
- A failed PNG fallback counts as a completed failed frame so the queue cannot
  deadlock; the existing viewer remains responsible for drawing only available
  images.

## Files

Expected implementation changes:

- `assets/homepage/spin.js`
- `assets/css/configurator-view.css` (new)
- `assets/js/configurator-view.js` (new)
- `functions.php`
- `style.css`
- `README.md`
- `tests/spin-loading.test.js` (restore/update)
- `tests/configurator-view.test.js` (new)

The 150 WebP assets already committed in `c5a633b` are consumed but not modified
by this implementation.

## Validation

### Automated

- Prove exactly 150 WebPs exist with the expected folders, filename stems,
  1500×750 dimensions, and alpha.
- Prove every WebP maps to one PNG fallback.
- Exercise bounded concurrency, priority order, constrained-data behavior, and
  one-time PNG fallback.
- Exercise delayed modal discovery, single button injection, full-content
  cloning, scale calculation, print invocation, and cleanup.
- Run `node tests/spin-loading.test.js`.
- Run `node tests/configurator-view.test.js`.
- Run `php -l functions.php`.
- Run the existing focused theme/Factory tests affected by enqueue assertions.
- Run `git diff --check`.

### Browser

- Verify the homepage first frame, drag, scroll spin, paint transition, lazy
  loading, transparency, mobile layout, reduced motion, and Save-Data behavior.
- Verify the private modal has readable black-on-white content, compact padding,
  working close/focus behavior, and only one print button.
- Print representative short and long submissions to PDF and confirm each PDF
  has exactly one Letter portrait page with all modal content present.

Authenticated production verification and deployment are separate actions and
are not authorized by this design.

## Preserved Boundaries

- No WordPress page, wpDataTables, form, submission, workbook, generated order
  data, pricing, RPO, or Cloudflare configuration changes.
- No plugin modification, dependency, or build system.
- No public Factory modal behavior change.
- No live deployment, SFTP, WordPress API call, or cache purge.
- No PNG deletion in this pass.

