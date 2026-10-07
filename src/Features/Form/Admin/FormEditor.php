<?php
/**
 * Address-only settings tab in the native Gravity Forms builder.
 *
 * @package GravityGeolocationAutocomplete\Features\Form\Admin
 * @since 1.0.0
 */

namespace GravityGeolocationAutocomplete\Features\Form\Admin;

use GravityGeolocationAutocomplete\Features\Form\Admin\FormEditor\Helpers\FieldText;
use GravityGeolocationAutocomplete\Features\Form\Fields\Address\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Own the autocomplete tab, translated tooltips and new Address defaults.
 *
 * @since 1.0.0
 */
class FormEditor {

	/**
	 * Attach autocomplete controls only on the native form-editor screen.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( ! \GFCommon::is_form_editor() || apply_filters( 'gfgeo_disable_form_editor', false ) ) {
			return;
		}
		add_filter( 'gform_field_settings_tabs', [ $this, 'geolocation_settings_tab' ], 10, 2 );
		add_action( 'gform_field_settings_tab_content', [ $this, 'geolocation_settings_tab_content' ], 50, 2 );
		add_filter( 'gform_tooltips', [ $this, 'tooltips' ] );
		add_action( 'gform_editor_js_set_default_values', [ $this, 'default_values' ] );
	}

	/**
	 * Add the Address Autocomplete tab while preserving its saved internal identity.
	 *
	 * @since 1.0.0
	 *
	 * @param array $tabs Existing field-settings tabs.
	 * @param array $form Current form, required by the host callback signature.
	 * @return array Tabs including the autocomplete panel.
	 */
	public function geolocation_settings_tab( $tabs, $form ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter -- Required Gravity Forms callback signature.
		$tabs[] = [
			'id'    => 'gfgeo_geolocation',
			'title' => esc_html__( 'Address Autocomplete', 'address-autocomplete-for-gravity-forms' ),
		];
		return $tabs;
	}

	/**
	 * Render Address options only for the autocomplete tab.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $form   Current form.
	 * @param string $tab_id Requested settings tab identity.
	 * @return void
	 */
	public function geolocation_settings_tab_content( $form, $tab_id ) {
		if ( 'gfgeo_geolocation' === $tab_id ) {
			$this->render_field_settings( $form );
		}
	}

	/**
	 * Return the supported renderer identity for integrations and fixtures.
	 *
	 * @since 1.0.0
	 *
	 * @return string[] Supported settings feature names.
	 */
	public static function get_field_settings_features() {
		return [ 'Address' ];
	}

	/**
	 * Render saved autocomplete controls without premium-only option groups.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form Current form, required by the host callback signature.
	 * @return void
	 */
	public function render_field_settings( $form ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter -- Required Gravity Forms callback signature.
		Settings::render();
	}

	/**
	 * Append Address tooltips without replacing native or third-party keys.
	 *
	 * @since 1.0.0
	 *
	 * @param array $tooltips Existing editor tooltips.
	 * @return array Tooltips including the supported Address settings.
	 */
	public function tooltips( $tooltips ) {
		$address_tt = FieldText::get_parts( 'address', null, 'tooltip' );

		$tooltips['gfgeo_address_autocomplete_tt']                 = FieldText::get_safe( $address_tt, 'autocomplete', 'enable' );
		$tooltips['gfgeo_force_autocomplete_selection_tt']         = FieldText::get_safe( $address_tt, 'autocomplete', 'force_selection' );
		$tooltips['gfgeo_force_autocomplete_selection_message_tt'] = FieldText::get_safe( $address_tt, 'autocomplete', 'force_selection_message' );
		$tooltips['gfgeo_address_autocomplete_types_tt']           = FieldText::get_safe( $address_tt, 'autocomplete', 'types' );
		$tooltips['gfgeo_address_autocomplete_country_tt']         = FieldText::get_safe( $address_tt, 'autocomplete', 'countries' );
		$tooltips['gfgeo_address_autocomplete_language_tt']        = FieldText::get_safe( $address_tt, 'autocomplete', 'language' );
		$tooltips['gfgeo_autocomplete_restriction_usage_tt']       = FieldText::get_safe( $address_tt, 'autocomplete', 'bias_type' );
		$tooltips['gfgeo_autocomplete_proximity_lat_tt']           = FieldText::get_safe( $address_tt, 'proximity', 'lat' );
		$tooltips['gfgeo_autocomplete_proximity_lng_tt']           = FieldText::get_safe( $address_tt, 'proximity', 'lng' );
		$tooltips['gfgeo_autocomplete_proximity_radius_tt']        = FieldText::get_safe( $address_tt, 'proximity', 'radius' );
		$tooltips['gfgeo_bounds_sw_point_tt']                      = FieldText::get_safe( $address_tt, 'area_bounds', 'sw_point' );
		$tooltips['gfgeo_bounds_ne_point_tt']                      = FieldText::get_safe( $address_tt, 'area_bounds', 'ne_point' );

		return $tooltips;
	}

	/**
	 * Emit defaults for newly inserted Address fields inside the host's switch.
	 *
	 * Existing saved fields are not modified by this callback.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function default_values() {
		?>
		case 'gfgeo_address' :
			field.label                                      = 'Address';
			field.gfgeo_address_autocomplete                 = true;
			field.gfgeo_address_autocomplete_language        = '';
			field.gfgeo_force_autocomplete_selection         = '';
			field.gfgeo_force_autocomplete_selection_message = 'Please select an address from the suggested results.';
			field.gfgeo_address_autocomplete_types           = '';
			field.gfgeo_autocomplete_restriction_usage        = '';
			field.gfgeo_address_autocomplete_strict_bounds   = false;
			field.gfgeo_autocomplete_bounds_sw_point         = '';
			field.gfgeo_autocomplete_bounds_ne_point         = '';
			field.gfgeo_autocomplete_proximity_lat           = '';
			field.gfgeo_autocomplete_proximity_lng           = '';
			field.gfgeo_autocomplete_proximity_radius        = '';
		break;
		<?php
	}
}
