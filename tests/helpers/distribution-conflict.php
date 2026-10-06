<?php
/**
 * Load a second distribution entry with the first copy's identity already set.
 *
 * @package GravityGeolocation\Tests\Fixtures
 */

define( 'ABSPATH', '/fixture/' );
define( 'GFGEO_PACKAGE_TYPE', $argv[1] );
$capable = 'denied' !== ( $argv[2] ?? '' );
$hooks   = [];

function add_action( $hook, $callback ) {
	$GLOBALS['hooks'][ $hook ][] = $callback;
}
function current_user_can( $capability ) {
	return 'activate_plugins' === $capability && $GLOBALS['capable'];
}
function __( $text, $domain ) {
	return $text;
}
function esc_html( $text ) {
	return htmlspecialchars( $text, ENT_QUOTES );
}

// Any bootstrap beyond the early guard would fail: no host/autoloader exists.
require $argv[3] ?? dirname( __DIR__, 2 ) . '/address-autocomplete-for-gravity-forms.php';
$checks = [
	'first package retained' => $argv[1] === GFGEO_PACKAGE_TYPE,
	'no second constants' => ! defined( 'GFGEO_VERSION' ),
	'no second Composer' => ! class_exists( 'GravityGeolocation\Loader', false ),
	'both admin surfaces registered' => isset( $hooks['admin_notices'], $hooks['network_admin_notices'] ),
];
foreach ( [ 'admin_notices', 'network_admin_notices' ] as $hook ) {
	ob_start();
	foreach ( $hooks[ $hook ] as $callback ) {
		$callback();
	}
	$output = ob_get_clean();
	$expected = 'free' === $argv[1] ? 'Please deactivate Address Autocomplete' : 'is not running because';
	$checks[ $hook . ' message' ] = $capable ? false !== strpos( $output, $expected ) : '' === $output;
}
echo json_encode( $checks );
exit( in_array( false, $checks, true ) ? 1 : 0 );
