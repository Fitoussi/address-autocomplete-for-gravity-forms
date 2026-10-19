<?php
/**
 * Local product overview for the standalone Address Autocomplete plugin.
 *
 * This presentation-only page never reads licenses, creates forms or writes
 * settings. Actual configuration remains on the native Gravity Forms screen.
 *
 * @package GravityGeolocationAutocomplete\Admin\Dashboard
 *
 * @since 1.0.0
 */

namespace GravityGeolocationAutocomplete\Admin\Dashboard;

use GravityGeolocationAutocomplete\Admin\SetupNotice;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Own local product-page metadata, routing and page-scoped presentation.
 *
 * @since 1.0.0
 */
final class Loader {

	/**
	 * Stable product-page identity independent of the installed folder name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private const PAGE_SLUG = 'wpgeofw_gfgeo_dashboard';

	/**
	 * Native WordPress page hook returned during menu registration.
	 *
	 * @since 1.0.0
	 *
	 * @var string|false
	 */
	private static $page_hook = '';

	/**
	 * Register the local product page before Gravity Forms initializes.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function register() {
		SetupNotice::register();
		add_action( 'admin_menu', [ self::class, 'register_page' ], 30 );
		add_filter( 'gfgeo_plugin_settings_sections', [ self::class, 'add_settings_link' ], 110 );
		add_action( 'admin_enqueue_scripts', [ self::class, 'enqueue' ] );
		add_filter( 'plugin_action_links_' . GFGEOAC_BASENAME, [ self::class, 'add_action_link' ], 120 );
	}

	/**
	 * Place the overview with its Gravity Forms host, without an account SDK.
	 *
	 * The Settings fallback keeps setup/help available while the host is inactive.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function register_page() {
		$title = __( 'Address Autocomplete', 'wpgeo-address-autocomplete-for-gravity-forms' );
		if ( class_exists( '\GFForms' ) ) {
			self::$page_hook = add_submenu_page( 'gf_edit_forms', GFGEOAC_PLUGIN_NAME, $title, 'manage_options', self::PAGE_SLUG, [ self::class, 'render' ] );
		} else {
			self::$page_hook = add_options_page( GFGEOAC_PLUGIN_NAME, $title, 'manage_options', self::PAGE_SLUG, [ self::class, 'render' ] );
		}
	}

	/**
	 * Add the standalone plugin's overview, settings and documentation links.
	 *
	 * @since 1.0.0
	 *
	 * @param array $links Existing WordPress plugin action links.
	 *
	 * @return array
	 */
	public static function add_action_link( $links ) {
		return [
			'overview' => '<a href="' . esc_url( self::get_url() ) . '">' . esc_html__( 'Overview', 'wpgeo-address-autocomplete-for-gravity-forms' ) . '</a>',
			'settings' => '<a href="' . esc_url( admin_url( 'admin.php?page=gf_settings&subview=gravityforms_geolocation' ) ) . '">' . esc_html__( 'Settings', 'wpgeo-address-autocomplete-for-gravity-forms' ) . '</a>',
			'docs'     => '<a href="' . esc_url( GFGEOAC_SITE_URL . '/docs/' ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Docs', 'wpgeo-address-autocomplete-for-gravity-forms' ) . '</a>',
		] + $links;
	}

	/**
	 * Build a local navigation link using a whitelisted, non-mutating section.
	 *
	 * @since 1.0.0
	 *
	 * @param string $section Requested section.
	 *
	 * @return string
	 */
	public static function get_url( $section = 'overview' ) {
		$section = \in_array( $section, [ 'overview', 'comparison', 'help', 'products' ], true ) ? $section : 'overview';
		return add_query_arg( 'section', $section, admin_url( 'admin.php?page=' . self::PAGE_SLUG ) );
	}

	/**
	 * Load scoped styles on the product page or its native settings subview.
	 *
	 * Native settings need only our card styles, not framework dashboard assets.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook_suffix WordPress screen hook.
	 *
	 * @return void
	 */
	public static function enqueue( $hook_suffix ) {
		$is_dashboard = ! empty( self::$page_hook ) && self::$page_hook === $hook_suffix;
		// Navigation context only; no submitted settings or mutation are read here.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$page    = isset( $_GET['page'] ) && \is_string( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
		$subview = isset( $_GET['subview'] ) && \is_string( $_GET['subview'] ) ? sanitize_key( wp_unslash( $_GET['subview'] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
		$is_settings = 'gf_settings' === $page && 'gravityforms_geolocation' === $subview;

		if ( ! $is_dashboard && ! $is_settings ) {
			return;
		}

		wp_enqueue_style(
			'gfgeo-product-dashboard',
			GFGEOAC_URL . 'src/Admin/Dashboard/assets/css/dashboard.min.css',
			[],
			GFGEOAC_VERSION
		);
	}

	/**
	 * Add one discovery panel without changing the existing settings definitions.
	 *
	 * The standalone overview comes first. This is presentation, not a writable
	 * plugin setting or an entitlement check.
	 *
	 * @since 1.0.0
	 *
	 * @param array $sections Native settings definitions.
	 *
	 * @return array
	 */
	public static function add_settings_link( $sections ) {
		array_splice(
			$sections,
			0,
			0,
			[
				[
					'id'     => 'section_gfgeo_product_overview',
					'title'  => __( 'Need more than address autocomplete?', 'wpgeo-address-autocomplete-for-gravity-forms' ),
					'class'  => 'gform-settings-panel--full gfgeo-product-settings gfgeo-product-settings--free',
					'fields' => [
						[
							'name' => 'gfgeo_product_overview',
							'type' => 'html',
							'html' => [ self::class, 'render_settings_link' ],
						],
					],
				],
			]
		);
		return $sections;
	}

	/**
	 * Render overview discovery links with no inputs, licensing or remote content.
	 *
	 * Link to the internal feature overview first and external pricing second.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function render_settings_link() {
		?>
		<div class="gfgeo-product-settings__content">
			<p>
				<?php esc_html_e( 'Explore maps, current-location detection, dynamic fields, directions, nearby locations, and more with Gravity Geolocation.', 'wpgeo-address-autocomplete-for-gravity-forms' ); ?>
			</p>
			<div class="gfgeo-product-settings__actions">
				<a class="button button-primary" href="<?php echo esc_url( self::get_url() ); ?>">
					<?php esc_html_e( 'Explore features', 'wpgeo-address-autocomplete-for-gravity-forms' ); ?>
				</a>
				<a class="button button-secondary" href="<?php echo esc_url( GFGEOAC_SITE_URL . '/pricing/' ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View packages', 'wpgeo-address-autocomplete-for-gravity-forms' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the selected server-side tab; malformed requests fall back to Overview.
	 *
	 * WordPress already gates the submenu. The second check also protects direct
	 * callback invocation. The template receives public metadata/URLs only.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// This GET value selects presentation only: there is no save/delete action.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$requested = isset( $_GET['section'] ) && \is_string( $_GET['section'] ) ? sanitize_key( wp_unslash( $_GET['section'] ) ) : '';
		$tabs      = [
			'overview'   => __( 'Overview', 'wpgeo-address-autocomplete-for-gravity-forms' ),
			'comparison' => __( 'Compare Packages', 'wpgeo-address-autocomplete-for-gravity-forms' ),
			'help'       => __( 'Help', 'wpgeo-address-autocomplete-for-gravity-forms' ),
			'products'   => __( 'More Plugins', 'wpgeo-address-autocomplete-for-gravity-forms' ),
		];

		$version      = GFGEOAC_VERSION;
		$section      = isset( $tabs[ $requested ] ) ? $requested : 'overview';
		$settings_url = admin_url( 'admin.php?page=gf_settings&subview=gravityforms_geolocation' );
		$pricing_url  = GFGEOAC_SITE_URL . '/pricing/';
		$docs_url     = GFGEOAC_SITE_URL . '/docs/';
		$demo_url     = 'https://demo.gravitygeolocation.com/';
		$product_name = __( 'Address Autocomplete', 'wpgeo-address-autocomplete-for-gravity-forms' );
		$package_name = GFGEOAC_PACKAGE_LABEL;
		$features     = Features::get_features();
		$overview_features = Features::order_for_overview( $features );
		$included     = array_filter(
			$overview_features,
			static function ( $feature ) {
				return $feature['included'];
			}
		);
		$available    = array_filter(
			$overview_features,
			static function ( $feature ) {
				return ! $feature['included'];
			}
		);
		$packages     = Features::get_packages();

		$hero_title     = __( 'Less typing. Better addresses.', 'wpgeo-address-autocomplete-for-gravity-forms' );
		$help_step      = __( 'Add a custom Address field or enable autocomplete on a native Address field.', 'wpgeo-address-autocomplete-for-gravity-forms' );
		$feature_groups = [
			'included'  => $included,
			'available' => $available,
		];

		require __DIR__ . '/views/dashboard.php';
	}

	/**
	 * Return bundled product descriptions without remote content or installers.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] Public product names, descriptions and website URLs.
	 */
	public static function get_products() {
		return [
			[
				'name'        => 'Gravity Geolocation',
				'url'         => 'https://gravitygeolocation.com/',
				'description' => __( 'Maps, geocoding, dynamic fields, directions, nearby locations and more for Gravity Forms.', 'wpgeo-address-autocomplete-for-gravity-forms' ),
			],
			[
				'name'        => 'Gravity Search',
				'url'         => 'https://gravitygeolocation.com/solutions/gravity-search/',
				'description' => __( 'Turn Gravity Forms entries into searchable directories, listings and customer portals—with field filters, proximity search, maps and flexible result layouts.', 'wpgeo-address-autocomplete-for-gravity-forms' ),
			],
			[
				'name'        => 'GEO my WP',
				'url'         => 'https://geomywp.com/',
				'description' => __( 'Build location-based searches and directories for your WordPress site.', 'wpgeo-address-autocomplete-for-gravity-forms' ),
			],
			[
				'name'        => 'Formidable Geolocation',
				'url'         => 'https://formidablegeolocation.com/',
				'description' => __( 'Add geolocation tools and connected location fields to Formidable Forms.', 'wpgeo-address-autocomplete-for-gravity-forms' ),
			],
			[
				'name'        => 'Geolocation for Ninja Forms',
				'url'         => 'https://ninjageolocation.com/',
				'description' => __( 'Bring address, map and location workflows to Ninja Forms.', 'wpgeo-address-autocomplete-for-gravity-forms' ),
			],
			[
				'name'        => 'WPForms Geolocation',
				'url'         => 'https://wpgeoforms.com/',
				'description' => __( 'Add address autocomplete, maps, coordinates and directions to WPForms.', 'wpgeo-address-autocomplete-for-gravity-forms' ),
			],
			[
				'name'        => 'WP Job Manager Geolocation',
				'url'         => 'https://jobmanagergeolocation.com/',
				'description' => __( 'Add proximity searches and interactive maps to your WP Job Manager job board.', 'wpgeo-address-autocomplete-for-gravity-forms' ),
			],
		];
	}
}
