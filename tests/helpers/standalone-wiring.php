<?php
/**
 * Dependency-free extraction smoke test using the actual plugin classes.
 *
 * Run: php tests/helpers/standalone-wiring.php
 * Minimal WordPress/Gravity Forms doubles check boot wiring and data preservation.
 * This is not installed Gravity Forms or browser acceptance.
 */

namespace {
	$root = dirname( __DIR__, 2 );
	$hooks = [];
	$checks = [];
	$admin = true;

	define( 'ABSPATH', $root . '/' );
	foreach ( [
		'GFGEOAC_PATH' => $root . '/', 'GFGEOAC_URL' => 'https://example.invalid/plugin/',
		'GFGEOAC_PREFIX' => 'gfgeo', 'GFGEOAC_VERSION' => '1.0.0',
		'GFGEOAC_PLUGIN_NAME' => 'Address Autocomplete for Gravity Forms',
		'GFGEOAC_PLUGIN_FILE' => $root . '/address-autocomplete-for-gravity-forms.php',
		'GFGEOAC_BASENAME' => 'address-autocomplete-for-gravity-forms/address-autocomplete-for-gravity-forms.php',
		'GFGEOAC_SITE_URL' => 'https://gravitygeolocation.com', 'GFGEOAC_PACKAGE_TYPE' => 'free',
		'GFGEOAC_PACKAGE_LABEL' => 'Address Autocomplete',
		'GFGEOAC_GOOGLE_MAPS_COUNTRY' => 'US', 'GFGEOAC_GOOGLE_MAPS_LANGUAGE' => 'en',
		'GFGEOAC_GOOGLE_MAPS_BROWSER_API_KEY' => '',
	] as $key => $value ) { define( $key, $value ); }
	define( 'GFGEOAC_MIN_HOST_VERSION', '2.10.3' );
	define( 'GFGEOAC_DISABLE_GOOGLE_MAPS_API', false );

	function add_filter( $name, $callback, $priority = 10, $arguments = 1 ) {
		$GLOBALS['hooks'][ $name ][ $priority ][] = [ $callback, $arguments ];
	}
	function add_action( $name, $callback, $priority = 10, $arguments = 1 ) { add_filter( $name, $callback, $priority, $arguments ); }
	function apply_filters( $name, $value, ...$arguments ) {
		$callbacks = $GLOBALS['hooks'][ $name ] ?? [];
		ksort( $callbacks );
		foreach ( $callbacks as $group ) {
			foreach ( $group as $callback ) {
				$value = call_user_func_array( $callback[0], array_slice( array_merge( [ $value ], $arguments ), 0, $callback[1] ) );
			}
		}
		return $value;
	}
	function is_admin() { return $GLOBALS['admin']; }
	function did_action( $name ) { return 0; }
	function __( $value, $domain = '' ) { return $value; }
	function esc_html__( $value, $domain = '' ) { return $value; }
	function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
	function esc_html_e( $value, $domain = '' ) { echo esc_html( $value ); }
	function esc_attr( $value ) { return esc_html( $value ); }
	function esc_attr_e( $value, $domain = '' ) { echo esc_attr( $value ); }
	function gform_tooltip( $key ) {}
	function disabled( $value, $current = true, $echo = true ) {
		$output = (string) $value === (string) $current ? ' disabled="disabled"' : '';
		if ( $echo ) { echo $output; }
		return $output;
	}
	function esc_url( $value ) { return $value; }
	function admin_url( $path ) { return 'https://example.invalid/wp-admin/' . $path; }
	function add_query_arg( $key, $value, $url ) { return $url . '&' . $key . '=' . rawurlencode( $value ); }
	function add_submenu_page( $parent, $page_title, $menu_title, $capability, $slug, $callback ) {
		$GLOBALS['dashboard_menu'] = compact( 'parent', 'page_title', 'menu_title', 'capability', 'slug', 'callback' );
		return 'forms_page_' . $slug;
	}
	function add_options_page( ...$args ) { throw new \RuntimeException( 'Active Gravity Forms should own the plugin page' ); }
	function absint( $value ) { return abs( (int) $value ); }
	function is_ssl() { return true; }
	function rgar( $array, $key, $default = null ) { return $array[ $key ] ?? $default; }
	function update_option( ...$args ) { throw new \RuntimeException( 'Unexpected option write' ); }
	function delete_option( ...$args ) { throw new \RuntimeException( 'Unexpected option deletion' ); }
	function wp_remote_get( ...$args ) { throw new \RuntimeException( 'Unexpected remote request' ); }
	function wp_remote_post( ...$args ) { throw new \RuntimeException( 'Unexpected remote request' ); }
	function wp_register_script( $handle, $src, $deps, $version, $footer ) { $GLOBALS['registered_scripts'][ $handle ] = compact( 'src', 'deps' ); }
	function wp_register_style( $handle, $src, $deps, $version ) { $GLOBALS['registered_styles'][ $handle ] = compact( 'src', 'deps' ); }
	function wp_enqueue_script( $handle ) { $GLOBALS['enqueued_scripts'][] = $handle; }
	function wp_enqueue_style( $handle ) { $GLOBALS['enqueued_styles'][] = $handle; }
	function wp_add_inline_script( $handle, $data, $position ) { $GLOBALS['inline_scripts'][] = $data; }
	function wp_json_encode( $data, $flags = 0 ) { return json_encode( $data, $flags ); }

	class GF_Field extends \stdClass {
		public function __construct( $data = [] ) { foreach ( $data as $key => $value ) { $this->$key = $value; } }
	}
	class GF_Fields {
		public static $fields = [];
		public static function register( $field ) { self::$fields[] = $field; }
	}
	class GFCommon {
		public static function is_form_editor() { return ! empty( $GLOBALS['is_form_editor'] ); }
		public static function is_entry_detail_edit() { return false; }
	}
	class GFForms {
		public static $version = '2.10.3';
		public static function include_addon_framework() {}
	}
	class GFAddOn {
		public static $registered = [];
		public static $saved_settings = [];
		public static function register( $class ) { self::$registered[] = $class; }
		public function get_plugin_settings() { return $GLOBALS['current_plugin_settings'] ?? []; }
		public function update_plugin_settings( $settings ) { self::$saved_settings = $settings; }
	}

	spl_autoload_register( static function ( $class ) use ( $root ) {
		$prefix = 'GravityGeolocationAutocomplete\\';
		if ( 0 === strpos( $class, $prefix ) ) {
			$file = $root . '/src/' . str_replace( '\\', '/', substr( $class, strlen( $prefix ) ) ) . '.php';
			if ( is_file( $file ) ) { require_once $file; }
		}
	} );

	// Loader works with licensing/migration classes physically absent.
	\GravityGeolocationAutocomplete\Loader::load();
	\GravityGeolocationAutocomplete\Loader::register_addon();
	$checks['add-on registers without licensing/migrations'] = GFAddOn::$registered === [ \GravityGeolocationAutocomplete\Core\AddOn::class ];
	$checks['licensing classes absent'] = ! class_exists( '\GravityGeolocationAutocomplete\Licensing\LicenseManager' );
	$checks['migration classes absent'] = ! class_exists( '\GravityGeolocationAutocomplete\Migrations\Manager' );
	$checks['local admin menu registered'] = isset( $hooks['admin_menu'] );
	\GravityGeolocationAutocomplete\Admin\Dashboard::register_page();
	$checks['Address Autocomplete belongs to Forms'] = 'gf_edit_forms' === $dashboard_menu['parent'] && 'Address Autocomplete' === $dashboard_menu['menu_title'];
	$links = apply_filters( 'plugin_action_links_' . GFGEOAC_BASENAME, [ 'deactivate' => '<a>Deactivate</a>' ] );
	$checks['plugin row retains overview/settings/docs'] = array_keys( $links ) === [ 'overview', 'settings', 'docs', 'deactivate' ];
	$checks['distribution loader removed'] = ! class_exists( '\\GravityGeolocationAutocomplete\\Core\\Distribution' );
	$checks['distribution folder removed'] = ! file_exists( $root . '/src/Distribution' );
	$checks['no framework loader or registry'] = ! class_exists( '\WPGeoFW\Admin\PluginManager\Registry' ) && ! defined( 'WPGEOFW_LOADED' );

	// Exercise the actual add-on boot methods without a live Gravity Forms host.
	$reflection = new \ReflectionClass( \GravityGeolocationAutocomplete\Core\AddOn::class );
	$addon = $reflection->newInstanceWithoutConstructor();
	foreach ( [ 'include_files', 'register_providers' ] as $method_name ) {
		$method = $reflection->getMethod( $method_name );
		if ( PHP_VERSION_ID < 80100 ) {
			$method->setAccessible( true );
		}
		$method->invoke( $addon );
	}
	$method->invoke( $addon );
	$checks['form integration boots only once'] = 1 === count( $hooks['gform_pre_render'][10] );
	$checks['dynamic feature provider removed'] = ! class_exists( '\\GravityGeolocationAutocomplete\\Features\\Form\\Provider' );
	$checks['one-call admin wrapper removed'] = ! class_exists( '\\GravityGeolocationAutocomplete\\Features\\Form\\Admin\\Admin' );
	$checks['generic group registry removed'] = ! class_exists( '\\GravityGeolocationAutocomplete\\Common\\Form\\Admin\\FormEditor\\FieldGroup' );
	$checks['one-use editor traits removed'] = ! trait_exists( '\\GravityGeolocationAutocomplete\\Features\\Form\\Admin\\FormEditor\\Tooltips' ) && ! trait_exists( '\\GravityGeolocationAutocomplete\\Features\\Form\\Admin\\FormEditor\\DefaultValues' );
	$groups = [ [ 'name' => 'standard_fields' ], [ 'name' => 'advanced_fields' ], [ 'name' => 'post_fields' ] ];
	$registered_groups = apply_filters( 'gform_field_groups_form_editor', $groups );
	$checks['single group follows advanced fields'] = array_column( $registered_groups, 'name' ) === [ 'standard_fields', 'advanced_fields', 'gfgeo_geolocation_fields', 'post_fields' ];
	$registered_groups[2]['fields'] = [ 'existing-address-button' ];
	$checks['populated group survives repeated registration'] = apply_filters( 'gform_field_groups_form_editor', $registered_groups ) === $registered_groups;
	$checks['group appended when advanced group absent'] = array_column( apply_filters( 'gform_field_groups_form_editor', [ [ 'name' => 'standard_fields' ] ] ), 'name' ) === [ 'standard_fields', 'gfgeo_geolocation_fields' ];
	$checks['invalid group input remains untouched'] = null === apply_filters( 'gform_field_groups_form_editor', null );
	$types = array_map( static function ( $field ) { return $field->type; }, GF_Fields::$fields );
	$checks['only Address plus four premium buttons'] = $types === [ 'gfgeo_address', 'gfgeo_map', 'gfgeo_directions', 'gfgeo_distance', 'gfgeo_address_validation' ];
	foreach ( GF_Fields::$fields as $field ) {
		$is_address = 'gfgeo_address' === $field->type;
		if ( $is_address ) {
			$checks['custom Address supports conditional logic'] = true === $field->is_conditional_logic_supported();
			$checks['custom Address offers conditional settings'] = in_array( 'conditional_logic_field_setting', $field->get_form_editor_field_settings(), true );
		}
		$checks[ $field->type . ' implementation' ] = $is_address
			? false === strpos( get_class( $field ), 'Placeholders' )
			: false !== strpos( get_class( $field ), 'Placeholders' );
		if ( ! $is_address ) {
			$checks[ $field->type . ' unavailable marker' ] = false !== strpos( $field->get_form_editor_field_icon(), 'gfgeo-pro-disabled' );
			$checks[ $field->type . ' no frontend input' ] = empty( $field->get_field_input( [] ) );
			$checks[ $field->type . ' no entry output' ] = '' === $field->get_value_save_entry( 'do not save', [], '', 0, [] );
		}
	}
	$checks['only Address settings renderer'] = \GravityGeolocationAutocomplete\Features\Form\Admin\FormEditor::get_field_settings_features() === [ 'Address' ];
	$GLOBALS['is_form_editor'] = true;
	$editor = new \GravityGeolocationAutocomplete\Features\Form\Admin\FormEditor();
	$tabs = apply_filters( 'gform_field_settings_tabs', [ [ 'id' => 'general', 'title' => 'General' ] ], [] );
	$checks['Address Autocomplete tab retains its existing identity'] = $tabs[1] === [ 'id' => 'gfgeo_geolocation', 'title' => 'Address Autocomplete' ];
	$tooltips = apply_filters( 'gform_tooltips', [ 'native_tooltip' => 'Preserved native text' ] );
	$checks['merged tooltip callback preserves native tooltips'] = 'Preserved native text' === $tooltips['native_tooltip'];
	$checks['all Address tooltips have text'] = 13 === count( $tooltips ) && ! in_array( '', $tooltips, true );
	$address_labels = \GravityGeolocationAutocomplete\Features\Form\Admin\FormEditor\Helpers\FieldText::get_parts( 'address', 'autocomplete' );
	$checks['clear autocomplete option labels'] = 'Autocomplete result types' === $address_labels['types'] && 'Restrict to countries' === $address_labels['countries'];
	$checks['language tooltip describes the global fallback'] = false !== strpos( $tooltips['gfgeo_address_autocomplete_language_tt'], 'global language setting' );
	ob_start();
	$editor->default_values();
	$defaults = ob_get_clean();
	$checks['merged defaults initialize only Address'] = 1 === substr_count( $defaults, 'case ' ) && false !== strpos( $defaults, "case 'gfgeo_address'" ) && false !== strpos( $defaults, 'field.gfgeo_address_autocomplete' );
	ob_start();
	$editor->geolocation_settings_tab_content( [], 'gfgeo_geolocation' );
	$settings_markup = ob_get_clean();
	$checks['direct renderer shows Address options'] = false !== strpos( $settings_markup, 'gfgeo-address-autocomplete-country' ) && false !== strpos( $settings_markup, 'gfgeo-force-autocomplete-selection' );
	$checks['direct renderer excludes locator and geocoder selector'] = false === strpos( $settings_markup, 'gfgeo-infield-locator' ) && false === strpos( $settings_markup, 'gfgeo-geocoder-id' );
	ob_start();
	$editor->geolocation_settings_tab_content( [], 'general' );
	$checks['unrelated tabs receive no Address markup'] = '' === ob_get_clean();
	$GLOBALS['is_form_editor'] = false;
	$checks['Search entry point remains promotional'] = isset( $hooks['gform_toolbar_menu'] );
	$checks['no premium AJAX/services booted'] = ! array_filter( array_keys( $hooks ), static function ( $hook ) {
		return 0 === strpos( $hook, 'wp_ajax_' ) || 0 === strpos( $hook, 'rest_api_' ) || false !== strpos( $hook, 'after_submission' );
	} );

	$sections = \GravityGeolocationAutocomplete\Admin\Settings::get_settings();
	$names = [];
	foreach ( $sections as $section ) { foreach ( $section['fields'] as $field ) { if ( 'html' !== ( $field['type'] ?? '' ) ) { $names[] = $field['name']; } } }
	$checks['only autocomplete global inputs'] = $names === [ 'gfgeo_google_maps_api_key', 'gfgeo_google_maps_country', 'gfgeo_google_maps_language' ];
	$api_sections = \GravityGeolocationAutocomplete\Admin\Settings::get_settings();
	$help = '';
	foreach ( $api_sections as $api_section ) {
		foreach ( $api_section['fields'] as $api_field ) {
			if ( 'gfgeo_google_maps_api_key' === ( $api_field['name'] ?? '' ) ) {
				$help = $api_section['description'] ?? '';
				$checks['browser-key input retains its accessible label'] = 'Browser API Key' === ( $api_field['label'] ?? '' );
				$checks['browser-key guidance is not duplicated below the input'] = empty( $api_field['after_input'] );
			}
		}
	}
	$checks['browser-key setup guide is in the section above the input'] = false !== strpos( $help, 'https://gravitygeolocation.com/docs/google-maps-api-keys-gravity-geolocation/' ) && false !== strpos( $help, 'target="_blank" rel="noopener noreferrer"' );
	$checks['setup help clarifies free browser-only requirements'] = false !== strpos( $help, 'Places API (New)' ) && false !== strpos( $help, 'No server key is required.' );
	$checks['API loading remains filterable'] = false !== strpos( file_get_contents( $root . '/src/Core/AddOn.php' ), "apply_filters( 'gfgeo_disable_google_maps_api'" );

	$current = [ 'gfgeo_google_maps_server_api_key' => 'private-test-value', 'gfgeo_enable_ip_locator' => '', 'gfgeo_token_old' => null, 'gfgeo_enable_high_accuracy_mode' => true, 'gfgeo_disable_google_maps_api' => true ];
	$submitted = [ 'gfgeo_google_maps_api_key' => 'new-browser-value', 'gfgeo_google_maps_server_api_key' => 'forged', 'gfgeo_token_new' => 'forged', 'gfgeo_enable_ip_locator' => 'forged', 'gfgeo_disable_google_maps_api' => false ];
	$saved = \GravityGeolocationAutocomplete\Admin\Settings::preserve_hidden_plugin_settings( $submitted, $current );
	foreach ( $current as $key => $value ) { $checks[ 'preserve hidden ' . $key ] = array_key_exists( $key, $saved ) && $saved[ $key ] === $value; }
	$checks['reject forged hidden setting'] = ! isset( $saved['gfgeo_token_new'] );
	$checks['allow browser-key change'] = 'new-browser-value' === $saved['gfgeo_google_maps_api_key'];
	$GLOBALS['current_plugin_settings'] = $current;
	$addon->update_plugin_settings( $submitted );
	$checks['AddOn directly preserves settings before native save'] = GFAddOn::$saved_settings === $saved;
	$checks['no distribution save/render hooks'] = ! isset( $hooks['gfgeo_plugin_settings_before_save'] ) && ! isset( $hooks['gfgeo_prepare_form_for_render'] );

	// Imported premium controls/data must not revive services or mutate saved fields.
	$fields = [
		new GF_Field( [ 'id' => 1, 'type' => 'gfgeo_address', 'gfgeo_infield_locator_button' => true ] ),
		new GF_Field( [ 'id' => 2, 'type' => 'address', 'gfgeo_address_autocomplete' => true ] ),
		new GF_Field( [ 'id' => 3, 'type' => 'address', 'gfgeo_address_autocomplete' => false ] ),
		new GF_Field( [ 'id' => 4, 'type' => 'gfgeo_geocoder', 'gfgeo_geocode_on_load' => true ] ),
		new GF_Field( [ 'id' => 5, 'type' => 'gfgeo_directions' ] ),
		new GF_Field( [ 'id' => 6, 'type' => 'text', 'gfgeo_dynamic_field_usage' => 'latitude' ] ),
	];
	$processor = new \GravityGeolocationAutocomplete\Features\Form\Runtime\FormGeoProcessor();
	$form = [ 'id' => 23, 'fields' => $fields, 'gravityforms_geolocation' => [ 'geocoderIds' => [4], 'ipLocatorStatus' => 'enabled' ] ];
	$checks['render does not clone or rewrite saved fields'] = $processor->render_form( $form ) === $form;
	$data = \GravityGeolocationAutocomplete\Features\Form\Runtime\FormGeoProcessor::$forms[23];
	$checks['collect only custom and opted-in native Address'] = array_column( $data['fields'], 'id' ) === [1, 2];
	$checks['no framework Form created'] = ! class_exists( '\WPGeoFW\Components\Geo\Form\Form' );
	$checks['no locator in frontend payload'] = ! isset( $data['fields'][0]['gfgeo_infield_locator_button'] );
	$checks['saved locator choice preserved'] = true === $fields[0]->gfgeo_infield_locator_button;
	$checks['saved fields not enriched'] = ! isset( $fields[0]->gfgeo_type );
	$checks['no auto-locator arguments'] = ! isset( $data['args'] );
	$checks['no entitlement context'] = ! isset( $data['config']['runtimeContext'] );
	$checks['no IP credentials'] = ! isset( $data['config']['ipProvider'] ) && ! isset( $data['config']['ipToken'] );
	$checks['only local autocomplete assets'] = array_unique( $enqueued_scripts ) === [ 'gfgeoac-autocomplete' ] && array_unique( $enqueued_styles ) === [ 'gfgeoac-autocomplete' ];
	$checks['no framework script dependencies'] = $registered_scripts['gfgeoac-autocomplete']['deps'] === [ 'jquery' ];
	$checks['framework asset definitions empty'] = ! $reflection->hasMethod( 'get_framework_related_assets' );
	$processor->render_form( [ 'id' => 24, 'fields' => array_slice( $fields, 2 ) ] );
	$checks['premium-only forms do not initialize runtime'] = 1 === count( \GravityGeolocationAutocomplete\Features\Form\Runtime\FormGeoProcessor::$forms );
	$checks['framework Form subclass removed'] = ! file_exists( $root . '/src/Features/Form/Runtime/Form.php' );

	$manifest = json_decode( file_get_contents( $root . '/composer.json' ), true );
	$checks['no runtime Composer packages'] = empty( json_decode( file_get_contents( $root . '/composer.lock' ), true )['packages'] );
	$checks['no eager premium Composer includes'] = empty( $manifest['autoload']['files'] );
	foreach ( [ 'Licensing', 'Migrations', 'Features/Search', 'Features/Map', 'Rest', 'Functions', 'Deprecated', 'Features/Form/Feeds', 'Features/Form/Helpers', 'Services', 'Utils', 'Integrations' ] as $path ) {
		$checks['removed ' . $path] = ! file_exists( $root . '/src/' . $path );
	}

	echo json_encode( $checks, JSON_PRETTY_PRINT ) . PHP_EOL;
	exit( in_array( false, $checks, true ) ? 1 : 0 );
}
