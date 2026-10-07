<?php
/**
 * Field registration for the standalone Address Autocomplete plugin.
 *
 * Registers the Address field, four promotional placeholders and their editor group.
 *
 * @package GravityGeolocationAutocomplete\Common\Form\Fields
 * @since 1.0.0
 */

namespace GravityGeolocationAutocomplete\Common\Form\Fields;

use GravityGeolocationAutocomplete\Features\Form\Fields\Address\Field as Address;
use GravityGeolocationAutocomplete\Features\Form\Fields\Placeholders\AddressValidation\Field as AddressValidation;
use GravityGeolocationAutocomplete\Features\Form\Fields\Placeholders\Directions\Field as Directions;
use GravityGeolocationAutocomplete\Features\Form\Fields\Placeholders\Distance\Field as Distance;
use GravityGeolocationAutocomplete\Features\Form\Fields\Placeholders\Map\Field as Map;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Central registrar for Gravity Forms field classes.
 *
 * @since 1.0.0
 */
final class Registrar {

	/**
	 * Guard to prevent duplicate field registration in the same request.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	private static $ran = false;

	/**
	 * Run a single field registration pass.
	 *
	 * Uses an explicit class list; there are no package variants or class resolvers.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function register_fields() {
		if ( self::$ran ) {
			return;
		}

		if ( ! method_exists( 'GF_Fields', 'register' ) ) {
			return;
		}

		self::$ran = true;
		add_filter( 'gform_field_groups_form_editor', [ __CLASS__, 'add_fields_group' ] );

		/**
		 * Filters the Gravity Forms field classes registered by Gravity Geolocation.
		 *
		 * Each value must be the fully qualified class name of a loaded class that
		 * extends `GF_Field`. The shared registrar instantiates and registers every
		 * valid class once during its registration pass.
		 *
		 * @since 1.0.0
		 *
		 * @param string[] $classes Fully qualified Gravity Forms field class names.
		 */
		$classes = apply_filters( 'gfgeo_form_field_classes', [ Address::class, Map::class, Directions::class, Distance::class, AddressValidation::class ] );
		if ( ! \is_array( $classes ) || [] === $classes ) {
			return;
		}

		$classes = array_unique( array_values( $classes ) );

		foreach ( $classes as $class ) {
			$class = \is_string( $class ) ? $class : '';
			if ( '' === $class || ! class_exists( $class ) ) {
				continue;
			}

			if ( is_subclass_of( $class, '\GF_Field' ) ) {
				\GF_Fields::register( new $class() );
			}
		}
	}

	/**
	 * Place the single plugin group after Gravity Forms' advanced fields.
	 *
	 * Preserve an existing group's fields if the host already populated it.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field_groups Existing Gravity Forms field groups.
	 * @return array Groups with the plugin group inserted once.
	 */
	public static function add_fields_group( $field_groups ) {
		if ( ! \is_array( $field_groups ) ) {
			return $field_groups;
		}

		$group     = [
			'name'   => 'gfgeo_geolocation_fields',
			'label'  => esc_html__( 'Geolocation Fields', 'address-autocomplete-for-gravity-forms' ),
			'fields' => [],
		];
		$remaining = [];
		foreach ( $field_groups as $existing ) {
			if ( 'gfgeo_geolocation_fields' === ( $existing['name'] ?? '' ) ) {
				$group = $existing;
			} else {
				$remaining[] = $existing;
			}
		}

		$result   = [];
		$inserted = false;
		foreach ( $remaining as $existing ) {
			$result[] = $existing;
			if ( ! $inserted && 'advanced_fields' === ( $existing['name'] ?? '' ) ) {
				$result[] = $group;
				$inserted = true;
			}
		}
		if ( ! $inserted ) {
			$result[] = $group;
		}

		return $result;
	}
}
