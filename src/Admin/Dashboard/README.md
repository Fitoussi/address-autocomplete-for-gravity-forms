# Gravity autocomplete dashboard module

This is a local presentation module to copy and adapt, not an SDK, shared runtime
or standalone plugin. It uses the host's existing namespace autoloader.

```text
Dashboard/
  Loader.php                    Registration, routing and host integrations
  Features.php                  Feature/package content
  views/dashboard.php           Existing page markup
  assets/scss/dashboard.scss    Editable styles
  assets/css/dashboard.css      Expanded build
  assets/css/dashboard.min.css  Runtime build
```

## Starting it

The host's existing src/Loader.php calls \GravityGeolocationAutocomplete\Admin\Dashboard\Loader::register().
The module keeps the existing host submenu/Settings fallback. SetupNotice and
native settings remain host-owned and call this module for overview links.
No lifecycle, menu URLs, capability checks, stored settings or feature behavior
change. No old-location aliases or new autoload registration are introduced.

## Copying and adapting

Copy this folder into another host src/Admin tree. Adjust the namespace,
GFGEOAC constants, translation domain, CSS prefix, page slug, existing
hooks, settings URLs and product content in Loader.php. Adapt Features.php to
the actual host capabilities and implementation paths; any default source root
points at the host src directory, two levels above this folder. Preserve the
new host's setup guards, native settings and licensing outside the module.

Point its existing Sass build at these styles and retain css:dashboard for
standalone rebuilding. Include the runtime PHP/template and compiled CSS in
packages. Keep the editable SCSS in free WordPress.org ZIPs with their readable sources;
this README remains source-only.
Update callers, editor watch paths, fixtures and package requirements together.
The unchanged stylesheet handles and existing host/WordPress assets remain
external dependencies. Published versions and dependencies are unchanged.
