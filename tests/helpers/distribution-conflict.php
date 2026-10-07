<?php
/**
 * Exercise the deferred premium guard without a live host.
 *
 * @package GravityGeolocationAutocomplete\Tests\Fixtures
 */

define( 'ABSPATH', '/fixture/' );
define( 'GFGEO_VERSION', 'premium-fixture' );
define( 'GFGEO_PACKAGE_TYPE', $argv[1] );
$capable = 'denied' !== ( $argv[2] ?? '' );
$hooks   = [];

function add_action( $hook, $callback ) {
	$GLOBALS['hooks'][ $hook ][] = $callback;
}
function register_activation_hook( $file, $callback ) {}
function plugin_basename( $file ) { return basename( $file ); }
function plugin_dir_path( $file ) { return dirname( $file ) . '/'; }
function plugin_dir_url( $file ) { return 'https://fixture.test/plugin/'; }
function current_user_can( $capability ) {
	return 'activate_plugins' === $capability && $GLOBALS['capable'];
}
function esc_html__( $text, $domain ) {
	return htmlspecialchars( $text, ENT_QUOTES );
}

// Bootstrap is isolated; conflict handling must wait until all files are loaded.
require $argv[3] ?? dirname( __DIR__, 2 ) . '/address-autocomplete-for-gravity-forms.php';
\GravityGeolocationAutocomplete\Loader::load();
$checks = [
	'premium identity retained' => $argv[1] === GFGEO_PACKAGE_TYPE && 'premium-fixture' === GFGEO_VERSION,
	'free identity isolated' => 'free' === GFGEOAC_PACKAGE_TYPE && '1.0.0' === GFGEOAC_VERSION,
	'no premium namespace loaded' => ! class_exists( 'GravityGeolocation\Loader', false ),
	'no runtime or dashboard hooks' => ! isset( $hooks['gform_loaded'] ) && ! isset( $hooks['admin_menu'] ),
	'both admin surfaces registered' => isset( $hooks['admin_notices'], $hooks['network_admin_notices'] ),
];
foreach ( [ 'admin_notices', 'network_admin_notices' ] as $hook ) {
	ob_start();
	foreach ( $hooks[ $hook ] as $callback ) {
		$callback();
	}
	$output = ob_get_clean();
	$expected = 'is not running because';
	$checks[ $hook . ' message' ] = $capable ? false !== strpos( $output, $expected ) : '' === $output;
}
echo json_encode( $checks );
exit( in_array( false, $checks, true ) ? 1 : 0 );
