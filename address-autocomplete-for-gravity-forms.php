<?php
/**
 * Plugin Name:            Address Autocomplete for Gravity Forms
 * Plugin URI:             https://gravitygeolocation.com
 * Description:            Modern Google Places address autocomplete for Gravity Forms. Includes a single-line Address field, native Address autofill, country and language controls, location bias, required suggestion selection, and entry map links. Requires Gravity Forms and your own Google API key.
 * Author:                 Eyal Fitoussi
 * Author URI:             https://www.wpgeo.com
 * Version:                1.0.0
 * Requires at least:      6.5
 * Tested up to:           7.1
 * Requires PHP:           7.4
 * Requires Gravity Forms: 2.10.3+
 * Text Domain:            address-autocomplete-for-gravity-forms
 * Domain Path:            /languages/
 * License:                GPL-3.0-or-later
 * License URI:            https://www.gnu.org/licenses/gpl-3.0.html
 *
 * Copyright: 2026 Eyal Fitoussi
 *
 * @package address-autocomplete-for-gravity-forms
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Distributions share PHP identities. Keep the first copy; do not load a second.
if ( \defined( 'GFGEO_PACKAGE_TYPE' ) ) {
	$gfgeo_conflict_notice = static function () {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$message = 'free' === GFGEO_PACKAGE_TYPE
			? __( 'Please deactivate Address Autocomplete for Gravity Forms so Gravity Geolocation or Gravity Search can run.', 'address-autocomplete-for-gravity-forms' )
			: __( 'Address Autocomplete for Gravity Forms is not running because Gravity Geolocation or Gravity Search is already loaded. You can deactivate and remove the free plugin.', 'address-autocomplete-for-gravity-forms' );

		echo '<div class="notice notice-warning"><p>' . esc_html( $message ) . '</p></div>';
	};

	add_action( 'admin_notices', $gfgeo_conflict_notice );
	add_action( 'network_admin_notices', $gfgeo_conflict_notice );

	return;
}

// Define plugin constants early so they are available for class constants.
define( 'GFGEO_VERSION', '1.0.0' );
define( 'GFGEO_PLUGIN_FILE', __FILE__ );
define( 'GFGEO_BASENAME', plugin_basename( __FILE__ ) );
define( 'GFGEO_PREFIX', 'gfgeo' );
define( 'GFGEO_SLUG', 'gravity_geolocation' );
define( 'GFGEO_PLUGIN_NAME', 'Address Autocomplete for Gravity Forms' );
define( 'GFGEO_PATH', plugin_dir_path( __FILE__ ) );
define( 'GFGEO_URL', plugin_dir_url( __FILE__ ) );
define( 'GFGEO_PACKAGE_TYPE', 'free' );
define( 'GFGEO_PACKAGE_LABEL', 'Address Autocomplete' );
define( 'GFGEO_ITEM_ID', '0' );
define( 'GFGEO_ITEM_NAME', 'Address Autocomplete for Gravity Forms' );
define( 'GFGEO_SITE_URL', 'https://gravitygeolocation.com' );

$gfgeo_dependencies = [
	'gravityforms' => '2.10.3',
];
define( 'GFGEO_DEPENDENCIES', $gfgeo_dependencies );

// Own classes only. Runtime does not require Composer, framework or account SDK.
// The autoloader keeps its conventional class-name parameter.
// phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.classFound
spl_autoload_register( static function ( $class ) {
	$prefix = 'GravityGeolocation\\';
	if ( 0 !== strpos( $class, $prefix ) ) {
		return;
	}
	$file = __DIR__ . '/src/' . str_replace( '\\', '/', substr( $class, strlen( $prefix ) ) ) . '.php';
	if ( is_file( $file ) ) {
		require_once $file;
	}
} );

// Register the local product page independently of the Gravity Forms host.
\GravityGeolocation\Loader::register_admin();

// Defer host-specific add-on initialization until Gravity Forms has loaded.
add_action( 'gform_loaded', [ \GravityGeolocation\Loader::class, 'load' ], 8 );
