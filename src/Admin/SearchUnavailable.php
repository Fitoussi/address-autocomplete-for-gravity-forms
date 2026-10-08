<?php
/**
 * Non-functional Search discovery for the standalone autocomplete plugin.
 *
 * Provides Search entry-point promotions when the Search feature is not
 * available in the current package.
 *
 * @package GravityGeolocationAutocomplete\Admin
 * @since 1.0.0
 */

namespace GravityGeolocationAutocomplete\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Promote Search through native admin entry points without loading Search.
 *
 * This standalone plugin never includes a Search implementation. This class:
 * - adds disabled Search entry points in Gravity Forms admin UI
 * - loads modal UI assets on Gravity Forms admin pages
 * - localizes dynamic copy/URLs consumed by the admin script
 *
 * @since 1.0.0
 */
final class SearchUnavailable {

	/**
	 * Register unavailable Search entry points and promotional UI hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register() {
		add_filter( 'gform_toolbar_menu', [ $this, 'add_search_toolbar_action' ], 20, 2 );
		add_action( 'admin_init', [ $this, 'maybe_intercept_search_actions' ], 20 );
		add_action( 'admin_notices', [ $this, 'render_add_new_form_notice' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_modal_assets' ] );
	}

	/**
	 * Add Search unavailable action in GF toolbar.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string,mixed> $items   Toolbar items.
	 * @param int|string          $form_id Current form ID (unused, required by GF filter signature).
	 * @return array<string,mixed> Toolbar items including the discovery action.
	 */
	public function add_search_toolbar_action( $items, $form_id ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter -- Required Gravity Forms callback signature.
		$items = \is_array( $items ) ? $items : [];

		if ( ! class_exists( '\GFCommon' ) || ! \GFCommon::current_user_can_any( 'gravityforms_create_form' ) ) {
			return $items;
		}

		$items['gfgeo_create_search_form'] = [
			'label'      => esc_html__( 'Create Search', 'advanced-address-autocomplete-for-gravity-forms' ),
			'url'        => '#',
			'menu_class' => 'gfgeo-create-search-form',
		];

		return $items;
	}

	/**
	 * Intercept legacy Search creation actions and route to unavailable page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function maybe_intercept_search_actions() {
		if ( ! is_admin() ) {
			return;
		}

		$page = isset( $_GET['page'] ) ? sanitize_key( (string) wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( 'gf_edit_forms' !== $page ) {
			return;
		}

		$action = isset( $_GET['gfgeo_action'] ) ? sanitize_key( (string) wp_unslash( $_GET['gfgeo_action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( 'create_search_form' !== $action ) {
			return;
		}

		wp_safe_redirect( $this->get_unavailable_url( 'legacy_action' ) );
		exit;
	}

	/**
	 * Show contextual notice on Gravity Forms "Add New Form" screen.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_add_new_form_notice() {
		$page = isset( $_GET['page'] ) ? sanitize_key( (string) wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( 'gf_new_form' !== $page ) {
			return;
		}

		if ( ! \GFCommon::current_user_can_any( 'gravityforms_create_form' ) ) {
			return;
		}

		$link = $this->get_pricing_url();
		?>
		<div class="notice notice-info">
			<p>
				<strong><?php esc_html_e( 'Gravity Search is not available.', 'advanced-address-autocomplete-for-gravity-forms' ); ?></strong>
				<a href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'View upgrade options', 'advanced-address-autocomplete-for-gravity-forms' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 * Get pricing URL.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	private function get_pricing_url() {
		return defined( 'GFGEOAC_SITE_URL' ) ? trailingslashit( GFGEOAC_SITE_URL ) . 'pricing/' : 'https://gravitygeolocation.com/pricing/';
	}

	/**
	 * Build contextual unavailable URL marker used by toolbar/navigation links.
	 *
	 * @since 1.0.0
	 *
	 * @param string $source Source context.
	 * @return string
	 */
	private function get_unavailable_url( $source ) {
		$source = sanitize_key( (string) $source );
		return add_query_arg(
			[
				'gfgeo_search_unavailable'        => 'search',
				'gfgeo_search_unavailable_source' => $source,
			],
			$this->get_pricing_url()
		);
	}

	/**
	 * Enqueue Search unavailable UI assets and localized runtime config.
	 *
	 * Runs only on Gravity Forms admin screens (`page=gf_*`).
	 *
	 * Localized config object shape:
	 * - `gfgeoSearchUnavailable.modal`:
	 *   - `selectors`, `title`, `message`, `pricingUrl`
	 *   - `cancelLabel`, `viewPricingLabel`, `confirmText`
	 * - `gfgeoSearchUnavailable.formsListButton`:
	 *   - `enabled`, `url`, `label`
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_modal_assets() {
		$page = isset( $_GET['page'] ) ? sanitize_key( (string) wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( '' === $page || strpos( $page, 'gf_' ) !== 0 ) {
			return;
		}

		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );

		wp_enqueue_style(
			'gfgeo-search-unavailable',
			GFGEOAC_URL . 'build/css/search-unavailable.min.css',
			[ 'wp-jquery-ui-dialog' ],
			GFGEOAC_VERSION
		);

		wp_enqueue_script(
			'gfgeo-search-unavailable',
			GFGEOAC_URL . 'build/js/admin/gfgeo-search-unavailable.min.js',
			[ 'jquery', 'jquery-ui-dialog' ],
			GFGEOAC_VERSION,
			true
		);

		wp_localize_script(
			'gfgeo-search-unavailable',
			'gfgeoSearchUnavailable',
			[
				'modal'           => [
					'selectors'        => '.gfgeo-create-search-form a, a.gfgeo-create-search-form',
					'title'            => esc_html__( 'Gravity Search is not available.', 'advanced-address-autocomplete-for-gravity-forms' ),
					'message'          => \sprintf(
						wp_kses(
							/* translators: %s: Pricing page URL. */
							__( 'Gravity Search is not available with your current Gravity Geolocation plan. <a href="%s" target="_blank" rel="noopener noreferrer">Upgrade now</a> to unlock advanced mapping features.', 'advanced-address-autocomplete-for-gravity-forms' ),
							[
								'a' => [
									'href'   => [],
									'target' => [],
									'rel'    => [],
								],
							]
						),
						esc_url( $this->get_pricing_url() )
					),
					'pricingUrl'       => esc_url( $this->get_pricing_url() ),
					'cancelLabel'      => esc_html__( 'Cancel', 'advanced-address-autocomplete-for-gravity-forms' ),
					'viewPricingLabel' => esc_html__( 'View pricing', 'advanced-address-autocomplete-for-gravity-forms' ),
					'confirmText'      => esc_html__( 'Gravity Search is not available with your current Gravity Geolocation plan. Open pricing page now?', 'advanced-address-autocomplete-for-gravity-forms' ),
				],
				'formsListButton' => [
					'enabled' => 'gf_edit_forms' === $page && \GFCommon::current_user_can_any( 'gravityforms_create_form' ),
					'url'     => esc_url( $this->get_unavailable_url( 'forms_list_button' ) ),
					'label'   => esc_html__( 'Add New Search', 'advanced-address-autocomplete-for-gravity-forms' ),
				],
			]
		);
	}
}
