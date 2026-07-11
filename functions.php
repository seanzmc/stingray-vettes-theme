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

require_once get_template_directory() . '/inc/legacy-redirects.php';

/**
 * Keep the local /order/ route while sending customers to the canonical app.
 *
 * The 27vette main branch deploys the authoritative order runtime at the
 * dedicated order subdomain. A temporary redirect keeps this launch reversible
 * and prevents the theme's dormant vendored fallback from drifting into use.
 */
function stingray_corvette_redirect_order_page() {
	if ( ! is_page( 'order' ) ) {
		return;
	}

	nocache_headers();
	wp_redirect( 'https://order.stingraychevroletcorvette.com/', 302, 'Stingray Corvette' );
	exit;
}
add_action( 'template_redirect', 'stingray_corvette_redirect_order_page', 1 );

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

	// Dormant local fallback if the canonical /order/ redirect is intentionally disabled.
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

	// Factory order lookup: compact table plus an accessible full-row dialog.
	if ( is_page( 'factory' ) ) {
		wp_enqueue_script( 'stingray-factory-table', $uri . '/assets/js/factory-table.js', array(), $ver, true );
	}

	// Payment calculator (/calculator/): vendored Stingcalc logic, DS-authored skin.
	if ( is_page( 'calculator' ) ) {
		wp_enqueue_style( 'stingray-calculator', $uri . '/assets/calculator/calculator.css', array( 'stingray-surfaces' ), $ver );
		wp_enqueue_script( 'stingray-calculator', $uri . '/assets/calculator/script.js', array(), $ver, true );
		wp_enqueue_script( 'stingray-calculator-qp', $uri . '/assets/calculator/qp-new.js', array( 'stingray-calculator' ), $ver, true );
	}
}
add_action( 'wp_enqueue_scripts', 'stingray_corvette_enqueue_styles' );

/**
 * Load dark embed skins for Formidable-backed pages.
 *
 * wpDataTables registers its page-specific skin while rendering the shortcode,
 * after wp_head has printed normal theme styles, so Factory is handled below.
 */
function stingray_corvette_enqueue_embed_styles() {
	if ( ! is_page( array( 'deposit', 'build-and-price' ) ) ) {
		return;
	}

	wp_enqueue_style(
		'stingray-embeds',
		get_template_directory_uri() . '/assets/css/embeds.css',
		array( 'stingray-surfaces' ),
		STINGRAY_CORVETTE_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'stingray_corvette_enqueue_embed_styles', 100 );

/**
 * Print the Factory embed skin after wpDataTables has rendered its shortcode
 * and registered the active table skin.
 */
function stingray_corvette_print_factory_embed_styles() {
	if ( ! is_page( 'factory' ) ) {
		return;
	}

	$dependencies = array( 'stingray-surfaces' );

	if ( wp_style_is( 'wdt-skin-mojito', 'registered' ) ) {
		$dependencies[] = 'wdt-skin-mojito';
	} elseif ( wp_style_is( 'wdt-wpdatatables', 'registered' ) ) {
		$dependencies[] = 'wdt-wpdatatables';
	}

	wp_enqueue_style(
		'stingray-embeds',
		get_template_directory_uri() . '/assets/css/embeds.css',
		$dependencies,
		STINGRAY_CORVETTE_VERSION
	);
	wp_print_styles( 'stingray-embeds' );
}
add_action( 'wp_footer', 'stingray_corvette_print_factory_embed_styles', 100 );

/**
 * Select the published worksheet requested by the configured Factory wpDataTable.
 *
 * wpDataTables 7.5.1 drops the gid when it converts a published 2PACX Google
 * Sheets URL to CSV, which loads the publication's first tab instead of the
 * Factory sheet. Fetch the requested tab and return its public columns while
 * preserving the page-owned source URL in wpDataTables.
 *
 * @param array  $sheet_rows Parsed rows supplied by wpDataTables.
 * @param int    $table_id   wpDataTables table ID.
 * @param string $sheet_url  Configured published Google Sheets URL.
 * @return array
 */
function stingray_corvette_filter_factory_sheet_rows( $sheet_rows, $table_id, $sheet_url ) {
	if ( 0 >= (int) $table_id || ! is_string( $sheet_url ) ) {
		return $sheet_rows;
	}

	$url_parts = wp_parse_url( $sheet_url );
	if (
		empty( $url_parts['scheme'] ) ||
		empty( $url_parts['host'] ) ||
		empty( $url_parts['path'] ) ||
		'docs.google.com' !== strtolower( $url_parts['host'] ) ||
		0 !== strpos( $url_parts['path'], '/spreadsheets/d/e/' )
	) {
		return $sheet_rows;
	}

	$query_args = array();
	if ( ! empty( $url_parts['query'] ) ) {
		parse_str( $url_parts['query'], $query_args );
	}

	if ( empty( $query_args['gid'] ) ) {
		return $sheet_rows;
	}

	$csv_path = preg_replace( '#/pubhtml$#', '/pub', $url_parts['path'] );
	if ( $csv_path === $url_parts['path'] ) {
		return $sheet_rows;
	}

	$csv_url = add_query_arg(
		array(
			'gid'    => sanitize_text_field( $query_args['gid'] ),
			'single' => 'true',
			'output' => 'csv',
		),
		$url_parts['scheme'] . '://' . $url_parts['host'] . $csv_path
	);
	$cache_key = 'stingray_factory_sheet_' . md5( $csv_url );
	$cached    = get_transient( $cache_key );

	if (
		is_array( $cached ) &&
		array_key_exists( 'rows', $cached ) &&
		is_array( $cached['rows'] )
	) {
		return $cached['rows'];
	}

	$response = wp_safe_remote_get(
		$csv_url,
		array(
			'timeout'     => 8,
			'redirection' => 3,
		)
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return $sheet_rows;
	}

	$csv_body = wp_remote_retrieve_body( $response );
	if ( '' === $csv_body ) {
		return $sheet_rows;
	}

	$stream = fopen( 'php://temp', 'r+' );
	if ( false === $stream ) {
		return $sheet_rows;
	}

	fwrite( $stream, $csv_body );
	rewind( $stream );
	$source_headers = fgetcsv( $stream, 0, ',', '"', '\\' );

	if ( ! is_array( $source_headers ) || count( $source_headers ) < 3 ) {
		fclose( $stream );
		return $sheet_rows;
	}

	$source_headers = array_map(
		static function ( $header ) {
			return trim( preg_replace( '/\s+/', ' ', (string) $header ) );
		},
		$source_headers
	);

	if (
		'Order #' !== $source_headers[0] ||
		'Last Updated @ Factory' !== $source_headers[1] ||
		'Current Event' !== $source_headers[2]
	) {
		fclose( $stream );
		return $sheet_rows;
	}

	$factory_rows = array();
	while ( false !== ( $row = fgetcsv( $stream, 0, ',', '"', '\\' ) ) ) {
		if ( '' === trim( implode( '', $row ) ) ) {
			continue;
		}

		$row = array_pad( $row, count( $source_headers ), '' );
		$row = array_map( 'trim', array_slice( $row, 0, count( $source_headers ) ) );
		$row = array_combine( $source_headers, $row );

		if ( false !== $row ) {
			$factory_rows[] = $row;
		}
	}

	fclose( $stream );
	set_transient(
		$cache_key,
		array( 'rows' => $factory_rows ),
		5 * MINUTE_IN_SECONDS
	);

	return $factory_rows;
}
add_filter( 'wpdatatables_filter_google_sheet_array', 'stingray_corvette_filter_factory_sheet_rows', 10, 3 );

/**
 * Render a page-owned embed shortcode.
 *
 * Templates own the surrounding layout and .sc-embed styling. The current
 * WordPress page owns the actual plugin shortcode so staging/live can use
 * different Formidable or wpDataTables IDs without editing theme files.
 *
 * @param string $meta_key Custom field key that stores the complete shortcode.
 * @return string Rendered embed markup, or an admin-only setup note.
 */
function stingray_corvette_render_page_embed_shortcode( $meta_key = 'stingray_embed_shortcode' ) {
	$post_id = get_queried_object_id();

	if ( ! $post_id ) {
		return '';
	}

	$shortcode = get_post_meta( $post_id, $meta_key, true );
	$shortcode = is_string( $shortcode ) ? trim( $shortcode ) : '';

	if ( '' === $shortcode ) {
		if ( current_user_can( 'edit_post', $post_id ) ) {
			return sprintf(
				'<div class="sc-note sc-note--info"><strong>%1$s</strong><span>%2$s <code>%3$s</code>.</span></div>',
				esc_html__( 'Embed shortcode missing.', 'stingray-corvette' ),
				esc_html__( 'Add the complete shortcode to this page custom field:', 'stingray-corvette' ),
				esc_html( $meta_key )
			);
		}

		return '';
	}

	$rendered = do_shortcode( $shortcode );

	if ( $rendered === $shortcode ) {
		if ( current_user_can( 'edit_post', $post_id ) ) {
			return sprintf(
				'<div class="sc-note sc-note--info"><strong>%1$s</strong><span>%2$s</span></div>',
				esc_html__( 'Embed shortcode did not render.', 'stingray-corvette' ),
				esc_html__( 'Confirm the required plugin is active and the page custom field shortcode is valid.', 'stingray-corvette' )
			);
		}

		return '';
	}

	return $rendered;
}

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
