# Address Autocomplete for Gravity Forms

This standalone free plugin adds Google Places suggestions to its single-line Address field and Gravity Forms' native Address field. It uses its own autocomplete adapter and local admin pages, without WPGeo Framework, account connections, licensing, migrations, or premium location services.

## Source organization

`src/Core/AddOn.php` connects the plugin to Gravity Forms. It registers the explicit field list through `src/Common/Form/Fields/Registrar.php`, starts the editor directly, and starts the frontend processor. The registrar also positions the single plugin field group.

`src/Features/Form/Fields/Address/` contains the Address renderer and editor settings. The four classes in `Fields/Placeholders/` expose promotional editor buttons only; they do not create functioning premium fields or save their submitted values.

`src/Features/Form/Admin/FormEditor.php` owns the Autocomplete tab, tooltips, and new-field defaults. `src/Features/Form/Runtime/FormGeoProcessor.php` collects supported Address options and enqueues the standalone assets without modifying saved form data.

`src/Admin/` contains global settings, the product overview, feature comparisons, and unavailable Search entry points. Reference countries and languages are bundled in `src/Helpers/ReferenceData.php`.

`assets/js/autocomplete/` separates Google loading, autocomplete requests, saved options, and native Address population. `assets/js/editor/` contains builder controls and unavailable-field dialogs. Readable JavaScript and SCSS are included in installable packages alongside generated assets.

## Compatibility boundaries

Keep `gfgeo_address`, saved `gfgeo_*` properties, the native Add-On settings slug `gravityforms_geolocation`, and existing PHP namespaces unchanged. Settings saves preserve hidden premium values instead of erasing them. Unsupported locator, IP, Geocoder, and Dynamic options are not sent to the frontend.

The early conflict guard retains the first loaded Gravity Geolocation package. Do not activate the free plugin alongside Gravity Geolocation or Gravity Search. Deactivate one before activating the other; existing Gravity Forms forms and settings remain stored.

## Local development

Install the Node development dependencies with `npm ci`, then build assets with `npm run build`. The plugin runtime does not require Composer dependencies.

Run `npm test` for PHP identity, wiring and package checks plus JavaScript autocomplete and editor tests. These use fixtures and do not replace installed WordPress browser acceptance. PHP-only checks also run through `composer test`.

Code-quality tooling remains separate in `tools/`. Run its Composer `phpcs`, `phpcbf`, and `phpcompat` scripts from that directory. The comprehensive formatting and documentation pass is a separate task from the structure cleanup.

## Packaging

Run `npm run package` to build assets and create a versioned ZIP in `dist/`. To use a fresh output directory, run `npm run build`, then `php tools/package/build.php /path/to/output`.

The builder uses an explicit allowlist, preserves the source identity, refuses to overwrite an existing artifact, and prints a checksum. It excludes tests, private docs, editor configuration, Composer tooling, and dependencies. It does not upload, install, activate, or publish the package.

Maintain the public description, setup instructions and release notes in the root `readme.txt`; keep the plain-text history in `change_log.txt`. Match the readme stable tag to the plugin version before packaging.

## Dashboard screenshots

Use locally bundled screenshots from our own demos for feature cards. Prioritize autocomplete, interactive maps, dynamic fields, directions, nearby locations, validation, drawing tools, entry maps, and search. Crop to the relevant interface, use representative data, and omit credentials and personal information.

Six approved real captures are bundled in `assets/screenshots/screenshot-1.png` through `screenshot-6.png`, with corresponding captions in `readme.txt`. Capture provenance is recorded in `docs/screenshots.txt`. Preserve the originals without stretching, compositing, or exposing credentials.

Overview screenshot placeholders are hidden until real feature-card images are wired in. The existing SVG template illustrations can support diagrams and decorative previews, but should not be presented as actual product screenshots. WordPress.org listing assets must later be copied into the separate top-level SVN `assets/` directory; nothing is uploaded by this repository's builder.

## Before release

Complete source formatting and documentation, review translations and public metadata, replace screenshot placeholders, run Plugin Check, and verify a fresh ZIP on a test site. Cover custom and native Address suggestions, required selection on blur, conditional and AJAX rendering, entry editing, country population, and package activation order. Publishing requires a separate approval.
