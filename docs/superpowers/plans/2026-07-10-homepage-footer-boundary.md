# Homepage and Footer Boundary Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Move the two external destination cards into homepage content and replace the shared footer with a compact, centered, navigation-free identity and legal block.

**Architecture:** `front-page.php` will own homepage-only discovery links, while `inc/site-footer.php` remains the single shared footer partial and owns only identity/legal content. Homepage-only card styling moves to `assets/homepage/homepage.css`; shared footer styling stays in `assets/css/theme.css`; `style.css` advances to `0.1.15` for cache busting.

**Tech Stack:** WordPress classic-theme PHP, semantic HTML, vanilla CSS, Git, WordPress.com SFTP staging.

## Global Constraints

- Preserve the existing carbon visual direction, ChevySans typography, design tokens, accent behavior, external-card copy, URLs, icons, focus states, hover behavior, and responsive stacking.
- Preserve the existing wordmark, dealer description, signoff, and legal copy.
- Keep `inc/site-footer.php` as the single shared footer partial and `inc/topbar.php` as the primary desktop/mobile navigation owner.
- Do not add dependencies, introduce a build system, edit vendored design-system files, or refactor unrelated code.
- Do not change forms, embeds, calculators, order redirects, submissions, navigation labels, or add leave-page warnings.
- Deploy only to the authorized staging environment; production is out of scope.
- The runtime file set is exactly `front-page.php`, `inc/site-footer.php`, `assets/homepage/homepage.css`, `assets/css/theme.css`, and `style.css`.

---

### Task 1: Reassign Homepage and Footer Content Ownership

**Files:**
- Modify: `front-page.php:136-138`
- Modify: `inc/site-footer.php:1-54`

**Interfaces:**
- Consumes: `get_template_part( 'inc/site-footer' )`, `get_template_directory_uri()`, `esc_url()`, `esc_attr_e()`, and the existing `.sc-ext-*` class contract.
- Produces: exactly two `.sc-ext-card` links in `front-page.php`; a shared `.sc-site-footer` containing only `.sc-foot-wordmark`, `.sc-foot-blurb`, `.sc-foot-signoff`, and `.sc-foot-legal`.

- [ ] **Step 1: Run the structural regression assertion before editing**

```bash
php -r '
$home = file_get_contents("front-page.php");
$footer = file_get_contents("inc/site-footer.php");
$ok = substr_count($home, "sc-ext-card") === 2
    && strpos($footer, "sc-ext-card") === false
    && strpos($footer, "sc-foot-link") === false
    && strpos($footer, chr(36) . "stingray_footer_links") === false;
exit($ok ? 0 : 1);
'
```

Expected: exit status `1`, because the external cards and duplicate navigation still live in `inc/site-footer.php`.

- [ ] **Step 2: Add the homepage-only external destination block**

Insert this markup after the closing `</section>` for `.sc-actions` and before `get_template_part( 'inc/site-footer' )` in `front-page.php`:

```php
<!-- ===================== EXTERNAL DESTINATIONS ===================== -->
<section class="sc-home-external" aria-label="<?php esc_attr_e( 'Explore more', 'stingray-corvette' ); ?>">
	<div class="sc-ext-grid">
		<a class="sc-ext-card" href="https://www.chevrolet.com/">
			<div>
				<div class="sc-ext-eyebrow"><?php esc_html_e( 'Explore the lineup', 'stingray-corvette' ); ?></div>
				<div class="sc-ext-title">Chevrolet.com</div>
			</div>
			<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="8 7 17 7 17 16"></polyline></svg>
		</a>
		<a class="sc-ext-card" href="https://www.stingraychevrolet.com/">
			<div>
				<div class="sc-ext-eyebrow"><?php esc_html_e( 'The full dealership', 'stingray-corvette' ); ?></div>
				<div class="sc-ext-title">Stingray Chevrolet</div>
			</div>
			<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="8 7 17 7 17 16"></polyline></svg>
		</a>
	</div>
</section>
```

- [ ] **Step 3: Reduce the shared footer partial to identity and legal content**

Replace `inc/site-footer.php` with this responsibility-preserving markup:

```php
<?php
/**
 * Shared site footer: wordmark, dealer identity, signoff, and legal line.
 * Used by footer.php (interior pages) and front-page.php.
 *
 * @package Stingray_Corvette
 */

$stingray_assets = get_template_directory_uri() . '/assets/homepage';
?>

<footer class="sc-site-footer">
	<div class="sc-site-footer-inner">
		<div class="sc-foot-row">
			<img src="<?php echo esc_url( $stingray_assets . '/wordmark-white.png' ); ?>" alt="<?php esc_attr_e( 'Stingray Corvette', 'stingray-corvette' ); ?>" class="sc-foot-wordmark">
			<p class="sc-foot-blurb"><?php esc_html_e( 'Corvettes by Sean at Stingray Chevrolet — Plant City, FL. Order, deposit, and track your 2027 Corvette, dealer-direct.', 'stingray-corvette' ); ?></p>
			<p class="sc-foot-signoff"><?php esc_html_e( 'Relax — and enjoy the difference.', 'stingray-corvette' ); ?></p>
			<div class="sc-foot-legal">&copy; 2027 Stingray Chevrolet &middot; Plant City, FL &middot; No affiliation or endorsement implied; Chevrolet and Corvette are marks of General Motors.</div>
		</div>
	</div>
</footer>
```

- [ ] **Step 4: Run PHP syntax and structural assertions**

```bash
php -l front-page.php
php -l inc/site-footer.php
php -r '
$home = file_get_contents("front-page.php");
$footer = file_get_contents("inc/site-footer.php");
$ok = substr_count($home, "sc-ext-card") === 2
    && strpos($footer, "sc-ext-card") === false
    && strpos($footer, "sc-foot-link") === false
    && strpos($footer, chr(36) . "stingray_footer_links") === false;
exit($ok ? 0 : 1);
'
```

Expected: both PHP files report no syntax errors and the structural assertion exits `0`.

---

### Task 2: Separate Homepage Card Styling and Compact the Footer

**Files:**
- Modify: `assets/homepage/homepage.css:115-152`
- Modify: `assets/css/theme.css:96-117`

**Interfaces:**
- Consumes: existing `--content-max`, responsive gutter clamps, `--carbon-panel-strong`, `--line`, `--line-hover`, `--accent`, `--ink`, `--dim`, `--muted`, `--hairline-footer`, and `--transition-fast` tokens.
- Produces: `.sc-home-external` and `.sc-ext-*` as homepage-only styles; `.sc-site-footer`, `.sc-site-footer-inner`, `.sc-foot-row`, `.sc-foot-wordmark`, `.sc-foot-blurb`, `.sc-foot-signoff`, and `.sc-foot-legal` as the complete shared-footer style contract.

- [ ] **Step 1: Run the CSS ownership assertion before editing**

```bash
php -r '
$home = file_get_contents("assets/homepage/homepage.css");
$theme = file_get_contents("assets/css/theme.css");
$ok = strpos($home, ".sc-home-external") !== false
    && strpos($home, ".sc-ext-card") !== false
    && strpos($theme, ".sc-ext-card") === false
    && strpos($theme, ".sc-foot-link") === false
    && strpos($theme, ".sc-foot-legal") !== false;
exit($ok ? 0 : 1);
'
```

Expected: exit status `1`, because homepage card styles still live in `theme.css`, footer link styles still exist, and legal typography is undefined.

- [ ] **Step 2: Add homepage-only external destination styles**

Append this block after the quick-action card rules in `assets/homepage/homepage.css`:

```css
/* ---- Homepage external destinations ---- */
.sc-home-external {
  max-width: var(--content-max);
  margin: 0 auto;
  padding: 0 clamp(16px, 4vw, 40px) clamp(36px, 5vw, 64px);
}
.sc-ext-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(min(100%, 260px), 1fr));
  gap: 14px;
}
.sc-ext-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
  min-height: 88px;
  padding: 22px 24px;
  background: var(--carbon-panel-strong);
  border: 1px solid var(--line);
  border-radius: var(--radius-xl);
  color: var(--ink);
  transition: border-color var(--transition-fast), background var(--transition-fast);
}
.sc-ext-card:hover {
  border-color: var(--line-hover);
  background: #171c1f;
}
.sc-ext-eyebrow {
  font-size: 0.68rem;
  font-weight: var(--weight-bold);
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: var(--dim);
}
.sc-ext-title {
  margin-top: 5px;
  font-size: 1.15rem;
  font-weight: var(--weight-heavy);
}
```

Do not add a new focus override: retain the existing global visible `:focus-visible` treatment.

- [ ] **Step 3: Replace the shared footer CSS block**

Replace the current footer block in `assets/css/theme.css` with:

```css
/* ---- Site footer ---- */
.sc-site-footer {
  border-top: 1px solid var(--hairline-footer);
  margin-top: clamp(20px, 4vw, 48px);
  background: rgba(8, 10, 11, 0.6);
}
.sc-site-footer-inner {
  max-width: var(--content-max);
  margin: 0 auto;
  padding: clamp(28px, 4vw, 44px) clamp(16px, 4vw, 40px);
}
.sc-foot-row {
  display: flex;
  flex-direction: column;
  align-items: center;
  max-width: 760px;
  margin: 0 auto;
  text-align: center;
}
.sc-foot-wordmark {
  display: block;
  width: auto;
  height: 36px;
  opacity: 0.92;
}
.sc-foot-blurb {
  max-width: 48ch;
  margin: 14px 0 0;
  font-size: 0.82rem;
  line-height: var(--leading-relaxed);
  color: var(--dim);
}
.sc-foot-signoff {
  margin: 12px 0 0;
  font-size: 0.68rem;
  font-weight: var(--weight-semibold);
  letter-spacing: var(--tracking-wider);
  text-transform: uppercase;
  color: #5f6469;
}
.sc-foot-legal {
  max-width: 80ch;
  margin-top: 8px;
  font-size: 0.72rem;
  line-height: var(--leading-relaxed);
  color: var(--dim);
}
```

Delete the old `.sc-ext-grid`, `.sc-ext-card`, `.sc-ext-card:hover`, `.sc-ext-eyebrow`, `.sc-ext-title`, `.sc-foot-links`, `.sc-foot-link`, and `.sc-foot-link:hover` rules from `assets/css/theme.css`.

- [ ] **Step 4: Run CSS ownership and whitespace checks**

```bash
php -r '
$home = file_get_contents("assets/homepage/homepage.css");
$theme = file_get_contents("assets/css/theme.css");
$ok = strpos($home, ".sc-home-external") !== false
    && strpos($home, ".sc-ext-card") !== false
    && strpos($theme, ".sc-ext-card") === false
    && strpos($theme, ".sc-foot-link") === false
    && strpos($theme, ".sc-foot-legal") !== false;
exit($ok ? 0 : 1);
'
git diff --check
```

Expected: assertion exits `0`; `git diff --check` produces no output.

---

### Task 3: Version, Validate, Commit, Push, and Deploy to Staging

**Files:**
- Modify: `style.css:7`
- Verify: `front-page.php`
- Verify: `inc/site-footer.php`
- Verify: `assets/homepage/homepage.css`
- Verify: `assets/css/theme.css`

**Interfaces:**
- Consumes: WordPress theme-version cache busting in `functions.php`, origin remote `main`, staging credentials in ignored `.env.local`, and remote theme root `/srv/htdocs/wp-content/themes/stingray-corvette`.
- Produces: Git commit(s) on `main`, pushed `origin/main`, and staging-rendered theme assets with `?ver=0.1.15`; production remains untouched.

- [ ] **Step 1: Bump the theme version**

Change the `style.css` header exactly:

```css
Version: 0.1.15
```

- [ ] **Step 2: Run the full local gate**

```bash
php -l front-page.php
php -l inc/site-footer.php
php -r '
$home = file_get_contents("front-page.php");
$footer = file_get_contents("inc/site-footer.php");
$home_css = file_get_contents("assets/homepage/homepage.css");
$theme_css = file_get_contents("assets/css/theme.css");
$style = file_get_contents("style.css");
$ok = substr_count($home, "sc-ext-card") === 2
    && strpos($footer, "sc-ext-card") === false
    && strpos($footer, "sc-foot-link") === false
    && strpos($home_css, ".sc-home-external") !== false
    && strpos($theme_css, ".sc-ext-card") === false
    && strpos($theme_css, ".sc-foot-link") === false
    && strpos($theme_css, ".sc-foot-legal") !== false
    && strpos($style, "Version: 0.1.15") !== false;
exit($ok ? 0 : 1);
'
git diff --check
git status --short
```

Expected: both PHP files have no syntax errors, the assertion exits `0`, the diff check is clean, and status lists only the approved runtime files plus this plan if it has not yet been committed.

- [ ] **Step 3: Perform local browser verification**

Start the existing local WordPress environment if one is configured. If no local WordPress runtime exists, perform this step immediately after the staging upload instead of creating a new environment.

Verify at desktop and mobile widths:

- Homepage: exactly two external cards directly below `Everything you need`, above the footer divider.
- Homepage: external cards retain visible focus, working URLs, aligned gutters, two-column desktop layout, and one-column mobile layout.
- Interior/form page: no external cards and no footer navigation links.
- Footer: only wordmark, dealer description, signoff, and legal text.
- Footer: centered layout, 36px wordmark, smaller legal copy, clean wrapping, and no overflow.
- Topbar/mobile drawer: all six local destinations remain available.
- No missing assets or browser-console errors.

- [ ] **Step 4: Commit the runtime change intentionally**

```bash
git add front-page.php inc/site-footer.php assets/homepage/homepage.css assets/css/theme.css style.css
git commit -m "feat: separate homepage links from compact footer"
```

Expected: one runtime commit is created and no unrelated files are staged. The
implementation plan was committed before execution began.

- [ ] **Step 5: Push `main` to origin**

```bash
git push origin main
```

Expected: `origin/main` advances to the implementation commit.

- [ ] **Step 6: Upload only the approved runtime files to staging**

Use the ignored `.env.local` staging credentials without printing them. Connect with password authentication to `sftp.wp.com`, confirm the remote root is `/srv/htdocs`, and upload exactly:

```text
front-page.php
inc/site-footer.php
assets/homepage/homepage.css
assets/css/theme.css
style.css
```

Target directory:

```text
/srv/htdocs/wp-content/themes/stingray-corvette/
```

Download the five remote files into a temporary verification directory and compare SHA-256 checksums with the committed local files. If any checksum differs, stop and restore the pre-upload backup before proceeding.

- [ ] **Step 7: Verify the public staging response**

Confirm:

```bash
STAGING=https://staging-427b-stingraychevroletcorvette.wpcomstaging.com
curl -fsSL "$STAGING/wp-content/themes/stingray-corvette/style.css?check=$(date +%s)" | awk -F': ' '/^Version:/{print $2; exit}'
curl -fsSL "$STAGING/?check=$(date +%s)" | rg -o "wp-content/themes/stingray-corvette/[^\"']+[?]ver=[0-9.]+" | sed -E 's/.*[?]ver=//' | sort -u
```

Expected: direct `style.css` reports `0.1.15` and rendered theme asset URLs use only `0.1.15`. If direct files are current but rendered HTML is stale, purge the staging edge cache from WordPress.com and repeat the checks.

Repeat the browser checks from Step 3 against staging at desktop and mobile widths, including one form-oriented interior page. Confirm production was not accessed or modified.

- [ ] **Step 8: Final repository and deployment audit**

```bash
git status --short
git log -3 --oneline --decorate
```

Expected: clean worktree, `HEAD`, `origin/main`, and the pushed implementation commit align; staging serves `0.1.15`; production remains unchanged.
