<?php
/**
 * Standalone product overview. Public variables come from Admin\Dashboard::render().
 *
 * Screenshot markers reserve our own local assets; no remote embeds or tracking.
 * Availability markers describe packaged implementations, not completed setup.
 *
 * @package GravityGeolocation\Admin
 * @since 1.0.0
 */

use GravityGeolocation\Admin\Dashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap gfgeo-product-dashboard">
	<header class="gfgeo-product-dashboard__header">
		<div>
			<p class="gfgeo-product-dashboard__eyebrow"><?php esc_html_e( 'For Gravity Forms', 'address-autocomplete-for-gravity-forms' ); ?></p>
			<h1><?php echo esc_html( $product_name ); ?></h1>
		</div>
		<div class="gfgeo-product-dashboard__actions">
			<span class="gfgeo-product-dashboard__version"><?php echo esc_html( 'v' . $version ); ?></span>
			<a class="button" href="<?php echo esc_url( Dashboard::get_url( 'products' ) ); ?>"><?php esc_html_e( 'Explore Plugins', 'address-autocomplete-for-gravity-forms' ); ?></a>
			<a class="button" href="<?php echo esc_url( $settings_url ); ?>"><?php esc_html_e( 'Open Settings', 'address-autocomplete-for-gravity-forms' ); ?></a>
			<a class="button button-primary" href="<?php echo esc_url( $demo_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View Live Demos', 'address-autocomplete-for-gravity-forms' ); ?></a>
		</div>
	</header>
	<hr class="wp-header-end">
	<nav class="gfgeo-product-dashboard__nav" aria-label="<?php esc_attr_e( 'Product overview sections', 'address-autocomplete-for-gravity-forms' ); ?>">
		<?php foreach ( $tabs as $key => $label ) : ?>
			<a href="<?php echo esc_url( Dashboard::get_url( $key ) ); ?>" aria-current="<?php echo $section === $key ? 'page' : 'false'; ?>"><?php echo esc_html( $label ); ?></a>
		<?php endforeach; ?>
	</nav>
	<div class="gfgeo-product-dashboard__layout">
		<main class="gfgeo-product-dashboard__main">
			<?php if ( 'overview' === $section ) : ?>
				<section class="gfgeo-product-dashboard__card gfgeo-product-dashboard__hero">
					<div>
						<p class="gfgeo-product-dashboard__eyebrow"><?php esc_html_e( 'Your installed package', 'address-autocomplete-for-gravity-forms' ); ?></p>
						<h2><?php echo esc_html( $hero_title ); ?></h2>
						<p><?php esc_html_e( 'Modern Google Places autocomplete, included free. Collect addresses with a dedicated single-line field or native Address autofill, tailor suggestions to your audience, and require a selection when it matters.', 'address-autocomplete-for-gravity-forms' ); ?></p>
						<div class="gfgeo-product-dashboard__actions">
							<a class="button button-primary" href="#included-features"><?php esc_html_e( 'Explore my features', 'address-autocomplete-for-gravity-forms' ); ?></a>
							<?php
							if ( $available ) :
								?>
								<a class="button" href="#available-features"><?php esc_html_e( 'See what else is available', 'address-autocomplete-for-gravity-forms' ); ?></a><?php endif; ?>
						</div>
						<p class="gfgeo-product-dashboard__note"><?php esc_html_e( 'Location tools require your own Google API configuration. Google service usage and billing are separate.', 'address-autocomplete-for-gravity-forms' ); ?></p>
					</div>
					<div class="gfgeo-product-dashboard__package-summary">
						<span class="gfgeo-product-dashboard__badge"><?php esc_html_e( 'Installed package', 'address-autocomplete-for-gravity-forms' ); ?></span>
						<h3><?php echo esc_html( $package_name ); ?></h3>
						<p><?php esc_html_e( 'Green badges identify included tools. Follow their setup guides to put them to work.', 'address-autocomplete-for-gravity-forms' ); ?></p>
						<a href="<?php echo esc_url( $settings_url ); ?>"><?php esc_html_e( 'Configuration & settings', 'address-autocomplete-for-gravity-forms' ); ?></a>
					</div>
				</section>
				<?php
				foreach ( $feature_groups as $group => $cards ) :
					if ( ! $cards ) {
						continue;
					}
					?>
					<section id="<?php echo esc_attr( $group . '-features' ); ?>" class="gfgeo-product-dashboard__feature-group" aria-labelledby="<?php echo esc_attr( $group . '-heading' ); ?>">
						<div class="gfgeo-product-dashboard__group-heading">
							<h2 id="<?php echo esc_attr( $group . '-heading' ); ?>"><?php echo esc_html( 'included' === $group ? __( 'Included in your package', 'address-autocomplete-for-gravity-forms' ) : __( 'Take your forms further', 'address-autocomplete-for-gravity-forms' ) ); ?></h2>
							<p><?php echo esc_html( 'included' === $group ? __( 'Your available building blocks. Choose a tool and open its guide.', 'address-autocomplete-for-gravity-forms' ) : __( 'Other Gravity packages add these capabilities. Explore the demos or compare packages to find the right fit.', 'address-autocomplete-for-gravity-forms' ) ); ?></p>
						</div>
						<div class="gfgeo-product-dashboard__feature-grid">
							<?php foreach ( $cards as $item_id => $feature ) : ?>
								<article class="gfgeo-product-dashboard__card gfgeo-product-dashboard__feature <?php echo $feature['included'] ? 'is-included' : 'is-available'; ?>" data-feature="<?php echo esc_attr( $item_id ); ?>">
									<div class="gfgeo-product-dashboard__feature-top">
										<span class="dashicons dashicons-<?php echo esc_attr( $feature['icon'] ); ?>" aria-hidden="true"></span>
										<span class="gfgeo-product-dashboard__badge <?php echo $feature['included'] ? '' : 'gfgeo-product-dashboard__badge--available'; ?>"><?php echo esc_html( $feature['included'] ? __( 'Included in your package', 'address-autocomplete-for-gravity-forms' ) : __( 'Available in other packages', 'address-autocomplete-for-gravity-forms' ) ); ?></span>
									</div>
									<h3><?php echo esc_html( $feature['title'] ); ?></h3>
									<p><?php echo esc_html( $feature['description'] ); ?></p>
									<p class="gfgeo-product-dashboard__note"><?php echo esc_html( $feature['details'] ); ?></p>
									<?php if ( \in_array( $item_id, [ 'autocomplete', 'location-map', 'dynamic-fields', 'directions', 'nearby', 'validation', 'drawing', 'entry-maps', 'search' ], true ) ) : ?>
										<figure class="gfgeo-product-dashboard__placeholder" data-screenshot="<?php echo esc_attr( $item_id ); ?>">
											<?php // Editorial replacement target: our own local screenshot of this feature. ?>
											<figcaption><?php echo esc_html( $feature['title'] ); ?><small><?php esc_html_e( 'Screenshot placeholder', 'address-autocomplete-for-gravity-forms' ); ?></small></figcaption>
										</figure>
									<?php endif; ?>
									<div class="gfgeo-product-dashboard__feature-footer">
										<?php if ( $feature['included'] ) : ?>
											<a href="<?php echo esc_url( $docs_url . $feature['doc'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Setup & documentation', 'address-autocomplete-for-gravity-forms' ); ?><span class="screen-reader-text"> — <?php echo esc_html( $feature['title'] ); ?></span></a>
										<?php else : ?>
											<p><?php echo esc_html( implode( ', ', array_intersect_key( $packages, array_flip( $feature['packages'] ) ) ) ); ?></p>
											<a href="<?php echo esc_url( $demo_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View demos', 'address-autocomplete-for-gravity-forms' ); ?><span class="screen-reader-text"> — <?php echo esc_html( $feature['title'] ); ?></span></a>
										<?php endif; ?>
									</div>
								</article>
							<?php endforeach; ?>
						</div>
					</section>
				<?php endforeach; ?>
				<?php if ( $available ) : ?>
					<section class="gfgeo-product-dashboard__upgrade">
						<h2><?php esc_html_e( 'Choose the tools your next project needs', 'address-autocomplete-for-gravity-forms' ); ?></h2>
						<p><?php esc_html_e( 'Location and Directions are focused alternatives. Toolkit combines the geolocation tools. All Access adds Gravity Search.', 'address-autocomplete-for-gravity-forms' ); ?></p>
						<a class="button button-primary" href="<?php echo esc_url( $pricing_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View packages & pricing', 'address-autocomplete-for-gravity-forms' ); ?></a>
					</section>
				<?php endif; ?>
			<?php elseif ( 'comparison' === $section ) : ?>
				<section class="gfgeo-product-dashboard__card">
					<h2><?php esc_html_e( 'Find the right package for your project', 'address-autocomplete-for-gravity-forms' ); ?></h2>
					<p><?php esc_html_e( 'Your installed package is highlighted. Packages are alternative builds, not add-ons to stack together; site allowances and license status are separate from the feature list.', 'address-autocomplete-for-gravity-forms' ); ?></p>
					<p class="gfgeo-product-dashboard__note"><?php esc_html_e( 'On narrow screens, scroll the comparison horizontally to see every package.', 'address-autocomplete-for-gravity-forms' ); ?></p>
					<div class="gfgeo-product-dashboard__table-wrap" tabindex="0" role="region" aria-label="<?php esc_attr_e( 'Feature comparison — scroll to see all columns', 'address-autocomplete-for-gravity-forms' ); ?>">
						<table>
							<caption class="screen-reader-text"><?php esc_html_e( 'Gravity package feature comparison', 'address-autocomplete-for-gravity-forms' ); ?></caption>
							<thead>
								<tr>
									<th scope="col"><?php esc_html_e( 'Feature', 'address-autocomplete-for-gravity-forms' ); ?></th>
									<?php foreach ( $packages as $package_id => $label ) : ?>
										<th scope="col" class="<?php echo GFGEO_PACKAGE_TYPE === $package_id ? 'is-current' : ''; ?>">
											<?php echo esc_html( $label ); ?>
											<?php if ( GFGEO_PACKAGE_TYPE === $package_id ) : ?>
												<small><?php esc_html_e( 'Installed', 'address-autocomplete-for-gravity-forms' ); ?></small>
											<?php endif; ?>
										</th>
									<?php endforeach; ?>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $features as $feature ) : ?>
									<tr>
										<th scope="row"><?php echo esc_html( $feature['title'] ); ?></th>
										<?php
										foreach ( $packages as $package_id => $label ) :
											// The installed column uses files; others describe standard composition.
											$has_feature  = GFGEO_PACKAGE_TYPE === $package_id ? $feature['included'] : \in_array( $package_id, $feature['packages'], true );
											$status_label = $has_feature ? __( 'Included', 'address-autocomplete-for-gravity-forms' ) : __( 'Not included', 'address-autocomplete-for-gravity-forms' );
											?>
											<td class="<?php echo GFGEO_PACKAGE_TYPE === $package_id ? 'is-current' : ''; ?>">
												<span class="<?php echo $has_feature ? 'gfgeo-product-dashboard__yes' : 'gfgeo-product-dashboard__no'; ?>" title="<?php echo esc_attr( $status_label ); ?>">
													<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
														<?php if ( $has_feature ) : ?>
															<path d="M5 12l4 4L19 6" />
														<?php else : ?>
															<path d="M6 6l12 12M18 6 6 18" />
														<?php endif; ?>
													</svg>
													<span class="screen-reader-text"><?php echo esc_html( $status_label ); ?></span>
												</span>
											</td>
										<?php endforeach; ?>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<p><?php esc_html_e( 'Google API usage and any required Gravity Forms or GEO my WP add-ons are separate. Included tools still need configuration; green badges are not a license-status check.', 'address-autocomplete-for-gravity-forms' ); ?></p>
					<a class="button button-primary" href="<?php echo esc_url( $pricing_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View packages & pricing', 'address-autocomplete-for-gravity-forms' ); ?></a>
				</section>
			<?php elseif ( 'products' === $section ) : ?>
				<section class="gfgeo-product-dashboard__card">
					<h2><?php esc_html_e( 'More tools for your WordPress site', 'address-autocomplete-for-gravity-forms' ); ?></h2>
					<p><?php esc_html_e( 'Explore our other plugins. These descriptions are bundled locally; nothing is downloaded or installed from this page.', 'address-autocomplete-for-gravity-forms' ); ?></p>
				</section>
				<div class="gfgeo-product-dashboard__feature-grid">
					<?php foreach ( Dashboard::get_products() as $product ) : ?>
						<article class="gfgeo-product-dashboard__card">
							<h2><?php echo esc_html( $product['name'] ); ?></h2>
							<p><?php echo esc_html( $product['description'] ); ?></p>
							<a class="button" href="<?php echo esc_url( $product['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Learn more', 'address-autocomplete-for-gravity-forms' ); ?><span class="screen-reader-text"> — <?php echo esc_html( $product['name'] ); ?></span></a>
						</article>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<section class="gfgeo-product-dashboard__card">
					<h2><?php esc_html_e( 'From installed to working', 'address-autocomplete-for-gravity-forms' ); ?></h2>
					<ol>
						<li><?php esc_html_e( 'Activate Gravity Forms and open this plugin’s native Settings screen.', 'address-autocomplete-for-gravity-forms' ); ?></li>
						<li><?php esc_html_e( 'Configure the Google services and API keys your location features require; enable billing and appropriate key restrictions.', 'address-autocomplete-for-gravity-forms' ); ?></li>
						<li><?php echo esc_html( $help_step ); ?></li>
						<li><?php esc_html_e( 'Save your form before previewing. Test the complete visitor flow.', 'address-autocomplete-for-gravity-forms' ); ?></li>
					</ol>
					<a class="button button-primary" href="<?php echo esc_url( $settings_url ); ?>"><?php esc_html_e( 'Open Settings', 'address-autocomplete-for-gravity-forms' ); ?></a>
					<a class="button" href="<?php echo esc_url( $docs_url . 'google-maps-api-keys-gravity-geolocation/' ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Google API setup guide', 'address-autocomplete-for-gravity-forms' ); ?></a>
				</section>
				<section class="gfgeo-product-dashboard__card">
					<h2><?php esc_html_e( 'Something not working?', 'address-autocomplete-for-gravity-forms' ); ?></h2>
					<p><?php esc_html_e( 'Check that autocomplete is enabled, then verify the browser API key, required Google services, billing and key restrictions.', 'address-autocomplete-for-gravity-forms' ); ?></p>
					<p><?php esc_html_e( 'Autocomplete fills an address after a visitor selects a suggestion. This free plugin does not provide current-location detection, manual geocoding or connected Dynamic fields.', 'address-autocomplete-for-gravity-forms' ); ?></p>
					<p><?php esc_html_e( 'Include the package name, field type and steps to reproduce when asking for help. Never share API keys or license keys in a public post.', 'address-autocomplete-for-gravity-forms' ); ?></p>
					<div class="gfgeo-product-dashboard__actions">
						<a class="button" href="<?php echo esc_url( $docs_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Browse documentation', 'address-autocomplete-for-gravity-forms' ); ?></a>
						<a class="button" href="https://geomywp.com/support/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View support options', 'address-autocomplete-for-gravity-forms' ); ?></a>
					</div>
				</section>
			<?php endif; ?>
		</main>
	</div>
</div>
