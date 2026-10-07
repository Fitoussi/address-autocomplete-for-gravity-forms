<?php
/**
 * Gravity Geolocation base form field class.
 *
 * Defines shared behavior for geolocation-related Gravity Forms fields.
 *
 * @package GravityGeolocationAutocomplete\Common\Form\Fields
 * @since 1.0.0
 */

namespace GravityGeolocationAutocomplete\Common\Form\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Abstract base class for Gravity Geolocation Gravity Forms fields.
 *
 * @package GravityGeolocationAutocomplete\Common\Form\Fields
 * @since 1.0.0
 */
abstract class BaseField extends \GF_Field {

	/**
	 * Prepare a field value for entry storage using the legacy Gravity Forms API.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  $value      Submitted value.
	 * @param array  $form       Form object.
	 * @param string $input_name Input name.
	 * @param int    $entry_id   Entry ID.
	 * @param array  $entry      Entry object.
	 * @return mixed
	 */
	public function get_value_save_entry( $value, $form, $input_name, $entry_id, $entry ) {
		return $this->prepare_value_for_save( $value, $form, $input_name, $entry_id, $entry );
	}

	/**
	 * Prepare a field value for entry storage using the Gravity Forms 3 API.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  $value          Submitted value.
	 * @param array  $form           Form object.
	 * @param string $input_name     Input name.
	 * @param int    $entry_id       Entry ID.
	 * @param array  $entry          Entry object.
	 * @param string $repeater_index Repeater index, when applicable.
	 * @return mixed
	 */
	public function get_value_save_input( $value, $form, $input_name, $entry_id, $entry, $repeater_index = '' ) {
		return $this->prepare_value_for_save( $value, $form, $input_name, $entry_id, $entry );
	}

	/**
	 * Prepare a field value for entry storage.
	 *
	 * Child fields override this method when their values require sanitization,
	 * normalization, or suppression before being saved.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  $value      Submitted value.
	 * @param array  $form       Form object.
	 * @param string $input_name Input name.
	 * @param int    $entry_id   Entry ID.
	 * @param array  $entry      Entry object.
	 * @return mixed
	 */
	protected function prepare_value_for_save( $value, $form, $input_name, $entry_id, $entry ) {
		return $value;
	}

	/**
	 * Default editor button group slug for GFGeo fields.
	 *
	 * Child fields can override this property to place the add-button in a
	 * feature-specific group.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $gfgeo_editor_button_group = 'gfgeo_geolocation_fields';

	/**
	 * Assigns the field button to the geolocation group in the Gravity Forms form editor.
	 *
	 * @since 1.0.0
	 *
	 * @return array Modified form editor button configuration array.
	 */
	public function get_form_editor_button() {
		return [
			'group' => (string) $this->gfgeo_editor_button_group,
			'text'  => $this->get_form_editor_field_title(),
		];
	}
}
