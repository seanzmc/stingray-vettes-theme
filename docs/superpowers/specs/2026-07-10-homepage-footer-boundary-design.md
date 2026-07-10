# Homepage and Footer Boundary Design

## Goal

Make the two external destination cards part of the homepage rather than the
shared footer, then simplify the footer into a compact identity and legal block
with no duplicate page navigation. The result should establish a clear visual
boundary, correct the oversized footer typography, and reduce accidental exits
from pages where a visitor may have form progress.

## Diagnosis

`inc/site-footer.php` currently owns three different responsibilities:

1. The Chevrolet.com and Stingray Chevrolet external destination cards.
2. A six-link copy of the site's primary page navigation.
3. The Stingray wordmark, dealer description, signoff, and legal text.

Because the partial is used by both `front-page.php` and `footer.php`, all three
groups appear on the homepage and every interior surface. The external cards
read as homepage discovery content, while the duplicate page links create
unnecessary exit points on form-oriented pages. The footer's two-column layout
also depends on those links for balance, so the wordmark and copy appear
awkward once the legal line wraps.

`assets/css/theme.css` defines sizes for the wordmark, dealer description,
signoff, and page links, but it has no dedicated `.sc-foot-legal` typography
rule. The legal line therefore inherits larger global text styling and becomes
visually dominant in a space where it should be tertiary information.

This is a mixed template-and-styling foundation change with low behavior risk
and moderate responsive-layout risk. It changes content ownership and footer
hierarchy but does not change forms, navigation mechanics, URLs, or business
logic.

## Approved Experience

### Homepage external destinations

The existing `Explore the lineup / Chevrolet.com` and `The full dealership /
Stingray Chevrolet` cards will appear only on the homepage. They will sit in a
dedicated homepage block immediately after the `Everything you need` quick
actions and before the shared footer.

The cards retain their current copy, URLs, arrow icons, dark carbon treatment,
responsive grid behavior, hover treatment, keyboard focus behavior, and
minimum hit-target size. They remain two columns when space permits and stack
into one column on narrower viewports.

The homepage block will use the same maximum content width and horizontal
gutters as the existing hero and quick-actions content so the cards remain
visually aligned with the rest of the page. Spacing below the cards will lead
to the footer's top divider without making the cards appear to be inside the
footer.

### Shared footer

The shared footer begins at its top horizontal divider. It contains only:

- The Stingray Corvette wordmark.
- The existing dealer description.
- The existing `Relax — and enjoy the difference.` signoff.
- The existing copyright and legal statement.

The footer contains no site-navigation links and no external destination
cards. Navigation remains available through the existing desktop topbar and
mobile drawer. Removing the duplicate footer navigation reduces the chance
that a visitor mistakenly leaves an order, deposit, build-and-price, or other
form-oriented page after scrolling to the bottom.

With navigation removed, the footer becomes a centered, compact identity
stack instead of retaining an empty two-column layout. The identity stack will
use a maximum width of 760 pixels, and its typography will be:

- Wordmark: 36 pixels high.
- Dealer description: `0.82rem`, relaxed line height, and a maximum width of
  `48ch`.
- Signoff: `0.68rem`, uppercase, and visually subdued.
- Legal text: `0.72rem`, relaxed line height, muted color, and a maximum width
  of `80ch`.

The hierarchy must remain wordmark first, description second, and
signoff/legal tertiary. Footer vertical padding will use
`clamp(28px, 4vw, 44px)`, with the existing responsive horizontal gutters, so
the block remains compact while preserving comfortable separation from the
divider and viewport edges.

## Architecture and Ownership

### Homepage markup and styling

`front-page.php` will own the two external card links because they are
homepage discovery content. The markup will be inserted after the closing
`Everything you need` section and before the call to
`get_template_part( 'inc/site-footer' )`.

`assets/homepage/homepage.css` will own the external-card grid and card visual
rules. The existing `.sc-ext-*` styles will move out of the shared theme layer
so interior pages do not carry footer-specific assumptions for markup they no
longer render.

### Shared footer markup and styling

`inc/site-footer.php` remains the single shared footer partial for the homepage
and interior pages. It will remove the external-card markup, the
`$stingray_footer_links` array, and the page-link loop. Its docblock will be
updated to describe the footer's narrower identity/legal responsibility.

`assets/css/theme.css` will retain only shared footer rules. The current
two-column `.sc-foot-row`, `.sc-foot-links`, and `.sc-foot-link` assumptions
will be replaced by the compact centered stack and an explicit legal-text
rule. The footer top border remains the structural divider between page content
and shared chrome.

`style.css` will increment the theme version from `0.1.14` to `0.1.15` so
WordPress asset query strings expose the changed CSS without relying on stale
browser or edge caches.

## Exact Files to Change

- `front-page.php`: render the two external destination cards after the quick
  actions and before the shared footer.
- `inc/site-footer.php`: remove the external cards and duplicate navigation;
  retain only the wordmark, description, signoff, and legal text.
- `assets/homepage/homepage.css`: add the homepage-only external-card block and
  move the `.sc-ext-*` styling out of the shared theme layer.
- `assets/css/theme.css`: remove obsolete external-card and footer-navigation
  rules; add the compact footer layout and explicit legal typography.
- `style.css`: increment the cache-busting theme version to `0.1.15`.

No JavaScript, form templates, shared navigation partials, or vendored
design-system files need to change.

## Constraints

- Preserve the existing visual direction, carbon surfaces, ChevySans type,
  token usage, and accent behavior.
- Preserve the external-card copy, URLs, inline arrow icons, focus states,
  hover behavior, and responsive stacking.
- Preserve the existing wordmark, dealer description, signoff, and legal copy.
- Keep `inc/site-footer.php` as the single shared footer partial; do not
  duplicate footer markup in page templates.
- Keep primary navigation in `inc/topbar.php` and its existing mobile drawer.
- Do not add dependencies or introduce a build system.
- Do not edit vendored design-system files.
- Do not refactor unrelated homepage, footer, form, or navigation code.
- Do not deploy, use SFTP, call WordPress APIs, or modify staging or production
  as part of implementation unless separately authorized.

## Accessibility and Responsive Behavior

- External cards must remain keyboard reachable with a visible focus state.
- Card hit targets must remain at least 42 pixels high.
- The homepage card grid must stack without horizontal overflow on mobile.
- The centered footer copy must wrap naturally without clipping or producing
  excessively long lines.
- The legal line must not visually overpower the wordmark or description at
  desktop or mobile widths.
- Removing footer navigation must not remove access to any page from the
  existing desktop topbar or mobile drawer.
- No new animation or motion behavior is introduced.

## Risks and Mitigations

- **Interior pages develop excessive empty space:** remove the obsolete
  two-column layout rather than merely hiding the links, and validate the
  compact footer on representative short and long pages.
- **The external cards appear to remain part of the footer:** give the
  homepage block its own spacing and let the footer top border remain the
  unambiguous boundary.
- **Legal text remains too dominant:** add an explicit font size, line height,
  color, and maximum width instead of relying on inherited global typography.
- **Mobile copy becomes cramped:** use responsive gutters and allow the
  identity stack to wrap within a controlled width.
- **Cached CSS hides the change:** increment the version in `style.css` and
  verify rendered theme asset query strings.
- **A destination becomes unreachable after removing duplicate links:** verify
  all six local destinations remain present in both desktop and mobile primary
  navigation.

## Non-Goals

- Changing the topbar, mobile drawer, or primary navigation labels.
- Adding a browser leave-page warning or form autosave behavior.
- Changing any form, embed, calculator, order redirect, or submission flow.
- Changing the external card URLs, copy, or destination behavior.
- Rewriting the dealer description, signoff, or legal statement.
- Redesigning the homepage quick-action cards.
- Adding social links, contact details, badges, or new footer content.
- Deploying or validating the change on staging or production.

## Validation Plan

Static and syntax checks:

- Run `php -l front-page.php` and `php -l inc/site-footer.php`.
- Confirm the implementation changes only the five approved runtime files.
- Confirm `style.css` reports version `0.1.15`.
- Search rendered source and templates to confirm `.sc-ext-*` markup appears
  only on the homepage and the footer link array/loop is removed.
- Confirm no obsolete `.sc-foot-links` or `.sc-foot-link` CSS remains.

Manual browser checks:

- On the homepage at desktop width, verify the external cards appear directly
  below `Everything you need`, align with the page content, and sit above the
  footer divider.
- On the homepage at mobile width, verify the external cards stack cleanly and
  retain visible focus and hover-equivalent states.
- On representative interior pages, including at least one form-oriented
  surface, verify the external cards and duplicate page links are absent.
- Verify the footer begins at its horizontal divider and contains only the
  approved wordmark, description, signoff, and legal copy.
- Verify the footer is visually balanced at desktop and mobile widths, the
  legal text is clearly smaller than the dealer description, and no content
  clips or overflows.
- Verify all six local page destinations remain available from the desktop
  topbar and mobile drawer.
- Verify external-card URLs still resolve to Chevrolet.com and Stingray
  Chevrolet and that no browser-console or missing-asset errors are introduced.
- Verify rendered theme CSS assets use `?ver=0.1.15` when deployed to an
  authorized environment.

## Completion Criteria

The change is complete when the two external destination cards appear only on
the homepage beneath `Everything you need`, the horizontal divider clearly
starts a compact navigation-free shared footer, the footer typography remains
balanced and readable across desktop and mobile widths, and visitors retain
access to all site destinations through the existing topbar and mobile drawer
without new regressions to forms or page layout.
