<?php
/**
 * Verify the copyable local dashboard layout and its existing host boundary.
 *
 * @package GravityGeolocationAutocomplete\Tests
 */

$root = dirname( __DIR__ );
$module = $root . '/src/Admin/Dashboard/';
foreach ( [ 'Loader.php', 'Features.php', 'views/dashboard.php', 'assets/scss/dashboard.scss', 'assets/css/dashboard.css', 'assets/css/dashboard.min.css', 'README.md' ] as $file ) {
	if ( ! is_file( $module . $file ) ) {
		throw new RuntimeException( 'Missing contained dashboard file: ' . $file );
	}
}
foreach ( [ 'src/Admin/Dashboard.php', 'src/Admin/DashboardFeatures.php', 'src/Admin/views/dashboard.php', 'assets/scss/admin/gfgeo-product-dashboard.scss', 'build/css/admin/gfgeo-product-dashboard.css', 'build/css/admin/gfgeo-product-dashboard.min.css' ] as $file ) {
	if ( file_exists( $root . '/' . $file ) ) {
		throw new RuntimeException( 'Retired dashboard file remains: ' . $file );
	}
}
$loader = file_get_contents( $module . 'Loader.php' );
if ( false === strpos( $loader, 'GFGEOAC_URL . \'src/Admin/Dashboard/assets/css/dashboard.min.css\'' ) || false === strpos( $loader, 'use GravityGeolocationAutocomplete\Admin\SetupNotice;' ) || false !== strpos( $loader, 'spl_autoload_' ) ) {
	throw new RuntimeException( 'Existing host boundary was changed.' );
}
$template = file_get_contents( $module . 'views/dashboard.php' );
preg_match( '/<header\\b.*?<\/header>/s', $template, $header );
preg_match_all( '/<a\\b.*?<\/a>/s', $header[0], $actions );
if ( 3 !== count( $actions[0] ) || false === strpos( $actions[0][0], 'button button-primary' ) || false === strpos( $actions[0][0], 'Open Settings' ) || false === strpos( $actions[0][1], 'View Live Demos' ) || false === strpos( $actions[0][2], 'Explore Plugins' ) || 1 !== substr_count( $header[0], 'button-primary' ) ) {
	throw new RuntimeException( 'The settings-first header action contract changed.' );
}
if ( false === strpos( $loader, "\$product_name = __( 'Address Autocomplete', 'wpgeo-address-autocomplete-for-gravity-forms' );" ) || false === strpos( $header[0], 'For Gravity Forms' ) || strpos( $header[0], 'For Gravity Forms' ) > strpos( $header[0], '<h1>' ) ) {
	throw new RuntimeException( 'The compact dashboard heading or host context changed.' );
}
$main = file_get_contents( $root . '/address-autocomplete-for-gravity-forms.php' );
if ( false === strpos( $main, "define( 'GFGEOAC_PLUGIN_NAME', 'Address Autocomplete for Gravity Forms' );" ) || false === strpos( $main, 'Plugin Name:            Logical Address Autocomplete for Gravity Forms' ) ) {
	throw new RuntimeException( 'The public identity or full internal name changed.' );
}
if ( 1 !== substr_count( $template, '<hr class="wp-header-end">' ) || strpos( $template, '<hr class="wp-header-end">' ) < strpos( $template, '</nav>' ) ) {
	throw new RuntimeException( 'The single admin-notice anchor must remain below navigation.' );
}
$css = file_get_contents( $module . 'assets/css/dashboard.css' );
if ( false === strpos( $template, 'gfgeo-product-dashboard__card gfgeo-product-dashboard__product-card' ) || false === strpos( $css, '.gfgeo-product-dashboard__product-card .button' ) || false === strpos( $css, 'align-self: flex-start;' ) ) {
	throw new RuntimeException( 'Scoped discovery-card alignment is missing.' );
}
echo "Contained dashboard, settings-first header, compact heading and card alignment passed.\n";
