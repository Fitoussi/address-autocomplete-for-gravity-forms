<?php
/**
 * Dependency-independent admin page and native Gravity Forms bootstrap.
 *
 * @package GravityGeolocationAutocomplete
 * @since 1.0.0
 */

namespace GravityGeolocationAutocomplete;

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
	 * Yield to premium after all plugin files load, before host initialization.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function load() {
		if ( defined( 'GFGEO_VERSION' ) ) {
			add_action( 'admin_notices', [ self::class, 'conflict_notice' ] );
			add_action( 'network_admin_notices', [ self::class, 'conflict_notice' ] );
			return;
		}
		self::register_admin();
		add_action( 'gform_loaded', [ self::class, 'register_addon' ], 8 );
	}

	/**
	 * Register only the free Add-On during Gravity Forms' native loading event.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function register_addon() {
		if ( defined( 'GFGEO_VERSION' ) ) {
			return;
		}
		if ( ! class_exists( '\GFForms' ) || ! method_exists( '\GFForms', 'include_addon_framework' ) ) {
			return;
		}
		if ( version_compare( \GFForms::$version, GFGEOAC_MIN_HOST_VERSION, '<' ) ) {
			return;
		}
		\GFForms::include_addon_framework();
		\GFAddOn::register( \GravityGeolocationAutocomplete\Core\AddOn::class );
	}

	/**
	 * Explain free inactivity without deleting data or changing activation.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function conflict_notice() {
		if ( current_user_can( 'activate_plugins' ) ) {
			echo '<div class="notice notice-warning"><p>' . esc_html__( 'WPGeo Address Autocomplete for Gravity Forms is not running because Gravity Geolocation or Gravity Search is active. Deactivate the free plugin; your existing Address fields and settings remain available in premium.', 'wpgeo-address-autocomplete-for-gravity-forms' ) . '</p></div>';
		}
	}

	/**
	 * Register local discovery pages and the host-dependency notice.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function register_admin() {
		\GravityGeolocationAutocomplete\Admin\Dashboard::register();
		add_action( 'admin_notices', [ self::class, 'dependency_notice' ] );
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
		if ( class_exists( '\GFForms' ) && version_compare( \GFForms::$version, GFGEOAC_MIN_HOST_VERSION, '>=' ) ) {
			return;
		}
		echo '<div class="notice notice-warning"><p>' . esc_html(
			sprintf(
			/* translators: %s: Minimum Gravity Forms version. */
				__( 'Address Autocomplete requires an active Gravity Forms installation, version %s or newer.', 'wpgeo-address-autocomplete-for-gravity-forms' ),
				GFGEOAC_MIN_HOST_VERSION
			)
		) . '</p></div>';
	}
}
