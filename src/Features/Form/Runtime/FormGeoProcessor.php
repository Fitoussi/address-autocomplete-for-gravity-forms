<?php
/**
 * Prepare Address-only settings and enqueue the standalone autocomplete adapter.
 *
 * No framework Form instance, location flow or geocoder data is created here.
 * The gfgeo property names remain unchanged for a later premium upgrade.
 *
 * @package GravityGeolocationAutocomplete\Features\Form\Runtime
 * @since 1.0.0
 */

namespace GravityGeolocationAutocomplete\Features\Form\Runtime;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Prepare Address-only frontend payloads and enqueue the standalone assets.
 *
 * @since 1.0.0
 */
class FormGeoProcessor {

	/**
	 * Collected form identities retained for integration compatibility.
	 *
	 * @since 1.0.0
	 *
	 * @var array<int,bool>
	 */
	public static $gforms = [];

	/**
	 * Address-only frontend payloads indexed by form identity.
	 *
	 * @since 1.0.0
	 *
	 * @var array<int,array>
	 */
	public static $forms = [];

	/**
	 * Saved preferences supported by the standalone autocomplete adapter.
	 *
	 * @since 1.0.0
	 *
	 * @var string[]
	 */
	private const FIELD_OPTIONS = [
		'gfgeo_address_autocomplete',
		'gfgeo_force_autocomplete_selection',
		'gfgeo_force_autocomplete_selection_message',
		'gfgeo_address_autocomplete_types',
		'gfgeo_address_autocomplete_country',
		'gfgeo_address_autocomplete_language',
		'gfgeo_autocomplete_restriction_usage',
		'gfgeo_address_autocomplete_strict_bounds',
		'gfgeo_autocomplete_bounds_sw_point',
		'gfgeo_autocomplete_bounds_ne_point',
		'gfgeo_autocomplete_proximity_lat',
		'gfgeo_autocomplete_proximity_lng',
		'gfgeo_autocomplete_proximity_radius',
	];

	/**
	 * Register render and asset hooks without submission or migration handlers.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'gform_pre_render', [ $this, 'render_form' ] );
		add_filter( 'gform_admin_pre_render', [ $this, 'admin_render_form' ] );
		add_action( 'gform_enqueue_scripts', [ $this, 'enqueue_form' ], 10, 2 );
	}

	/**
	 * Collect settings without changing the saved form or its fields.
	 * The Address renderer and payload whitelist omit unsupported controls.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form Gravity Forms form object.
	 * @return array Render-only form.
	 */
	public function render_form( $form ) {
		$this->enqueue_form( $form );
		return $form;
	}

	/**
	 * Enable autocomplete for native entry editing, not the form builder.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form Current Gravity Forms form.
	 * @return array Unchanged form with entry-editing assets prepared when needed.
	 */
	public function admin_render_form( $form ) {
		return \GFCommon::is_entry_detail_edit() ? $this->render_form( $form ) : $form;
	}

	/**
	 * Enqueue only when this form contains an opted-in Address input.
	 * Called before normal render and again after render filters; the last payload
	 * wins, allowing conditional/AJAX renders to use their current field settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form Gravity Forms form object.
	 * @param bool  $ajax Whether Gravity Forms uses AJAX; retained for the host hook signature.
	 * @return void
	 */
	public function enqueue_form( $form, $ajax = false ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter -- Required Gravity Forms callback signature.
		if ( \GFCommon::is_form_editor() ) {
			return;
		}
		$form_id = absint( $form['id'] ?? 0 );
		$fields  = $this->collect_fields( $form['fields'] ?? [] );
		if ( ! $form_id || ! $fields ) {
			return;
		}

		self::$gforms[ $form_id ] = true;
		self::$forms[ $form_id ]  = [
			'formId' => $form_id,
			'fields' => array_values( $fields ),
			'config' => $this->get_form_config(),
		];

		wp_register_script( 'gfgeoac-autocomplete', GFGEOAC_URL . 'build/js/frontend/address-autocomplete.min.js', [ 'jquery' ], GFGEOAC_VERSION, true );
		wp_register_style( 'gfgeoac-autocomplete', GFGEOAC_URL . 'assets/css/address-autocomplete.css', [], GFGEOAC_VERSION );
		wp_enqueue_script( 'gfgeoac-autocomplete' );
		wp_enqueue_style( 'gfgeoac-autocomplete' );
		wp_add_inline_script(
			'gfgeoac-autocomplete',
			'window.gfgeoAutocompleteForms = Object.assign(window.gfgeoAutocompleteForms || {}, ' . wp_json_encode( self::$forms, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ');',
			'before'
		);
	}

	/**
	 * Collect only supported saved settings, including native repeater children.
	 * Private server keys, connection IDs and complete field objects never leave PHP.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields Native field objects.
	 * @return array Address settings keyed by logical field ID.
	 */
	private function collect_fields( $fields ) {
		$result = [];
		foreach ( (array) $fields as $field ) {
			if ( ! \is_object( $field ) ) {
				continue;
			}
			if ( ! empty( $field->fields ) && \is_array( $field->fields ) ) {
				$result += $this->collect_fields( $field->fields );
			}
			$type = $field->type ?? '';
			if ( ! \in_array( $type, [ 'gfgeo_address', 'address' ], true ) ) {
				continue;
			}
			$enabled = $field->gfgeo_address_autocomplete ?? ( 'gfgeo_address' === $type );
			if ( ! \in_array( $enabled, [ true, 1, '1', 'true' ], true ) ) {
				continue;
			}
			$id = absint( $field->id ?? 0 );
			if ( ! $id ) {
				continue;
			}
			$options = [
				'id'                         => $id,
				'type'                       => $type,
				'gfgeo_address_autocomplete' => true,
			];
			foreach ( self::FIELD_OPTIONS as $key ) {
				if ( isset( $field->$key ) && ( \is_scalar( $field->$key ) || \is_array( $field->$key ) ) ) {
					$options[ $key ] = $field->$key;
				}
			}
			$result[ $id ] = $options;
		}
		return $result;
	}

	/**
	 * Return browser-key and localization configuration without premium data.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string,mixed> Standalone browser configuration.
	 */
	public function get_form_config() {
		return [
			'googleMapsBrowserApiKey' => GFGEOAC_GOOGLE_MAPS_BROWSER_API_KEY,
			'regionCode'              => GFGEOAC_GOOGLE_MAPS_COUNTRY,
			'languageCode'            => GFGEOAC_GOOGLE_MAPS_LANGUAGE,
			'disableGoogleApi'        => (bool) GFGEOAC_DISABLE_GOOGLE_MAPS_API,
			'isAdmin'                 => \GFCommon::is_entry_detail_edit(),
		];
	}
}
