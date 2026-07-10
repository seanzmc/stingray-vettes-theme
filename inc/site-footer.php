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
