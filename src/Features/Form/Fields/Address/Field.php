<?php
/**
 * Gravity Geolocation custom Address field.
 *
 * Defines a Gravity Forms address field with geolocation enhancements such as
 * autocomplete and optional map-link output formatting.
 *
 * @package GravityGeolocation\Features\Form\Fields\Address
 * @since 1.0.0
 */

namespace GravityGeolocation\Features\Form\Fields\Address;

use GravityGeolocation\Common\Form\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Full Address Field Class.
 *
 * This class registers and renders a custom Address field (`gfgeo_address`) for Gravity Forms,
 * working in both the Gravity Forms admin/editor and frontend rendering contexts.
 * It supports address autocomplete via Google Places,
 * conditional logic, placeholders, and optional Google Maps link integration in entry views.
 *
 * @since 1.0.0
 *
 * @property int|string $id
 * @property string     $size
 * @property int|string $maxLength
 * @property bool       $enableAutocomplete
 * @property string     $placeholder
 * @property bool       $isRequired
 * @property bool       $failed_validation
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
	 * Field type slug registered with Gravity Forms.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const TYPE = 'gfgeo_address';

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
	protected $supports_placeholder = true;

	/**
	 * Indicates that this field supports input masks.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $supports_input_mask = true;

	/**
	 * Indicates that this field does not support calculations.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $supports_calculation = false;

	/**
	 * Initializes the Full Address field with optional field data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Optional associative array of field properties.
	 */
	public function __construct( $data = [] ) {
		parent::__construct( $data );

		$this->google_map_link = [
			'merge_tag'    => false,
			'entry_list'   => false,
			'entry_detail' => true,
		];
	}

	/**
	 * Indicates whether the submitted value is an array.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_value_submission_array() {
		return false;
	}

	/**
	 * Determines if this field supports conditional logic.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if supported.
	 */
	public function is_conditional_logic_supported() {
		return true;
	}

	/**
	 * Returns the icon for this field in the Gravity Forms editor.
	 *
	 * @since 1.0.0
	 *
	 * @return string Icon class name or URL.
	 */
	public function get_form_editor_field_icon() {
		return 'gform-icon--place';
	}

	/**
	 * Returns the field title as displayed in the form editor.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_form_editor_field_title() {
		return __( 'Address', 'address-autocomplete-for-gravity-forms' );
	}

	/**
	 * Returns a short description for this field in the Gravity Forms editor.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_form_editor_field_description() {
		return __( 'A single-line address field with Google Places autocomplete.', 'address-autocomplete-for-gravity-forms' );
	}

	/**
	 * Returns the array of field settings displayed in the Gravity Forms editor for this field.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of editor setting identifiers.
	 */
	public function get_form_editor_field_settings() {
		return [
			// gfgeo options.
			"{$this->prefix}_address_autocomplete_setting",
			// Gravity Forms native options.
			'default_value_setting',
			'post_custom_field_setting',
			'conditional_logic_field_setting',
			'prepopulate_field_setting',
			'error_message_setting',
			'label_setting',
			'label_placement_setting',
			'admin_label_setting',
			'size_setting',
			'rules_setting',
			'visibility_setting',
			'duplicate_setting',
			'placeholder_setting',
			'description_setting',
			'css_class_setting',
		];
	}

	/**
	 * Returns the full input ID attribute based on the form context.
	 *
	 * @since 1.0.0
	 *
	 * @param int $form_id The current form ID.
	 * @return string The input ID string (e.g., input_1_2).
	 */
	protected function get_input_id_attr( $form_id ) {
		$id = (int) $this->id;
		return ( $this->is_entry_detail() || $this->is_form_editor() || 0 === $form_id )
			? "input_$id"
			: "input_{$form_id}_$id";
	}

	/**
	 * Generates the HTML markup for the field input.
	 *
	 * @since 1.0.0
	 *
	 * @param array      $form  The current Gravity Forms form object.
	 * @param string     $value The current input value.
	 * @param null|array $entry Optional. The entry data when editing or viewing entries.
	 *
	 * @return string HTML markup for the address field input.
	 */
	public function get_field_input( $form, $value = '', $entry = null ) {
		$form_id = isset( $form['id'] ) ? absint( $form['id'] ) : 0;

		if ( ! \is_scalar( $value ) ) {
			$value = '';
		}

		$is_entry_detail       = $this->is_entry_detail();
		$is_form_editor        = $this->is_form_editor();
		$id                    = (int) $this->id;
		$field_id              = $this->get_input_id_attr( $form_id );
		$size                  = $this->size;
		$class_suffix          = $is_entry_detail ? '_admin' : '';
		$class                 = esc_attr( $size . $class_suffix );
		$max_length            = is_numeric( $this->maxLength ) ? "maxlength='{$this->maxLength}'" : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$tabindex              = $this->get_tabindex();
		$disabled_text         = $is_form_editor ? 'disabled="disabled"' : '';
		$aria_describedby      = $this->get_aria_describedby();
		$placeholder_attribute = $this->get_field_placeholder_attribute();
		$required_attribute    = $this->isRequired ? 'aria-required="true"' : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$invalid_attribute     = $this->failed_validation ? 'aria-invalid="true"' : 'aria-invalid="false"';
		$autocomplete          = $this->enableAutocomplete ? $this->get_field_autocomplete_attribute() : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$value                 = esc_attr( sanitize_text_field( stripslashes( (string) $value ) ) );

		$input = \sprintf(
			"<input name='input_%d' id='%s' type='text' value='%s' class='%s' %s %s %s %s %s %s %s />",
			$id,
			esc_attr( $field_id ),
			$value,
			$class,
			$max_length,
			$aria_describedby,
			$tabindex,
			$placeholder_attribute,
			$required_attribute,
			$invalid_attribute,
			$disabled_text . ' ' . $autocomplete
		);

		// Imported locator flags are preserved in data but never generate controls.
		return "<div class='ginput_container ginput_container_gfgeo_address'>{$input}</div>";
	}

	/**
	 * Filters and returns the value to be saved to the entry.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value       The submitted value.
	 * @param array  $form        The form object.
	 * @param string $input_name  The input name attribute.
	 * @param int    $lead_id     The entry ID.
	 * @param array  $lead        The full entry array.
	 *
	 * @return string The sanitized value to store in the database.
	 */
	protected function prepare_value_for_save( $value, $form, $input_name, $lead_id, $lead ) {
		return sanitize_text_field( $value );
	}

	/**
	 * Formats the field value for use in merge tags (e.g., email, confirmations),
	 * with optional map link integration.
	 *
	 * @since 1.0.0
	 *
	 * @param string $address    The raw address value.
	 * @param string $input_id   The input ID.
	 * @param array  $entry      The entry data.
	 * @param array  $form       The form object.
	 * @param string $modifier   Merge tag modifier (if any).
	 * @param string $raw_value  Raw (unformatted) value.
	 * @param bool   $url_encode Whether to URL-encode the value.
	 * @param bool   $esc_html   Whether to escape HTML characters.
	 * @param string $format     Format to return (text/html).
	 * @param bool   $nl2br      Convert newlines to <br> if true.
	 *
	 * @return string The formatted value with optional map link.
	 */
	public function get_value_merge_tag( $address, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format, $nl2br ) {
		$address = \is_string( $address ) ? $address : '';
		return $this->maybe_add_map_link( $address, 'merge_tag' );
	}

	/**
	 * Returns the formatted value for display in the entry list table,
	 * with optional map link integration.
	 *
	 * @since 1.0.0
	 *
	 * @param string $address  The field value.
	 * @param array  $entry    The entry data.
	 * @param string $field_id The field ID.
	 * @param array  $columns  The column definitions.
	 * @param array  $form     The current form.
	 *
	 * @return string The formatted address with optional map link, or an empty string if no address.
	 */
	public function get_value_entry_list( $address, $entry, $field_id, $columns, $form ) {
		$address = \is_string( $address ) ? $address : '';
		$address = $this->maybe_add_map_link( $address, 'entry_list' );

		return $address;
	}

	/**
	 * Returns the formatted value for display on the entry detail page,
	 * with optional map link integration.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $address   The field value.
	 * @param string  $currency  Currency symbol or code.
	 * @param boolean $use_text  Whether to return a text-only version.
	 * @param string  $format    Output format (html or text).
	 * @param string  $media     Output context (screen, email, etc).
	 *
	 * @return string The formatted address with optional map link for entry view, or an empty string if none.
	 */
	public function get_value_entry_detail( $address, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {
		$address = \is_string( $address ) ? $address : '';
		$address = $this->maybe_add_map_link( $address, 'entry_detail' );

		return $address;
	}

	/**
	 * Optionally appends a Google Maps link to the address string.
	 *
	 * The output depends on the context ('merge_tag', 'entry_list', or 'entry_detail') and field settings.
	 * Hook: "{$this->prefix}_full_address_field_output_map_link"
	 *
	 * @since 1.0.0
	 *
	 * @param string $address The raw address string.
	 * @param string $context The output context (merge_tag, entry_list, entry_detail).
	 * @return string Modified address with map link if enabled, or original string.
	 */
	protected function maybe_add_map_link( $address, $context ) {
		if ( empty( $address ) ) {
			return $address;
		}

		$settings = apply_filters( "{$this->prefix}_full_address_field_output_map_link", $this->google_map_link, $this );

		if ( ! empty( $settings[ $context ] ) ) {
			$sep      = ( 'entry_list' === $context ) ? '<br /> ' : ' - ';
			$address .= $sep . '<a href="' . esc_url( 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $address ) ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Open Map', 'address-autocomplete-for-gravity-forms' ) . '</a>';
		}

		return $address;
	}
}
