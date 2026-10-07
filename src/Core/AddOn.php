<?php
/**
 * Native Gravity Forms integration for the standalone autocomplete plugin.
 *
 * @package GravityGeolocationAutocomplete\Core
 * @since 1.0.0
 */

namespace GravityGeolocationAutocomplete\Core;

use GravityGeolocationAutocomplete\Admin\Settings;
use GravityGeolocationAutocomplete\Admin\SearchUnavailable;
use GravityGeolocationAutocomplete\Common\Form\Fields\Registrar;
use GravityGeolocationAutocomplete\Features\Form\Admin\FormEditor;
use GravityGeolocationAutocomplete\Features\Form\Runtime\FormGeoProcessor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

\GFForms::include_addon_framework();

/**
 * Integrate autocomplete with the native Add-On API and shared settings identity.
 *
 * Host override property names intentionally match GFAddOn.
 *
 * @since 1.0.0
 */
class AddOn extends \GFAddOn {

	/**
	 * Plugin version exposed to the native Add-On framework.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $_version = GFGEOAC_VERSION; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore -- Retained native Add-On naming contract.

	/**
	 * Minimum supported Gravity Forms version.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $_min_gravityforms_version = GFGEOAC_MIN_HOST_VERSION; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore -- Retained native Add-On naming contract.

	/**
	 * Full product title shown by the host.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $_title = GFGEOAC_PLUGIN_NAME; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore -- Retained native Add-On naming contract.

	/**
	 * Shared Add-On settings identity retained for premium upgrades.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $_slug = 'gravityforms_geolocation'; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore -- Retained native Add-On naming contract.

	/**
	 * Absolute path to the standalone plugin entry point.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $_full_path = GFGEOAC_PLUGIN_FILE; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore -- Retained native Add-On naming contract.

	/**
	 * Compact title displayed in the Gravity Forms settings navigation.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $_short_title = 'Address Autocomplete'; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore -- Retained native Add-On naming contract.

	/**
	 * Single Add-On instance for the current request.
	 *
	 * @since 1.0.0
	 *
	 * @var self|null
	 */
	private static $_instance = null; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore -- Retained native Add-On naming contract.

	/**
	 * Whether this instance has registered its form integration.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	private $form_registered = false;

	/**
	 * Return the single native Add-On instance for this request.
	 *
	 * @since 1.0.0
	 *
	 * @return self Add-On instance.
	 */
	public static function get_instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Initialize shared capabilities and whitelist the standalone local assets.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->_capabilities               = [ 'gravityforms_gfgeo_settings', 'gravityforms_gfgeo_form_settings', 'gravityforms_gfgeo_uninstall' ];
		$this->_capabilities_settings_page = $this->_capabilities[0];
		$this->_capabilities_form_settings = $this->_capabilities[1];
		$this->_capabilities_uninstall     = $this->_capabilities[2];
		parent::__construct();
		add_filter( 'gform_noconflict_scripts', [ $this, 'register_noconflict_assets' ], 12 );
		add_filter( 'gform_noconflict_styles', [ $this, 'register_noconflict_assets' ], 12 );
	}

	/**
	 * Initialize autocomplete controls without a framework or licensing runtime.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function pre_init() {
		parent::pre_init();
		$this->define_constants();
		$this->include_files();
		$this->register_providers();
	}

	/**
	 * Read autocomplete configuration without modifying hidden premium settings.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function define_constants() {
		define( 'GFGEOAC_GOOGLE_MAPS_BROWSER_API_KEY', $this->sanitize_string_setting( $this->get_plugin_setting( 'gfgeo_google_maps_api_key' ) ) );
		define( 'GFGEOAC_GOOGLE_MAPS_COUNTRY', $this->sanitize_string_setting( $this->get_plugin_setting( 'gfgeo_google_maps_country' ) ) );
		define( 'GFGEOAC_GOOGLE_MAPS_LANGUAGE', $this->sanitize_string_setting( $this->get_plugin_setting( 'gfgeo_google_maps_language' ) ) );
		define( 'GFGEOAC_DISABLE_GOOGLE_MAPS_API', (bool) apply_filters( 'gfgeo_disable_google_maps_api', ! empty( $this->get_plugin_setting( 'gfgeo_disable_google_maps_api' ) ) ) );
	}

	/**
	 * Register non-functional Search discovery entry points in the admin.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function include_files() {
		if ( is_admin() ) {
			( new SearchUnavailable() )->register();
		}
	}

	/**
	 * Register Address integration and four promotional field buttons once.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function register_providers() {
		if ( $this->form_registered || apply_filters( 'gfgeo_disable_form_provider', false ) ) {
			return;
		}

		$this->form_registered = true;
		Registrar::register_fields();

		if ( is_admin() && ! apply_filters( 'gfgeo_disable_form_admin_loader', false ) ) {
			new FormEditor();
		}

		new FormGeoProcessor();
	}

	/**
	 * Register bundled translations in addition to WordPress.org language packs.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_text_domain() {
		// Bundled translations need their custom path before directory language packs exist.
		// phpcs:ignore PluginCheck.CodeAnalysis.DiscouragedFunctions.load_plugin_textdomainFound -- Retain bundled translations on supported WordPress versions.
		load_plugin_textdomain( 'address-autocomplete-for-gravity-forms', false, dirname( GFGEOAC_BASENAME ) . '/languages/' );
	}

	/**
	 * Append local asset handles to the Gravity Forms no-conflict whitelist.
	 *
	 * @since 1.0.0
	 *
	 * @param string[] $assets Existing host whitelist.
	 * @return string[] Unique handles allowed by the host.
	 */
	public function register_noconflict_assets( $assets ) {
		return array_values( array_unique( array_merge( $assets, [ 'gfgeoac-autocomplete', 'gfgeoac-choices', 'gfgeo-form-editor', 'gfgeo-admin', 'gfgeo-search-unavailable', 'jquery-ui-dialog', 'wp-jquery-ui-dialog', 'gfgeo-product-dashboard' ] ) ) );
	}

	/**
	 * Define the builder-only autocomplete controls and unavailable-field dialog.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] Native Add-On script definitions.
	 */
	public function scripts() {
		return [
			[
				'handle'    => 'gfgeo-form-editor',
				'src'       => GFGEOAC_URL . 'build/js/admin/gfgeo-form-editor.min.js',
				'version'   => $this->_version,
				'deps'      => [ 'jquery' ],
				'in_footer' => true,
				'callback'  => [ $this, 'localize_form_editor_script' ],
				'enqueue'   => [ [ 'admin_page' => [ 'form_editor' ] ] ],
			],
		];
	}

	/**
	 * Provide translated builder labels and unavailable-field dialog copy.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function localize_form_editor_script() {
		wp_localize_script(
			'gfgeo-form-editor',
			'gfgeoFormEditorStrings',
			[
				'autocomplete'     => [
					'allCountries' => __( 'All countries', 'address-autocomplete-for-gravity-forms' ),
					'allTypes'     => __( 'All place types', 'address-autocomplete-for-gravity-forms' ),
				],
				'unavailableField' => [
					/* translators: %s: Premium field title. */
					'title'          => __( 'The %s field is not available.', 'address-autocomplete-for-gravity-forms' ),
					/* translators: %s: Premium field title. */
					'message'        => __( 'The %s field is available with Gravity Geolocation.', 'address-autocomplete-for-gravity-forms' ),
					'upgradeNow'     => __( 'View packages', 'address-autocomplete-for-gravity-forms' ),
					'unlockFeatures' => __( 'to explore additional location tools.', 'address-autocomplete-for-gravity-forms' ),
				],
			]
		);
	}

	/**
	 * Define builder styles; frontend assets are enqueued by the form processor.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] Native Add-On stylesheet definitions.
	 */
	public function styles() {
		return [
			[
				'handle'  => 'gfgeoac-choices',
				'src'     => GFGEOAC_URL . 'assets/css/choices.css',
				'version' => $this->_version,
				'enqueue' => [ [ 'admin_page' => [ 'form_editor' ] ] ],
			],
			[
				'handle'  => 'gfgeo-admin',
				'src'     => GFGEOAC_URL . 'build/css/admin/gfgeo-admin.min.css',
				'version' => $this->_version,
				'enqueue' => [ [ 'admin_page' => [ 'form_editor' ] ] ],
			],
		];
	}

	/**
	 * Return browser-key, region, language and local discovery settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] Native Add-On settings sections.
	 */
	public function plugin_settings_fields() {
		return Settings::get_settings();
	}

	/**
	 * Preserve hidden premium values before native settings persistence.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Validated submitted plugin settings.
	 * @return void
	 */
	public function update_plugin_settings( $settings ) {
		parent::update_plugin_settings( Settings::preserve_hidden_plugin_settings( $settings, $this->get_plugin_settings() ) );
	}

	/**
	 * Sanitize a stored scalar preference without accepting compound values.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $setting Stored setting value.
	 * @return string Sanitized scalar or an empty string.
	 */
	private function sanitize_string_setting( $setting ) {
		return \is_scalar( $setting ) ? sanitize_text_field( (string) $setting ) : '';
	}

	/**
	 * Return the native Gravity Forms location icon identifier.
	 *
	 * @since 1.0.0
	 *
	 * @return string Menu icon class.
	 */
	public function get_menu_icon() {
		return 'gform-icon--place';
	}
}
