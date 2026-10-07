<?php
/**
 * Local Address labels/tooltips only; no shared engine text catalog.
 *
 * @package GravityGeolocationAutocomplete\Features\Form\Admin\FormEditor\Helpers
 * @since 1.0.0
 */

namespace GravityGeolocationAutocomplete\Features\Form\Admin\FormEditor\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Provide local Address text without a shared geolocation-engine catalog.
 *
 * @since 1.0.0
 */
final class FieldText {

	/**
	 * Project Address labels or tooltips into the existing grouped lookup shape.
	 *
	 * @since 1.0.0
	 *
	 * @param string      $field_type Field text catalog identity.
	 * @param string|null $group      Optional group; null returns every group.
	 * @param string      $type       Requested text property: label or tooltip.
	 * @return array Grouped text, or an empty array for an unknown identity.
	 */
	public static function get_parts( $field_type, $group = null, $type = 'label' ) {
		$definitions = [
			'address' => [
				'autocomplete' => [
					'enable'                  => [
						'label'   => __( 'Enable autocomplete', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'Show address suggestions powered by Google Places.', 'address-autocomplete-for-gravity-forms' ),
					],
					'force_selection'         => [
						'label'   => __( 'Require address selection from suggestions', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'If the visitor leaves a newly typed address without selecting a suggestion, clear the input and show your configured alert.', 'address-autocomplete-for-gravity-forms' ),
					],
					'force_selection_message' => [
						'label'   => __( 'Selection alert message', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'Show this alert when the visitor leaves a newly typed address without selecting an autocomplete suggestion.', 'address-autocomplete-for-gravity-forms' ),
					],
					'types'                   => [
						'label'   => __( 'Autocomplete result types', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'Choose the types of places to include in autocomplete suggestions. <a href="https://developers.google.com/maps/documentation/places/web-service/place-types" target="_blank">Learn more about result types.</a>', 'address-autocomplete-for-gravity-forms' ),
					],
					'countries'               => [
						'label'   => __( 'Restrict to countries', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'Select one or more countries (up to 5) to limit address suggestions within those regions.', 'address-autocomplete-for-gravity-forms' ),
					],
					'language'                => [
						'label'   => __( 'Language', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'Choose a language for suggestions and place details. Leave the default selected to use the global language setting.', 'address-autocomplete-for-gravity-forms' ),
					],
					'bias_type'               => [
						'label'   => __( 'Location bias', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'Favor suggestions near a configured point or within a rectangular area. This does not detect the visitor’s location.', 'address-autocomplete-for-gravity-forms' ),
					],
				],
				'proximity'    => [
					'lat'    => [
						'label'   => __( 'Latitude', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'Latitude of the center point used to favor nearby suggestions.', 'address-autocomplete-for-gravity-forms' ),
					],
					'lng'    => [
						'label'   => __( 'Longitude', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'Longitude of the center point used to favor nearby suggestions.', 'address-autocomplete-for-gravity-forms' ),
					],
					'radius' => [
						'label'   => __( 'Radius (in meters)', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'Radius around the center point, in meters (up to 50,000).', 'address-autocomplete-for-gravity-forms' ),
					],
				],
				'area_bounds'  => [
					'sw_point' => [
						'label'   => __( 'Southwest Point', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'Enter the southwest corner as latitude, longitude, for example: 26.423277, -82.1371324.', 'address-autocomplete-for-gravity-forms' ),
					],
					'ne_point' => [
						'label'   => __( 'Northeast Point', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'Enter the northeast corner as latitude, longitude, for example: 26.4724595, -82.0217760.', 'address-autocomplete-for-gravity-forms' ),
					],
				],
			],
		];
		$result      = [];
		foreach ( $definitions[ $field_type ] ?? [] as $name => $items ) {
			foreach ( $items as $key => $item ) {
				$result[ $name ][ $key ] = $item[ $type ] ?? '';
			}
		}
		return null === $group ? $result : ( $result[ $group ] ?? [] );
	}

	/**
	 * Read an explicit text path with an empty fallback for missing keys.
	 *
	 * @since 1.0.0
	 *
	 * @param array       $items  Grouped labels or tooltips.
	 * @param string      $key    Group or item key.
	 * @param string|null $subkey Optional item key inside a group.
	 * @return array|string Requested group or text, or an empty string.
	 */
	public static function get_safe( $items, $key, $subkey = null ) {
		return null === $subkey ? ( $items[ $key ] ?? '' ) : ( $items[ $key ][ $subkey ] ?? '' );
	}
}
