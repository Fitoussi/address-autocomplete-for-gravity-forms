/**
 * Native Gravity Forms dialogs for standalone promotional field buttons.
 * Uses Gravity Forms' native dialog; it does not determine field availability.
 */

/**
 * Escape plain text before placing it in Gravity's HTML dialog content.
 *
 * @param {string} value Text to display.
 * @returns {string} Safe HTML text.
 */
function escapeHtml(value) {
	return String(value).replace(/[&<>"']/g, character => ({
		'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;',
	})[character]);
}

/**
 * Open the existing unavailable-field upsell using the native Gravity dialog.
 *
 * @param {string} fieldName Localized title of the unavailable field.
 * @param {Object} [options={}] Native dialog and localization dependencies.
 * @param {Object} [options.gformInstance=globalThis.gform] Gravity Forms API.
 * @param {Object} [options.strings] Gravity's localized unavailable-field copy.
 * @returns {boolean} Whether the native dialog was opened.
 * @throws {Error} When the editor localization is missing.
 */
export function showUnavailableFieldDialog(fieldName, {
	gformInstance = globalThis.gform,
	strings = globalThis.gfgeoFormEditorStrings?.unavailableField,
} = {}) {
	if (typeof gformInstance?.instances?.dialogAlert !== 'function') {
		return false;
	}
	if (!strings?.title || !strings.message || !strings.upgradeNow || !strings.unlockFeatures) {
		throw new Error('Gravity Geolocation unavailable-field messages were not localized.');
	}
	const name = escapeHtml(fieldName);
	const title = escapeHtml(strings.title).replace('%s', name);
	const content = escapeHtml(strings.message).replace('%s', name)
		+ ` <a href="https://gravitygeolocation.com/pricing/" target="_blank" rel="noopener noreferrer">${escapeHtml(strings.upgradeNow)}</a> `
		+ escapeHtml(strings.unlockFeatures);
	gformInstance.instances.dialogAlert(title, content);
	return true;
}
