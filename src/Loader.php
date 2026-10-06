<?php
/**
 * Dependency-independent admin page and native Gravity Forms bootstrap.
 *
 * @package GravityGeolocation
 * @since 1.0.0
 */

namespace GravityGeolocation;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bootstrap the host integration and dependency-independent admin pages.
 *
 * @since 1.0.0
 */
final class Loader {

	/**
	 * Register the native Add-On when a supported Gravity Forms host is loaded.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function load() {
		if ( ! class_exists( '\GFForms' ) || ! method_exists( '\GFForms', 'include_addon_framework' ) ) {
			return;
		}
		if ( version_compare( \GFForms::$version, GFGEO_DEPENDENCIES['gravityforms'], '<' ) ) {
			return;
		}
		\GFForms::include_addon_framework();
		\GFAddOn::register( \GravityGeolocation\Core\AddOn::class );
	}

	/**
	 * Register local discovery pages and the host-dependency notice.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function register_admin() {
		\GravityGeolocation\Admin\Dashboard::register();
		add_action( 'admin_notices', array( self::class, 'dependency_notice' ) );
	}

	/**
	 * Explain a missing or outdated host without changing plugin activation.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function dependency_notice() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		if ( class_exists( '\GFForms' ) && version_compare( \GFForms::$version, GFGEO_DEPENDENCIES['gravityforms'], '>=' ) ) {
			return;
		}
		echo '<div class="notice notice-warning"><p>' . esc_html(
			sprintf(
			/* translators: %s: Minimum Gravity Forms version. */
				__( 'Address Autocomplete requires an active Gravity Forms installation, version %s or newer.', 'address-autocomplete-for-gravity-forms' ),
				GFGEO_DEPENDENCIES['gravityforms']
			)
		) . '</p></div>';
	}
}
