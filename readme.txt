=== WPGeo Address Autocomplete for Gravity Forms ===
Contributors: ninjew
Tags: gravity forms, address autocomplete, google places, address autofill, address field
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Modern Google Places autocomplete for Gravity Forms, with country and language controls, required selection, and a dedicated Address field.

== Description ==

Add Google-powered address suggestions to Gravity Forms. Use the included single-line Address field or enable autocomplete on Gravity Forms' native Address field to help visitors enter addresses with less typing.

= Features =

* Dedicated single-line Address field.
* Autocomplete and autofill for the native Address field.
* Country restrictions: up to five countries per field.
* Result types: addresses, cities, establishments and more.
* Per-field suggestion language.
* Location bias by point and radius or rectangular bounds.
* Global language and region preferences.
* Required suggestion selection with a custom alert message.
* Support for AJAX forms, conditional fields and entry editing.
* Open saved single-line addresses in Google Maps from entry details.

= Two ways to collect an address =

The dedicated Address field collects a complete address in one input. Prefer separate address components? Enable autocomplete on the native Address field to fill available street, city, state/region, postal code and country values after a visitor selects a suggestion.

= Customize suggestions =

Choose countries, place types and language for each field. Prioritize suggestions near a point or within map bounds. Location bias and the global region preference influence results; country restrictions limit which countries can appear.

= Require a suggestion selection =

Optionally require visitors to choose an address suggestion. Manually typed, unselected text is cleared, and your custom alert prompts them to select a suggestion. This is not postal address validation or a deliverability guarantee.

= Google API requirements =

Requires Gravity Forms 2.10.3 or newer and your own Google browser API key with **Maps JavaScript API and Places API (New)** enabled. Configure Google Cloud billing and restrict the key to your website. Gravity Forms is sold separately. **Google API usage costs are not included with this plugin** and are billed through your Google Cloud account.

Address text entered into autocomplete is sent to Google to provide suggestions and details for the selected address.

[Google Maps Platform terms](https://cloud.google.com/maps-platform/terms) | [Google privacy policy](https://policies.google.com/privacy)

= Need more than autocomplete? =

**Gravity Geolocation** offers paid packages for interactive maps, geocoding, coordinates, current-location detection, directions, distance calculations, address validation and drawing tools. Feature availability depends on the package.

**Gravity Search** turns Gravity Forms entries into searchable directories and listings, with filters, proximity search and map displays.

These are separate plugins, not features unlocked inside this free plugin by entering a license.

[Explore packages and pricing](https://gravitygeolocation.com/pricing/) | [Try the live demos](https://demo.gravitygeolocation.com/) | [Read the documentation](https://gravitygeolocation.com/docs/)

== Installation ==

1. Install and activate Gravity Forms 2.10.3 or newer.
2. Install and activate this plugin. Deactivate any other Gravity Geolocation package first.
3. Enable Maps JavaScript API and Places API (New) in your Google Cloud project. Open Forms > Settings > Address Autocomplete and configure your browser API key.
4. Add a custom Address field, or enable autocomplete on a native Address field in its Address Autocomplete tab.
5. Save the form and test selecting a suggestion on the front end.

The overview is available under Forms > Address Autocomplete. If Gravity Forms is unavailable, the overview appears under Settings instead.

== Frequently Asked Questions ==

= Which fields can I use? =

Use the custom single-line Address field or Gravity Forms' native multi-part Address field. This plugin does not add autocomplete to arbitrary Text fields.

= Can I open a submitted address on a map? =

Yes. The dedicated single-line Address field includes an Open Map link on the entry detail page. It opens the saved address in Google Maps; it does not embed an interactive map or store a complete geocoding record.

= Does manual typing validate or geocode an address? =

No. Autocomplete uses suggestions selected by the visitor. Manual geocoding, address validation and complete geocoding records are not included. Google may not return every address component for every suggestion.

Need to verify and standardize addresses? See Gravity Geolocation's paid [Address Validation solution](https://gravitygeolocation.com/solutions/address-validation/).

= Can I limit suggestions? =

Yes. Field settings include countries, place types, proximity bias and rectangular area options. Bias prioritizes results; it is not address validation or proof of a service-area match. This plugin does not detect a visitor's current location or IP location.

= Can I use this alongside Gravity Geolocation or Gravity Search? =

Deactivate the free plugin when switching to Gravity Geolocation or Gravity Search. If both are active, the free plugin pauses its field, settings and dashboard integration and displays an administrator notice, regardless of plugin load order. Deactivation does not delete your Gravity Forms forms or their saved settings.

= Is a plugin account or license required? =

No WPGeo account or plugin license is required. You do need your own Google Cloud account and API key, as described above.

= Where can developers find the source code? =

Readable JavaScript/SCSS sources and build files are included in the plugin and available on [GitHub](https://github.com/Fitoussi/address-autocomplete-for-gravity-forms). To rebuild assets, install Node.js/npm and run `npm ci`, then `npm run build` (esbuild and Sass). These tools are not required to use the plugin.

Includes [Choices.js 11.1.0](https://github.com/Choices-js/Choices/tree/v11.1.0), MIT licensed; readable distribution: assets/js/editor/vendor/choices.js; license: assets/licenses/choices.txt.

== Screenshots ==

1. Google Places suggestions in the dedicated single-line Address field.
2. Automatically fill the native Address field's street, city, state/region, postal code and country after selecting a suggestion.
3. Configure required suggestion selection, a custom alert, result types, countries, language and location bias for the dedicated single-line Address field.
4. Enable and configure autocomplete for Gravity Forms' native multipart Address field in its Address Autocomplete tab.
5. Set your Google browser API key, default region and result language in the global settings.
6. View saved addresses in entry details and open their locations in Google Maps. The single-line field adds an Open Map link; the native Address field retains its Map It link.
7. Explore included autocomplete features and other Gravity Geolocation packages from the locally bundled overview.

== Changelog ==

= 1.0.0 =
* Updated the public name to WPGeo Address Autocomplete for Gravity Forms.
* Documented the public repository, bundled readable sources, build steps and third-party library.
* Removed unrelated Search promotional entry points and notices from Gravity Forms pages.
* Removed disabled premium field buttons and their upgrade dialogs; the local overview remains available.
* Added a one-time administrator setup notice with permanent dismissal and no activation redirect.
* Initial standalone release with custom and native Address autocomplete.
* Included Google Places API (New) support with a temporary legacy compatibility fallback.
* Added per-field country, place-type, language and location bias controls, plus global language and region settings.
* Added required suggestion selection with a custom clear-and-alert message and submission checks.
* Included the single-line Address field's entry-detail map link and autocomplete during entry editing.
* Included local overview, comparison, help and product information.
