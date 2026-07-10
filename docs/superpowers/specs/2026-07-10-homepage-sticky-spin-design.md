# Homepage Sticky Spin Design

## Goal

Keep the homepage 360-degree Corvette viewer visible long enough for the first
scroll interaction to produce one complete revolution, without capturing or
blocking native page scrolling.

## Diagnosis

`assets/homepage/spin.js` currently converts every 500 pixels of global page
scroll into one revolution. The hero remains in normal document flow, so the
same scroll distance that rotates the car also moves the viewer toward or out
of the viewport. The rotation therefore loses its visual focus before it
finishes.

This is a mixed behavior-and-styling change with moderate interaction risk. It
changes how homepage scroll progress controls the viewer, but it does not alter
the viewer assets, canvas rendering model, page content, or visual direction.

## Approved Experience

On eligible desktop and landscape-tablet viewports, the complete hero remains
sticky beneath the existing topbar during a short, bounded scroll segment. The
initial configuration uses 600 pixels of normal document scrolling to advance
the car through exactly one clockwise revolution. Once that segment is
complete, the hero releases and the quick-actions section enters normally.

The browser keeps full control of scrolling at all times. The implementation
must not cancel wheel, touch, keyboard, scrollbar, or trackpad navigation.
Scrolling upward through the segment reverses the rotation. Rotation stops
changing once scroll progress is outside the bounded hero segment.

Manual drag-to-spin remains available. The bounded scroll segment contributes
one revolution relative to any manually dragged starting angle. Existing paint
cycling, accent changes, frame lazy loading, canvas rendering, and clockwise
direction remain intact.

## Responsive and Accessibility Behavior

The sticky scroll sequence is enabled only when all of these conditions hold:

- The viewport is at least 900 pixels wide.
- The viewport is at least 700 pixels tall.
- The user has not requested reduced motion.

On narrower or shorter viewports, the hero stays in normal document flow and
the car remains manually draggable. The viewer hint changes to `360° · Drag to
spin` when scroll-driven rotation is unavailable.

When `prefers-reduced-motion: reduce` is active, scrolling must not rotate or
pin the viewer. Manual drag remains available because it is a direct,
deliberate interaction rather than motion coupled to ordinary page navigation.

## Architecture and Data Flow

### Sticky layout

`assets/homepage/homepage.css` will make the existing `.sc-hero` provide the
bounded scroll distance and make `.sc-hero-row` sticky below the 75-pixel
topbar footprint. Logged-in WordPress views will add the existing 32-pixel
desktop or 46-pixel mobile admin-bar offset. The sticky rules will live inside
viewport and reduced-motion conditions so the existing stacked mobile layout
remains unchanged.

The scroll distance will be exposed through a homepage-specific CSS custom
property set from the JavaScript configuration. This keeps the configured
pixels-per-revolution value and the physical sticky distance synchronized.

### Rotation mapping

`assets/homepage/spin.js` will replace unrestricted global scroll-delta
rotation with bounded hero progress:

1. Determine whether the sticky sequence is currently eligible.
2. Calculate the hero segment's document start and end positions.
3. Clamp scroll progress to the range from zero to one.
4. Convert the change in clamped progress into rotation.
5. Draw the corresponding frame and preserve existing paint-cycle accounting.

When progress approaches the end of the revolution, the next paint set will
begin its existing on-demand preload. This gives the established paint/accent
transition a chance to complete at the revolution boundary without requiring
additional off-screen page scrolling, while still avoiding eager loading of
every paint set.

Using clamped progress ensures the first traversal produces one revolution,
scrolling upward reverses it, and scrolling farther down the page cannot keep
rotating an off-screen car. The initial progress must be calculated on load so
browser scroll restoration and direct mid-page loads do not cause a jump.

Eligibility and geometry must be recalculated when viewport size or motion
preferences change. Entering an ineligible state removes sticky spacing,
stops scroll-coupled rotation, and updates the viewer hint without affecting
manual dragging.

## Exact Files to Change

- `assets/homepage/spin.js`: add bounded hero-progress mapping, eligibility
  handling, responsive hint text, and reduced-motion scroll bypass.
- `assets/homepage/homepage.css`: add the desktop sticky hero segment and its
  responsive/reduced-motion fallbacks.
- `style.css`: increment the theme version so WordPress cache-busting exposes
  the changed homepage CSS and JavaScript.

No PHP templates or vendored design-system files need to change.

## Constraints

- Preserve the current visual design, hero content, page order, and markup.
- Preserve the existing canvas, frame assets, paint palettes, lazy-loading
  strategy, accent transitions, and manual drag selector contracts.
- Do not intercept or suppress native scrolling.
- Do not add dependencies or introduce a build system.
- Do not refactor unrelated homepage or shared-theme code.
- Do not modify canonical design-system files.
- Do not deploy, use SFTP, call WordPress APIs, or modify the live site.

## Failure and Fallback Behavior

If the hero element or required browser APIs are unavailable, the viewer must
fall back to its existing static/manual-drag behavior without preventing page
scrolling. A resize or orientation change that makes the viewport ineligible
must release the sticky layout immediately. Reduced-motion changes made while
the page is open must also release the sticky layout and stop scroll-driven
rotation.

Frame-load failures retain the existing behavior: unavailable images are
counted as completed load attempts and the viewer continues rendering any
valid frames. This change does not expand asset error handling.

## Risks and Mitigations

- **The hero feels like a scroll trap:** keep the added distance at 600
  pixels and never cancel native input.
- **The sticky hero overlaps the topbar:** offset the sticky row below the
  existing 75-pixel topbar footprint and verify WordPress admin-bar states.
- **Short screens cannot contain the hero:** require at least 700 pixels of
  viewport height and retain normal flow below that threshold.
- **Mobile becomes excessively tall:** disable the sticky segment below 900
  pixels wide and retain manual drag.
- **Resize causes a rotation jump:** recalculate geometry and reset the stored
  progress baseline when eligibility changes.
- **Cached assets hide the update:** increment the theme version in
  `style.css`.

## Non-Goals

- Redesigning the hero or viewer controls.
- Adding autoplay, inertia, scroll snapping, or a separate animation library.
- Changing paint order, frame counts, frame files, or accent colors.
- Making the sticky sequence repeat elsewhere on the page.
- Changing the shared topbar, quick-action cards, or interior surfaces.
- Deploying or validating against the live WordPress site.

## Validation Plan

Automated and static checks:

- Run JavaScript syntax validation on `assets/homepage/spin.js`.
- Confirm only the three approved files change during implementation.
- Confirm no wheel or touch event cancellation is introduced.

Manual browser checks:

- At eligible desktop dimensions, verify the hero stays below the topbar,
  remains visible for one complete revolution, and releases after 600
  pixels.
- Verify scrolling upward reverses the rotation and does not jump.
- Verify scrolling after the hero releases no longer rotates the car.
- Reload and restore the page from within and below the sticky segment; verify
  the frame matches the restored scroll position.
- Verify drag-to-spin before, during, and after the sticky segment.
- Verify the current paint frame set loads, the next paint remains lazy-loaded,
  and paint/accent cycling still works.
- Verify desktop and landscape-tablet layouts have no hero, topbar, quick-action,
  or WordPress admin-bar overlap.
- At widths below 900 pixels and heights below 700 pixels, verify normal page
  flow, `360° · Drag to spin`, and functional manual dragging.
- With reduced motion enabled, verify there is no sticky segment or
  scroll-coupled rotation and that manual dragging still works.
- Check for missing assets and browser-console errors.

## Completion Criteria

The change is ready when an eligible desktop visitor can scroll naturally
through one visible, reversible revolution before the hero releases, while
mobile, short-screen, and reduced-motion visitors retain a normal-scrolling,
manual-drag experience with no regressions to loading, paint cycling, layout,
or shared navigation.
