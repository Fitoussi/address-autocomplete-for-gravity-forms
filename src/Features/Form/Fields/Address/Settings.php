<?php
/**
 * Gravity Geolocation address field settings UI.
 *
 * Renders Address field settings in the Gravity Forms editor, including
 * Google Places Autocomplete configuration and restriction options.
 *
 * @package GravityGeolocationAutocomplete\Features\Form\Fields\Address
 * @since 1.0.0
 */

namespace GravityGeolocationAutocomplete\Features\Form\Fields\Address;

use GravityGeolocationAutocomplete\Features\Form\Admin\FormEditor\Helpers\FieldText;
use GravityGeolocationAutocomplete\Helpers\ReferenceData;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Address field settings renderer for Gravity Forms editor.
 *
 * @since 1.0.0
 */
class Settings {

	/**
	 * Output the full Address field settings panel markup for the form editor.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function render() {
		$labels = FieldText::get_parts( 'address', null, 'label' );
		?>
		<li class="field_setting gfgeo_address_autocomplete_setting gfgeo-settings-group-wrapper">

			<ul class="gfgeo-settings-group-inner">
				<li class="gfgeo_field_setting">
					<input
						type="checkbox"
						id="gfgeo-address-autocomplete"
						class="gfgeo-address-autocomplete"
						onclick="SetFieldProperty( 'gfgeo_address_autocomplete', this.checked );"
					/>
					<label for="gfgeo-address-autocomplete" class="inline">
						<?php echo esc_html( FieldText::get_safe( $labels, 'autocomplete', 'enable' ) ); ?>
						<?php gform_tooltip( 'gfgeo_address_autocomplete_tt' ); ?>
					</label>
				</li>

				<li id="gfgeo-address-autocomplete-options-wrapper">

					<ul>
						<li class="gfgeo_field_setting">
							<input
								type="checkbox"
								id="gfgeo-force-autocomplete-selection"
								class="gfgeo-force-autocomplete-selection"
								onclick="SetFieldProperty( 'gfgeo_force_autocomplete_selection', this.checked );"
							/>
							<label for="gfgeo-force-autocomplete-selection" class="inline">
								<?php echo esc_html( FieldText::get_safe( $labels, 'autocomplete', 'force_selection' ) ); ?>
								<?php gform_tooltip( 'gfgeo_force_autocomplete_selection_tt' ); ?>
							</label>
						</li>

						<li class="gfgeo_field_setting gfgeo-force-autocomplete-message">
							<label for="gfgeo-force-autocomplete-selection-message" class="section_label">
								<?php echo esc_html( FieldText::get_safe( $labels, 'autocomplete', 'force_selection_message' ) ); ?>
								<?php gform_tooltip( 'gfgeo_force_autocomplete_selection_message_tt' ); ?>
							</label>
							<input
								type="text"
								id="gfgeo-force-autocomplete-selection-message"
								class="gfgeo-force-autocomplete-selection-message fieldwidth-3"
								onkeyup="SetFieldProperty( 'gfgeo_force_autocomplete_selection_message', this.value );"
							/>
						</li>

						<li class="gfgeo_field_setting gfgeo-address-autocomplete-types-setting">
							<label for="gfgeo-address-autocomplete-types" class="section_label">
								<?php echo esc_html( FieldText::get_safe( $labels, 'autocomplete', 'types' ) ); ?>
								<?php gform_tooltip( 'gfgeo_address_autocomplete_types_tt' ); ?>
							</label>
							<select
								name="gfgeo_address_autocomplete_types"
								id="gfgeo-address-autocomplete-types"
								class="gfgeo-address-autocomplete-types fieldwidth-3 gfgeo-enhanced-select"
								onchange="SetFieldProperty( 'gfgeo_address_autocomplete_types', jQuery( this ).val() );"
								multiple="multiple"
								data-max-items="5"
							>
								<!-- Options Will Be Populated via JavaScript -->
							</select>
						</li>

						<li class="gfgeo_field_setting">
							<label for="gfgeo-address-autocomplete-country" class="section_label">
								<?php echo esc_html( FieldText::get_safe( $labels, 'autocomplete', 'countries' ) ); ?>
								<?php gform_tooltip( 'gfgeo_address_autocomplete_country_tt' ); ?>
							</label>
							<select
								multiple="multiple"
								name="gfgeo_address_autocomplete_country"
								id="gfgeo-address-autocomplete-country"
								class="gfgeo-address-autocomplete-country fieldwidth-3 gfgeo-enhanced-select"
								data-max-items="5"
								onchange="SetFieldProperty( 'gfgeo_address_autocomplete_country', jQuery(this).val());"
							>
								<?php
								foreach ( ReferenceData::get_countries() as $value => $name ) {
									echo '<option value="' . esc_attr( $value ) . '">' . esc_html( $name ) . '</option>';
								}
								?>
							</select>
						</li>
						<!-- Language Selector Field Setting -->
						<li class="gfgeo_field_setting">
							<label for="gfgeo-address-autocomplete-language" class="section_label">
								<?php echo esc_html( FieldText::get_safe( $labels, 'autocomplete', 'language' ) ); ?>
								<?php gform_tooltip( 'gfgeo_address_autocomplete_language_tt' ); ?>
							</label>
							<select
								name="gfgeo_address_autocomplete_language"
								id="gfgeo-address-autocomplete-language"
								class="gfgeo-address-autocomplete-language fieldwidth-3"
								onchange="SetFieldProperty( 'gfgeo_address_autocomplete_language', jQuery(this).val() );"
							>
								<option value=""><?php esc_html_e( 'Default (global language)', 'address-autocomplete-for-gravity-forms' ); ?></option>
								<?php
								foreach ( ReferenceData::get_languages() as $code => $label ) {
									echo '<option value="' . esc_attr( $code ) . '">' . esc_html( $label ) . '</option>';
								}
								?>
							</select>
						</li>

						<li class="gfgeo_field_setting">
							<label for="gfgeo-autocomplete-restriction-usage" class="section_label">
								<?php echo esc_html( FieldText::get_safe( $labels, 'autocomplete', 'bias_type' ) ); ?>
								<?php gform_tooltip( 'gfgeo_autocomplete_restriction_usage_tt' ); ?>
							</label>
							<select
								name="gfgeo_autocomplete_restriction_usage"
								id="gfgeo-autocomplete-restriction-usage"
								class="gfgeo-autocomplete-restriction-usage fieldwidth-3"
								onchange="SetFieldProperty( 'gfgeo_autocomplete_restriction_usage', jQuery( this ).val() );"
							>
								<?php foreach ( self::get_restriction_choices() as $choice ) { ?>
									<option value="<?php echo esc_attr( $choice['value'] ); ?>" <?php disabled( ! empty( $choice['disabled'] ) ); ?>><?php echo esc_html( $choice['label'] ); ?></option>
								<?php } ?>
							</select>
						</li>

						<li class="gfgeo-multiple-settings restriction-option-proximity">

							<div class="gfgeo_field_setting gfgeo-half">
								<label for="gfgeo-autocomplete-proximity-lat" class="section_label">
									<?php echo esc_html( FieldText::get_safe( $labels, 'proximity', 'lat' ) ); ?>
									<?php gform_tooltip( 'gfgeo_autocomplete_proximity_lat_tt' ); ?>
								</label>
								<input
									type="text"
									id="gfgeo-autocomplete-proximity-lat"
									class="gfgeo-autocomplete-proximity-lat"
									onkeyup="SetFieldProperty( 'gfgeo_autocomplete_proximity_lat', this.value );"
									placeholder="e.g. 26.423277"
								/>
							</div>

							<div class="gfgeo_field_setting gfgeo-half">
								<label for="gfgeo-autocomplete-proximity-lng" class="section_label">
									<?php echo esc_html( FieldText::get_safe( $labels, 'proximity', 'lng' ) ); ?>
									<?php gform_tooltip( 'gfgeo_autocomplete_proximity_lng_tt' ); ?>
								</label>
								<input
									type="text"
									id="gfgeo-autocomplete-proximity-lng"
									class="gfgeo-autocomplete-proximity-lng"
									onkeyup="SetFieldProperty( 'gfgeo_autocomplete_proximity_lng', this.value );"
									placeholder="e.g. -82.0217760"
								/>
							</div>

							<div class="gfgeo_field_setting gfgeo-full">
								<label for="gfgeo-autocomplete-proximity-radius" class="section_label">
									<?php echo esc_html( FieldText::get_safe( $labels, 'proximity', 'radius' ) ); ?>
									<?php gform_tooltip( 'gfgeo_autocomplete_proximity_radius_tt' ); ?>
								</label>
								<input
									type="text"
									id="gfgeo-autocomplete-proximity-radius"
									class="gfgeo-autocomplete-proximity-radius"
									onkeyup="SetFieldProperty( 'gfgeo_autocomplete_proximity_radius', this.value );"
									placeholder="e.g. 100"
								/>
							</div>
						</li>

						<li class="gfgeo-multiple-settings restriction-option-area_bounds">

							<div class="gfgeo_field_setting gfgeo-half">
								<label for="gfgeo-autocomplete-bounds-sw-point" class="section_label">
									<?php echo esc_html( FieldText::get_safe( $labels, 'area_bounds', 'sw_point' ) ); ?>
									<?php gform_tooltip( 'gfgeo_bounds_sw_point_tt' ); ?>
								</label>
								<input
									type="text"
									id="gfgeo-autocomplete-bounds-sw-point"
									class="gfgeo-autocomplete-bounds-sw-point"
									onkeyup="SetFieldProperty( 'gfgeo_autocomplete_bounds_sw_point', this.value );"
									placeholder="e.g. 26.423277,-82.137132"
								/>
							</div>

							<div class="gfgeo_field_setting gfgeo-half">
								<label for="gfgeo-autocomplete-bounds-ne-point" class="section_label">
									<?php echo esc_html( FieldText::get_safe( $labels, 'area_bounds', 'ne_point' ) ); ?>
									<?php gform_tooltip( 'gfgeo_bounds_ne_point_tt' ); ?>
								</label>
								<input
									type="text"
									id="gfgeo-autocomplete-bounds-ne-point"
									class="gfgeo-autocomplete-bounds-ne-point"
									onkeyup="SetFieldProperty( 'gfgeo_autocomplete_bounds_ne_point', this.value );"
									placeholder="e.g. 26.4724595,-82.0217760"
								/>
							</div>
						</li>
					</ul>
				</li>
			</ul>
		</li>
		<?php
	}

	/**
	 * Provide location-bias choices for the standalone Address settings panel.
	 *
	 * The retained filter allows integrations to customize choices without
	 * duplicating the settings template or changing saved field preferences.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] Choice value, label and optional disabled flag.
	 */
	public static function get_restriction_choices() {
		return apply_filters(
			'gfgeo_address_autocomplete_restriction_choices',
			[
				[
					'value' => '',
					'label' => __( 'None', 'address-autocomplete-for-gravity-forms' ),
				],
				[
					'value' => 'proximity',
					'label' => __( 'Proximity', 'address-autocomplete-for-gravity-forms' ),
				],
				[
					'value' => 'area_bounds',
					'label' => __( 'Area bounds', 'address-autocomplete-for-gravity-forms' ),
				],
			]
		);
	}
}
