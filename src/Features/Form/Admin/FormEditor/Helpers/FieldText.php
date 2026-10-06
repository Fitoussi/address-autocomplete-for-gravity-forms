<?php
/**
 * Local Address labels/tooltips only; no shared engine text catalog.
 *
 * @package GravityGeolocation\Features\Form\Admin\FormEditor\Helpers
 * @since 1.0.0
 */

namespace GravityGeolocation\Features\Form\Admin\FormEditor\Helpers;

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
		$definitions = array(
			'address' => array(
				'autocomplete' => array(
					'enable'                  => array(
						'label'   => __( 'Enable autocomplete', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'Turn on real-time address suggestions powered by the Google Maps Places API.', 'address-autocomplete-for-gravity-forms' ),
					),
					'force_selection'         => array(
						'label'   => __( 'Require Address Selection from Suggestions', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'If the visitor leaves a newly typed address without selecting a suggestion, clear the input and show your configured alert.', 'address-autocomplete-for-gravity-forms' ),
					),
					'force_selection_message' => array(
						'label'   => __( 'Message for Manual Entry Attempt', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'Show this alert when the visitor leaves a newly typed address without selecting an autocomplete suggestion.', 'address-autocomplete-for-gravity-forms' ),
					),
					'types'                   => array(
						'label'   => __( 'Autocomplete Results Types', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'Choose the types of places to include in autocomplete suggestions. <a href="https://developers.google.com/maps/documentation/places/web-service/place-types" target="_blank">Learn more about result types.</a>', 'address-autocomplete-for-gravity-forms' ),
					),
					'countries'               => array(
						'label'   => __( 'Restrict by Countries', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'Select one or more countries (up to 5) to limit address suggestions within those regions.', 'address-autocomplete-for-gravity-forms' ),
					),
					'language'                => array(
						'label'   => __( 'Language', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'Select the language to be used for address autocomplete suggestions and returned place details. Leave as default to use the browser’s language.', 'address-autocomplete-for-gravity-forms' ),
					),
					'bias_type'               => array(
						'label'   => __( 'Location Bias Type', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'Bias suggestions toward a center point or rectangular area. Existing strict-bounds preferences remain supported; no visitor location is detected.', 'address-autocomplete-for-gravity-forms' ),
					),
				),
				'proximity'    => array(
					'lat'    => array(
						'label'   => __( 'Latitude', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'Latitude of the center point for proximity-based restrictions.', 'address-autocomplete-for-gravity-forms' ),
					),
					'lng'    => array(
						'label'   => __( 'Longitude', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'Longitude of the center point for proximity-based restrictions.', 'address-autocomplete-for-gravity-forms' ),
					),
					'radius' => array(
						'label'   => __( 'Radius (in meters)', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'Search radius around the center point, in meters.', 'address-autocomplete-for-gravity-forms' ),
					),
				),
				'area_bounds'  => array(
					'sw_point' => array(
						'label'   => __( 'Southwest Point', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'Enter a set of coordinates, comma-separated ( ex. 26.423277,-82.1371324 ), that represents the southwest point of the area that you would like to restrict.', 'address-autocomplete-for-gravity-forms' ),
					),
					'ne_point' => array(
						'label'   => __( 'Northeast Point', 'address-autocomplete-for-gravity-forms' ),
						'tooltip' => __( 'Enter a set of coordinates, comma-separated ( ex. 26.4724595,-82.0217760 ), that represents the northeast point of the area that you would like to restrict.', 'address-autocomplete-for-gravity-forms' ),
					),
				),
			),
		);
		$result      = array();
		foreach ( $definitions[ $field_type ] ?? array() as $name => $items ) {
			foreach ( $items as $key => $item ) {
				$result[ $name ][ $key ] = $item[ $type ] ?? '';
			}
		}
		return null === $group ? $result : ( $result[ $group ] ?? array() );
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
