<?php
/**
 * Shared site footer: external cards + wordmark + quick links + legal line.
 * Used by footer.php (interior pages) and front-page.php.
 *
 * @package Stingray_Corvette
 */

$stingray_assets = get_template_directory_uri() . '/assets/homepage';

$stingray_footer_links = array(
	array( 'label' => __( 'Order Form', 'stingray-corvette' ), 'url' => home_url( '/order/' ) ),
	array( 'label' => __( 'Deposit Form', 'stingray-corvette' ), 'url' => home_url( '/deposit/' ) ),
	array( 'label' => __( 'Build & Price Share', 'stingray-corvette' ), 'url' => home_url( '/build-and-price/' ) ),
	array( 'label' => __( 'Payment Calculator', 'stingray-corvette' ), 'url' => home_url( '/calculator/' ) ),
	array( 'label' => __( 'Orders @ Factory', 'stingray-corvette' ), 'url' => home_url( '/factory/' ) ),
	array( 'label' => __( 'Process Guidelines', 'stingray-corvette' ), 'url' => home_url( '/process/' ) ),
);
?>

<footer class="sc-site-footer">
	<div class="sc-site-footer-inner">
		<div class="sc-ext-grid">
			<a class="sc-ext-card" href="https://www.chevrolet.com/">
				<div>
					<div class="sc-ext-eyebrow"><?php esc_html_e( 'Explore the lineup', 'stingray-corvette' ); ?></div>
					<div class="sc-ext-title">Chevrolet.com</div>
				</div>
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="8 7 17 7 17 16"></polyline></svg>
			</a>
			<a class="sc-ext-card" href="https://www.stingraychevrolet.com/">
				<div>
					<div class="sc-ext-eyebrow"><?php esc_html_e( 'The full dealership', 'stingray-corvette' ); ?></div>
					<div class="sc-ext-title">Stingray Chevrolet</div>
				</div>
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="8 7 17 7 17 16"></polyline></svg>
			</a>
		</div>

		<div class="sc-foot-row">
			<div>
				<img src="<?php echo esc_url( $stingray_assets . '/wordmark-white.png' ); ?>" alt="<?php esc_attr_e( 'Stingray Corvette', 'stingray-corvette' ); ?>" class="sc-foot-wordmark">
				<p class="sc-foot-blurb"><?php esc_html_e( 'Corvettes by Sean at Stingray Chevrolet — Plant City, FL. Order, deposit, and track your 2027 Corvette, dealer-direct.', 'stingray-corvette' ); ?></p>
				<p class="sc-foot-signoff"><?php esc_html_e( 'Relax — and enjoy the difference.', 'stingray-corvette' ); ?></p>
			</div>
			<div class="sc-foot-links">
				<?php foreach ( $stingray_footer_links as $stingray_link ) : ?>
					<a class="sc-foot-link" href="<?php echo esc_url( $stingray_link['url'] ); ?>"><?php echo esc_html( $stingray_link['label'] ); ?></a>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="sc-foot-legal">&copy; 2027 Stingray Chevrolet &middot; Plant City, FL &middot; No affiliation or endorsement implied; Chevrolet and Corvette are marks of General Motors.</div>
	</div>
</footer>
