=== Advanced Address Autocomplete for Gravity Forms ===
Contributors: eyalfitoussi
Tags: gravity forms, address autocomplete, google places, address autofill, address field
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Modern Google Places autocomplete for Gravity Forms, with country and language controls, required selection, and a dedicated Address field.

== Description ==

**Less typing. More control over address entry.**

Advanced Address Autocomplete for Gravity Forms brings **Google Places API (New) to your forms for free**. Help visitors find and select an address, automatically fill native address components, and tailor suggestions to your audience with country, place-type, language and location preferences.

Choose a dedicated single-line Address field or add autocomplete to Gravity Forms' existing multi-part Address field. Require visitors to select a suggestion when your workflow needs it, and open a submitted single-line address in Google Maps directly from its entry detail page.

Built by the creator of Gravity Geolocation and GEO my WP, this focused, standalone plugin includes the autocomplete controls below without a paid plugin upgrade or account connection.

= Google Places API (New), included free =

Autocomplete uses Google's modern Places API by default, including its current suggestion and place-details services. With Places API (New) enabled for your key, the normal autocomplete flow does not use the legacy autocomplete services that trigger Google's deprecation warnings.

= Two ways to collect an address =

* **Dedicated single-line Address field:** a purpose-built field for a complete address in one input, with an Open Map link on the saved entry's detail page.
* **Native Address autofill:** enable autocomplete on Gravity Forms' multi-part Address field to populate available street, city, state/region, postal code and country components after a suggestion is selected.
* **Existing forms and entry editing:** enable autocomplete on supported Address fields in your forms, including AJAX and conditionally displayed fields, and use suggestions when editing an entry.

= Customize suggestions for each field =

* **Country restrictions:** allow suggestions from up to five selected countries per field.
* **Result types:** choose the supported place types that suit your form, such as addresses, cities or establishments.
* **Field language:** choose a suggestion language for an individual field, overriding the global language setting when needed.
* **Location bias:** prioritize suggestions near a configured point and radius, or within rectangular map bounds.

= Set global language and region preferences =

Set your browser API key, default result language and region code in the plugin settings. The region preference influences regional results; per-field country restrictions control which countries can appear in suggestions.

= Require a selection, not just typed text =

Enable **Require address selection from suggestions** when visitors should choose a Google suggestion. Newly typed, unselected text is cleared when the visitor leaves the input, and your custom alert explains what to do. Submission checks also prevent unselected text from slipping through a configured autocomplete field.

This is a suggestion-selection requirement, not postal address validation or a deliverability guarantee.

= Requirements =

Gravity Forms 2.10.3 or newer and your own Google browser API key are required. Enable Maps JavaScript API and Places API (New) for your Google Cloud project, configure billing, and restrict the key to your website. Gravity Forms is a separate commercial plugin; Google API usage is billed separately through your Google Cloud account.

This plugin does not require a Gravity Geolocation license, a WPGeo account, or WPGeo Framework.

= Need more than autocomplete? =

**Take your forms beyond address entry with Gravity Geolocation.** Its paid packages add tools for richer location workflows:

* **Collect and connect location data:** interactive maps, Geocoder and Coordinates fields, current-location detection, and Dynamic Location Data in standard Gravity Forms fields.
* **Calculate routes and travel:** directions, route displays, waypoints, distance and travel time, including values for pricing and calculations.
* **Build advanced location workflows:** nearby-location results, address validation, drawing tools and saved map shapes, plus entry maps.
* **Search and display entries:** Gravity Search turns Gravity Forms entries into searchable directories, listings and customer portals, with location filters and map displays.

Feature availability depends on the paid package. These are separate installations, not features unlocked inside this free plugin by entering a license. The free plugin includes a local overview and package comparison so you can explore the right fit without connecting an account.

[Explore packages and pricing](https://gravitygeolocation.com/pricing/) | [Try the live demos](https://demo.gravitygeolocation.com/) | [Read the documentation](https://gravitygeolocation.com/docs/)

== External services ==

= Google Maps Platform =

Autocomplete loads Google Maps JavaScript resources and uses Google Places suggestions and place details. Address text, selected place identifiers and request options are sent to Google using the site owner's browser API key. Configure the required APIs, billing and website restrictions using the setup guide before enabling autocomplete.

[Google Maps Platform terms](https://cloud.google.com/maps-platform/terms) | [Google privacy policy](https://policies.google.com/privacy)

The plugin overview and More Plugins information are bundled locally. They do not fetch a remote catalog, connect an account, or download or install other plugins. Clicking an external documentation, demo or product link opens that website.

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

= Is Places API (New) a premium feature? =

No. Places API (New) support is included free and used by default. Enable it for your Google API key to use the modern autocomplete flow. A temporary legacy compatibility fallback may run if the new API is unavailable, so the plugin cannot promise a warning-free console for every API configuration.

= Can I open a submitted address on a map? =

Yes. The dedicated single-line Address field includes an Open Map link on the entry detail page. It opens the saved address in Google Maps; it does not embed an interactive map or store a complete geocoding record.

= Does manual typing validate or geocode an address? =

No. Autocomplete uses suggestions selected by the visitor. Manual geocoding, address validation and complete geocoding records are not included. Google may not return every address component for every suggestion.

= Can I limit suggestions? =

Yes. Field settings include countries, place types, proximity bias and rectangular area options. Bias prioritizes results; it is not address validation or proof of a service-area match. This plugin does not detect a visitor's current location or IP location.

= Can I use this alongside Gravity Geolocation or Gravity Search? =

Deactivate the free plugin when switching to Gravity Geolocation or Gravity Search. If both are active, the free plugin pauses its field, settings and dashboard integration and displays an administrator notice, regardless of plugin load order. Deactivation does not delete your Gravity Forms forms or their saved settings.

= Is an account or license required? =

No. This plugin has no account connection, license activation, paid updater or remote product installation flow.

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
* Added a one-time administrator setup notice with permanent dismissal and no activation redirect.
* Initial standalone release with custom and native Address autocomplete.
* Included Google Places API (New) support with a temporary legacy compatibility fallback.
* Added per-field country, place-type, language and location bias controls, plus global language and region settings.
* Added required suggestion selection with a custom clear-and-alert message and submission checks.
* Included the single-line Address field's entry-detail map link and autocomplete during entry editing.
* Included local overview, comparison, help and product information.
