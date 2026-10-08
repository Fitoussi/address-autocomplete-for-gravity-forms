<?php
/**
 * Public feature catalog for the Gravity product overview.
 *
 * This describes packaged capabilities, not license validity or frontend gates.
 * Every availability probe names concrete source files, never upsell placeholders.
 *
 * @package GravityGeolocationAutocomplete\Admin
 * @since 1.0.0
 */

namespace GravityGeolocationAutocomplete\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Keep public feature cards, comparison rows and packaged-file evidence together.
 *
 * @since 1.0.0
 */
final class DashboardFeatures {

	/**
	 * Prioritize major capabilities without changing comparison-table order.
	 *
	 * @since 1.0.0
	 * @param array<string,array> $features Existing feature definitions.
	 * @return array<string,array> Overview cards in feature-first order.
	 */
	public static function order_for_overview( $features ) {
		$order = [
			'autocomplete',
			'location-map',
			'geocoder',
			'dynamic-fields',
			'distance',
			'pricing',
			'directions',
			'drawing',
			'nearby',
			'validation',
			'entry-maps',
			'search',
			'entry-details',
			'current-location',
			'coordinates',
			'location-controls',
			'quick-setups',
			'templates',
			'gmw-integration',
		];
		$ordered = [];
		foreach ( $order as $id ) {
			if ( isset( $features[ $id ] ) ) {
				$ordered[ $id ] = $features[ $id ];
			}
		}
		return $ordered + $features;
	}

	/**
	 * Return locally curated demos that actually illustrate the feature.
	 *
	 * Empty results deliberately omit links for setup helpers or features without
	 * a relevant demo. These are ordinary outbound links, never remote embeds.
	 *
	 * @since 1.0.0
	 * @param string $feature_id Public feature identifier.
	 * @return array<array{label:string,url:string}> Feature-specific demo links.
	 */
	public static function get_demo_links( $feature_id ) {
		$demos = [
			'location-map' => [
				[ 'label' => __( 'View demo', 'advanced-address-autocomplete-for-gravity-forms' ), 'url' => 'https://demo.gravitygeolocation.com/single-address-field-form/' ],
			],
			'geocoder' => [
				[ 'label' => __( 'View demo', 'advanced-address-autocomplete-for-gravity-forms' ), 'url' => 'https://demo.gravitygeolocation.com/single-address-field-form/' ],
			],
			'dynamic-fields' => [
				[ 'label' => __( 'View demo', 'advanced-address-autocomplete-for-gravity-forms' ), 'url' => 'https://demo.gravitygeolocation.com/single-address-field-form/' ],
			],
			'distance' => [
				[ 'label' => __( 'View demo', 'advanced-address-autocomplete-for-gravity-forms' ), 'url' => 'https://demo.gravitygeolocation.com/distance-duration-price-calculations/' ],
			],
			'pricing' => [
				[ 'label' => __( 'View demo', 'advanced-address-autocomplete-for-gravity-forms' ), 'url' => 'https://demo.gravitygeolocation.com/distance-duration-price-calculations/' ],
			],
			'directions' => [
				[ 'label' => __( 'View demo', 'advanced-address-autocomplete-for-gravity-forms' ), 'url' => 'https://demo.gravitygeolocation.com/routes-directions/' ],
			],
			'drawing' => [
				[ 'label' => __( 'View demo', 'advanced-address-autocomplete-for-gravity-forms' ), 'url' => 'https://demo.gravitygeolocation.com/drawing-tools/' ],
			],
			'nearby' => [
				[ 'label' => __( 'View demo', 'advanced-address-autocomplete-for-gravity-forms' ), 'url' => 'https://demo.gravitygeolocation.com/nearest-locations/' ],
			],
			'entry-maps' => [
				[ 'label' => __( 'View single-entry demo', 'advanced-address-autocomplete-for-gravity-forms' ), 'url' => 'https://demo.gravitygeolocation.com/single-entry-map/' ],
				[ 'label' => __( 'View mashup demo', 'advanced-address-autocomplete-for-gravity-forms' ), 'url' => 'https://demo.gravitygeolocation.com/mashup-map/' ],
			],
			'current-location' => [
				[ 'label' => __( 'View demo', 'advanced-address-autocomplete-for-gravity-forms' ), 'url' => 'https://demo.gravitygeolocation.com/single-address-field-form/' ],
			],
			'coordinates' => [
				[ 'label' => __( 'View demo', 'advanced-address-autocomplete-for-gravity-forms' ), 'url' => 'https://demo.gravitygeolocation.com/single-address-field-form/' ],
			],
			'validation' => [
				[ 'label' => __( 'View demo', 'advanced-address-autocomplete-for-gravity-forms' ), 'url' => 'https://demo.gravitygeolocation.com/address-validation/' ],
			],
			'search' => [
				[ 'label' => __( 'View demo', 'advanced-address-autocomplete-for-gravity-forms' ), 'url' => 'https://demo.gravitygeolocation.com/full-search/' ],
			],
		];
		return $demos[ $feature_id ] ?? [];
	}

	/**
	 * Return the public comparison headings in the normal package order.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string,string>
	 */
	public static function get_packages() {
		return [
			'free'       => __( 'Free', 'advanced-address-autocomplete-for-gravity-forms' ),
			'location'   => __( 'Location', 'advanced-address-autocomplete-for-gravity-forms' ),
			'directions' => __( 'Directions', 'advanced-address-autocomplete-for-gravity-forms' ),
			'toolkit'    => __( 'Toolkit', 'advanced-address-autocomplete-for-gravity-forms' ),
			'search'     => __( 'Search', 'advanced-address-autocomplete-for-gravity-forms' ),
			'allaccess'  => __( 'All Access', 'advanced-address-autocomplete-for-gravity-forms' ),
		];
	}

	/**
	 * Resolve included cards from this installation's actual implementation files.
	 *
	 * The optional root supports tests against real staged package inventories.
	 * Production always reads its own src directory. No field is autoloaded and no
	 * license, saved option or runtime behavior is changed by this presentation.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $source_root Source directory; null uses this installation.
	 * @return array<string,array>
	 */
	public static function get_features( $source_root = null ) {
		$source_root = null === $source_root ? dirname( __DIR__ ) : $source_root;
		$features    = self::get_definitions();

		foreach ( $features as &$feature ) {
			$feature['included'] = true;
			foreach ( $feature['files'] as $file ) {
				if ( ! is_file( $source_root . '/' . $file ) ) {
					$feature['included'] = false;
					break;
				}
			}
		}
		unset( $feature );
		return $features;
	}

	/**
	 * Define the complete supported feature set in task-oriented reading order.
	 *
	 * Related fields share a card only when their package coverage is identical.
	 * Inline location detection is deliberately separate from standalone Locator /
	 * Reset fields; Directions includes the former but excludes the latter.
	 * Dormant Directory Search experiments are not advertised as shipped features.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string,array>
	 */
	private static function get_definitions() {
		$location = [ 'location', 'directions', 'toolkit', 'allaccess' ];
		$travel   = [ 'directions', 'toolkit', 'allaccess' ];
		$advanced = [ 'toolkit', 'allaccess' ];
		$fields   = 'Features/Form/Fields/';
		$base     = [ $fields . 'Address/Field.php', $fields . 'Geocoder/Field.php', $fields . 'Map/Field.php' ];

		return [
			'autocomplete'      => [
				'title'       => __( 'Address autocomplete', 'advanced-address-autocomplete-for-gravity-forms' ),
				'description' => __( 'Google Places API (New), included free. Choose a dedicated single-line Address field with an entry map link, or autofill native Gravity Forms Address fields.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'details'     => __( 'Per-field countries, place types, language and location bias; global language and region defaults; required suggestion selection with your own alert.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'icon'        => 'location-alt',
				'doc'         => 'gravity-forms-address-field/',
				'packages'    => array_merge( [ 'free' ], $location ),
				'files'       => [ $fields . 'Address/Field.php' ],
			],
			'location-map'      => [
				'title'       => __( 'Interactive form maps', 'advanced-address-autocomplete-for-gravity-forms' ),
				'description' => __( 'Show connected locations on a map and let visitors refine a location with a draggable marker.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'details'     => __( 'Configure map appearance, zoom, default location and marker behavior.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'icon'        => 'location',
				'doc'         => 'gravity-forms-google-map-field/',
				'packages'    => $location,
				'files'       => [ $fields . 'Map/Field.php', $fields . 'Geocoder/Field.php' ],
			],
			'geocoder'          => [
				'title'       => __( 'Geocoder & complete location data', 'advanced-address-autocomplete-for-gravity-forms' ),
				'description' => __( 'Store shared geocoding results behind the form: coordinates, formatted address and individual location components.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'details'     => __( 'Connect address and map inputs; use stored data in entries, merge tags and exports.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'icon'        => 'database',
				'doc'         => 'gravity-forms-geocoder-field/',
				'packages'    => $location,
				'files'       => [ $fields . 'Geocoder/Field.php' ],
			],
			'dynamic-fields'    => [
				'title'       => __( 'Dynamic location fields', 'advanced-address-autocomplete-for-gravity-forms' ),
				'description' => __( 'Populate connected form fields with city, region, postal code, coordinates and other geocoded values.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'details'     => __( 'Use ordinary form fields for location data, conditional logic and your own workflows.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'icon'        => 'randomize',
				'doc'         => 'dynamic-geolocation-field/',
				'packages'    => $location,
				'files'       => [ $fields . 'Geocoder/Field.php', $fields . 'Dynamic/Settings.php' ],
			],
			'quick-setups'      => [
				'title'       => __( 'Quick Setups & connection helpers', 'advanced-address-autocomplete-for-gravity-forms' ),
				'description' => __( 'Start with connected field combinations, or create and connect supporting fields from a field’s options.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'details'     => __( 'Recipes depend on your included fields. Review, add and save ordinary editable Gravity Forms fields.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'icon'        => 'admin-tools',
				'doc'         => '',
				'packages'    => $location,
				'files'       => array_merge( $base, [ 'Features/Form/Admin/FormEditor/QuickSetupsPanel.php' ] ),
			],
			'templates'         => [
				'title'       => __( 'Ready-to-use form templates', 'advanced-address-autocomplete-for-gravity-forms' ),
				'description' => __( 'Create a Location & Map starter form from the native template library, then customize it for your project.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'details'     => __( 'Distance & Travel Time and Directions & Route templates appear when their fields are included.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'icon'        => 'layout',
				'doc'         => '',
				'packages'    => $location,
				'files'       => array_merge( $base, [ 'Features/Form/Admin/FormTemplates.php' ] ),
			],
			'directions'        => [
				'title'       => __( 'Directions & route displays', 'advanced-address-autocomplete-for-gravity-forms' ),
				'description' => __( 'Connect an origin, destination and waypoints. Display a route map, summary and turn-by-turn directions.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'details'     => __( 'Choose inline details or a separate Directions Panel; use automatic or manual calculation.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'icon'        => 'leftright',
				'doc'         => 'gravity-forms-directions-field/',
				'packages'    => $travel,
				'files'       => [ $fields . 'Directions/Field.php', $fields . 'DirectionsPanel/Field.php' ],
			],
			'distance'          => [
				'title'       => __( 'Distance & travel time', 'advanced-address-autocomplete-for-gravity-forms' ),
				'description' => __( 'Calculate travel distance and duration between connected locations, or measure straight-line distance.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'details'     => __( 'Use readable results or raw numeric distance and duration values in your form.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'icon'        => 'performance',
				'doc'         => 'gravity-forms-distance-duration-field/',
				'packages'    => $travel,
				'files'       => [ $fields . 'Distance/Field.php' ],
			],
			'pricing'           => [
				'title'       => __( 'Distance-based pricing & calculations', 'advanced-address-autocomplete-for-gravity-forms' ),
				'description' => __( 'Build delivery fees, mileage quotes and travel-time estimates using numeric location outputs.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'details'     => __( 'Quick Setups can add ordinary calculated fields; your formula controls the estimate.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'icon'        => 'money-alt',
				'doc'         => 'gravity-forms-distance-duration-field/',
				'packages'    => $travel,
				'files'       => [ $fields . 'Distance/Field.php', $fields . 'Dynamic/Settings.php' ],
			],
			'nearby'            => [
				'title'       => __( 'Nearby locations & result panels', 'advanced-address-autocomplete-for-gravity-forms' ),
				'description' => __( 'Find destinations near a visitor location and display selectable results with an optional results map.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'details'     => __( 'Configure destination sources and show inline details or a separate Nearest Locations Panel.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'icon'        => 'search',
				'doc'         => 'gravity-forms-nearest-locations-field/',
				'packages'    => $advanced,
				'files'       => [ $fields . 'NearestLocations/Field.php', $fields . 'NearestLocationsPanel/Field.php' ],
			],
			'validation'        => [
				'title'       => __( 'Address validation', 'advanced-address-autocomplete-for-gravity-forms' ),
				'description' => __( 'Compare an entered address with Google Address Validation recommendations before continuing.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'details'     => __( 'Review suggested corrections and retain validation status for conditional logic. Requires the Address Validation API.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'icon'        => 'yes-alt',
				'doc'         => 'gravity-forms-address-validation-field/',
				'packages'    => $advanced,
				'files'       => [ $fields . 'AddressValidation/Field.php' ],
			],
			'drawing'           => [
				'title'       => __( 'Drawing Tools & Drawing Shape', 'advanced-address-autocomplete-for-gravity-forms' ),
				'description' => __( 'Let visitors draw markers, lines, polygons, rectangles, circles and freehand shapes on a connected map.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'details'     => __( 'Collect shape collections or predefined shapes, with geometry, labels, area, length and perimeter outputs.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'icon'        => 'edit',
				'doc'         => 'gravity-forms-drawing-tools-field/',
				'packages'    => $advanced,
				'files'       => [ $fields . 'DrawingTools/Field.php', $fields . 'DrawingShape/Field.php', $fields . 'Map/Field.php' ],
			],
			'entry-maps'        => [
				'title'       => __( 'Single Entry & Mashup maps', 'advanced-address-autocomplete-for-gravity-forms' ),
				'description' => __( 'Display saved entry locations outside the form: one entry on its own map, or multiple entries together.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'details'     => __( 'Use map shortcodes, markers and information windows for location-based content.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'icon'        => 'admin-multisite',
				'doc'         => 'gravity-forms-mashup-map-shortcode/',
				'packages'    => $advanced,
				'files'       => [ 'Features/Map/Components/SingleEntryMap.php', 'Features/Map/Components/MashupMap.php' ],
			],
			'search'            => [
				'title'       => __( 'Gravity Search: directories & entry search', 'advanced-address-autocomplete-for-gravity-forms' ),
				'description' => __( 'Turn saved Gravity Forms entries into front-end searches, directories and location-aware listings.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'details'     => __( 'Field filters, proximity search, result layouts, maps, sorting and pagination. Search maps are part of Search, not the standalone map shortcodes.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'icon'        => 'filter',
				'doc'         => 'gravity-search-overview/',
				'packages'    => [ 'search', 'allaccess' ],
				'files'       => [ 'Features/Search/Provider.php' ],
			],
			'entry-details'     => [
				'title'       => __( 'Search entry details & custom output', 'advanced-address-autocomplete-for-gravity-forms' ),
				'description' => __( 'Link search results to individual entry-detail pages and customize how entry data is presented.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'details'     => __( 'Use supported structured output, page content or custom HTML within the Search configuration.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'icon'        => 'media-document',
				'doc'         => '',
				'packages'    => [ 'search', 'allaccess' ],
				'files'       => [ 'Features/Search/Provider.php', 'Features/Search/Support/DetailsRoutePolicy.php' ],
			],
			'current-location'  => [
				'title'       => __( 'Current-location detection', 'advanced-address-autocomplete-for-gravity-forms' ),
				'description' => __( 'Offer an in-field locator button or detect a visitor location automatically when the form loads.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'details'     => __( 'Browser location requires visitor permission; optional IP location requires a configured provider.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'icon'        => 'admin-site-alt3',
				'doc'         => 'gravity-forms-address-field/',
				'packages'    => $location,
				'files'       => [ $fields . 'Address/Field.php', $fields . 'Geocoder/Field.php' ],
			],
			'coordinates'       => [
				'title'       => __( 'Coordinate inputs', 'advanced-address-autocomplete-for-gravity-forms' ),
				'description' => __( 'Collect latitude and longitude directly and connect them to your shared location data.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'details'     => __( 'A dedicated Coordinates field for workflows where an address is not the starting point.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'icon'        => 'admin-generic',
				'doc'         => 'gravity-forms-coordinates-field/',
				'packages'    => [ 'location', 'toolkit', 'allaccess' ],
				'files'       => [ $fields . 'Coordinates/Field.php' ],
			],
			'location-controls' => [
				'title'       => __( 'Standalone Locator & Reset fields', 'advanced-address-autocomplete-for-gravity-forms' ),
				'description' => __( 'Place dedicated current-location and reset buttons wherever they fit your form.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'details'     => __( 'These separate fields complement the in-field locator available in Address settings.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'icon'        => 'controls-repeat',
				'doc'         => 'gravity-forms-locator-button-field/',
				'packages'    => [ 'location', 'toolkit', 'allaccess' ],
				'files'       => [ $fields . 'LocatorButton/Field.php', $fields . 'ResetLocation/Field.php' ],
			],
			'gmw-integration'   => [
				'title'       => __( 'GEO my WP feed integration', 'advanced-address-autocomplete-for-gravity-forms' ),
				'description' => __( 'Pass form location data to post and user locations through supported creation and registration feeds.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'details'     => __( 'Requires GEO my WP and the relevant Gravity Forms add-on; those integrations are separate products.', 'advanced-address-autocomplete-for-gravity-forms' ),
				'icon'        => 'admin-plugins',
				'doc'         => '',
				'packages'    => $location,
				'files'       => [ $fields . 'Geocoder/Field.php', 'Features/Form/Feeds/FeedProcessing.php' ],
			],
		];
	}
}
