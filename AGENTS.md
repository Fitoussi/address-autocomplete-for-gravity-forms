# Codex Project Guidance

## Standalone free plugin

Read `docs/README.md` before changing this repository. Inspect the current branch,
HEAD, remote and complete working tree; preserve all local changes.

- Work only in this repository unless the owner explicitly authorizes changes
  to the premium plugin, framework, another consumer or a WordPress site.
- Preserve `gfgeo_address`, saved `gfgeo_*` properties, the native Add-On settings
  slug, existing PHP namespaces and the early distribution-conflict guard.
- Keep this plugin focused on custom/native Address autocomplete. Do not add
  WPGeo runtime, account connections, licensing, migrations or premium services.
- Retain the four non-functional field promotions and local product overview.
  They must not initialize premium features or modify existing paid settings.
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
