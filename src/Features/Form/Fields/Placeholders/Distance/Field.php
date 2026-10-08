<?php
/**
 * Gravity Geolocation UI-only Distance field.
 *
 * Defines a non-persistent UI field used to configure/display distance-related
 * behavior in form rendering flows. This field does not save entry values.
 *
 * @since 1.0.0
 * @package GravityGeolocationAutocomplete\Features\Form\Fields\Distance
 */

namespace GravityGeolocationAutocomplete\Features\Form\Fields\Placeholders\Distance;

use GravityGeolocationAutocomplete\Features\Form\Fields\Placeholders\Directions\Field as Directions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * UI-only Distance & Duration field type for Gravity Geolocation.
 *
 * Used as a UI/configuration carrier in editor/runtime contexts.
 * It intentionally does not persist or output entry values.
 *
 * @since 1.0.0
 */
class Field extends Directions {

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
	public const TYPE = 'gfgeo_distance';

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
	 * This could be an icon url or a gform-icon class.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_form_editor_field_icon() {
		return 'gform-icon--flag gfgeo-pro-disabled';
	}

	/**
	 * Retrieves the field label for display in the Gravity Forms editor.
	 *
	 * @since 1.0.0
	 *
	 * @return string The label for the Distance & Duration field.
	 */
	public function get_form_editor_field_title() {
		return __( 'Distance & Travel Time', 'advanced-address-autocomplete-for-gravity-forms' );
	}

	/**
	 * Returns a short description for this UI-only field in the Gravity Forms editor.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_form_editor_field_description() {
		return __( 'Displays calculated travel distance and duration between geolocation points.', 'advanced-address-autocomplete-for-gravity-forms' );
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
	 * @return bool
	 */
	public function is_value_submission_array() {
		return false;
	}

	/**
	 * Allow HTML output.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function allow_html() {
		return false;
	}

	/**
	 * Generate the field's HTML output for the form editor and front-end form.
	 * This UI-only field intentionally returns null (no direct input markup).
	 *
	 * @since 1.0.0
	 *
	 * @param array  $form  The form array.
	 * @param string $value The field value (unused).
	 * @param array  $entry The entry array.
	 *
	 * @return string|null HTML output for the field or null.
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
	 * @return string Empty string to prevent data saving.
	 */
	protected function prepare_value_for_save( $value, $form, $input_name, $entry_id, $entry ) {
		return '';
	}

	/**
	 * Returns the value to display for this UI-only field in the entry list.
	 *
	 * No data is saved or displayed in entry list views.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value    The field value (unused).
	 * @param array  $entry    The entry array.
	 * @param string $field_id The field ID.
	 * @param array  $columns  Column data for the entry list.
	 * @param array  $form     The full form object.
	 *
	 * @return string Empty string since the field stores no value.
	 */
	public function get_value_entry_list( $value, $entry, $field_id, $columns, $form ) {
		return '';
	}

	/**
	 * Prevent merge tag output for this UI-only field.
	 *
	 * @param string $value       The field value (unused).
	 * @param string $input_id    The input ID.
	 * @param array  $entry       The entry array.
	 * @param array  $form        The form object.
	 * @param string $modifier    The merge tag modifier.
	 * @param string $raw_value   The raw field value.
	 * @param bool   $url_encode  Whether to URL-encode the output.
	 * @param bool   $esc_html    Whether to escape HTML entities.
	 * @param string $format      The format requested for the output.
	 * @param bool   $nl2br       Whether to convert newlines to line breaks.
	 *
	 * @since 1.0.0
	 * @return string Empty string.
	 */
	public function get_value_merge_tag( $value, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format, $nl2br ) {
		return '';
	}

	/**
	 * Display the distance link in the entry page.
	 *
	 * This field intentionally returns an empty string because it does not display in entry detail contexts.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value         The value of the field.
	 * @param string $currency      The currency for the field.
	 * @param bool   $use_text      Whether to use text for the link.
	 * @param string $format        The format for output.
	 * @param string $media         Media type for styling.
	 *
	 * @return string Empty string.
	 */
	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {
		return '';
	}
}
