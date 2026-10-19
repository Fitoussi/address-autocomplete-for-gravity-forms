/** Real shipped editor bundle; only the native Gravity host API is mocked. */
import test from 'node:test';
import assert from 'node:assert/strict';
import {createRequire} from 'node:module';
import {readFileSync} from 'node:fs';

const require = createRequire(import.meta.url);
const {JSDOM} = require('jsdom');
const jquery = readFileSync(require.resolve('jquery'), 'utf8');
const bundle = readFileSync(new URL('../../build/js/admin/gfgeo-form-editor.min.js', import.meta.url), 'utf8');

/** Opening settings must only hydrate controls, never alter the saved field. */
function fixture({advanced = true} = {}) {
	const dom = new JSDOM(`
		<button id="general_tab_toggle">General</button><div id="general_tab"></div>
		<button id="appearance_tab_toggle">Appearance</button><div id="appearance_tab"></div>
		${advanced ? '<button id="advanced_tab_toggle" aria-expanded="false">Advanced</button><div id="advanced_tab"></div>' : ''}
		<button id="gfgeo_geolocation_tab_toggle" aria-expanded="false">Autocomplete</button>
		<div id="gfgeo_geolocation_tab">
			<input type="checkbox" id="gfgeo-address-autocomplete">
			<input type="checkbox" id="gfgeo-force-autocomplete-selection">
			<div id="gfgeo-address-autocomplete-options-wrapper">
				<input class="gfgeo-force-autocomplete-message" id="gfgeo-force-autocomplete-selection-message">
				<select multiple id="gfgeo-address-autocomplete-country" onchange="SetFieldProperty('bad', [])"><option value="US">United States</option><option value="IL">Israel</option></select>
				<select multiple id="gfgeo-address-autocomplete-types"></select>
				<select id="gfgeo-autocomplete-restriction-usage"><option value=""></option><option value="proximity">Proximity</option><option value="area_bounds">Bounds</option></select>
				<div class="restriction-option-proximity"><input id="gfgeo-autocomplete-proximity-lat"></div>
				<div class="restriction-option-area_bounds"><input id="gfgeo-autocomplete-bounds-sw-point"></div>
			</div>
		</div>
		<div class="add-buttons"><button data-type="gfgeo_address" value="Address">Address</button><button data-type="text" value="Text">Text</button><button class="ui-draggable" data-type="gfgeo_map" data-icon="gfgeo-pro-disabled" value="Map">Map</button><button class="ui-draggable" data-type="gfgeo_directions" data-icon="gfgeo-pro-disabled" value="Directions & Travel Routes">Directions & Travel Routes</button><button class="ui-draggable" data-type="gfgeo_distance" data-icon="gfgeo-pro-disabled" value="Distance & Travel Time">Distance & Travel Time</button><button class="ui-draggable" data-type="gfgeo_address_validation" data-icon="gfgeo-pro-disabled" value="Address Validation">Address Validation</button></div>
	`, {runScripts: 'outside-only', pretendToBeVisual: true, url: 'https://example.test/'});
	const {window} = dom;
	window.matchMedia = () => ({matches: false, addListener() {}, removeListener() {}});
	window.eval(jquery);
	const state = {writes: [], dialogs: [], filters: {}};
	window.form = {fields: []};
	window.fieldSettings = {address: '.native_address_setting', text: '.native_text_setting'};
	window.SetFieldProperty = (...args) => state.writes.push(args);
	window.gform = {
		addFilter: (name, callback) => {state.filters[name] = callback;},
		instances: {dialogAlert: (...args) => state.dialogs.push(args)},
	};
	window.gfgeoFormEditorStrings = {autocomplete: {allCountries: 'All countries', allTypes: 'All place types'}, unavailableField: {
		title: 'The %s field is not available.', message: 'The %s field is available with Gravity Geolocation.',
		upgradeNow: 'View packages', unlockFeatures: 'to explore additional location tools.',
	}};
	window.eval(bundle);
	window.GFGEO_FormEditor.initializeEditor();
	return {dom, window, state};
}

test('editor bundle has no framework, connection or premium-feature imports', () => {
	const map = JSON.parse(readFileSync(new URL('../../build/js/admin/gfgeo-form-editor.min.js.map', import.meta.url), 'utf8'));
	assert.ok(map.sources.every(source => source.startsWith('../../../assets/js/')));
	assert.ok(!map.sources.some(source => /wpgeo-framework|quick-setups|relationship|\/fields\//.test(source)));
	assert.ok(map.sources.some(source => source.endsWith('vendor/choices.js')));
	assert.ok(map.sources.some(source => source.endsWith('unavailable-fields.js')));
	assert.ok(!map.sources.some(source => source.includes('search-unavailable')));
});

test('Autocomplete heading and panel precede Advanced without changing state or saved fields', () => {
	const {dom, window, state} = fixture();
	try {
		const document = window.document;
		const toggle = document.getElementById('gfgeo_geolocation_tab_toggle');
		const panel = document.getElementById('gfgeo_geolocation_tab');
		const advanced = document.getElementById('advanced_tab_toggle');
		assert.equal(toggle.nextElementSibling, panel);
		assert.equal(panel.nextElementSibling, advanced);
		assert.deepEqual(Array.from(document.querySelectorAll('button[id$="_tab_toggle"]'), element => element.id), [
			'general_tab_toggle', 'appearance_tab_toggle', 'gfgeo_geolocation_tab_toggle', 'advanced_tab_toggle',
		]);
		assert.equal(toggle.getAttribute('aria-expanded'), 'false');
		assert.equal(advanced.getAttribute('aria-expanded'), 'false');
		window.GFGEO_FormEditor.initializeEditor();
		window.GFGEO_FormEditor.loadFieldSettings({type: 'gfgeo_address'});
		assert.equal(document.getElementById('gfgeo_geolocation_tab_toggle'), toggle);
		assert.equal(document.getElementById('gfgeo_geolocation_tab'), panel);
		assert.equal(document.querySelectorAll('#gfgeo_geolocation_tab_toggle').length, 1);
		assert.equal(panel.nextElementSibling, advanced);
		assert.equal(state.writes.length, 0);
	} finally {dom.window.close();}
});

test('missing Advanced tab leaves autocomplete controls usable', () => {
	const {dom, window, state} = fixture({advanced: false});
	try {
		window.GFGEO_FormEditor.loadFieldSettings({type: 'address', gfgeo_address_autocomplete: true});
		assert.equal(window.document.getElementById('gfgeo-address-autocomplete').checked, true);
		assert.equal(state.writes.length, 0);
	} finally {dom.window.close();}
});

test('Address options hydrate without saving and native Text stays unmodified', () => {
	const {dom, window, state} = fixture();
	try {
		const field = {type: 'gfgeo_address', gfgeo_address_autocomplete_country: ['US'], gfgeo_address_autocomplete_types: ['restaurant'], gfgeo_force_autocomplete_selection: 'false', gfgeo_geocoder_id: '12'};
		const before = JSON.stringify(field);
		window.GFGEO_FormEditor.loadFieldSettings(field);
		assert.equal(JSON.stringify(field), before);
		assert.equal(state.writes.length, 0);
		assert.equal(window.document.querySelector('#gfgeo-address-autocomplete').checked, true);
		assert.equal(window.document.querySelector('#gfgeo-force-autocomplete-selection').checked, false);
		assert.equal(window.document.querySelectorAll('.choices').length, 2);
		assert.equal(window.document.querySelector('#gfgeo-address-autocomplete-country').choices.getValue(true)[0], 'US');
		assert.equal(window.document.querySelector('#gfgeo-address-autocomplete-types').choices.getValue(true)[0], 'restaurant');
		assert.equal(window.fieldSettings.text, '.native_text_setting');
		assert.ok(window.fieldSettings.address.includes('.gfgeo_address_autocomplete_setting'));
		window.GFGEO_FormEditor.loadFieldSettings({type: 'address', gfgeo_address_autocomplete: 'false'});
		assert.equal(window.document.querySelector('#gfgeo-address-autocomplete').checked, false);
		assert.equal(state.writes.length, 0);
	} finally {dom.window.close();}
});

test('switching fields retains later custom multiselect choices, user changes save once', () => {
	const {dom, window, state} = fixture();
	try {
		window.GFGEO_FormEditor.loadFieldSettings({type: 'gfgeo_address', gfgeo_address_autocomplete_country: ['US']});
		window.GFGEO_FormEditor.loadFieldSettings({type: 'gfgeo_address', gfgeo_address_autocomplete_country: ['CUSTOM'], gfgeo_address_autocomplete_types: ['custom_place']});
		const country = window.document.querySelector('#gfgeo-address-autocomplete-country');
		assert.equal(country.choices.getValue(true)[0], 'CUSTOM');
		assert.equal(window.document.querySelector('#gfgeo-address-autocomplete-types').choices.getValue(true)[0], 'custom_place');
		assert.equal(state.writes.length, 0);
		country.dispatchEvent(new window.Event('change', {bubbles: true}));
		assert.equal(state.writes.length, 1);
		assert.equal(state.writes[0][0], 'gfgeo_address_autocomplete_country');
		assert.deepEqual(Array.from(state.writes[0][1]), ['CUSTOM']);
	} finally {dom.window.close();}
});

test('four original discovery buttons open native dialogs without changing fields', () => {
	const {dom, window, state} = fixture();
	try {
		const buttons = Array.from(window.document.querySelectorAll('[data-icon*="gfgeo-pro-disabled"]'));
		assert.equal(buttons.length, 4);
		assert.deepEqual(buttons.map(button => button.dataset.type), ['gfgeo_map', 'gfgeo_directions', 'gfgeo_distance', 'gfgeo_address_validation']);
		const before = JSON.stringify(window.form);
		for (const [index, button] of buttons.entries()) {
			assert.equal(button.getAttribute('draggable'), 'false');
			assert.equal(button.classList.contains('ui-draggable'), false);
			assert.equal(button.style.opacity, '0.6');
			button.click();
			assert.equal(state.dialogs.length, index + 1);
			assert.equal(state.dialogs[index][0], `The ${button.value} field is not available.`.replaceAll('&', '&amp;'));
			assert.match(state.dialogs[index][1], /https:\/\/gravitygeolocation\.com\/pricing\//);
			assert.match(state.dialogs[index][1], /rel="noopener noreferrer"/);
		}
		assert.equal(JSON.stringify(window.form), before);
		assert.equal(state.writes.length, 0);
		window.document.querySelector('[data-type="gfgeo_address"]').click();
		assert.equal(state.dialogs.length, 4);
	} finally {dom.window.close();}
});

test('discovery buttons block insertion while preserving native and other plugin decisions', () => {
	const {dom, window, state} = fixture();
	try {
		const canAdd = state.filters.gform_form_editor_can_field_be_added;
		for (const type of ['gfgeo_map', 'gfgeo_directions', 'gfgeo_distance', 'gfgeo_address_validation']) {
			assert.equal(canAdd(true, type), false);
		}
		assert.equal(canAdd(true, 'gfgeo_address'), true);
		assert.equal(canAdd(false, 'gfgeo_address'), false);
		assert.equal(canAdd(true, 'text'), true);
		assert.equal(canAdd(false, 'third_party_field'), false);
		assert.equal(state.dialogs.length, 4);
		assert.equal(window.form.fields.length, 0);
		assert.equal(state.writes.length, 0);
	} finally {dom.window.close();}
});

test('keyboard discovery opens the same native dialog without accepting other keys', () => {
	const {dom, window, state} = fixture();
	try {
		const button = window.document.querySelector('[data-type="gfgeo_map"]');
		for (const key of ['Enter', ' ']) {
			const event = new window.KeyboardEvent('keydown', {key, bubbles: true, cancelable: true});
			button.dispatchEvent(event);
			assert.equal(event.defaultPrevented, true);
		}
		button.dispatchEvent(new window.KeyboardEvent('keydown', {key: 'ArrowDown', bubbles: true, cancelable: true}));
		assert.equal(state.dialogs.length, 2);
		assert.equal(window.form.fields.length, 0);
	} finally {dom.window.close();}
});

test('missing native dialog API still prevents discovery field insertion and creates no fallback UI', () => {
	const {dom, window, state} = fixture();
	try {
		delete window.gform.instances;
		assert.equal(state.filters.gform_form_editor_can_field_be_added(true, 'gfgeo_map'), false);
		assert.equal(state.dialogs.length, 0);
		assert.equal(window.form.fields.length, 0);
		assert.equal(window.document.querySelectorAll('[role="dialog"]').length, 0);
	} finally {dom.window.close();}
});

test('native Text keeps its original settings and no Search promotion is bundled', () => {
	const {dom, window} = fixture();
	try {
		window.GFGEO_FormEditor.loadFieldSettings({type: 'text'});
		assert.equal(window.document.querySelector('#gfgeo_geolocation_tab_toggle').style.display, 'none');
		assert.equal(bundle.includes('search-unavailable'), false);
		assert.equal(bundle.includes('Create Search'), false);
	} finally {dom.window.close();}
});
