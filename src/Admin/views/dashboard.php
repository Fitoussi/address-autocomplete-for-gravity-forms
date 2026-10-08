<?php
/**
 * Standalone product overview. Public variables come from Admin\Dashboard::render().
 *
 * Screenshot markers reserve our own local assets; no remote embeds or tracking.
 * Availability markers describe packaged implementations, not completed setup.
 *
 * @package GravityGeolocationAutocomplete\Admin
 * @since 1.0.0
 */

use GravityGeolocationAutocomplete\Admin\Dashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div
	class="wrap gfgeo-product-dashboard<?php echo 'overview' === $section ? ' gfgeo-product-dashboard--overview' : ''; ?>">
	<header class="gfgeo-product-dashboard__header">
		<div>
			<p class="gfgeo-product-dashboard__eyebrow">
				<?php esc_html_e( 'For Gravity Forms', 'advanced-address-autocomplete-for-gravity-forms' ); ?></p>
			<h1><?php echo esc_html( $product_name ); ?></h1>
		</div>
		<div class="gfgeo-product-dashboard__actions">
			<span class="gfgeo-product-dashboard__version"><?php echo esc_html( 'v' . $version ); ?></span>
			<a class="button"
				href="<?php echo esc_url( Dashboard::get_url( 'products' ) ); ?>"><?php esc_html_e( 'Explore Plugins', 'advanced-address-autocomplete-for-gravity-forms' ); ?></a>
			<a class="button"
				href="<?php echo esc_url( $settings_url ); ?>"><?php esc_html_e( 'Open Settings', 'advanced-address-autocomplete-for-gravity-forms' ); ?></a>
			<a class="button button-primary" href="<?php echo esc_url( $demo_url ); ?>" target="_blank"
				rel="noopener noreferrer"><?php esc_html_e( 'View Live Demos', 'advanced-address-autocomplete-for-gravity-forms' ); ?></a>
		</div>
	</header>
	<hr class="wp-header-end">
	<nav class="gfgeo-product-dashboard__nav"
		aria-label="<?php esc_attr_e( 'Product overview sections', 'advanced-address-autocomplete-for-gravity-forms' ); ?>">
		<?php foreach ( $tabs as $gfgeoac_key => $gfgeoac_label ) : ?>
			<a href="<?php echo esc_url( Dashboard::get_url( $gfgeoac_key ) ); ?>"
				aria-current="<?php echo $section === $gfgeoac_key ? 'page' : 'false'; ?>"><?php echo esc_html( $gfgeoac_label ); ?></a>
		<?php endforeach; ?>
	</nav>
	<div class="gfgeo-product-dashboard__layout">
		<main class="gfgeo-product-dashboard__main">
			<?php if ( 'overview' === $section ) : ?>
				<section class="gfgeo-product-dashboard__card gfgeo-product-dashboard__hero">
					<div>
						<p class="gfgeo-product-dashboard__eyebrow">
							<?php esc_html_e( 'Your installed package', 'advanced-address-autocomplete-for-gravity-forms' ); ?></p>
						<h2><?php echo esc_html( $hero_title ); ?></h2>
						<p><?php esc_html_e( 'Modern Google Places autocomplete, included free. Collect addresses with a dedicated single-line field or native Address autofill, tailor suggestions to your audience, and require a selection when it matters.', 'advanced-address-autocomplete-for-gravity-forms' ); ?>
						</p>
						<div class="gfgeo-product-dashboard__actions">
							<a class="button button-primary"
								href="#included-features"><?php esc_html_e( 'Explore my features', 'advanced-address-autocomplete-for-gravity-forms' ); ?></a>
							<?php
							if ( $available ) :
								?>
								<a class="button"
									href="#available-features"><?php esc_html_e( 'See what else is available', 'advanced-address-autocomplete-for-gravity-forms' ); ?></a><?php endif; ?>
						</div>
						<p class="gfgeo-product-dashboard__note">
							<?php esc_html_e( 'Location tools require your own Google API configuration. Google service usage and billing are separate.', 'advanced-address-autocomplete-for-gravity-forms' ); ?>
						</p>
					</div>
					<div class="gfgeo-product-dashboard__package-summary">
						<span
							class="gfgeo-product-dashboard__badge"><?php esc_html_e( 'Installed package', 'advanced-address-autocomplete-for-gravity-forms' ); ?></span>
						<h3><?php echo esc_html( $package_name ); ?></h3>
						<p><?php esc_html_e( 'Green badges identify included tools. Follow their setup guides to put them to work.', 'advanced-address-autocomplete-for-gravity-forms' ); ?>
						</p>
						<a
							href="<?php echo esc_url( $settings_url ); ?>"><?php esc_html_e( 'Configuration & settings', 'advanced-address-autocomplete-for-gravity-forms' ); ?></a>
					</div>
				</section>
				<?php
				foreach ( $feature_groups as $gfgeoac_group => $gfgeoac_cards ) :
					if ( ! $gfgeoac_cards ) {
						continue;
					}
					?>
					<section id="<?php echo esc_attr( $gfgeoac_group . '-features' ); ?>"
						class="gfgeo-product-dashboard__feature-group"
						aria-labelledby="<?php echo esc_attr( $gfgeoac_group . '-heading' ); ?>">
						<div class="gfgeo-product-dashboard__group-heading">
							<h2 id="<?php echo esc_attr( $gfgeoac_group . '-heading' ); ?>">
								<?php echo esc_html( 'included' === $gfgeoac_group ? __( 'Included in your package', 'advanced-address-autocomplete-for-gravity-forms' ) : __( 'Take your forms further', 'advanced-address-autocomplete-for-gravity-forms' ) ); ?>
							</h2>
							<p><?php echo esc_html( 'included' === $gfgeoac_group ? __( 'Your available building blocks. Choose a tool and open its guide.', 'advanced-address-autocomplete-for-gravity-forms' ) : __( 'Other Gravity Geolocation packages add these capabilities. Explore the demos or compare packages to find the right fit.', 'advanced-address-autocomplete-for-gravity-forms' ) ); ?>
							</p>
						</div>
						<div class="gfgeo-product-dashboard__feature-grid">
							<?php foreach ( $gfgeoac_cards as $gfgeoac_item_id => $gfgeoac_feature ) : ?>
								<article
									class="gfgeo-product-dashboard__card gfgeo-product-dashboard__feature <?php echo $gfgeoac_feature['included'] ? 'is-included' : 'is-available'; ?>"
									data-feature="<?php echo esc_attr( $gfgeoac_item_id ); ?>">
									<div class="gfgeo-product-dashboard__feature-top">
										<span class="dashicons dashicons-<?php echo esc_attr( $gfgeoac_feature['icon'] ); ?>"
											aria-hidden="true"></span>
										<span
											class="gfgeo-product-dashboard__badge <?php echo $gfgeoac_feature['included'] ? '' : 'gfgeo-product-dashboard__badge--available'; ?>"><?php echo esc_html( $gfgeoac_feature['included'] ? __( 'Included in your package', 'advanced-address-autocomplete-for-gravity-forms' ) : __( 'Available in other packages', 'advanced-address-autocomplete-for-gravity-forms' ) ); ?></span>
									</div>
									<h3><?php echo esc_html( $gfgeoac_feature['title'] ); ?></h3>
									<p><?php echo esc_html( $gfgeoac_feature['description'] ); ?></p>
									<p class="gfgeo-product-dashboard__note"><?php echo esc_html( $gfgeoac_feature['details'] ); ?>
									</p>
									<?php if ( \in_array( $gfgeoac_item_id, [ 'autocomplete', 'location-map', 'dynamic-fields', 'directions', 'nearby', 'validation', 'drawing', 'entry-maps', 'search' ], true ) ) : ?>
										<figure class="gfgeo-product-dashboard__placeholder"
											data-screenshot="<?php echo esc_attr( $gfgeoac_item_id ); ?>">
											<?php // Editorial replacement target: our own local screenshot of this feature. ?>
											<figcaption>
												<?php echo esc_html( $gfgeoac_feature['title'] ); ?><small><?php esc_html_e( 'Screenshot placeholder', 'advanced-address-autocomplete-for-gravity-forms' ); ?></small>
											</figcaption>
										</figure>
									<?php endif; ?>
									<div class="gfgeo-product-dashboard__feature-footer">
										<?php if ( $gfgeoac_feature['included'] ) : ?>
											<a href="<?php echo esc_url( $docs_url . $gfgeoac_feature['doc'] ); ?>" target="_blank"
												rel="noopener noreferrer"><?php esc_html_e( 'Setup & documentation', 'advanced-address-autocomplete-for-gravity-forms' ); ?><span
													class="screen-reader-text"> —
													<?php echo esc_html( $gfgeoac_feature['title'] ); ?></span></a>
										<?php else : ?>
											<p><span class="dashicons dashicons-category" aria-hidden="true"></span><span><?php
											/* translators: %s: Comma-separated package names that include this feature. */
											echo esc_html( sprintf( __( 'Included in: %s', 'advanced-address-autocomplete-for-gravity-forms' ), implode( ', ', array_intersect_key( $packages, array_flip( $gfgeoac_feature['packages'] ) ) ) ) );
											?></span></p>
											<?php $gfgeoac_feature_demos = \GravityGeolocationAutocomplete\Admin\DashboardFeatures::get_demo_links( $gfgeoac_item_id ); ?>
											<?php if ( $gfgeoac_feature_demos ) : ?>
												<div class="gfgeo-product-dashboard__demo-links">
													<?php foreach ( $gfgeoac_feature_demos as $gfgeoac_feature_demo ) : ?>
														<a href="<?php echo esc_url( $gfgeoac_feature_demo['url'] ); ?>" target="_blank"
															rel="noopener noreferrer"><?php echo esc_html( $gfgeoac_feature_demo['label'] ); ?><span
																class="screen-reader-text"> —
																<?php echo esc_html( $gfgeoac_feature['title'] ); ?></span></a>
													<?php endforeach; ?>
												</div>
											<?php endif; ?>
										<?php endif; ?>
									</div>
								</article>
							<?php endforeach; ?>
						</div>
					</section>
				<?php endforeach; ?>
				<?php if ( $available ) : ?>
					<section class="gfgeo-product-dashboard__upgrade">
						<h2><?php esc_html_e( 'Choose the tools your next project needs', 'advanced-address-autocomplete-for-gravity-forms' ); ?>
						</h2>
						<p><?php esc_html_e( 'Location and Directions are focused alternatives. Toolkit combines the geolocation tools. All Access adds Gravity Search.', 'advanced-address-autocomplete-for-gravity-forms' ); ?>
						</p>
						<a class="button button-primary" href="<?php echo esc_url( $pricing_url ); ?>" target="_blank"
							rel="noopener noreferrer"><?php esc_html_e( 'View packages & pricing', 'advanced-address-autocomplete-for-gravity-forms' ); ?></a>
					</section>
				<?php endif; ?>
			<?php elseif ( 'comparison' === $section ) : ?>
				<section class="gfgeo-product-dashboard__card">
					<h2><?php esc_html_e( 'Find the right package for your project', 'advanced-address-autocomplete-for-gravity-forms' ); ?>
					</h2>
					<p><?php esc_html_e( 'Your installed package is highlighted. Packages are alternative builds, not add-ons to stack together; site allowances and license status are separate from the feature list.', 'advanced-address-autocomplete-for-gravity-forms' ); ?>
					</p>
					<p class="gfgeo-product-dashboard__note">
						<?php esc_html_e( 'On narrow screens, scroll the comparison horizontally to see every package.', 'advanced-address-autocomplete-for-gravity-forms' ); ?>
					</p>
					<div class="gfgeo-product-dashboard__table-wrap" tabindex="0" role="region"
						aria-label="<?php esc_attr_e( 'Feature comparison — scroll to see all columns', 'advanced-address-autocomplete-for-gravity-forms' ); ?>">
						<table>
							<caption class="screen-reader-text">
								<?php esc_html_e( 'Gravity package feature comparison', 'advanced-address-autocomplete-for-gravity-forms' ); ?>
							</caption>
							<thead>
								<tr>
									<th scope="col">
										<?php esc_html_e( 'Feature', 'advanced-address-autocomplete-for-gravity-forms' ); ?></th>
									<?php foreach ( $packages as $gfgeoac_package_id => $gfgeoac_label ) : ?>
										<th scope="col"
											class="<?php echo GFGEOAC_PACKAGE_TYPE === $gfgeoac_package_id ? 'is-current' : ''; ?>">
											<?php echo esc_html( $gfgeoac_label ); ?>
											<?php if ( GFGEOAC_PACKAGE_TYPE === $gfgeoac_package_id ) : ?>
												<small><?php esc_html_e( 'Installed', 'advanced-address-autocomplete-for-gravity-forms' ); ?></small>
											<?php endif; ?>
										</th>
									<?php endforeach; ?>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $features as $gfgeoac_feature ) : ?>
									<tr>
										<th scope="row"><?php echo esc_html( $gfgeoac_feature['title'] ); ?></th>
										<?php
										foreach ( $packages as $gfgeoac_package_id => $gfgeoac_label ) :
											// The installed column uses files; others describe standard composition.
											$gfgeoac_has_feature  = GFGEOAC_PACKAGE_TYPE === $gfgeoac_package_id ? $gfgeoac_feature['included'] : \in_array( $gfgeoac_package_id, $gfgeoac_feature['packages'], true );
											$gfgeoac_status_label = $gfgeoac_has_feature ? __( 'Included', 'advanced-address-autocomplete-for-gravity-forms' ) : __( 'Not included', 'advanced-address-autocomplete-for-gravity-forms' );
											?>
											<td
												class="<?php echo GFGEOAC_PACKAGE_TYPE === $gfgeoac_package_id ? 'is-current' : ''; ?>">
												<span
													class="<?php echo $gfgeoac_has_feature ? 'gfgeo-product-dashboard__yes' : 'gfgeo-product-dashboard__no'; ?>"
													title="<?php echo esc_attr( $gfgeoac_status_label ); ?>">
													<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
														stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
														stroke-linejoin="round" aria-hidden="true" focusable="false">
														<?php if ( $gfgeoac_has_feature ) : ?>
															<path d="M5 12l4 4L19 6" />
														<?php else : ?>
															<path d="M6 6l12 12M18 6 6 18" />
														<?php endif; ?>
													</svg>
													<span
														class="screen-reader-text"><?php echo esc_html( $gfgeoac_status_label ); ?></span>
												</span>
											</td>
										<?php endforeach; ?>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<p><?php esc_html_e( 'Google API usage and any required form-builder editions or add-ons are separate. Optional integrations may require other plugins. Feature checks show package contents, not setup or license status.', 'advanced-address-autocomplete-for-gravity-forms' ); ?>
					</p>
					<a class="button button-primary" href="<?php echo esc_url( $pricing_url ); ?>" target="_blank"
						rel="noopener noreferrer"><?php esc_html_e( 'View packages & pricing', 'advanced-address-autocomplete-for-gravity-forms' ); ?></a>
				</section>
			<?php elseif ( 'products' === $section ) : ?>
				<section class="gfgeo-product-dashboard__card">
					<h2><?php esc_html_e( 'More tools for your WordPress site', 'advanced-address-autocomplete-for-gravity-forms' ); ?>
					</h2>
					<p><?php esc_html_e( 'Explore our other plugins. These descriptions are bundled locally; nothing is downloaded or installed from this page.', 'advanced-address-autocomplete-for-gravity-forms' ); ?>
					</p>
				</section>
				<div class="gfgeo-product-dashboard__feature-grid">
					<?php foreach ( Dashboard::get_products() as $gfgeoac_product ) : ?>
						<article class="gfgeo-product-dashboard__card">
							<h2><?php echo esc_html( $gfgeoac_product['name'] ); ?></h2>
							<p><?php echo esc_html( $gfgeoac_product['description'] ); ?></p>
							<a class="button" href="<?php echo esc_url( $gfgeoac_product['url'] ); ?>" target="_blank"
								rel="noopener noreferrer"><?php esc_html_e( 'Learn more', 'advanced-address-autocomplete-for-gravity-forms' ); ?><span
									class="screen-reader-text"> — <?php echo esc_html( $gfgeoac_product['name'] ); ?></span></a>
						</article>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<section class="gfgeo-product-dashboard__card">
					<h2><?php esc_html_e( 'From installed to working', 'advanced-address-autocomplete-for-gravity-forms' ); ?></h2>
					<ol>
						<li><?php esc_html_e( 'Activate Gravity Forms and open this plugin’s native Settings screen.', 'advanced-address-autocomplete-for-gravity-forms' ); ?>
						</li>
						<li><?php esc_html_e( 'Enter your Google browser API key. Enable Maps JavaScript API and Places API (New), configure billing, and restrict the key to your website.', 'advanced-address-autocomplete-for-gravity-forms' ); ?>
						</li>
						<li><?php echo esc_html( $help_step ); ?></li>
						<li><?php esc_html_e( 'Save your form before previewing. Test the complete visitor flow.', 'advanced-address-autocomplete-for-gravity-forms' ); ?>
						</li>
					</ol>
					<a class="button button-primary"
						href="<?php echo esc_url( $settings_url ); ?>"><?php esc_html_e( 'Open Settings', 'advanced-address-autocomplete-for-gravity-forms' ); ?></a>
					<a class="button"
						href="<?php echo esc_url( $docs_url . 'google-maps-api-keys-gravity-geolocation/' ); ?>"
						target="_blank"
						rel="noopener noreferrer"><?php esc_html_e( 'Google API setup guide', 'advanced-address-autocomplete-for-gravity-forms' ); ?></a>
				</section>
				<section class="gfgeo-product-dashboard__card">
					<h2><?php esc_html_e( 'Something not working?', 'advanced-address-autocomplete-for-gravity-forms' ); ?></h2>
					<p><?php esc_html_e( 'Check that autocomplete is enabled, then verify the browser API key, required Google services, billing and key restrictions.', 'advanced-address-autocomplete-for-gravity-forms' ); ?>
					</p>
					<p><?php esc_html_e( 'Autocomplete fills an address after a visitor selects a suggestion. This free plugin does not provide current-location detection, manual geocoding or connected dynamic location fields.', 'advanced-address-autocomplete-for-gravity-forms' ); ?>
					</p>
					<p><?php esc_html_e( 'Include the plugin version, field type and steps to reproduce when asking for help. Never share API keys or other credentials in a public post.', 'advanced-address-autocomplete-for-gravity-forms' ); ?>
					</p>
					<div class="gfgeo-product-dashboard__actions">
						<a class="button" href="<?php echo esc_url( $docs_url ); ?>" target="_blank"
							rel="noopener noreferrer"><?php esc_html_e( 'Browse documentation', 'advanced-address-autocomplete-for-gravity-forms' ); ?></a>
						<a class="button" href="https://geomywp.com/support/" target="_blank"
							rel="noopener noreferrer"><?php esc_html_e( 'View support options', 'advanced-address-autocomplete-for-gravity-forms' ); ?></a>
					</div>
				</section>
			<?php endif; ?>
		</main>
	</div>
</div>
