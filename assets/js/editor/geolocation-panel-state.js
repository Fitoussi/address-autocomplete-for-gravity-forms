/**
 * @file geolocation-panel-state.js
 * @description Keeps the Autocomplete accordion state consistent using its retained tab identity.
 */

/**
 * Whether an editor accordion panel is currently hidden.
 *
 * Gravity Forms can leave aria-expanded="true" on a toggle while hiding its
 * panel during a field-settings rebuild. Inline style and ARIA state are used
 * here because they remain available in both the live editor and unit tests.
 *
 * @param {HTMLElement|null} panel Accordion panel.
 * @returns {boolean}
 */
export function isEditorPanelHidden(panel) {
	if (!panel) {
		return true;
	}

	return true === panel.hidden
		|| 'true' === panel.getAttribute?.('aria-hidden')
		|| /(?:^|;)\s*display\s*:\s*none(?:\s*!important)?\s*(?:;|$)/i.test(
			String(panel.getAttribute?.('style') || '')
		);
}

/**
 * Restore an intended open Geolocation accordion through its native toggle.
 *
 * When Gravity Forms marks the toggle as expanded while the panel is hidden,
 * the first native click only normalizes the stale expanded state. A second
 * click then opens the panel. Keeping that repair in one helper prevents
 * field creation and ordinary field switches from implementing it differently.
 *
 * @param {Document|HTMLElement} root Editor root.
 * @param {boolean} shouldBeOpen Whether the panel should be open.
 * @returns {boolean} Whether the toggle was activated.
 */
export function restoreGeolocationPanelState(root = document, shouldBeOpen = false) {
	const documentObject = 9 === root?.nodeType ? root : root?.ownerDocument;
	const tabToggle = documentObject?.getElementById?.('gfgeo_geolocation_tab_toggle');
	const tabPanel = documentObject?.getElementById?.('gfgeo_geolocation_tab');

	if (!tabToggle || !shouldBeOpen) {
		return false;
	}

	const isExpanded = 'true' === tabToggle.getAttribute?.('aria-expanded');
	const panelHidden = isEditorPanelHidden(tabPanel);

	if (isExpanded && panelHidden) {
		tabToggle.click?.();
		tabToggle.click?.();
		return true;
	}

	if (!isExpanded) {
		tabToggle.click?.();
		return true;
	}

	return false;
}
