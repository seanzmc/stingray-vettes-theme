<?php
/**
 * Shared sticky topbar + mobile drawer. Used by header.php (interior pages)
 * and front-page.php (full-bleed homepage) so there is exactly one nav.
 *
 * Links point at the local WordPress slugs from SiteWireframe.md item 1;
 * pages that don't exist yet still get their intended route.
 *
 * @package Stingray_Corvette
 */

$stingray_nav_links = array(
	array( 'label' => __( 'Order', 'stingray-corvette' ), 'url' => home_url( '/order/' ) ),
	array( 'label' => __( 'Deposit', 'stingray-corvette' ), 'url' => home_url( '/deposit/' ) ),
	array( 'label' => __( 'Build & Price', 'stingray-corvette' ), 'url' => home_url( '/build-and-price/' ) ),
	array( 'label' => __( 'Calculator', 'stingray-corvette' ), 'url' => home_url( '/calculator/' ) ),
	array( 'label' => __( 'Factory', 'stingray-corvette' ), 'url' => home_url( '/factory/' ) ),
	array( 'label' => __( 'Process', 'stingray-corvette' ), 'url' => home_url( '/process/' ) ),
);

$stingray_drawer_links = array(
	array( 'label' => __( 'Order Form', 'stingray-corvette' ), 'url' => home_url( '/order/' ) ),
	array( 'label' => __( 'Deposit Form', 'stingray-corvette' ), 'url' => home_url( '/deposit/' ) ),
	array( 'label' => __( 'Build & Price Share', 'stingray-corvette' ), 'url' => home_url( '/build-and-price/' ) ),
	array( 'label' => __( 'Payment Calculator', 'stingray-corvette' ), 'url' => home_url( '/calculator/' ) ),
	array( 'label' => __( 'Orders @ Factory', 'stingray-corvette' ), 'url' => home_url( '/factory/' ) ),
	array( 'label' => __( 'Process Guidelines', 'stingray-corvette' ), 'url' => home_url( '/process/' ) ),
);

$stingray_assets = get_template_directory_uri() . '/assets/homepage';
?>

<div class="sc-topbar">
	<div class="sc-topbar-stripe"></div>
	<nav class="sc-nav">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="sc-nav-brand">
			<img src="<?php echo esc_url( $stingray_assets . '/crossflags-white.png' ); ?>" alt="<?php esc_attr_e( 'Corvette', 'stingray-corvette' ); ?>" class="sc-nav-flags">
			<img src="<?php echo esc_url( $stingray_assets . '/wordmark-white.png' ); ?>" alt="<?php esc_attr_e( 'Stingray Corvette', 'stingray-corvette' ); ?>" class="sc-nav-wordmark">
		</a>
		<div class="sc-nav-links">
			<?php if ( has_nav_menu( 'primary' ) ) : ?>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'items_wrap'     => '%3$s',
						'depth'          => 1,
					)
				);
				?>
			<?php else : ?>
				<?php foreach ( $stingray_nav_links as $stingray_link ) : ?>
					<a class="sc-nav-link" href="<?php echo esc_url( $stingray_link['url'] ); ?>"><?php echo esc_html( $stingray_link['label'] ); ?></a>
				<?php endforeach; ?>
			<?php endif; ?>
			<a class="sc-nav-cta" href="<?php echo esc_url( home_url( '/order/' ) ); ?>"><?php esc_html_e( 'Start Your Order', 'stingray-corvette' ); ?></a>
		</div>
		<button class="sc-burger" id="menuBtn" aria-label="<?php esc_attr_e( 'Menu', 'stingray-corvette' ); ?>" aria-expanded="false" aria-controls="drawer">
			<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><line x1="3" y1="7" x2="21" y2="7"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="17" x2="21" y2="17"></line></svg>
		</button>
	</nav>
</div>

<div class="sc-drawer" id="drawer">
	<div class="sc-drawer-head">
		<img src="<?php echo esc_url( $stingray_assets . '/wordmark-white.png' ); ?>" alt="<?php esc_attr_e( 'Stingray Corvette', 'stingray-corvette' ); ?>" class="sc-drawer-wordmark">
		<button id="closeBtn" class="sc-drawer-close" aria-label="<?php esc_attr_e( 'Close', 'stingray-corvette' ); ?>">&times;</button>
	</div>
	<div class="sc-drawer-links">
		<?php foreach ( $stingray_drawer_links as $stingray_link ) : ?>
			<a class="sc-drawer-link" href="<?php echo esc_url( $stingray_link['url'] ); ?>"><?php echo esc_html( $stingray_link['label'] ); ?><span>&rsaquo;</span></a>
		<?php endforeach; ?>
	</div>
	<div class="sc-drawer-foot">
		<a href="https://www.chevrolet.com/"><?php esc_html_e( 'Chevrolet.com', 'stingray-corvette' ); ?> &#8599;</a>
		<a href="https://www.stingraychevrolet.com/"><?php esc_html_e( 'Stingray Chevrolet', 'stingray-corvette' ); ?> &#8599;</a>
	</div>
</div>
