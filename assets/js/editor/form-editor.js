/**
 * Address-only native builder controls, with no framework editor imports.
 *
 * @since 1.0.0
 */
import Choices from './vendor/choices.js';
import {PlaceTypes} from './place-types.js';
import {normalizeList, isEnabled} from '../autocomplete/address-options.js';
import {restoreGeolocationPanelState} from './geolocation-panel-state.js';
import {showUnavailableFieldDialog} from './unavailable-fields.js';

const supportedTypes = ['gfgeo_address', 'address'];
const checkboxes = {
	'#gfgeo-address-autocomplete': 'gfgeo_address_autocomplete',
	'#gfgeo-force-autocomplete-selection': 'gfgeo_force_autocomplete_selection',
};
const textInputs = {
	'#gfgeo-force-autocomplete-selection-message': 'gfgeo_force_autocomplete_selection_message',
	'#gfgeo-address-autocomplete-language': 'gfgeo_address_autocomplete_language',
	'#gfgeo-autocomplete-restriction-usage': 'gfgeo_autocomplete_restriction_usage',
	'#gfgeo-autocomplete-proximity-lat': 'gfgeo_autocomplete_proximity_lat',
	'#gfgeo-autocomplete-proximity-lng': 'gfgeo_autocomplete_proximity_lng',
	'#gfgeo-autocomplete-proximity-radius': 'gfgeo_autocomplete_proximity_radius',
	'#gfgeo-autocomplete-bounds-sw-point': 'gfgeo_autocomplete_bounds_sw_point',
	'#gfgeo-autocomplete-bounds-ne-point': 'gfgeo_autocomplete_bounds_ne_point',
};
const enhancedSelects = {
	'#gfgeo-address-autocomplete-country': 'gfgeo_address_autocomplete_country',
	'#gfgeo-address-autocomplete-types': 'gfgeo_address_autocomplete_types',
};
let initialized = false;
let hydrating = false;

/**
 * Translate retained type aliases for display without rewriting saved fields.
 *
 * @since 1.0.0
 *
 * @param {string|string[]} value Saved place-type preference.
 * @returns {string[]} Editor-compatible place-type values.
 */
function displayTypes(value) {
	const aliases = {address: 'street_address', '(cities)': '(cities)', '(regions)': '(regions)'};
	return normalizeList(value).map(type => aliases[type] || type);
}

/**
 * Create or reuse the local searchable multi-select instance.
 *
 * @since 1.0.0
 *
 * @param {HTMLSelectElement} element Country or place-type selector.
 * @returns {Choices} Existing or newly created Choices instance.
 */
function enhanceSelect(element) {
	if (!element.choices) {
		element.choices = new Choices(element, {
			removeItemButton: true,
			maxItemCount: 5,
			shouldSort: false,
			allowHTML: false,
			itemSelectText: '',
			placeholderValue: element.id.endsWith('-country')
				? (globalThis.gfgeoFormEditorStrings?.autocomplete?.allCountries || 'All countries')
				: (globalThis.gfgeoFormEditorStrings?.autocomplete?.allTypes || 'All place types'),
		});
	}
	return element.choices;
}

/**
 * Update dependent controls without saving or changing field properties.
 *
 * @since 1.0.0
 *
 * @returns {void}
 */
function refreshVisibility() {
	const enabled = document.querySelector('#gfgeo-address-autocomplete')?.checked;
	const required = document.querySelector('#gfgeo-force-autocomplete-selection')?.checked;
	globalThis.jQuery('#gfgeo-address-autocomplete-options-wrapper').toggle(Boolean(enabled));
	globalThis.jQuery('.gfgeo-force-autocomplete-message').toggle(Boolean(required));
	const usage = document.querySelector('#gfgeo-autocomplete-restriction-usage')?.value;
	globalThis.jQuery('[class*="restriction-option-"]').hide();
	if (['proximity', 'area_bounds'].includes(usage)) {
		globalThis.jQuery(`.restriction-option-${usage}`).show();
	}
}

/**
 * Hydrate Address controls while preserving hidden premium field properties.
 *
 * @since 1.0.0
 *
 * @param {Object} field Selected native Gravity Forms field.
 * @returns {void}
 */
export function loadFieldSettings(field) {
	const supported = supportedTypes.includes(field?.type);
	const toggle = document.querySelector('#gfgeo_geolocation_tab_toggle');
	globalThis.jQuery(toggle).toggle(supported);
	if (!supported) {
		if (toggle?.getAttribute('aria-expanded') === 'true') {
			toggle.click();
		}
		return;
	}
	hydrating = true;
	try {
		for (const [selector, key] of Object.entries(checkboxes)) {
			const value = key === 'gfgeo_address_autocomplete' ? (field[key] ?? field.type === 'gfgeo_address') : field[key];
			globalThis.jQuery(selector).prop('checked', isEnabled(value));
		}
		for (const [selector, key] of Object.entries(textInputs)) {
			globalThis.jQuery(selector).val(field[key] ?? '');
		}
		for (const [selector, key] of Object.entries(enhancedSelects)) {
			const element = document.querySelector(selector);
			if (!element) {
				continue;
			}
			const values = key.endsWith('_types') ? displayTypes(field[key]) : normalizeList(field[key]);
			// Retain unknown saved values instead of silently dropping custom filters.
			const missing = values.filter(value => !Array.from(element.options).some(option => option.value === value));
			const choices = enhanceSelect(element);
			// Choices caches its option list. Add later custom values through its API
			// so switching between saved fields does not drop those values in the UI.
			if (missing.length) choices.setChoices(missing.map(value => ({value, label: value})), 'value', 'label', false);
			choices.removeActiveItems();
			choices.setChoiceByValue(values);
		}
		refreshVisibility();
		restoreGeolocationPanelState(document, toggle?.getAttribute('aria-expanded') === 'true');
	} finally {
		hydrating = false;
	}
}

/**
 * Show promotional field buttons while preventing actual field insertion.
 *
 * @since 1.0.0
 *
 * @returns {void}
 */
function blockPremiumButtons() {
	const types = new Map();
	globalThis.jQuery('.add-buttons button[data-icon*="gfgeo-pro-disabled"]').each(function () {
		const button = globalThis.jQuery(this);
		const type = button.data('type');
		const name = button.val() || button.text().trim() || type;
		types.set(type, name);
		button.removeClass('ui-draggable').attr('draggable', 'false').css({cursor: 'not-allowed', opacity: 0.6});
		button.off('mousedown');
		this.onclick = null;
		this.onkeypress = null;
		button.on('click.gfgeoac keydown.gfgeoac', event => {
			if (event.type === 'keydown' && !['Enter', ' '].includes(event.key)) {
				return;
			}
			event.preventDefault();
			event.stopImmediatePropagation();
			showUnavailableFieldDialog(name);
		});
	});
	globalThis.gform?.addFilter('gform_form_editor_can_field_be_added', (canAdd, type) => {
		if (!types.has(type)) {
			return canAdd;
		}
		showUnavailableFieldDialog(types.get(type));
		return false;
	});
}

/**
 * Place the existing Autocomplete heading and panel immediately before Advanced.
 *
 * Gravity Forms renders custom tabs after its built-in tabs. Move both existing
 * nodes together without recreating controls or changing their accordion state.
 *
 * @since 1.0.0
 *
 * @returns {void}
 */
function positionAutocompleteTab() {
	const advanced = document.getElementById('advanced_tab_toggle');
	const toggle = document.getElementById('gfgeo_geolocation_tab_toggle');
	const panel = document.getElementById('gfgeo_geolocation_tab');
	if (!advanced || !toggle || !panel || !advanced.parentElement
		|| toggle.parentElement !== advanced.parentElement || panel.parentElement !== advanced.parentElement) {
		return;
	}
	advanced.before(toggle, panel);
}

/**
 * Register Address controls and scoped builder events once per editor instance.
 *
 * @since 1.0.0
 *
 * @returns {void}
 */
export function initializeEditor() {
	if (initialized || !globalThis.jQuery || !globalThis.fieldSettings || !globalThis.form) {
		return;
	}
	initialized = true;
	const $ = globalThis.jQuery;
	positionAutocompleteTab();
	const settings = String(globalThis.fieldSettings.address || '').split(',').map(value => value.trim()).filter(Boolean);
	if (!settings.includes('.gfgeo_address_autocomplete_setting')) {
		settings.push('.gfgeo_address_autocomplete_setting');
	}
	globalThis.fieldSettings.address = settings.join(', ');
	const typeSelect = document.querySelector('#gfgeo-address-autocomplete-types');
	if (typeSelect) {
		for (const [label, types] of Object.entries(PlaceTypes)) {
			const group = document.createElement('optgroup');
			group.label = label;
			for (const type of types) {
				group.append(new Option(type.replace(/_/g, ' '), type));
			}
			typeSelect.append(group);
		}
	}
	$(document).on('gform_load_field_settings.gfgeoac', (event, field) => loadFieldSettings(field));
	$(document).on('change.gfgeoac', '#gfgeo-address-autocomplete, #gfgeo-force-autocomplete-selection, #gfgeo-autocomplete-restriction-usage', refreshVisibility);
	for (const [selector, key] of Object.entries(enhancedSelects)) {
		// Replace the legacy inline callback so hydration cannot save accidentally.
		const element = document.querySelector(selector);
		if (!element) {
			continue;
		}
		element.removeAttribute('onchange');
		element.onchange = null;
		$(element).on('change.gfgeoac', () => {
			if (!hydrating) {
				globalThis.SetFieldProperty(key, $(element).val() || []);
			}
		});
	}
	blockPremiumButtons();
}

globalThis.jQuery?.(initializeEditor);
