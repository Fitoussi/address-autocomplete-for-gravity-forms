<?php
/**
 * Native Gravity Forms settings for Address Autocomplete.
 *
 * Saved gfgeo keys stay unchanged so a premium upgrade needs no migration.
 * Only autocomplete controls are rendered; hidden paid values are preserved
 * separately at the native Add-On save boundary.
 *
 * @package GravityGeolocation\Admin
 * @since 1.0.0
 */

namespace GravityGeolocation\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define autocomplete settings while preserving hidden premium configuration.
 *
 * @since 1.0.0
 */
class Settings {

	/**
	 * Return browser-key and regional settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] Gravity Forms settings sections.
	 */
	public static function get_settings() {
		$sections = [
			[
				'id'          => 'section_gfgeo_google_maps_api_keys',
				'title'       => __( 'Google API Key', 'address-autocomplete-for-gravity-forms' ),
				'description' => __( 'Enter your browser API key to enable Google Places address autocomplete.', 'address-autocomplete-for-gravity-forms' ),
				'class'       => 'gform-settings-panel--full',
				'fields'      => [
					[
						'name'  => 'gfgeo_google_maps_api_key',
						'label' => __( 'Browser API Key', 'address-autocomplete-for-gravity-forms' ),
						'type'  => 'text',
					],
				],
			],
			[
				'id'          => 'section_gfgeo_google_maps_country',
				'title'       => __( 'Region Code', 'address-autocomplete-for-gravity-forms' ),
				'description' => __( 'Enter a two-letter region code, such as US. This influences regional results; use the field settings to restrict suggestions to specific countries.', 'address-autocomplete-for-gravity-forms' ),
				'class'       => 'gform-settings-panel--half',
				'fields'      => [
					[
						'name'          => 'gfgeo_google_maps_country',
						'type'          => 'text',
						'default_value' => 'US',
					],
				],
			],
			[
				'id'          => 'section_gfgeo_google_maps_language',
				'title'       => __( 'Language', 'address-autocomplete-for-gravity-forms' ),
				'description' => __( 'Enter a language code, such as en, for Google Places results.', 'address-autocomplete-for-gravity-forms' ),
				'class'       => 'gform-settings-panel--half',
				'fields'      => [
					[
						'name'          => 'gfgeo_google_maps_language',
						'type'          => 'text',
						'default_value' => 'en',
					],
				],
			],
		];

		/**
		 * Filter autocomplete settings sections and local discovery panels.
		 *
		 * @since 1.0.0
		 *
		 * @param array[] $sections Native Add-On settings sections.
		 */
		return apply_filters( 'gfgeo_plugin_settings_sections', $sections );
	}

	/**
	 * Preserve existing hidden settings when saving the standalone controls.
	 *
	 * Shared gfgeo keys survive an upgrade or switch back to premium. Submitted
	 * hidden credentials/options are ignored, including empty and null values.
	 *
	 * @since 1.0.0
	 *
	 * @param array       $settings Validated submitted plugin settings.
	 * @param array|false $current  Existing decrypted native plugin settings.
	 * @return array
	 */
	public static function preserve_hidden_plugin_settings( $settings, $current ) {
		$current = \is_array( $current ) ? $current : [];
		$keys    = array_unique( array_merge( array_keys( $settings ), array_keys( $current ) ) );

		foreach ( $keys as $key ) {
			$is_hidden = 0 === strpos( $key, 'gfgeo_token_' )
				|| \in_array( $key, [ 'gfgeo_google_maps_server_api_key', 'gfgeo_enable_ip_locator', 'gfgeo_enable_high_accuracy_mode', 'gfgeo_disable_advanced_markers', 'gfgeo_disable_google_maps_api' ], true );
			if ( ! $is_hidden ) {
				continue;
			}
			if ( array_key_exists( $key, $current ) ) {
				$settings[ $key ] = $current[ $key ];
			} else {
				unset( $settings[ $key ] );
			}
		}
		return $settings;
	}
}
