<?php
/** Run: php tests/legacy-redirects.php */

function add_action() {}
function wp_parse_url( $url, $component = -1 ) { return parse_url( $url, $component ); }
function trailingslashit( $value ) { return rtrim( $value, '/' ) . '/'; }
function wp_unslash( $value ) { return $value; }
function home_url( $path = '' ) { return 'https://stingraychevroletcorvette.com' . $path; }
function wp_safe_redirect() { return true; }

require dirname( __DIR__ ) . '/inc/legacy-redirects.php';

$expected = array(
	'/order-landing-page/'                            => '/order/',
	'/order-and-production-update/'                  => '/order/',
	'/order-landing-page/deposit-form/'               => '/deposit/',
	'/order-landing-page/build-and-price-link-share/' => '/build-and-price/',
	'/orders-in-production/'                          => '/factory/',
	'/corvette-process-guide/'                        => '/process/',
	'/process-links/'                                  => '/process/',
);
$failures = array();

if ( $expected !== stingray_corvette_legacy_redirect_map() ) {
	$failures[] = 'The legacy redirect map must match the approved paths exactly.';
}

foreach ( $expected as $source => $destination ) {
	if ( $destination !== stingray_corvette_get_legacy_redirect_path( $source . '?source=test' ) ) {
		$failures[] = "Failed mapping: {$source}";
	}
	if ( isset( $expected[ $destination ] ) ) {
		$failures[] = "Redirect destination is also a legacy source: {$destination}";
	}
}

if ( '/factory/' !== stingray_corvette_get_legacy_redirect_path( '/orders-in-production' ) ) {
	$failures[] = 'A missing trailing slash must still normalize safely.';
}

if ( '' !== stingray_corvette_get_legacy_redirect_path( '/learning-center/' ) ) {
	$failures[] = 'Unmapped public pages must remain unchanged.';
}

if ( $failures ) {
	fwrite( STDERR, implode( "\n", $failures ) . "\n" );
	exit( 1 );
}

echo "Legacy redirect regression tests passed.\n";
