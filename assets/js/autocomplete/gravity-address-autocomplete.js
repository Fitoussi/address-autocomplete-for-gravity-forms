/**
 * Standalone Gravity Forms autocomplete adapter.
 *
 * Owns custom/native input bindings and AJAX/DOM replacement teardown only.
 * It never creates a FormCore, Geocoder, map or dynamic-field connection.
 */
import {AddressAutocomplete} from './services/address-autocomplete.js';
import {buildAddressOptions, isEnabled} from './address-options.js';
import {populateNativeAddress} from './native-address.js';
import {loadGooglePlaces} from './google-places-loader.js';

const bindings = new Map();
const pending = new WeakSet();
let submissionFilterRegistered = false;

/**
 * Validate only this physical form's visible, bound Address inputs.
 *
 * @since 1.0.0
 *
 * @param {HTMLFormElement} form Form being submitted.
 * @returns {boolean} Whether submission may proceed.
 */
function validateFormSelection(form) {
	for (const [input, binding] of bindings) {
		if (input.form === form && !binding.enforceSelection(true)) {
			return false;
		}
	}
	return true;
}

/**
 * Guard Gravity Forms before either AJAX or postback serialization.
 * Allow Previous and Save and Continue to retain partially completed values.
 *
 * @since 1.0.0
 *
 * @returns {void}
 */
function registerSubmissionFilter() {
	if (submissionFilterRegistered || !globalThis.gform?.utils?.addAsyncFilter) {
		return;
	}
	globalThis.gform.utils.addAsyncFilter('gform/submission/pre_submission', async data => {
		if (data.abort || !['submit', 'next'].includes(data.submissionType)) {
			return data;
		}
		// Wait briefly for a clicked prediction's details rather than clearing it.
		const deadline = Date.now() + 5000;
		while (Array.from(bindings).some(([input, binding]) =>
			input.form === data.form && isActiveInput(input) && binding.isSelectionPending()
		) && Date.now() < deadline) {
			await new Promise(resolve => setTimeout(resolve, 50));
		}
		data.abort = !validateFormSelection(data.form);
		return data;
	});
	submissionFilterRegistered = true;
}

/**
 * Check whether selection enforcement should apply to a connected input.
 *
 * @since 1.0.0
 *
 * @param {HTMLInputElement} input Bound Address input.
 * @returns {boolean} Whether the input is enabled and visibly available.
 */
function isActiveInput(input) {
	if (input.disabled || !input.isConnected) {
		return false;
	}
	for (let element = input; element && element !== input.form; element = element.parentElement) {
		const style = getComputedStyle(element);
		if (element.hidden || style.display === 'none' || style.visibility === 'hidden') {
			return false;
		}
	}
	return true;
}

/**
 * Attach one service per physical input and tear down obsolete render bindings.
 *
 * @since 1.0.0
 *
 * @param {HTMLInputElement} input Address input receiving suggestions.
 * @param {HTMLElement} wrapper Scoped native or custom field container.
 * @param {Object} field Whitelisted saved field options.
 * @param {Object} config Browser API and localization configuration.
 * @returns {Promise<void>} Resolves after initialization or a recoverable failure.
 */
async function bindInput(input, wrapper, field, config) {
	const signature = JSON.stringify({field, config});
	const previous = bindings.get(input);
	if ((previous?.signature === signature && previous.container.isConnected) || pending.has(input)) {
		return;
	}
	previous?.destroy();
	pending.add(input);
	try {
		await loadGooglePlaces(config);
		if (!input.isConnected) {
			return;
		}
		const service = new AddressAutocomplete({
			inputElement: input,
			prefix: 'gfgeo',
			// Native Address needs components; neither field needs coordinates.
			fetchFields: field.type === 'address' ? ['formattedAddress', 'addressComponents'] : ['formattedAddress'],
			debounceDelay: 200,
		}, buildAddressOptions(field, config));

		let selectedValue = input.value; // Preserve saved/default values on first load.
		let blurTimer;
		const selected = event => {
			clearTimeout(blurTimer);
			if (field.type === 'address') {
				populateNativeAddress(wrapper, event.detail.place);
			}
			selectedValue = input.value;
		};
		const enforceSelection = (submitting = false) => {
			clearTimeout(blurTimer);
			if (!isActiveInput(input) || !isEnabled(field.gfgeo_force_autocomplete_selection)
				|| !input.value.trim() || input.value === selectedValue) {
				return true;
			}
			// A clicked suggestion may still be fetching its Place details.
			if (service.pendingSelections > 0) {
				if (!submitting) {
					blurTimer = setTimeout(enforceSelection, 100);
				}
				return false;
			}
			input.value = '';
			selectedValue = '';
			service.resetInteractionState();
			service.clearSuggestions();
			input.dispatchEvent(new Event('input', {bubbles: true}));
			input.dispatchEvent(new Event('change', {bubbles: true}));
			globalThis.alert(field.gfgeo_force_autocomplete_selection_message || 'Please select an address from the suggested results.');
			input.focus();
			return false;
		};
		const blurred = () => {
			clearTimeout(blurTimer);
			blurTimer = setTimeout(enforceSelection, 200);
		};
		// Returning to the input cancels enforcement for its earlier blur.
		const focused = () => clearTimeout(blurTimer);
		// Keep focus in the input while the user clicks a dropdown suggestion.
		const choosing = event => {
			if (!event.target.closest('li')) {
				return;
			}
			event.preventDefault();
			clearTimeout(blurTimer);
		};
		input.addEventListener('blur', blurred);
		input.addEventListener('focus', focused);
		input.addEventListener('place_changed', selected);
		service.container.addEventListener('mousedown', choosing);
		bindings.set(input, {
			signature,
			container: service.container,
			enforceSelection,
			isSelectionPending: () => service.pendingSelections > 0,
			destroy() {
				clearTimeout(blurTimer);
				service.container?.removeEventListener('mousedown', choosing);
				service.destroy();
				input.removeEventListener('blur', blurred);
				input.removeEventListener('focus', focused);
				input.removeEventListener('place_changed', selected);
				bindings.delete(input);
			},
		});
	} catch (error) {
		// Leave the normal Address input usable when the API is unavailable.
		console.warn('[Address Autocomplete] Initialization failed:', error.message);
	} finally {
		pending.delete(input);
	}
}

/**
 * Initialize each physical form/input instance from the PHP settings whitelist.
 * Scoped field queries support duplicate form embeds without a global ID lookup.
 *
 * @since 1.0.0
 *
 * @returns {void}
 */
export function initializeAutocomplete() {
	registerSubmissionFilter();
	for (const [input, binding] of bindings) {
		if (!input.isConnected) {
			binding.destroy();
		}
	}
	for (const data of Object.values(globalThis.gfgeoAutocompleteForms || {})) {
		const roots = data.config.isAdmin
			? document.querySelectorAll('#entry_form')
			: document.querySelectorAll(`[id="gform_wrapper_${data.formId}"]`);
		for (const root of roots) {
			for (const field of data.fields) {
				if (!isEnabled(field.gfgeo_address_autocomplete)) {
					continue;
				}
				const id = `${data.formId}_${field.id}`;
				const wrappers = root.querySelectorAll(`[id="field_${id}"], [id^="field_${id}_r"]`);
				for (const wrapper of wrappers) {
					const input = field.type === 'address'
						? wrapper.querySelector('.address_line_1 input')
						: wrapper.querySelector(`input[id="input_${id}"], input[id^="input_${id}_r"]`);
					if (input && !input.disabled) {
						bindInput(input, wrapper, field, data.config);
					}
				}
				// Entry inputs omit the form ID even when their wrappers include it.
				if (data.config.isAdmin) {
					const input = root.querySelector(`[id="input_${field.id}${field.type === 'address' ? '_1' : ''}"]`);
					if (input && !input.disabled) {
						bindInput(input, input.closest('.gfield') || input.parentElement, field, data.config);
					}
				}
			}
		}
	}
}

/**
 * Initialize autocomplete and register native render and DOM-replacement listeners.
 *
 * @since 1.0.0
 *
 * @returns {void}
 */
function boot() {
	initializeAutocomplete();
	document.addEventListener('gform/theme/scripts_loaded', registerSubmissionFilter);
	// Native/admin submission fallback; modern GF also validates via its hook.
	document.addEventListener('submit', event => {
		const submitter = event.submitter;
		if (submitter?.dataset.submissionType === 'previous'
			|| submitter?.id?.startsWith('gform_previous_button_')
			|| submitter?.dataset.submissionType === 'save-continue') {
			return;
		}
		if (!validateFormSelection(event.target)) {
			event.preventDefault();
			event.stopImmediatePropagation();
		}
	}, true);
	document.addEventListener('gform/postRender', initializeAutocomplete);
	globalThis.jQuery?.(document).on('gform_post_render.gfgeoac', initializeAutocomplete);
	let refresh;
	const observer = new MutationObserver(records => {
		const inputsChanged = records.some(record => Array.from(record.addedNodes).some(node =>
			node.nodeType === 1 && (node.matches('input') || node.querySelector('input'))
		)) || Array.from(bindings.keys()).some(input => !input.isConnected);
		if (inputsChanged) {
			clearTimeout(refresh);
			refresh = setTimeout(initializeAutocomplete, 50);
		}
	});
	observer.observe(document.body, {childList: true, subtree: true});
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', boot, {once: true});
} else {
	boot();
}
