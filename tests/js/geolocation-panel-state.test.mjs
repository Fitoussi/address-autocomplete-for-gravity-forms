import test from 'node:test';
import assert from 'node:assert/strict';

import {
	isEditorPanelHidden,
	restoreGeolocationPanelState,
} from '../../assets/js/editor/geolocation-panel-state.js';

function createAccordion({ expanded = false, hidden = true } = {}) {
	const toggle = {
		attributes: { 'aria-expanded': expanded ? 'true' : 'false' },
		clickCount: 0,
		getAttribute(name) {
			return this.attributes[name] ?? null;
		},
		click() {
			this.clickCount += 1;
			const nextExpanded = 'true' !== this.attributes['aria-expanded'];
			this.attributes['aria-expanded'] = nextExpanded ? 'true' : 'false';
			panel.hidden = !nextExpanded;
		},
	};
	const panel = {
		hidden,
		attributes: {},
		getAttribute(name) {
			return this.attributes[name] ?? null;
		},
	};
	const elements = {
		gfgeo_geolocation_tab_toggle: toggle,
		gfgeo_geolocation_tab: panel,
	};
	const root = {
		nodeType: 9,
		getElementById(id) {
			return elements[id] || null;
		},
	};

	return { root, toggle, panel };
}

test('detects hidden editor panels from native, ARIA, and inline states', () => {
	assert.equal(isEditorPanelHidden({ hidden: true, getAttribute: () => null }), true);
	assert.equal(isEditorPanelHidden({
		hidden: false,
		getAttribute: name => 'aria-hidden' === name ? 'true' : null,
	}), true);
	assert.equal(isEditorPanelHidden({
		hidden: false,
		getAttribute: name => 'style' === name ? 'display: none !important;' : null,
	}), true);
	assert.equal(isEditorPanelHidden({ hidden: false, getAttribute: () => null }), false);
});

test('repairs stale expanded state and opens the Geolocation panel', () => {
	const { root, toggle, panel } = createAccordion({ expanded: true, hidden: true });

	assert.equal(restoreGeolocationPanelState(root, true), true);
	assert.equal(toggle.clickCount, 2);
	assert.equal(toggle.getAttribute('aria-expanded'), 'true');
	assert.equal(panel.hidden, false);
});

test('opens a collapsed panel once and leaves a visible expanded panel alone', () => {
	const collapsed = createAccordion({ expanded: false, hidden: true });
	assert.equal(restoreGeolocationPanelState(collapsed.root, true), true);
	assert.equal(collapsed.toggle.clickCount, 1);
	assert.equal(collapsed.panel.hidden, false);

	const open = createAccordion({ expanded: true, hidden: false });
	assert.equal(restoreGeolocationPanelState(open.root, true), false);
	assert.equal(open.toggle.clickCount, 0);
});
