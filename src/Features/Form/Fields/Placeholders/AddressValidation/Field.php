<?php
/**
 * Gravity Geolocation UI-only Address Validation field.
 *
 * Defines a non-persistent placeholder field when the full implementation is unavailable.
 *
 * @package GravityGeolocationAutocomplete\Features\Form\Fields\Placeholders\AddressValidation
 * @since 1.0.0
 */

namespace GravityGeolocationAutocomplete\Features\Form\Fields\Placeholders\AddressValidation;

use GravityGeolocationAutocomplete\Common\Form\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * UI-only Address Validation field type for Gravity Geolocation.
 *
 * Used as a placeholder in the editor when the Toolkit/All Access implementation is unavailable.
 * It intentionally does not persist or output entry values.
 *
 * @since 1.0.0
 */
class Field extends BaseField {

	/**
	 * Plugin-specific prefix used for setting keys and filters.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $prefix = GFGEOAC_PREFIX;

	/**
	 * Field type slug registered with Gravity Forms.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const TYPE = 'gfgeo_address_validation';

	/**
	 * Dynamic field type used by the Gravity Forms framework.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $type = self::TYPE;

	/**
	 * Indicates that this field supports placeholder functionality.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $supports_placeholder = false;

	/**
	 * Indicates that this field does not support input masks.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $supports_input_mask = false;

	/**
	 * Indicates that this field does not support calculations.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $supports_calculation = false;

	/**
	 * Returns the field's form editor icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_form_editor_field_icon() {
		return 'gform-icon--check gfgeo-pro-disabled';
	}

	/**
	 * Returns the field title as shown in the form editor.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_form_editor_field_title() {
		return __( 'Address Validation', 'wpgeo-address-autocomplete-for-gravity-forms' );
	}

	/**
	 * Returns a short description for this field in the editor.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_form_editor_field_description() {
		return __( 'Verifies and standardizes addresses for delivery workflows. Available in Gravity Geolocation Toolkit and All Access.', 'wpgeo-address-autocomplete-for-gravity-forms' );
	}

	/**
	 * Returns available field settings in the form editor.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_form_editor_field_settings() {
		return [];
	}

	/**
	 * Conditional logic is not supported for this UI-only field.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_conditional_logic_supported() {
		return false;
	}

	/**
	 * Indicates whether the field submits a multi-part array.
	 * This panel is UI-only and does not store a value.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_value_submission_array() {
		return false;
	}

	/**
	 * Allow HTML output.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function allow_html() {
		return false;
	}

	/**
	 * Generate the field's HTML output for the form editor and front-end form.
	 * This UI-only field intentionally returns null.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $form  The form array.
	 * @param string $value The field value (unused).
	 * @param array  $entry The entry array.
	 *
	 * @return string|null
	 */
	public function get_field_input( $form, $value = '', $entry = null ) {
		return null;
	}

	/**
	 * Prevent saving any entry value for this UI-only field.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value      The field value.
	 * @param array  $form       The form object.
	 * @param string $input_name The input name.
	 * @param int    $entry_id   The entry ID.
	 * @param array  $entry      The entry array.
	 *
	 * @return string
	 */
	protected function prepare_value_for_save( $value, $form, $input_name, $entry_id, $entry ) {
		return '';
	}

	/**
	 * Returns the value to display for this UI-only field in the entry list.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value    The field value (unused).
	 * @param array  $entry    The entry array.
	 * @param string $field_id The field ID.
	 * @param array  $columns  Column data for the entry list.
	 * @param array  $form     The full form object.
	 *
	 * @return string
	 */
	public function get_value_entry_list( $value, $entry, $field_id, $columns, $form ) {
		return '';
	}

	/**
	 * Prevent merge tag output for this UI-only field.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value      The field value (unused).
	 * @param string $input_id   The input ID.
	 * @param array  $entry      The entry array.
	 * @param array  $form       The form object.
	 * @param string $modifier   The merge tag modifier.
	 * @param string $raw_value  The raw field value.
	 * @param bool   $url_encode Whether to URL-encode the output.
	 * @param bool   $esc_html   Whether to escape HTML entities.
	 * @param string $format     The format requested for the output.
	 * @param bool   $nl2br      Whether to convert newlines to line breaks.
	 *
	 * @return string
	 */
	public function get_value_merge_tag( $value, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format, $nl2br ) {
		return '';
	}

	/**
	 * Does not display data in entry detail contexts (UI-only).
	 *
	 * @since 1.0.0
	 *
	 * @param string $value    Field value.
	 * @param string $currency Currency code.
	 * @param bool   $use_text Whether text output is requested.
	 * @param string $format   Output format.
	 * @param string $media    Output media.
	 *
	 * @return string
	 */
	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {
		return '';
	}
}
