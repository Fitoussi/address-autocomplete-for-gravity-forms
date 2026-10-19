<?php
/**
 * Plugin Name:            Logical Address Autocomplete for Gravity Forms
 * Plugin URI:             https://gravitygeolocation.com
 * Description:            Modern Google Places address autocomplete for Gravity Forms. Includes a single-line Address field, native Address autofill, country and language controls, location bias, required suggestion selection, and entry map links. Requires Gravity Forms and your own Google API key.
 * Author:                 Eyal Fitoussi
 * Author URI:             https://www.wpgeo.com
 * Version:                1.0.1
 * Requires at least:      6.5
 * Requires PHP:           7.4
 * Requires Gravity Forms: 2.10.3+
 * Text Domain:            wpgeo-address-autocomplete-for-gravity-forms
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

// Isolated bootstrap; saved gfgeo field/settings identities remain compatible.
define( 'GFGEOAC_VERSION', '1.0.1' );
define( 'GFGEOAC_PLUGIN_FILE', __FILE__ );
define( 'GFGEOAC_BASENAME', plugin_basename( __FILE__ ) );
define( 'GFGEOAC_PREFIX', 'gfgeo' );
define( 'GFGEOAC_PLUGIN_NAME', 'Address Autocomplete for Gravity Forms' );
define( 'GFGEOAC_PATH', plugin_dir_path( __FILE__ ) );
define( 'GFGEOAC_URL', plugin_dir_url( __FILE__ ) );
define( 'GFGEOAC_PACKAGE_TYPE', 'free' );
define( 'GFGEOAC_PACKAGE_LABEL', 'Address Autocomplete' );
define( 'GFGEOAC_SITE_URL', 'https://gravitygeolocation.com' );

define( 'GFGEOAC_MIN_HOST_VERSION', '2.10.3' );

// Own classes only. Runtime does not require Composer, framework or account SDK.
// The autoloader keeps its conventional class-name parameter.
// phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.classFound
spl_autoload_register( static function ( $class ) {
	$prefix = 'GravityGeolocationAutocomplete\\';
	if ( 0 !== strpos( $class, $prefix ) ) {
		return;
	}
	$file = __DIR__ . '/src/' . str_replace( '\\', '/', substr( $class, strlen( $prefix ) ) ) . '.php';
	if ( is_file( $file ) ) {
		require_once $file;
	}
} );

// Record first activation only; do not redirect or alter host settings.
register_activation_hook( __FILE__, [ \GravityGeolocationAutocomplete\Admin\SetupNotice::class, 'activate' ] );

// Inspect every active plugin bootstrap before Gravity Forms initializes add-ons.
add_action( 'plugins_loaded', [ \GravityGeolocationAutocomplete\Loader::class, 'load' ], 5 );
