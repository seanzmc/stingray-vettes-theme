<?php
/**
 * The marketing homepage, ported from stingray-homepage/index.html.
 * Full-bleed: renders its own document (no get_header/get_footer chrome)
 * and shares the topbar/site-footer partials so there is exactly one nav.
 * wp_head()/wp_footer() carry the enqueued DS layer and the admin bar.
 *
 * @package Stingray_Corvette
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'carbon-checker' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'inc/topbar' ); ?>

<!-- ===================== HERO ===================== -->
<header class="sc-hero">
	<div class="sc-hero-glow"></div>
	<div class="sc-hero-row">
		<div class="sc-hero-copy">
			<div class="sc-hero-badge">
				<span class="sc-hero-badge-dot"></span>
				2027 Corvette &middot; Dealer Direct
			</div>
			<h1 class="sc-hero-title">Build the one<br>you actually<br><span class="sc-accent">want.</span></h1>
			<p class="sc-hero-lede">Customize your Corvette exactly how you want it &mdash; the latest options, features, and live pricing &mdash; then place your order with Stingray Chevrolet, a top-10 Corvette dealer in Plant City, FL.</p>
			<div class="sc-hero-ctas">
				<a class="sc-btn-primary" href="<?php echo esc_url( home_url( '/order/' ) ); ?>">
					Start Your Order
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="13 6 19 12 13 18"></polyline></svg>
				</a>
				<a class="sc-btn-ghost" href="<?php echo esc_url( home_url( '/process/' ) ); ?>">See the Process</a>
			</div>
			<div class="sc-hero-trims">
				<span>Stingray</span><span class="sc-accent">&middot;</span><span>Grand Sport</span><span class="sc-accent">&middot;</span><span>Z06</span>
			</div>
		</div>
		<div class="sc-hero-stage">
			<div class="sc-spin-frame">
				<canvas id="spinCanvas"></canvas>
				<div id="spinLoader">
					<span>Loading 360&deg;&hellip;</span>
				</div>
				<div class="sc-spin-pill">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3.5 12a8.5 8.5 0 0 1 14.5-6"></path><polyline points="18 2 18 6.5 13.5 6.5"></polyline><path d="M20.5 12a8.5 8.5 0 0 1-14.5 6"></path><polyline points="6 22 6 17.5 10.5 17.5"></polyline></svg>
					<span id="spinHint">360&deg; &middot; Scroll to spin</span>
				</div>
			</div>
		</div>
	</div>
</header>

<!-- ===================== QUICK ACTIONS ===================== -->
<section class="sc-actions">
	<div class="sc-actions-head">
		<div>
			<div class="sc-actions-eyebrow">One Click Away</div>
			<h2 class="sc-actions-title">Everything you need</h2>
		</div>
		<p class="sc-actions-lede">Start an order, lock your allocation, or check where your build stands &mdash; all from here.</p>
	</div>

	<div class="sc-actions-grid">

		<a class="sc-card sc-card--priority sc-card--lead" href="<?php echo esc_url( home_url( '/order/' ) ); ?>">
			<div class="sc-card-head">
				<div class="sc-card-icon sc-card-icon--solid">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="4" width="14" height="17" rx="2"></rect><path d="M9 4V3a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v1"></path><line x1="9" y1="10" x2="15" y2="10"></line><line x1="9" y1="14" x2="15" y2="14"></line></svg>
				</div>
				<span class="sc-card-pill">Start Here</span>
			</div>
			<div>
				<div class="sc-card-title">Order Form</div>
				<p class="sc-card-desc">Configure your Corvette by RPO, watch the live MSRP, and submit your build to the dealer.</p>
			</div>
		</a>

		<a class="sc-card sc-card--priority" href="<?php echo esc_url( home_url( '/deposit/' ) ); ?>">
			<div class="sc-card-icon sc-card-icon--accent">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.5 8 8 11 4.5-3 8-6 8-11V5z"></path><path d="M9 12l2 2 4-4"></path></svg>
			</div>
			<div>
				<div class="sc-card-title">Deposit Form</div>
				<p class="sc-card-desc">Secure your allocation with a refundable deposit and reserve your place in line.</p>
			</div>
		</a>

		<a class="sc-card sc-card--priority" href="<?php echo esc_url( home_url( '/process/' ) ); ?>">
			<div class="sc-card-icon sc-card-icon--accent">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="6" r="2.4"></circle><circle cx="6" cy="18" r="2.4"></circle><path d="M6 8.5v7"></path><path d="M8.5 6H16a3 3 0 0 1 0 6H9a3 3 0 0 0 0 6h5"></path></svg>
			</div>
			<div>
				<div class="sc-card-title">Process Guidelines</div>
				<p class="sc-card-desc">Know exactly what happens from order to delivery &mdash; every step, in plain terms.</p>
			</div>
		</a>

		<a class="sc-card" href="<?php echo esc_url( home_url( '/build-and-price/' ) ); ?>">
			<div class="sc-card-icon">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 13a4 4 0 0 0 6 .5l3-3a4 4 0 0 0-5.7-5.7l-1.5 1.5"></path><path d="M15 11a4 4 0 0 0-6-.5l-3 3a4 4 0 0 0 5.7 5.7l1.5-1.5"></path></svg>
			</div>
			<div>
				<div class="sc-card-title">Build &amp; Price Share</div>
				<p class="sc-card-desc">Send us your GM Build &amp; Price link and we'll turn it into a real order.</p>
			</div>
		</a>

		<a class="sc-card" href="<?php echo esc_url( home_url( '/calculator/' ) ); ?>">
			<div class="sc-card-icon">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="14" height="18" rx="2"></rect><line x1="8" y1="7" x2="16" y2="7"></line><line x1="8" y1="11" x2="8.01" y2="11"></line><line x1="12" y1="11" x2="12.01" y2="11"></line><line x1="16" y1="11" x2="16.01" y2="11"></line><line x1="8" y1="15" x2="8.01" y2="15"></line><line x1="12" y1="15" x2="12.01" y2="15"></line><line x1="16" y1="15" x2="16" y2="17"></line></svg>
			</div>
			<div>
				<div class="sc-card-title">Payment Calculator</div>
				<p class="sc-card-desc">Estimate your monthly payment before you commit to the build.</p>
			</div>
		</a>

		<a class="sc-card" href="<?php echo esc_url( home_url( '/factory/' ) ); ?>">
			<div class="sc-card-icon">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21V10l6 4V10l6 4V6l3-2v17z"></path><line x1="3" y1="21" x2="21" y2="21"></line></svg>
			</div>
			<div>
				<div class="sc-card-title">Orders @ Factory</div>
				<p class="sc-card-desc">Track builds that are scheduled, sequenced, and in production at the plant.</p>
			</div>
		</a>

	</div>
</section>

<?php get_template_part( 'inc/site-footer' ); ?>

<?php wp_footer(); ?>
</body>
</html>
