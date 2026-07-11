<?php
/**
 * Temporary, reversible redirects from replaced legacy surfaces.
 *
 * @package Stingray_Corvette
 */

function stingray_corvette_legacy_redirect_map() {
	return array(
		'/order-landing-page/'                            => '/order/',
		'/order-and-production-update/'                  => '/order/',
		'/order-landing-page/deposit-form/'               => '/deposit/',
		'/order-landing-page/build-and-price-link-share/' => '/build-and-price/',
		'/orders-in-production/'                          => '/factory/',
		'/corvette-process-guide/'                        => '/process/',
		'/process-links/'                                  => '/process/',
	);
}

function stingray_corvette_normalize_legacy_path( $request_uri ) {
	$path = wp_parse_url( $request_uri, PHP_URL_PATH );
	if ( ! is_string( $path ) || '' === $path ) {
		return '';
	}

	return trailingslashit( '/' . ltrim( $path, '/' ) );
}

function stingray_corvette_get_legacy_redirect_path( $request_uri ) {
	$path = stingray_corvette_normalize_legacy_path( $request_uri );
	$map  = stingray_corvette_legacy_redirect_map();

	return isset( $map[ $path ] ) ? $map[ $path ] : '';
}

function stingray_corvette_redirect_legacy_path() {
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$destination = stingray_corvette_get_legacy_redirect_path( $request_uri );

	if ( '' === $destination ) {
		return;
	}

	wp_safe_redirect( home_url( $destination ), 302, 'Stingray Corvette' );
	exit;
}
add_action( 'template_redirect', 'stingray_corvette_redirect_legacy_path', 0 );
