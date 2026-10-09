/**
* Build the standalone frontend and local field-editor bundle.
*
* @since 1.0.0
*
* @file build.js
*/
const esbuild = require('esbuild');

const builds = [
	{
		entryPoints: ['./assets/js/autocomplete/gravity-address-autocomplete.js'],
		outfile: './build/js/frontend/address-autocomplete.min.js',
		globalName: 'GFGeoAddressAutocomplete',
		standalone: true,
	},
	{
		entryPoints: ['./assets/js/editor/form-editor.js'],
		outfile: './build/js/admin/gfgeo-form-editor.min.js',
		globalName: 'GFGEO_FormEditor',
	},
];

// Select an explicitly requested local entry, or build both entries.
const requested = process.argv[2];
const selectedBuilds = requested === 'autocomplete'
	? builds.filter(config => config.standalone)
	: requested === 'editor'
		? builds.filter(config => config.globalName === 'GFGEO_FormEditor')
		: builds;

Promise.all(
	selectedBuilds.map(({standalone, ...cfg}) =>
		esbuild.build({
			...cfg,
			bundle: true,
			minify: true,
			sourcemap: true,
			target: ['es2017'],
			format: 'iife',
			absWorkingDir: __dirname,
		})
	)
).then(() => {
	console.log('All builds complete');
}).catch(() => process.exit(1));
