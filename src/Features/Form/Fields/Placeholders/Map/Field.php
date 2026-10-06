<?php
/**
 * Gravity Geolocation UI-only Map placeholder field.
 *
 * Supplies a promotional editor identity without a premium implementation.
 * It intentionally renders no input and does not save or display entry values.
 *
 * @package GravityGeolocation\Features\Form\Fields\Placeholders\Map
 * @since 1.0.0
 */

namespace GravityGeolocation\Features\Form\Fields\Placeholders\Map;

use GravityGeolocation\Common\Form\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Non-functional Map field promotion for the standalone plugin.
 *
 * Mirrors the existing placeholder contract without inheriting the real field.
 * The standalone registrar lists this class explicitly; no resolver is used.
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
	protected $prefix = GFGEO_PREFIX;

	/**
	 * Retain the premium field identity without providing its implementation.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const TYPE = 'gfgeo_map';

	/**
	 * Gravity Forms field type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $type = self::TYPE;

	/**
	 * Disable input placeholder settings for this non-functional field.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $supports_placeholder = false;

	/**
	 * Input masks are unavailable without an input.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $supports_input_mask = false;

	/**
	 * This placeholder cannot participate in calculations.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $supports_calculation = false;

	/**
	 * Return the real field's icon with the existing unavailable-feature marker.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_form_editor_field_icon() {
		return 'dashicons-location-alt gfgeo-pro-disabled';
	}

	/**
	 * Return the field's label in the builder palette.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_form_editor_field_title() {
		return __( 'Map', 'address-autocomplete-for-gravity-forms' );
	}

	/**
	 * Describe the feature represented by this unavailable field.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_form_editor_field_description() {
		return __( 'Displays an interactive map linked to address or coordinate fields.', 'address-autocomplete-for-gravity-forms' );
	}

	/**
	 * Do not expose settings for an unavailable implementation.
	 *
	 * @since 1.0.0
	 *
	 * @return string[]
	 */
	public function get_form_editor_field_settings() {
		return [];
	}

	/**
	 * Conditional logic is unavailable for this UI-only placeholder.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_conditional_logic_supported() {
		return false;
	}

	/**
	 * This placeholder does not submit a multipart value.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_value_submission_array() {
		return false;
	}

	/**
	 * Do not accept HTML through this UI-only placeholder.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function allow_html() {
		return false;
	}

	/**
	 * Render no input in the builder or on the frontend.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $form  Form object.
	 * @param string $value Field value (unused).
	 * @param array  $entry Entry object (unused).
	 * @return null
	 */
	public function get_field_input( $form, $value = '', $entry = null ) {
		return null;
	}

	/**
	 * Suppress entry storage through both inherited Gravity Forms save APIs.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  $value      Submitted value (unused).
	 * @param array  $form       Form object.
	 * @param string $input_name Input name.
	 * @param int    $entry_id   Entry ID.
	 * @param array  $entry      Entry object.
	 * @return string Empty string.
	 */
	protected function prepare_value_for_save( $value, $form, $input_name, $entry_id, $entry ) {
		return '';
	}

	/**
	 * Do not display entry values without the real implementation.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value    Field value (unused).
	 * @param array  $entry    Entry object.
	 * @param string $field_id Field ID.
	 * @param array  $columns  Entry-list columns.
	 * @param array  $form     Form object.
	 * @return string Empty string.
	 */
	public function get_value_entry_list( $value, $entry, $field_id, $columns, $form ) {
		return '';
	}

	/**
	 * Do not expose entry values through placeholder merge tags.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value      Field value (unused).
	 * @param string $input_id   Input ID.
	 * @param array  $entry      Entry object.
	 * @param array  $form       Form object.
	 * @param string $modifier   Merge-tag modifier.
	 * @param string $raw_value  Raw field value.
	 * @param bool   $url_encode Whether URL encoding is requested.
	 * @param bool   $esc_html   Whether HTML escaping is requested.
	 * @param string $format     Requested output format.
	 * @param bool   $nl2br      Whether newline conversion is requested.
	 * @return string Empty string.
	 */
	public function get_value_merge_tag( $value, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format, $nl2br ) {
		return '';
	}

	/**
	 * Do not display entry-detail values without the real implementation.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value    Field value (unused).
	 * @param string $currency Currency code.
	 * @param bool   $use_text Whether text output is requested.
	 * @param string $format   Requested output format.
	 * @param string $media    Output media.
	 * @return string Empty string.
	 */
	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {
		return '';
	}
}
