<?php
/**
 * Regression tests for the Factory Google Sheet filter.
 *
 * Run: php tests/factory-sheet-filter.php
 */

class Stingray_Test_Theme {
	public function get( $key ) {
		return '0.1.9';
	}
}

function wp_get_theme() {
	return new Stingray_Test_Theme();
}

function add_action() {}
function add_filter() {}
function wp_parse_url( $url ) {
	return parse_url( $url );
}
function sanitize_text_field( $value ) {
	return trim( (string) $value );
}
function add_query_arg( $args, $url ) {
	return $url . '?' . http_build_query( $args );
}
function is_wp_error() {
	return false;
}
function wp_safe_remote_get() {
	$GLOBALS['stingray_remote_calls']++;
	return array(
		'response' => array( 'code' => 200 ),
		'body'     => $GLOBALS['stingray_csv_body'],
	);
}
function wp_remote_retrieve_response_code( $response ) {
	return $response['response']['code'];
}
function wp_remote_retrieve_body( $response ) {
	return $response['body'];
}
function get_transient( $key ) {
	return array_key_exists( $key, $GLOBALS['stingray_transients'] )
		? $GLOBALS['stingray_transients'][ $key ]
		: false;
}
function set_transient( $key, $value ) {
	$GLOBALS['stingray_transients'][ $key ] = $value;
	return true;
}

if ( ! defined( 'MINUTE_IN_SECONDS' ) ) {
	define( 'MINUTE_IN_SECONDS', 60 );
}

require dirname( __DIR__ ) . '/functions.php';

$source_url = 'https://docs.google.com/spreadsheets/d/e/test/pubhtml?gid=520639850&single=true';
$fallback   = array( array( 'wrong' => 'default worksheet' ) );
$headers    = "Order #,Last Updated @ Factory,Current Event,TPW\n";
$failures   = array();

function stingray_test_reset( $csv_body ) {
	$GLOBALS['stingray_csv_body']   = $csv_body;
	$GLOBALS['stingray_remote_calls'] = 0;
	$GLOBALS['stingray_transients'] = array();
}

function stingray_test_same( $expected, $actual, $message ) {
	global $failures;
	if ( $expected !== $actual ) {
		$failures[] = $message . "\nExpected: " . var_export( $expected, true ) . "\nActual: " . var_export( $actual, true );
	}
}

stingray_test_reset( $headers );
$rows = stingray_corvette_filter_factory_sheet_rows( $fallback, 7, $source_url );
stingray_test_same( array(), $rows, 'A valid header-only worksheet must return no rows instead of the wrong worksheet fallback.' );

stingray_test_reset( $headers . "FMNPZX,10/13/2025,5000\n" );
$rows = stingray_corvette_filter_factory_sheet_rows( $fallback, 7, $source_url );
stingray_test_same(
	array(
		array(
			'Order #'                  => 'FMNPZX',
			'Last Updated @ Factory'   => '10/13/2025',
			'Current Event'            => '5000',
			'TPW'                      => '',
		),
	),
	$rows,
	'A CSV record with omitted trailing empty fields must be padded and retained.'
);

stingray_test_reset( $headers . "FMNPZX,10/13/2025,5000,\n" );
stingray_corvette_filter_factory_sheet_rows( $fallback, 7, $source_url );
stingray_corvette_filter_factory_sheet_rows( $fallback, 7, $source_url );
stingray_test_same( 1, $GLOBALS['stingray_remote_calls'], 'A successful worksheet response must be reused from a short-lived cache.' );

if ( $failures ) {
	fwrite( STDERR, implode( "\n\n", $failures ) . "\n" );
	exit( 1 );
}

echo "Factory sheet filter regression tests passed.\n";
