# Process Surface Completion Spec

> **For Hermes:** Use `software-development:subagent-driven-development` to implement this plan task-by-task after approval.

**Goal:** Complete the `/process/` surface in the Stingray Corvette WordPress theme by rebuilding the existing Corvette Order Process Guide as a customer-friendly, design-system-backed `page-process.php` template.

**Architecture:** This pass creates one classic WordPress page template bound by slug: `page-process.php` ↔ `/process/`. The page should use shared theme chrome (`get_header()` / `get_footer()`), existing surface components from `assets/css/surfaces.css`, and customer-facing copy ported from the live Process Guide. It should not add plugins, shortcodes, JavaScript, data integrations, or new build tooling.

**Tech Stack:** Classic WordPress theme, PHP 7.4-compatible template code, vanilla CSS already present in the theme, WordPress escaping helpers, existing Stingray Corvette design-system classes.

---

## Current repo state at spec time

Date checked: 2026-07-07.

Latest relevant commits:

- `4c3520d feat: add factory orders page with status codes and wpDataTables embed`
- `1a63712 feat: add build-and-price page with configurator links and Formidable embed`
- `acbb764 feat: add deposit form page with instructions and Formidable embed`
- `ccab308 feat: complete calculator surface integration with page template and JS contracts`
- `51ea15b Mount the 1a order form at /order/ + shared surface/embed style layers`

Existing dedicated page templates:

- `page-order.php`
- `page-calculator.php`
- `page-deposit.php`
- `page-build-and-price.php`
- `page-factory.php`

Missing dedicated page template:

- `page-process.php`

Integration plan currently says:

- Create `page-process.php`.
- Rebuild full content from the live page.
- Include deposit list status grid, deposit details by model, policies, order process, pricing, fees, payment options, delivery, restrictions, and staying informed.
- Use DS typesetting and replace live-page `<hr>` separators with theme dividers.

## Source/reference content

Live source page inspected:

- URL: `https://stingraychevroletcorvette.com/corvette-process-guide/`
- Planned local slug: `/process/`
- Existing live title: `Corvette Order Process Guide`

Live-page links observed:

- Deposit Form: `https://stingraychevroletcorvette.com/order-landing-page/deposit-form/`
  - In new theme, use local slug: `home_url( '/deposit/' )`.
- ZR1 Process Page: `https://stingraychevroletcorvette.com/zr1-process/`
  - Keep as a link if this content is retained; no local replacement exists in this theme plan.
- Email: `mailto:smccann@stingraychevrolet.com`
- Phone copy: `813-359-5000`

Live-page body content observed on 2026-07-07:

### Intro

- `Welcome to our Comprehensive Corvette Order Process Guide.`
- The guide walks customers through every step of ordering a new Corvette, from initial deposit to delivery.
- Policy disclaimer: Stingray Chevrolet may change policy regarding deposits, ordering, and purchasing at any time.

### Deposit Lists — Current Status

Overall status copy:

- All deposit lists are currently open and accepting new customers except ZR1 and ZR1X, where Stingray has collected more deposits than anticipated allocations.

Status cards:

- Stingray
  - Status: `OPEN`
  - Deposit: `$500`
  - Fully refundable until order placed
  - Sold at MSRP unless specifically noted
- Z06
  - Status: `OPEN`
  - Deposit: `$500 + $2,500 at order`
  - Full deposit becomes non-refundable at order
  - Sold at MSRP unless specifically noted
- Grand Sport
  - Status: `OPEN`
  - Deposit: `$500`
  - Fully refundable until order placed
  - Sold at MSRP unless specifically noted
- Grand Sport X
  - Status: `OPEN`
  - Deposit: `$500 + $2,500 at order`
  - Full deposit becomes non-refundable at order
  - Sold at MSRP unless specifically noted
- ZR1
  - Status: `CLOSED`
  - Not accepting new deposits at this time
  - See the ZR1 Process Page for details
- ZR1X
  - Status: `CLOSED`
  - Not accepting new deposits at this time
  - Contact Stingray if already on this list
- E-Ray
  - Status: `DISCONTINUED`
  - GM has discontinued the E-Ray
  - No new deposits accepted
  - Existing E-Ray depositors have been automatically transferred to the Grand Sport X list; contact Stingray to adjust or request a refund

### Deposit Details by Model

Stingray:

- `$500` deposit to secure place on list.
- Deposit fully refundable until order is submitted to factory.
- Additional deposit may be due at time of order if build includes:
  - `BV4` Personalized Plaque
  - `R8C` Museum Delivery
  - `D30` Color Combination Override

Grand Sport:

- List is open and accepting new deposits.
- `$500` deposit to secure place on the Grand Sport list.
- Deposit fully refundable until order is submitted to factory.
- Grand Sport and Grand Sport X are separate lists because they have separate GM allocations.
- Deposit on one does not reserve a spot on the other.
- Sold at MSRP unless specifically noted.

Grand Sport X:

- List is open and accepting new deposits.
- Former E-Ray depositors have been automatically transferred here.
- `$500` deposit to secure place on the Grand Sport X list.
- Additional `$2,500` due when order is placed for production.
- Entire deposit becomes non-refundable once order is submitted to factory.
- Former E-Ray depositors may contact Stingray to move deposit elsewhere or request refund.
- Sold at MSRP unless specifically noted.

Z06:

- List is open and accepting new deposits.
- `$500` deposit to secure place on list.
- Additional `$2,500` due when order is placed for production.
- Entire deposit becomes non-refundable at time order is submitted to factory.
- Sold at MSRP unless specifically noted.

ZR1 & ZR1X:

- Lists are currently closed.
- Stingray has collected more deposits than anticipated allocations.
- Not accepting new names at this time.
- Refer to ZR1 Process Page or contact Stingray directly.

### Deposit Policies

Refund Policy:

- Deposit fully refundable until order is placed for production.
- Once order is submitted to factory, entire deposit becomes non-refundable.
- If customer cancels, Stingray verifies mailing address and issues refund check.
- Refunds are issued only to individual named on original deposit receipt.

Placing a Deposit:

- Use online Deposit Form, which accepts credit card payments.
- Position in line is determined by date deposit is received.
- One deposit per household on each model list.
- Additional deposits may be placed only after first order is in production.

### The Order Process

Ordering Cycles:

- Stingray places Corvette orders twice per month.
- Allocation quantities vary by model and ordering cycle.
- GM provides allocation info approximately two weeks before each ordering period.

Configuration & Constraints:

- Specific option constraints, if any, are released 5 days before orders must be finalized.
- Constraints can change monthly and affect available colors, options, and packages.
- Stingray recommends scheduling an appointment with the Corvette Specialist before the ordering window opens.
- Stingray contacts customer when it is their turn to finalize order.

### Pricing Policy

- Majority of new Corvettes at Stingray Chevrolet are sold at or below MSRP.
- Limited-edition or high-demand variants may be priced differently at dealer discretion.
- Any such pricing should be communicated clearly before customer commits to order.
- Pricing is subject to GM change without notice.
- Stingray does not offer price protection against GM-initiated price adjustments.
- All prices are plus tax, title, and dealer fees.

Dealer fees:

- Dealer Fee: `$999`
- Tag Agency Fee: `$362`
- These apply to every customer on every deal, without exception.
- Fees are in addition to MSRP, titling fees, and applicable taxes.

### Payment Options

Cash & Check:

- Stingray accepts cash, cashier's checks, and personal checks.
- Checks payable to `Stingray Chevrolet`.
- Additional verification may be required.

Financing:

- Stingray works with retail banks and credit unions.
- Loans should not be secured more than 30 days before vehicle delivery.

Leasing:

- Leasing is available through multiple lenders.
- Lease programs and terms are lender-set and change monthly.

Your Deposit & Down Payment:

- Deposit applies to final purchase as down payment or partial payment.
- All funds must be verified in-house before vehicle release.

### Vehicle Delivery

Shipping & Preparation:

- It typically takes about 2 days for Corvette to arrive after factory shipping.
- Stingray performs comprehensive Pre-Delivery Inspection (`PDI`).
- Stingray installs dealer-installed (`LPO`) accessories ordered by customer.

Delivery Requirements:

- Buyers must present valid driver's license and proof of insurance.
- Person named on order must be present at delivery unless exception is approved in advance.
- Stingray handles tax and registration paperwork.
- Trade-ins are evaluated at current market value at time of contract.
- Stingray cannot accept early trade-ins.

### Restrictions

Stingray does not sell or lease vehicles:

- To be titled and registered in New Hampshire, Oregon, Montana, Alaska, or Delaware.
- To individuals who may not be permanent U.S. residents, may live or work outside the U.S., or may conduct business outside the U.S.
- To brokers, wholesalers, or exporters.

### Staying Informed

- Stingray will reach out when it is the customer's turn to finalize order.
- Customers should add `smccann@stingraychevrolet.com` to safe senders.
- For up-to-date allocation/process changes, contact the Corvette Specialist at `813-359-5000`.
- Final policy disclaimer: Stingray Chevrolet may modify deposit requirements, pricing policies, and order procedures at any time. Page reflects current policy and supersedes prior versions.

## Constraints

- Do not deploy, push, SFTP, call WordPress APIs, or modify the live site.
- Do not add dependencies or introduce a build step.
- Do not modify the order form app, calculator assets, Formidable forms, wpDataTables, Google Sheets, CarSales, or Cloudflare Worker projects.
- Do not connect or attach `/Users/seandm/Projects/CarSales`, `corvette-sales-content-manager`, or `sales.stingraychevroletcorvette.com` to the public WordPress site; that Worker/subdomain is separate.
- Do not create a shortcode or plugin for this page. This is static theme template content.
- Preserve local planned slug: `/process/`.
- Use `page-process.php` so WordPress binds the page by slug.
- Use shared theme chrome via `get_header()` and `get_footer()`.
- Use WordPress escaping APIs:
  - `home_url()`
  - `esc_url()`
  - `esc_html_e()` / `esc_html__()` / `esc_html()`
  - `esc_attr_e()` when needed
- Keep PHP 7.4 compatibility.
- Preserve existing visual direction: dark carbon surface language, ChevySans typography, Torch Red accents, hard-shadow hover treatments, short transitions, visible focus states.
- Avoid emoji and new icon libraries.
- Do not update `style.css` version in this pass unless explicitly asked; final live-readiness/version bump should be a later QA/deployment pass.

## Proposed content architecture

Build `page-process.php` as a long-form guide made of short, scannable DS sections.

Recommended structure:

1. Page hero
   - Eyebrow: `Order Process`
   - Title: `Corvette Order Process Guide`
   - Lede: concise version of intro copy.
   - Note/disclaimer using `.sc-note`: policy may change.

2. Quick action cards
   - Link card to `/deposit/`: `Place a Deposit`
   - Link card to `/order/`: `Start an Order Form`
   - Link card to `/factory/`: `Track Factory Orders`
   - Optional link card to `/build-and-price/`: `Share a Build Code`

3. Deposit lists status grid
   - Section title: `Deposit Lists — Current Status`
   - Use `.sc-grid` with one `.sc-card-panel` per model/list.
   - Use `.sc-pill` for status:
     - `OPEN`
     - `CLOSED`
     - `DISCONTINUED`
   - If existing `.sc-pill` variants are available, use them. If no variants exist, keep base `.sc-pill` and do not invent broad CSS unless visual QA requires it.
   - Each card should preserve model name, status, deposit amount/terms, and the core note.

4. Deposit details by model
   - Use native `<details>` inside `.sc-accordion` or a `.sc-grid` of `.sc-card-panel` blocks.
   - Preferred: `.sc-accordion` to keep long content compact on mobile.
   - Include Stingray, Grand Sport, Grand Sport X, Z06, ZR1 & ZR1X.
   - Use `.sc-code` for RPO/status codes such as `BV4`, `R8C`, and `D30`.

5. Deposit policies
   - Two-card grid:
     - `Refund Policy`
     - `Placing a Deposit`
   - Include a local link to `/deposit/` for the Deposit Form.

6. The order process
   - Two-card grid or step list:
     - `Ordering Cycles`
     - `Configuration & Constraints`
   - Preserve twice-monthly ordering and 5-day constraint timing.

7. Pricing and dealer fees
   - Pricing policy card.
   - Dealer fees as `.sc-fact-row` values:
     - Dealer Fee `$999`
     - Tag Agency Fee `$362`
   - Preserve tax/title/dealer-fee caveat.

8. Payment options
   - Cards for:
     - `Cash & Check`
     - `Financing`
     - `Leasing`
     - `Your Deposit & Down Payment`

9. Vehicle delivery
   - Cards for:
     - `Shipping & Preparation`
     - `Delivery Requirements`
   - Preserve PDI, LPO, license/insurance, named buyer, tax/registration, trade-in timing.

10. Restrictions
   - Strong `.sc-note` or `.sc-card-panel` with list of no-sale/no-lease restrictions.
   - Do not soften the legal/customer-eligibility meaning.

11. Staying informed
   - Include safe sender email: `smccann@stingraychevrolet.com` with `mailto:` link.
   - Include phone: `813-359-5000` with `tel:` link.
   - Include final policy disclaimer.

## Task 1: Create `page-process.php` shell and data arrays

**Objective:** Create the process page template with reusable arrays for status cards and repeated content while keeping markup readable.

**Files:**

- Create: `page-process.php`

**Steps:**

1. Read neighboring templates for style:
   - `page-deposit.php`
   - `page-build-and-price.php`
   - `page-factory.php`
2. Create `page-process.php` with docblock:
   - `/process/ — Corvette process guide surface (SiteWireframe item 7).`
3. Define PHP arrays near top for repeated data:
   - `$process_status_cards`
   - `$process_model_details`
   - `$process_fees`
   - Optional arrays for payment/delivery cards if it keeps template DRY.
4. Call `get_header();` after arrays.
5. Add shell:
   - `<main class="sc-page">`
   - `<div class="sc-page-inner">`
   - page header section
   - `get_footer();`

**Acceptance criteria:**

- `page-process.php` exists.
- Template uses `get_header()` and `get_footer()`.
- No plugin shortcodes are introduced.
- No CSS/JS files are created in this task.

## Task 2: Add hero and quick action cards

**Objective:** Make the long process guide navigable and connect it to the already-built theme surfaces.

**Files:**

- Modify: `page-process.php`

**Steps:**

1. Add page header:
   - eyebrow: `Order Process`
   - title: `Corvette Order Process Guide`
   - lede: customer-friendly summary of the guide.
2. Add policy note:
   - `Stingray Chevrolet reserves the right to change its policy regarding deposits, ordering, and purchasing at any time.`
3. Add quick action link cards using `.sc-link-card`:
   - `/deposit/` — `Place a Deposit`
   - `/order/` — `Start an Order Form`
   - `/factory/` — `Track Factory Orders`
   - `/build-and-price/` — `Share a Build Code`

**Acceptance criteria:**

- Local links use `home_url()` and `esc_url()`.
- Shared chrome is not duplicated.
- Page gives customers a clear next step without replacing the detailed guide.

## Task 3: Add deposit list status grid

**Objective:** Port the live page's current list availability into DS cards.

**Files:**

- Modify: `page-process.php`

**Steps:**

1. Add section with heading `Deposit Lists — Current Status`.
2. Add section lede preserving the overall status copy.
3. Render cards from `$process_status_cards` with:
   - model/list title
   - status pill (`OPEN`, `CLOSED`, `DISCONTINUED`)
   - list of details
4. Include all seven entries:
   - Stingray
   - Z06
   - Grand Sport
   - Grand Sport X
   - ZR1
   - ZR1X
   - E-Ray
5. For ZR1 card, keep link to `https://stingraychevroletcorvette.com/zr1-process/` unless user provides a local replacement.

**Acceptance criteria:**

- All live status categories are represented.
- `OPEN`, `CLOSED`, and `DISCONTINUED` appear visibly.
- `$500`, `$2,500`, and MSRP notes are preserved where applicable.
- E-Ray transfer-to-Grand-Sport-X note is preserved.

## Task 4: Add model-specific deposit details

**Objective:** Preserve model-by-model deposit rules without making the page feel like a wall of text.

**Files:**

- Modify: `page-process.php`

**Steps:**

1. Add section heading `Deposit Details by Model`.
2. Use `.sc-accordion` with native `<details>`.
3. Add detail rows for:
   - Stingray
   - Grand Sport
   - Grand Sport X
   - Z06
   - ZR1 & ZR1X
4. In Stingray details, include codes as `.sc-code`:
   - `BV4`
   - `R8C`
   - `D30`
5. Preserve Grand Sport / Grand Sport X separate-list explanation.
6. Preserve non-refundable-at-order language for Grand Sport X and Z06.

**Acceptance criteria:**

- Native `<details>` works without JS.
- All model sections from live page are present.
- No behavioral/data meaning is changed.

## Task 5: Add policies and order-process sections

**Objective:** Port refund, deposit-placement, ordering-cycle, and constraints content.

**Files:**

- Modify: `page-process.php`

**Steps:**

1. Add `Deposit Policies` section with two `.sc-card-panel` cards:
   - `Refund Policy`
   - `Placing a Deposit`
2. Add `The Order Process` section with two cards:
   - `Ordering Cycles`
   - `Configuration & Constraints`
3. Link `Deposit Form` to local `/deposit/`.
4. Preserve one deposit per household per model-list rule.
5. Preserve twice-monthly order cadence and GM allocation timing.
6. Preserve 5-day constraints timing.

**Acceptance criteria:**

- Refund/non-refundable boundaries are clear.
- Deposit position-by-date rule is present.
- Deposit Form link points to `home_url( '/deposit/' )`.
- Order-cycle and constraint timing are preserved.

## Task 6: Add pricing, fees, payment, delivery, restrictions, and staying informed

**Objective:** Complete the remaining guide content and final customer-contact section.

**Files:**

- Modify: `page-process.php`

**Steps:**

1. Add `Pricing Policy` section.
2. Add dealer fees using `.sc-fact-row`:
   - `Dealer Fee` → `$999`
   - `Tag Agency Fee` → `$362`
3. Add `Payment Options` section with cards:
   - `Cash & Check`
   - `Financing`
   - `Leasing`
   - `Your Deposit & Down Payment`
4. Add `Vehicle Delivery` section:
   - `Shipping & Preparation`
   - `Delivery Requirements`
5. Add `Restrictions` section preserving all state/customer/exporter restrictions.
6. Add `Staying Informed` section:
   - safe sender email `smccann@stingraychevrolet.com`
   - phone `813-359-5000`
   - final policy disclaimer

**Acceptance criteria:**

- Fee amounts are present and correct.
- Payment categories from live page are present.
- Delivery requirements are present.
- Restrictions are preserved exactly in meaning.
- Contact email and phone are linked with `mailto:` and `tel:`.

## Task 7: Docs update

**Objective:** Keep project docs aligned with implemented surfaces.

**Files:**

- Modify: `README.md`
- Modify: `docs/superpowers/plans/2026-07-03-surface-integration.md`

**Steps:**

1. Update README `## Surfaces`:
   - Add `page-process.php` as implemented for `/process/`.
   - Remove or replace the “planned dedicated templates remaining” sentence because all six planned templates now exist.
2. In the integration plan, mark Task 8 complete after `php -l page-process.php` passes.
3. Do not mark Task 9 complete unless final QA/report work is actually done.

**Acceptance criteria:**

- README accurately reflects all implemented surfaces.
- Integration plan marks only the process template/lint/commit-pending work complete.

## Task 8: Verification

**Objective:** Prove the spec was implemented without PHP syntax errors or missing critical content.

**Commands:**

Run from repo root:

```bash
php -l page-process.php
php -l functions.php
git status --short
git diff --stat
```

Static content checks:

```bash
grep -n "Corvette Order Process Guide\|Deposit Lists\|Grand Sport X\|ZR1X\|Dealer Fee\|Tag Agency Fee\|smccann@stingraychevrolet.com\|813-359-5000" page-process.php
```

Optional broader lint before final handoff:

```bash
php -l page-order.php
php -l page-calculator.php
php -l page-deposit.php
php -l page-build-and-price.php
php -l page-factory.php
php -l page-process.php
php -l header.php
php -l footer.php
php -l inc/topbar.php
php -l inc/site-footer.php
```

Manual browser/WP preview checklist:

- Open `/process/` in WordPress preview.
- Confirm shared topbar/footer render once.
- Confirm quick action cards route to local slugs.
- Confirm mobile layout does not feel like an over-boxed wall of panels.
- Confirm status grid is readable on mobile and desktop.
- Confirm accordions open/close with mouse and keyboard.
- Confirm contact links work:
  - `mailto:smccann@stingraychevrolet.com`
  - `tel:18133595000`
- Confirm no console errors attributable to this page.
- Confirm no stale old links remain for pages that now have local slugs, especially Deposit Form.

**Acceptance criteria:**

- PHP lint passes.
- Static grep confirms critical content present.
- WordPress preview is reported honestly as run or pending.

## Commit recommendation

After implementation and verification, commit separately:

Suggested subject:

```text
feat: add process guide page
```

Include:

- `page-process.php`
- `README.md`
- `docs/superpowers/plans/2026-07-03-surface-integration.md`
- this spec file if it has not already been committed:
  - `docs/superpowers/plans/2026-07-07-process-surface-completion.md`

Do not include unrelated files.

## Risks / assumptions

- The live process guide contains policy/legal-adjacent restrictions. Preserve meaning; do not soften, omit, or reinterpret without Sean's approval.
- Deposit-list statuses and fees can go stale. This pass ports current live content; final live-readiness should include a business-content review by Sean before deployment.
- This page is long. Prefer scannable sections, cards, and accordions, but avoid excessive nesting and duplicate summaries.
- Existing `.sc-pill` styling may not provide semantic variants for Open/Closed/Discontinued. If visual differentiation is needed, make the smallest scoped CSS addition in `assets/css/surfaces.css` and document it; otherwise use base `.sc-pill`.
- The old live page references legacy URLs. Convert only known planned theme surfaces to local slugs. Keep external/specialty pages such as ZR1 Process Page external unless Sean provides a local replacement.
- This pass does not deploy, version-bump, or replace the live site.

## Non-goals

- Do not build a CMS/admin interface for process content.
- Do not use the CarSales content manager or Cloudflare Worker.
- Do not attach `sales.stingraychevroletcorvette.com` or the CarSales Worker to the public WordPress site.
- Do not modify deposit form, order form, calculator, Formidable, or wpDataTables behavior.
- Do not create new global design-system tokens.
- Do not deploy or push.
- Do not mark final QA Task 9 complete.

## Recommended reasoning level

Medium-high. The implementation is static PHP template work, but the source page is long and policy-heavy. The main risks are omission, accidental semantic change, stale local-vs-live links, and over-boxed visual hierarchy.
