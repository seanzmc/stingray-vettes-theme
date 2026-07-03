<?php
/**
 * Stingray Corvette theme bootstrap: supports, menus, and the vendored
 * design-system style layer (see README.md for what was vendored and why).
 *
 * @package Stingray_Corvette
 */

define( 'STINGRAY_CORVETTE_VERSION', wp_get_theme()->get( 'Version' ) );

/**
 * Theme supports + nav menu.
 */
function stingray_corvette_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'stingray-corvette' ),
		)
	);
}
add_action( 'after_setup_theme', 'stingray_corvette_setup' );

/**
 * Global style layer.
 *
 * Load order is normative — it comes from the design system's
 * `_ds_manifest.json` → `globalCssPaths`:
 *   tokens/colors.css → tokens/typography.css → tokens/spacing.css
 *   → tokens/base.css → styles.css
 * Fonts enqueue first (ChevySans @font-face, then the Inter fallback stack).
 * Each handle depends on the previous one so the order can't be reshuffled.
 */
function stingray_corvette_enqueue_styles() {
	$uri = get_template_directory_uri();
	$ver = STINGRAY_CORVETTE_VERSION;

	// Fonts first: ChevySans @font-face (local woff2), then Inter as --font-fallback.
	wp_enqueue_style( 'stingray-fonts', $uri . '/assets/css/fonts.css', array(), $ver );
	wp_enqueue_style(
		'stingray-inter',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400..900&display=swap',
		array( 'stingray-fonts' ),
		null
	);

	// Design-system global layer, in manifest order.
	wp_enqueue_style( 'stingray-ds-colors', $uri . '/assets/css/tokens/colors.css', array( 'stingray-inter' ), $ver );
	wp_enqueue_style( 'stingray-ds-typography', $uri . '/assets/css/tokens/typography.css', array( 'stingray-ds-colors' ), $ver );
	wp_enqueue_style( 'stingray-ds-spacing', $uri . '/assets/css/tokens/spacing.css', array( 'stingray-ds-typography' ), $ver );
	wp_enqueue_style( 'stingray-ds-base', $uri . '/assets/css/tokens/base.css', array( 'stingray-ds-spacing' ), $ver );
	wp_enqueue_style( 'stingray-ds-styles', $uri . '/assets/css/styles.css', array( 'stingray-ds-base' ), $ver );

	// Theme chrome shared by every surface (topbar, drawer, buttons, page shell, footer).
	wp_enqueue_style( 'stingray-theme', $uri . '/assets/css/theme.css', array( 'stingray-ds-styles' ), $ver );

	// Interior-surface components (link cards, accordions, pills, step lists).
	wp_enqueue_style( 'stingray-surfaces', $uri . '/assets/css/surfaces.css', array( 'stingray-theme' ), $ver );

	// Homepage-only layer: hero + quick actions + the 360° spin viewer.
	if ( is_front_page() ) {
		wp_enqueue_style( 'stingray-homepage', $uri . '/assets/homepage/homepage.css', array( 'stingray-theme' ), $ver );
		wp_enqueue_script( 'stingray-spin', $uri . '/assets/homepage/spin.js', array(), $ver, true );
		wp_add_inline_script(
			'stingray-spin',
			'window.SC_SPIN_BASE = ' . wp_json_encode( $uri . '/assets/homepage/spin/' ) . ';',
			'before'
		);
	}

	// Order form (/order/): vendored form-app in its 1a "Full Carbon" skin.
	if ( is_page( 'order' ) ) {
		wp_enqueue_style( 'stingray-order', $uri . '/assets/order/order.css', array( 'stingray-surfaces' ), $ver );
		wp_enqueue_script(
			'stingray-turnstile',
			'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit',
			array(),
			null,
			array(
				'in_footer' => true,
				'strategy'  => 'async',
			)
		);
		wp_enqueue_script( 'stingray-order-data', $uri . '/assets/order/data.js', array(), $ver, true );
		wp_enqueue_script( 'stingray-order-app', $uri . '/assets/order/app.js', array( 'stingray-order-data' ), $ver, true );
		wp_add_inline_script(
			'stingray-order-app',
			'window.SC_FORM_ASSET_BASE = ' . wp_json_encode( $uri . '/assets/order/assets/' ) . ';',
			'before'
		);
	}

	// Payment calculator (/calculator/): vendored Stingcalc logic, DS-authored skin.
	if ( is_page( 'calculator' ) ) {
		wp_enqueue_style( 'stingray-calculator', $uri . '/assets/calculator/calculator.css', array( 'stingray-surfaces' ), $ver );
		wp_enqueue_script( 'stingray-calculator', $uri . '/assets/calculator/script.js', array(), $ver, true );
		wp_enqueue_script( 'stingray-calculator-qp', $uri . '/assets/calculator/qp-new.js', array( 'stingray-calculator' ), $ver, true );
	}

	// Dark skins for third-party embeds: Formidable (deposit, B&P) + wpDataTables (@Factory).
	if ( is_page( array( 'deposit', 'build-and-price', 'factory' ) ) ) {
		wp_enqueue_style( 'stingray-embeds', $uri . '/assets/css/embeds.css', array( 'stingray-surfaces' ), $ver );
	}
}
add_action( 'wp_enqueue_scripts', 'stingray_corvette_enqueue_styles' );

/**
 * Primary-menu items render as bare <a> tags inside .sc-nav-links (the topbar
 * partial passes items_wrap '%3$s'); this puts the DS nav-link class on them.
 */
function stingray_corvette_nav_link_class( $atts, $item, $args ) {
	if ( isset( $args->theme_location ) && 'primary' === $args->theme_location ) {
		$atts['class'] = 'sc-nav-link';
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'stingray_corvette_nav_link_class', 10, 3 );

/**
 * The mobile drawer toggle used by the shared topbar (all surfaces).
 */
function stingray_corvette_enqueue_nav_script() {
	wp_enqueue_script( 'stingray-nav', get_template_directory_uri() . '/assets/js/nav.js', array(), STINGRAY_CORVETTE_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'stingray_corvette_enqueue_nav_script' );
