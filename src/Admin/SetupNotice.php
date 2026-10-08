<?php
/**
 * One-time setup guidance with site-wide, persistent dismissal.
 *
 * @package GravityGeolocationAutocomplete\Admin
 * @since 1.0.0
 */

namespace GravityGeolocationAutocomplete\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Guide administrators without activation redirects or remote requests.
 *
 * @since 1.0.0
 */
final class SetupNotice {

	/**
	 * Site-local state, separate from the host's shared plugin preferences.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private const OPTION = 'gfgeoac_setup_notice';

	/**
	 * Nonce-protected dismissal action.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private const DISMISS_ACTION = 'gfgeoac_dismiss_setup_notice';

	/**
	 * Record first activation without resetting dismissed or completed setup.
	 *
	 * Network activation does not initialize individual sites' preferences.
	 *
	 * @since 1.0.0
	 * @param bool $network_wide Whether the plugin is activated for a network.
	 * @return void
	 */
	public static function activate( $network_wide = false ) {
		if ( $network_wide || defined( 'GFGEO_VERSION' ) ) {
			return;
		}
		add_option( self::OPTION, 'pending', '', false );
	}

	/**
	 * Register guidance and its authenticated dismissal handler.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function register() {
		add_action( 'admin_notices', [ self::class, 'render' ] );
		add_action( 'admin_post_' . self::DISMISS_ACTION, [ self::class, 'dismiss' ] );
	}

	/**
	 * Show setup guidance across site admin screens until dismissed or configured.
	 *
	 * Missing hosts and inactive free distributions have their own notices.
	 * Existing installations are not enrolled merely by updating plugin files.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function render() {
		if ( ! is_admin() || is_network_admin() || ! current_user_can( 'manage_options' ) || defined( 'GFGEO_VERSION' ) ) {
			return;
		}
		if ( ! ( class_exists( '\\GFForms' ) && version_compare( \GFForms::$version, GFGEOAC_MIN_HOST_VERSION, '>=' ) ) || 'pending' !== get_option( self::OPTION ) ) {
			return;
		}
		if ( self::has_api_key() ) {
			update_option( self::OPTION, 'configured', false );
			return;
		}
		?>
		<div class="notice notice-info gfgeoac-setup-notice">
			<p>
				<strong><?php
					echo esc_html(
						sprintf(
							/* translators: %s: Plugin name. */
							__( '%s is ready to configure.', 'advanced-address-autocomplete-for-gravity-forms' ),
							GFGEOAC_PLUGIN_NAME
						)
					);
				?></strong><br>
				<?php esc_html_e( 'Add your Google API key, then enable autocomplete in your form\'s field options.', 'advanced-address-autocomplete-for-gravity-forms' ); ?>
			</p>
			<div style="margin-bottom: 12px;">
				<a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=gf_settings&subview=gravityforms_geolocation' ) ); ?>"><?php esc_html_e( 'Set up autocomplete', 'advanced-address-autocomplete-for-gravity-forms' ); ?></a>
				<a class="button button-secondary" href="<?php echo esc_url( Dashboard::get_url() ); ?>"><?php esc_html_e( 'View plugin overview', 'advanced-address-autocomplete-for-gravity-forms' ); ?></a>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display: inline-block; margin-left: 12px;">
					<input type="hidden" name="action" value="<?php echo esc_attr( self::DISMISS_ACTION ); ?>">
					<?php wp_nonce_field( self::DISMISS_ACTION ); ?>
					<button class="button-link" type="submit"><?php esc_html_e( 'Dismiss', 'advanced-address-autocomplete-for-gravity-forms' ); ?></button>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Detect existing configuration without reading or exporting paid settings.
	 *
	 * @since 1.0.0
	 * @return bool Whether a nonempty scalar browser key is configured.
	 */
	private static function has_api_key() {
		$key = defined( 'GFGEOAC_GOOGLE_MAPS_BROWSER_API_KEY' ) ? GFGEOAC_GOOGLE_MAPS_BROWSER_API_KEY : '';
		return is_scalar( $key ) && '' !== trim( (string) $key );
	}

	/**
	 * Permanently dismiss for this site and return to the same admin page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function dismiss() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to dismiss this notice.', 'advanced-address-autocomplete-for-gravity-forms' ), '', [ 'response' => 403 ] );
		}
		check_admin_referer( self::DISMISS_ACTION );
		update_option( self::OPTION, 'dismissed', false );
		$referer = wp_get_referer();
		$target  = is_string( $referer ) && 0 === strpos( $referer, admin_url() ) ? $referer : admin_url();
		wp_safe_redirect( $target );
		exit;
	}
}
