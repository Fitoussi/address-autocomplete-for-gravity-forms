# WPGeo Address Autocomplete for Gravity Forms

## Standalone bootstrap convention

Use this convention for these three plugins and future standalone autocomplete plugins:

- Give free its own constant prefix and PHP namespace: GFGEOAC / GravityGeolocationAutocomplete,
  NFGEOAC / NinjaGeolocationAutocomplete, or FRMGEOAC / FormidableGeolocationAutocomplete.
  Never define aliases using premium's constants or classes.
- Register the guard on plugins_loaded, after all active plugin files have loaded.
  Check the corresponding premium version constant before registering any dashboard,
  field, settings or runtime integration. Premium wins regardless of file load order.
- Gravity runs the guard at priority 5, then registers its Add-On on gform_loaded at
  priority 8; Gravity Forms initializes Add-Ons during plugins_loaded at priority 10.
  Ninja/Formidable check at priority 20. Ninja constructs translated fields on init
  at priority 1, before its host's priority-5 field list. Match host lifecycle, not
  arbitrary identical hook numbers.
- On conflict, free registers only capability-gated site/network admin notices.
  Do not auto-deactivate plugins, redirect, delete data or change saved preferences.
  Premium being active but unable to run does not permit free to claim its field types.
- Preserve saved field types, field option names, global setting keys, native settings
  slugs, capabilities and existing integration hooks. These are data/API contracts;
  PHP bootstrap identities are not shared storage. Third-party PHP integrations must
  use the intended plugin's namespace/constants explicitly.
- Use short arrays in first-party production PHP. Tool/test formatting is independent.
- Test free alone and both premium/free load orders in separate processes; test
  notices with and without administrator capability. Run installed acceptance too.


This standalone free plugin adds Google Places suggestions to its single-line Address field and Gravity Forms' native Address field. It uses its own autocomplete adapter and local admin pages, without WPGeo Framework, account connections, licensing, migrations, or premium location services.

## Source organization

`src/Core/AddOn.php` connects the plugin to Gravity Forms. It registers the explicit field list through `src/Common/Form/Fields/Registrar.php`, starts the editor directly, and starts the frontend processor. The registrar also positions the single plugin field group.

`src/Features/Form/Fields/Address/` contains the functional Address renderer and editor settings. Only this custom field is registered; native Address autocomplete remains supported. The four disabled premium field buttons, placeholder classes and upgrade-dialog code were permanently removed by the owner on October 9. Keep product discovery on the local overview instead.

`src/Features/Form/Admin/FormEditor.php` owns the Address Autocomplete tab, tooltips, and new-field defaults. `src/Features/Form/Runtime/FormGeoProcessor.php` collects supported Address options and enqueues the standalone assets without modifying saved form data.

`src/Admin/` contains global settings, the product overview, feature comparisons and dismissible setup guidance. Unrelated Search toolbar/buttons/notices and their source/generated assets were removed in response to the October 9 review. Reference countries and languages are bundled in `src/Helpers/ReferenceData.php`.

`assets/js/autocomplete/` separates Google loading, autocomplete requests, saved options, and native Address population. `assets/js/editor/` contains functional autocomplete builder controls, without disabled-field promotions or upgrade dialogs. Readable JavaScript and SCSS are included in installable packages alongside generated assets.

## Compatibility boundaries

Keep `gfgeo_address`, saved `gfgeo_*` properties and the native Add-On settings slug `gravityforms_geolocation` unchanged. Free uses isolated GFGEOAC constants and the GravityGeolocationAutocomplete namespace. Settings saves preserve hidden premium values instead of erasing them. Unsupported locator, IP, Geocoder, and Dynamic options are not sent to the frontend.

The deferred guard yields to Gravity Geolocation or Gravity Search in either file load order. Free does not register overlapping fields, settings or product pages while premium is active. Deactivate free after switching to premium; existing Gravity Forms forms and settings remain stored. No automatic deactivation or migration is performed.

## Activation setup notice

First site activation records a setup notice in the plugin's own option. It appears
across site admin pages for administrators until permanently dismissed or a browser
API key is configured. It never redirects on activation. Reactivation does not reset
dismissed or completed guidance, and existing active installs are not enrolled by
an update alone. Network activation, missing/outdated hosts and premium conflicts
do not display setup guidance. The explicit Dismiss action is a capability-checked,
nonce-protected POST that returns to the same admin page. Shared host settings and
premium options are not changed. Plugin-row Settings and Overview links remain.

## Local development

Install the Node development dependencies with `npm ci`, then build assets with `npm run build`. The plugin runtime does not require Composer dependencies.

Run `npm test` for PHP identity, wiring and package checks plus JavaScript autocomplete and editor tests. These use fixtures and do not replace installed WordPress browser acceptance. PHP-only checks also run through `composer test`.

Code-quality tooling remains separate in `tools/`. Run its Composer `phpcs`, `phpcbf`, and `phpcompat` scripts from that directory. The comprehensive formatting and documentation pass is a separate task from the structure cleanup.

## Packaging

Run `npm run package` to build assets and create a versioned ZIP in `dist/`. To use a fresh output directory, run `npm run build`, then `php tools/package/build.php /path/to/output`.

The builder uses an explicit allowlist, preserves the source identity, refuses to overwrite an existing artifact, and prints a checksum. It excludes tests, private docs, editor configuration, Composer tooling, and dependencies. It does not upload, install, activate, or publish the package.

The October 9 review requested a distinctive name. The owner chose
WPGeo Address Autocomplete for Gravity Forms and the requested new slug is
`wpgeo-address-autocomplete-for-gravity-forms`. This slug is prepared locally,
NOT yet reserved/confirmed by the reviewer. Use it for the corrected ZIP root,
translation domain and bundled catalogs, and explicitly request it in the reply
to the existing review email. Upload through the existing submission; do not
create a duplicate. A domain warning against the old reserved slug can remain
until WordPress.org changes the reservation.
The local checkout/GitHub name and main PHP filename remain unchanged, as do the
native settings slug and saved field identities. GFGEOAC_PLUGIN_NAME uses the full
public name; compact menu and field labels remain Address Autocomplete.
Run Plugin Check on the extracted assigned-slug folder, explicitly supplying the
same `--slug` value; checking only the old local folder can hide domain mismatches.

The GitHub repository remains public at
https://github.com/Fitoussi/address-autocomplete-for-gravity-forms and its name is
unchanged. The readme documents bundle/source mappings, locked npm build steps
and Choices.js version/source/license. Sources/build inputs also ship in the ZIP.
Contributors uses the submitting account `ninjew`; the PHP author remains Eyal
Fitoussi. Keep the local overview, but do not restore disabled premium field
buttons, their upgrade dialogs or unrelated Search advertising on normal Gravity
Forms pages. No review reply,
submission upload or repository visibility change is performed by the builder.

Maintain the public description, setup instructions and release notes in the root `readme.txt`; keep the plain-text history in `change_log.txt`. Match the readme stable tag to the plugin version before packaging.

Keep `Tested up to` only in `readme.txt`. The WordPress.org upload checker rejects
it in the main PHP header; the package builder and fixture enforce that separation.

## Dashboard screenshots

Use locally bundled screenshots from our own demos for feature cards. Prioritize autocomplete, interactive maps, dynamic fields, directions, nearby locations, validation, drawing tools, entry maps, and search. Crop to the relevant interface, use representative data, and omit credentials and personal information.

Seven approved real captures are bundled in `assets/screenshots/screenshot-1.png` through `screenshot-7.png`, with corresponding captions in `readme.txt`. Separate editor captures show the dedicated single-line and native multipart Address options. Capture provenance is recorded in `docs/screenshots.txt`. Preserve the originals without stretching, compositing, or exposing credentials.

Overview screenshot placeholders are hidden until real feature-card images are wired in. The existing SVG template illustrations can support diagrams and decorative previews, but should not be presented as actual product screenshots. WordPress.org listing assets must later be copied into the separate top-level SVN `assets/` directory; nothing is uploaded by this repository's builder.

## Before release

Complete source formatting and documentation, review translations and public metadata, replace screenshot placeholders, run Plugin Check, and verify a fresh ZIP on a test site. Cover custom and native Address suggestions, required selection on blur, conditional and AJAX rendering, entry editing, country population, and package activation order. Publishing requires a separate approval.
