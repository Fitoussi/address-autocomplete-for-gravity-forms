/** Focused standalone acceptance with mocked Google Places and a real DOM. */
import test from 'node:test';
import assert from 'node:assert/strict';
import {createRequire} from 'node:module';
import {readFileSync} from 'node:fs';
import {buildAddressOptions, isEnabled, normalizeList} from '../../assets/js/autocomplete/address-options.js';
import {getNativeAddressParts, resolveInputValue} from '../../assets/js/autocomplete/native-address.js';
import {buildLegacyPlaceFields, shouldUseLegacyPlacesFallback} from '../../assets/js/autocomplete/services/compat/places-autocomplete.js';

const require = createRequire(import.meta.url);
const {JSDOM} = require('jsdom');
const bundle = readFileSync(new URL('../../build/js/frontend/address-autocomplete.min.js', import.meta.url), 'utf8');
const dropdownCSS = readFileSync(new URL('../../assets/css/address-autocomplete.css', import.meta.url), 'utf8');
const sleep = ms => new Promise(resolve => setTimeout(resolve, ms));
const components = [
	['street_number', '1150'], ['route', 'Broadway'], ['locality', 'New York'],
	['administrative_area_level_1', 'New York', 'NY'], ['postal_code', '10001'],
	['postal_code_suffix', '1234'], ['country', 'United States', 'US'],
].map(([type, longText, shortText = longText]) => ({types: [type], longText, shortText}));

test('shipped frontend contains only six local autocomplete modules', () => {
	const map = JSON.parse(readFileSync(new URL('../../build/js/frontend/address-autocomplete.min.js.map', import.meta.url), 'utf8'));
	assert.equal(map.sources.length, 6);
	assert.ok(map.sources.every(source => source.startsWith('../../../assets/js/autocomplete/')));
	assert.ok(!bundle.includes('FormCore'));
});

test('saved options retain exact flags and explicit restrictions only', () => {
	assert.equal(isEnabled('false'), false);
	assert.equal(isEnabled('0'), false);
	assert.equal(isEnabled('1'), true);
	assert.deepEqual(normalizeList(' US, IL, US, '), ['US', 'IL']);
	const options = buildAddressOptions({
		gfgeo_address_autocomplete_country: ['US', 'US', 'IL'],
		gfgeo_address_autocomplete_language: 'he',
		gfgeo_autocomplete_restriction_usage: 'area_bounds',
		gfgeo_autocomplete_bounds_sw_point: '20,-130',
		gfgeo_autocomplete_bounds_ne_point: '50,-70',
		gfgeo_address_autocomplete_strict_bounds: 'false',
		gfgeo_infield_locator_button: true,
		gfgeo_ip_locator_status: true,
	}, {languageCode: 'en', regionCode: 'us'});
	assert.deepEqual(options.includedRegionCodes, ['US', 'IL']);
	assert.equal(options.language, 'he');
	assert.deepEqual(options.locationBias, {south: 20, west: -130, north: 50, east: -70});
	assert.equal(options.locationRestriction, undefined);
	assert.equal(options.gfgeo_infield_locator_button, undefined);
	const proximity = buildAddressOptions({
		gfgeo_autocomplete_restriction_usage: 'proximity',
		gfgeo_autocomplete_proximity_lat: '32', gfgeo_autocomplete_proximity_lng: '34',
		gfgeo_autocomplete_proximity_radius: '500',
	});
	assert.deepEqual(proximity.locationBias, {center: {lat: 32, lng: 34}, radius: 500});
	assert.equal(buildAddressOptions({gfgeo_autocomplete_restriction_usage: 'proximity', gfgeo_autocomplete_proximity_lat: '999'}).locationBias, undefined);
});

test('native country dropdown accepts explicit long/short values, never fuzzy matches', () => {
	const dom = new JSDOM('<select><option value="">Choose</option><option value="US">United States</option></select><input>');
	try {
		const parts = getNativeAddressParts({addressComponents: components});
		assert.equal(parts.line1, '1150 Broadway');
		assert.equal(parts.zip, '10001-1234');
		assert.equal(getNativeAddressParts({formattedAddress: 'New York, NY, USA'}).line1, 'New York, NY, USA');
		assert.equal(resolveInputValue(dom.window.document.querySelector('select'), parts.country, [parts.countryCode]), 'US');
		assert.equal(resolveInputValue(dom.window.document.querySelector('input'), parts.country, [parts.countryCode]), 'United States');
		assert.equal(resolveInputValue(dom.window.document.querySelector('select'), 'United States of America'), '');
	} finally { dom.window.close(); }
});

test('legacy fallback requests address fields only and ignores unrelated API failures', () => {
	assert.deepEqual(buildLegacyPlaceFields(['formattedAddress']), ['formatted_address']);
	assert.deepEqual(buildLegacyPlaceFields(['formattedAddress', 'addressComponents']), ['formatted_address', 'address_components']);
	assert.equal(shouldUseLegacyPlacesFallback(new TypeError('AutocompleteSuggestion is unavailable')), true);
	assert.equal(shouldUseLegacyPlacesFallback({code: 'REQUEST_DENIED', message: 'API key referrer is invalid'}), false);
	assert.equal(shouldUseLegacyPlacesFallback({code: 'OVER_QUERY_LIMIT'}), false);
});

/** Each fixture evaluates the actual shipped bundle without any WPGeo globals. */
async function fixture({admin = false, lateHooks = false} = {}) {
	const dom = new JSDOM(`<div id="gform_wrapper_1"><form id="gform_1">
		<div id="field_1_1"><div><input id="input_1_1" value="Saved address"></div></div>
		<div id="field_1_2">
			<span class="address_line_1"><input id="input_1_2_1"></span>
			<span class="address_line_2"><input value="Unit 7"></span>
			<span class="address_city"><input></span>
			<span class="address_state"><select><option value=""></option><option value="NY">New York</option></select></span>
			<span class="address_zip"><input></span>
			<span class="address_country"><select><option value=""></option><option value="US">United States</option></select></span>
		</div></form></div>`, {runScripts: 'outside-only', url: 'https://example.test/'});
	const {window} = dom;
	const state = {requests: [], fields: [], tokens: 0, imports: 0, delayDetails: null, alerts: [], filters: []};
	const installHooks = () => {
		window.gform = {utils: {addAsyncFilter: (name, callback) => state.filters.push({name, callback})}};
	};
	if (!lateHooks) installHooks();
	if (admin) {
		window.document.querySelector('form').id = 'entry_form';
		window.document.querySelector('#input_1_1').id = 'input_1';
		window.document.querySelector('#input_1_2_1').id = 'input_2_1';
	}
	window.alert = message => state.alerts.push(message);
	window.google = {maps: {
		importLibrary: async () => { state.imports++; },
		places: {
			AutocompleteSessionToken: class { constructor() { this.id = ++state.tokens; } },
			AutocompleteSuggestion: {fetchAutocompleteSuggestions: async options => {
				state.requests.push({...options});
				return {suggestions: [{placePrediction: {
					text: {toString: () => '1150 Broadway, New York, NY, USA'},
					toPlace: () => ({formattedAddress: '1150 Broadway, New York, NY 10001, USA', addressComponents: components,
						fetchFields: async options => {state.fields.push(options.fields); if (state.delayDetails) await state.delayDetails;},
					}),
				}}]};
			}},
		},
	}};
	window.gfgeoAutocompleteForms = {1: {formId: 1, config: {disableGoogleApi: true, isAdmin: admin}, fields: [
		{id: 1, type: 'gfgeo_address', gfgeo_address_autocomplete: true, gfgeo_force_autocomplete_selection: true},
		{id: 2, type: 'address', gfgeo_address_autocomplete: true, gfgeo_force_autocomplete_selection: true, gfgeo_force_autocomplete_selection_message: 'Choose a native address.'},
	]}};
	window.eval(bundle);
	await sleep(30);
	if (lateHooks) {
		installHooks();
		window.document.dispatchEvent(new window.Event('gform/theme/scripts_loaded'));
	}
	return {dom, window, state};
}

async function suggest(window, input) {
	input.focus();
	input.value = '1150 Broad';
	input.dispatchEvent(new window.Event('input', {bubbles: true}));
	await sleep(240);
	assert.ok(input.parentElement.querySelector('.gfgeo-autocomplete-dropdown li'));
}

test('entry editing binds custom admin input IDs inside form-prefixed wrappers and retains native binding', async () => {
	const {dom, window, state} = await fixture({admin: true});
	try {
		const custom = window.document.querySelector('#input_1');
		const native = window.document.querySelector('#input_2_1');
		assert.equal(window.document.querySelectorAll('.gfgeo-autocomplete-suggestions').length, 2);
		await suggest(window, custom);
		custom.parentElement.querySelector('li').click();
		await sleep(10);
		assert.equal(custom.value, '1150 Broadway, New York, NY, USA');
		await suggest(window, native);
		native.parentElement.querySelector('li').click();
		await sleep(10);
		assert.equal(native.value, '1150 Broadway');
		window.GFGeoAddressAutocomplete.initializeAutocomplete();
		await sleep(10);
		assert.equal(window.document.querySelectorAll('.gfgeo-autocomplete-suggestions').length, 2);
		assert.equal(state.alerts.length, 0);
	} finally { dom.window.close(); }
});

test('native submit and entry-save fallback synchronously clears unselected input and cancels submission', async () => {
	const {dom, window, state} = await fixture({admin: true});
	try {
		const input = window.document.querySelector('#input_1');
		const form = input.form;
		input.value = 'Unselected immediate-submit QA';
		input.dispatchEvent(new window.Event('blur'));
		assert.equal(form.dispatchEvent(new window.Event('submit', {bubbles: true, cancelable: true})), false);
		assert.equal(input.value, '');
		assert.equal(window.document.activeElement, input);
		assert.equal(state.alerts.length, 1);
		await sleep(220);
		assert.equal(state.alerts.length, 1); // Delayed blur does not alert again.
		assert.equal(form.dispatchEvent(new window.Event('submit', {bubbles: true, cancelable: true})), true);
	} finally { dom.window.close(); }
});

for (const method of ['postback', 'iframe', 'ajax']) {
	test(`${method} pre-submission rejects unselected custom/native addresses and allows a selected retry`, async () => {
		const {dom, window, state} = await fixture();
		try {
			const input = window.document.querySelector('#input_1_1');
			const native = window.document.querySelector('#input_1_2_1');
			const filter = state.filters.find(item => item.name === 'gform/submission/pre_submission').callback;
			const data = () => ({form: input.form, submissionType: 'submit', submissionMethod: method, abort: false});
			input.value = 'Immediate unselected custom';
			assert.equal((await filter(data())).abort, true);
			assert.equal(input.value, '');
			native.value = 'Immediate unselected native';
			assert.equal((await filter(data())).abort, true);
			assert.equal(native.value, '');
			assert.equal(state.alerts[1], 'Choose a native address.');
			await suggest(window, input);
			input.parentElement.querySelector('li').click();
			await sleep(10);
			assert.equal((await filter(data())).abort, false);
		} finally { dom.window.close(); }
	});
}

test('pre-submission waits for pending selected Place details without clearing or alerting', async () => {
	const {dom, window, state} = await fixture();
	try {
		const input = window.document.querySelector('#input_1_1');
		const filter = state.filters[0].callback;
		let release;
		state.delayDetails = new Promise(resolve => {release = resolve;});
		await suggest(window, input);
		input.parentElement.querySelector('li').click();
		let settled = false;
		const submission = filter({form: input.form, submissionType: 'submit', abort: false}).then(data => {settled = true; return data;});
		await sleep(80);
		assert.equal(settled, false);
		assert.equal(input.value, '1150 Broad');
		assert.equal(state.alerts.length, 0);
		release();
		assert.equal((await submission).abort, false);
		assert.equal(input.value, '1150 Broadway, New York, NY, USA');
		assert.equal(state.alerts.length, 0);
	} finally { dom.window.close(); }
});

test('submission respects hidden/disabled/saved values, form scope, Next, Previous and Save and Continue', async () => {
	const {dom, window, state} = await fixture();
	try {
		const input = window.document.querySelector('#input_1_1');
		const filter = state.filters[0].callback;
		const data = type => ({form: input.form, submissionType: type, abort: false});
		assert.equal((await filter(data('submit'))).abort, false);
		input.value = 'Unselected';
		assert.equal((await filter(data('previous'))).abort, false);
		assert.equal((await filter(data('save-continue'))).abort, false);
		input.parentElement.hidden = true;
		assert.equal((await filter(data('submit'))).abort, false);
		input.parentElement.hidden = false;
		input.disabled = true;
		assert.equal((await filter(data('submit'))).abort, false);
		input.disabled = false;
		const otherForm = window.document.createElement('form');
		window.document.body.append(otherForm);
		assert.equal((await filter({form: otherForm, submissionType: 'submit', abort: false})).abort, false);
		assert.equal((await filter(data('next'))).abort, true);
		assert.equal(input.value, '');
		assert.equal(state.alerts.length, 1);
	} finally { dom.window.close(); }
});

test('late Gravity Forms scripts register one submission filter across repeated renders', async () => {
	const {dom, window, state} = await fixture({lateHooks: true});
	try {
		window.document.dispatchEvent(new window.Event('gform/theme/scripts_loaded'));
		window.document.dispatchEvent(new window.Event('gform/postRender'));
		window.GFGeoAddressAutocomplete.initializeAutocomplete();
		assert.equal(state.filters.length, 1);
	} finally { dom.window.close(); }
});

test('dropdown styles survive a later Gravity Forms theme reset', async () => {
	const {dom, window} = await fixture();
	try {
		const document = window.document;
		const root = document.querySelector('#gform_wrapper_1');
		root.className = 'gform_wrapper gform-theme--framework';
		const style = document.createElement('style');
		style.textContent = `${dropdownCSS}\n.gform-theme--framework :where(*) { position: static; border: 0; box-shadow: none; text-align: left; }`;
		document.head.appendChild(style);
		const input = document.querySelector('#input_1_1');
		await suggest(window, input);
		const dropdown = input.parentElement.querySelector('.gfgeo-autocomplete-suggestions');
		// jsdom's cascade is source-order only; assert browser specificity explicitly.
		assert.match(dropdownCSS, /\.gfgeo-has-autocomplete \.gfgeo-autocomplete-suggestions\s*\{/);
		assert.match(dropdownCSS, /\.gfgeo-autocomplete-suggestions \.gfgeo-autocomplete-attribution\s*\{/);
		assert.equal(dropdown.querySelectorAll('li').length, 1);
		assert.equal(dropdown.querySelector('.gfgeo-autocomplete-attribution').textContent, 'Google Maps');
	} finally { dom.window.close(); }
});

test('leaving an unselected address clears it, alerts and refocuses; saved/empty/hidden values are retained', async () => {
	const {dom, window, state} = await fixture();
	try {
		const input = window.document.querySelector('#input_1_1');
		input.dispatchEvent(new window.Event('blur'));
		await sleep(220);
		assert.equal(input.value, 'Saved address');
		assert.equal(state.alerts.length, 0);
		await suggest(window, input);
		input.dispatchEvent(new window.Event('blur'));
		await sleep(220);
		assert.equal(input.value, '');
		assert.equal(state.alerts.length, 1);
		assert.equal(state.alerts[0], 'Please select an address from the suggested results.');
		assert.equal(window.document.activeElement, input);
		input.dispatchEvent(new window.Event('blur'));
		await sleep(220);
		assert.equal(state.alerts.length, 1); // No repeat alert for an empty field.
		await suggest(window, input);
		input.parentElement.hidden = true;
		input.dispatchEvent(new window.Event('blur'));
		await sleep(220);
		assert.equal(input.value, '1150 Broad');
		assert.equal(state.alerts.length, 1);
		input.parentElement.hidden = false;
		await suggest(window, input);
		input.parentElement.querySelector('li').click();
		await sleep(10);
		input.dispatchEvent(new window.Event('blur'));
		await sleep(220);
		assert.equal(input.value, '1150 Broadway, New York, NY, USA');
		assert.equal(state.alerts.length, 1);
	} finally { dom.window.close(); }
});

test('native Address uses its configured blur message; a slow clicked suggestion is not rejected', async () => {
	const {dom, window, state} = await fixture();
	try {
		const native = window.document.querySelector('#input_1_2_1');
		await suggest(window, native);
		native.dispatchEvent(new window.Event('blur'));
		await sleep(220);
		assert.equal(native.value, '');
		assert.deepEqual(state.alerts, ['Choose a native address.']);
		let release;
		state.delayDetails = new Promise(resolve => {release = resolve;});
		await suggest(window, native);
		native.parentElement.querySelector('li').click();
		native.dispatchEvent(new window.Event('blur'));
		await sleep(250);
		assert.equal(native.value, '1150 Broad');
		assert.equal(state.alerts.length, 1);
		release();
		await sleep(150);
		assert.equal(native.value, '1150 Broadway');
		assert.equal(state.alerts.length, 1);
		native.dispatchEvent(new window.Event('blur'));
		await sleep(220);
		assert.equal(state.alerts.length, 1);
	} finally { dom.window.close(); }
});

test('actual standalone bundle: keyboard selection, native parts, tokens and idempotent re-render', async () => {
	const {dom, window, state} = await fixture();
	try {
		const document = window.document;
		assert.equal(window.WPGEO, undefined);
		assert.equal(document.querySelectorAll('.gfgeo-autocomplete-suggestions').length, 2);
		window.GFGeoAddressAutocomplete.initializeAutocomplete();
		document.dispatchEvent(new window.Event('gform/postRender'));
		await sleep(10);
		assert.equal(document.querySelectorAll('.gfgeo-autocomplete-suggestions').length, 2);
		assert.equal(state.imports, 1);
		const input = document.querySelector('#input_1_1');
		assert.equal(input.checkValidity(), true); // Existing saved/default values are not invalidated.
		await suggest(window, input);
		assert.equal(input.checkValidity(), true); // Requirement is handled on blur, not native validity.
		assert.equal(input.parentElement.querySelector('.gfgeo-autocomplete-attribution').textContent, 'Google Maps');
		input.dispatchEvent(new window.KeyboardEvent('keydown', {key: 'ArrowDown', bubbles: true}));
		input.dispatchEvent(new window.KeyboardEvent('keydown', {key: 'Enter', bubbles: true, cancelable: true}));
		await sleep(10);
		assert.equal(input.value, '1150 Broadway, New York, NY, USA');
		assert.equal(input.checkValidity(), true);
		assert.deepEqual(Array.from(state.fields[0]), ['formattedAddress']);
		assert.equal(state.tokens, 3); // Two initial sessions, then a fresh token after selection.
		input.value = '';
		input.dispatchEvent(new window.Event('input', {bubbles: true}));
		assert.equal(input.checkValidity(), true);
		const native = document.querySelector('#input_1_2_1');
		await suggest(window, native);
		native.parentElement.querySelector('li').click();
		await sleep(10);
		assert.equal(native.value, '1150 Broadway');
		assert.equal(document.querySelector('.address_line_2 input').value, 'Unit 7');
		assert.equal(document.querySelector('.address_city input').value, 'New York');
		assert.equal(document.querySelector('.address_state select').value, 'NY');
		assert.equal(document.querySelector('.address_country select').value, 'US');
		assert.equal(document.querySelector('.address_zip input').value, '10001-1234');
		assert.deepEqual(Array.from(state.fields[1]), ['formattedAddress', 'addressComponents']);
		assert.equal(document.querySelectorAll('script[src*="maps.googleapis"]').length, 0);
	} finally { dom.window.close(); }
});

test('removed input cannot receive late Place details; inserted row gets its own binding', async () => {
	const {dom, window, state} = await fixture();
	try {
		const input = window.document.querySelector('#input_1_1');
		let release;
		state.delayDetails = new Promise(resolve => {release = resolve;});
		await suggest(window, input);
		input.parentElement.querySelector('li').click();
		const oldField = window.document.querySelector('#field_1_1');
		oldField.remove();
		release();
		await sleep(80);
		assert.equal(input.value, '1150 Broad');
		assert.equal(input.checkValidity(), true); // Teardown clears the free adapter's validity message.
		window.document.querySelector('form').insertAdjacentHTML('beforeend', '<div id="field_1_1_r2"><div><input id="input_1_1_r2"></div></div>');
		await sleep(80);
		assert.equal(window.document.querySelectorAll('.gfgeo-autocomplete-suggestions').length, 2);
		const row = window.document.querySelector('#input_1_1_r2');
		await suggest(window, row);
		row.parentElement.querySelector('li').click();
		await sleep(10);
		assert.equal(row.value, '1150 Broadway, New York, NY, USA');
	} finally { dom.window.close(); }
});

test('duplicate embeds of one form bind independently and tear down independently', async () => {
	const {dom, window, state} = await fixture();
	try {
		const first = window.document.querySelector('#gform_wrapper_1');
		const second = first.cloneNode(true);
		second.querySelectorAll('.gfgeo-autocomplete-suggestions').forEach(container => container.remove());
		window.document.body.append(second);
		await sleep(80);
		assert.equal(window.document.querySelectorAll('.gfgeo-autocomplete-suggestions').length, 4);
		const input = second.querySelector('#input_1_1');
		await suggest(window, input);
		input.parentElement.querySelector('li').click();
		await sleep(10);
		assert.equal(input.value, '1150 Broadway, New York, NY, USA');
		assert.equal(first.querySelector('#input_1_1').value, 'Saved address');
		assert.equal(state.requests.length, 1);
		second.remove();
		await sleep(80);
		assert.equal(window.document.querySelectorAll('.gfgeo-autocomplete-suggestions').length, 2);
	} finally { dom.window.close(); }
});
