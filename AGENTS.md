# Codex Project Guidance

Use the Standalone bootstrap convention in docs/README.md for this and future
autocomplete plugins: isolated PHP identities, deferred premium-wins guard,
unchanged saved data/API keys, and short arrays in production PHP.

## Standalone free plugin

Read `docs/README.md` before changing this repository. Inspect the current branch,
HEAD, remote and complete working tree; preserve all local changes.

- Work only in this repository unless the owner explicitly authorizes changes
  to the premium plugin, framework, another consumer or a WordPress site.
- Preserve `gfgeo_address`, saved `gfgeo_*` properties and the native Add-On settings
  slug. Use the isolated GravityGeolocationAutocomplete namespace, GFGEOAC constants
  and deferred premium-wins guard; do not restore shared bootstrap aliases.
- Keep this plugin focused on custom/native Address autocomplete. Do not add
  WPGeo runtime, account connections, licensing, migrations or premium services.
- Retain the local product overview, but do not restore disabled premium field
  buttons or their upsell dialogs. The owner removed them permanently on October 9.
  Register only the functional Address field; preserve existing paid settings.
- Public name is WPGeo Address Autocomplete for Gravity Forms. The requested
  review slug/domain is wpgeo-address-autocomplete-for-gravity-forms; reservation
  is not confirmed until WordPress.org replies. Keep local/repo/PHP filenames and
  saved data identities unchanged. GitHub stays public. Do not restore unrelated
  Create Search buttons/notices or the removed SearchUnavailable integration.
- Use `npm run build` and `npm test` for local assets and fixture coverage.
  Fixtures do not substitute for installed WordPress/browser acceptance.
- Build only this standalone package with `tools/package/build.php`; do not use
  premium variant builders. Packaging does not authorize upload or installation.
- Keep readable JavaScript, SCSS and third-party notices in packages. Exclude
  private notes, tools, tests, dependencies and editor metadata.
- Public setup and release notes live in `readme.txt` and `change_log.txt`.
  Keep version metadata consistent. Release approval is still required.
- Use focused staging. Never reset, clean, restore, stash or overwrite
  unrelated changes. No production site changes without explicit authorization.
