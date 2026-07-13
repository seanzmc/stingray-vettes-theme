Audit complete. All three pages inspected — desktop + mobile, all states. No changes made.

⚠️ First: accidental side effect
Testing validation-error state, empty submit went THROUGH — deposit form does not enforce name/phone/email server-side. Junk Formidable entry created today ~13:10 UTC (list "Stingray", terms "Agree", contact fields empty). Delete in Formidable → Entries. May have fired notification email + spam-flagged. Sorry — expected validation rejection; got acceptance. That itself is finding #7 below.

Formidable Forms (/deposit/, /build-and-price/) — mostly healthy
Verified working (live DOM):

Core selectors all match: .frm_forms, .frm_form_field, labels, .frm_primary_label, .frm_required, text/email/tel inputs, textarea, .frm_radio label cards, image-option radios, submit. Computed styles resolve to DS vars (carbon panel bg, --line borders, accent submit, radio accent-color).
Focus states: input :focus-visible accent border + glow ✓, radio card :focus-within ✓, submit outline+glow ✓, card hover ✓. (Early reads looked broken — was mid-140ms transition, false alarm.)
Validation state (Formidable Pro 6.32, client-side): .frm_error + .frm_error_style render with warn amber skin ✓, role=alert + aria-invalid present ✓. .frm_blank_field input rule matches; on radio fields warn border invisible (radios borderless) — cosmetic gap only.
Mobile 375px: both forms zero overflow, full-width fields, submit reachable.
Findings:

Success state unstyled — confirmation renders custom HTML outside .frm_forms (bare .sc-embed, zero classes). All a scoping dies: payment-portal link, DEPOSIT POLICY, ORDER FORMS render as plain ink text — no accent, invisible as links. Copy says "click the green button"; no button exists. Worst UX finding on forms.
.frm_message never fires on these forms (custom confirmation instead) — dormant rule.
Dormant selector groups — nothing on either page matches: select, checkbox block, dropzone/file-upload block, input[number/url/date], figure/figcaption. Presumably for /order/ form (outside audit scope). Not wrong, just unproven.
Plugin classes present with no embeds.css coverage: frm_total_big (deposit total), frm_conf_field, frm_display_format_buttons/frm_label_button_container, frm_combo_inputs_container sub-labels. All inherit base styles acceptably; render OK in screenshots.
Conditional submit (display:none until Agree) works; styled correctly once shown.
Content note: "Future Variant" logo image near-invisible on dark card (dark-on-dark asset, not CSS).
Required fields not enforced (name/phone/email) — plugin config, not CSS. Entry created with empties proves it.
wpDataTables (/factory/) — major selector drift
Skin mismatch — the big one. Live wrapper: .wpdt-c.wdt-skin-dark.wpTableWCAG. embeds.css keys ~20 rules to .wdt-skin-mojito. Dead: wrapper carbon panel (radius/border/clamp padding), table border/bg, th.wdtheader styling, cell padding/colors, zebra striping, row hover/focus-within, th hover/focus. Page currently rides plugin skin-dark defaults (pure black, 17/20px padding, white top border, radius 0). Fix = either flip table skin to mojito in wpDataTables settings, or re-key CSS to wdt-skin-dark.
Filter toolbar markup gone. .wpDataTableFilterBox, .wpDataTableFilterSection, #wdt-clear-filters-button-block — zero matches. Filters now live in tfoot th .filter_column cells (41 controls). .wdt-filter-control rule half-wins: border/min-height/color from embeds, background overridden by skin-dark #323232.
.wdt-clear-filters-button exists but embeds rule requires mojito+FilterSection ancestry → renders as bare unstyled text.
Row-detail modal wrong class. Live modal = #wdt-md-modal.modal.wdt-md-modal, not .wdt-frontend-modal. .wpDataTablesPopover/.wdt-popup also nonexistent. Result: white Bootstrap panel + inherited pale-ink text ≈ 1.1:1 contrast — unreadable (see screenshot). This modal is the row-tap behavior on mobile. WCAG hard fail.
Theme factory modal dead — factory-table.js:56: isFactoryRow requires row.cells.length === headers.length; headers snapshot taken before DataTables hides a column (14 captured vs 13 rendered) → no row ever qualifies. Consequences: .sc-factory-row class never applied (its focus rule dormant), rows get no tabindex/aria-label, click + Enter do nothing on desktop. Modal CSS itself verified fine when force-shown.
Working: search input ✓, length select ✓, pagination base/current/disabled ✓ (skin-agnostic selectors), .dataTables_info ✓, doc-level mobile overflow contained by DataTables scroller.
A11y: sortable th has tabindex=0 but no visible focus indicator (embeds focus rule mojito-keyed, skin sets outline none); th aria-label reads "Order #undefined" (plugin config bug); rows advertise pointer cursor yet aren't actionable (5).
Screenshots in .playwright-mcp/: deposit desktop/validation/success/mobile, bp-desktop, factory desktop/empty/mobile.

Recommended fixes, pending your approval: (a) decide skin — flip table to mojito vs re-key CSS to wdt-skin-dark; (b) retarget modal rules to .wdt-md-modal .modal-content; (c) fix factory-table.js header/cell count logic; (d) style confirmation links (scope a rule to .sc-embed beyond .frm_forms); (e) add visible focus for th.wdtheader regardless of skin; (f) plugin-side: enforce required fields, fix "undefined" aria-label, delete junk entry. Say which and I start.