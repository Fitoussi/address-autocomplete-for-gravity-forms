<?php
/**
 * Verify standalone identity without booting WordPress or installing dependencies.
 *
 * Run with: php tests/helpers/standalone-identity.php
 * The conflict fixture executes the real early guard in separate PHP processes.
 * This checks metadata and boot isolation, not frontend or installed acceptance.
 *
 * @package GravityGeolocation\Tests\Fixtures
 */

$root   = dirname( __DIR__, 2 );
$entry  = $root . '/address-autocomplete-for-gravity-forms.php';
$main   = file_get_contents( $entry );
$addon  = file_get_contents( $root . '/src/Core/AddOn.php' );
$checks = [];

foreach ( [
	'Plugin Name' => 'Address Autocomplete for Gravity Forms',
	'Version'     => '1.0.0',
	'Text Domain' => 'address-autocomplete-for-gravity-forms',
	'License'     => 'GPL-3.0-or-later',
] as $header => $expected ) {
	$checks[ $header ] = 1 === preg_match( '/^ \* ' . preg_quote( $header, '/' ) . ':\s*' . preg_quote( $expected, '/' ) . '\s*$/m', $main );
}

foreach ( [
	'GFGEO_VERSION'      => '1.0.0',
	'GFGEO_PLUGIN_NAME'  => 'Address Autocomplete for Gravity Forms',
	'GFGEO_PACKAGE_TYPE' => 'free',
	'GFGEO_PREFIX'       => 'gfgeo',
	'GFGEO_SLUG'         => 'gravity_geolocation',
	'GFGEO_ITEM_ID'      => '0',
] as $constant => $expected ) {
	$checks[ $constant ] = false !== strpos( $main, "define( '{$constant}', '{$expected}' );" );
}

$checks['native settings slug retained'] = 1 === preg_match( '/protected\s+\$_slug\s*=\s*\x27gravityforms_geolocation\x27;/', $addon );
$checks['native full title']             = 1 === preg_match( '/protected\s+\$_title\s*=\s*GFGEO_PLUGIN_NAME;/', $addon );
$checks['native short title']            = 1 === preg_match( '/protected\s+\$_short_title\s*=\s*\x27Address Autocomplete\x27;/', $addon );
$checks['custom Address type retained']  = false !== strpos( file_get_contents( $root . '/src/Features/Form/Fields/Address/Field.php' ), "public const TYPE = 'gfgeo_address';" );
$checks['single renamed entry']          = ! file_exists( $root . '/gravityforms_geolocation.php' );
$checks['renamed workspace']             = is_file( $root . '/address-autocomplete-for-gravity-forms.code-workspace' ) && ! file_exists( $root . '/gravity-geolocation.code-workspace' );
$checks['guard before constants']        = strpos( $main, "if ( \\defined( 'GFGEO_PACKAGE_TYPE' ) )" ) < strpos( $main, "define( 'GFGEO_VERSION'" );
$checks['WordPress update identity']      = false === strpos( $main, 'Update URI:' );

$checks['prefixed dependency variable'] = false !== strpos( $main, '$gfgeo_dependencies = [' )
	&& false === strpos( $main, '$dependencies = [' );
$checks['bundled translation path retained'] = false !== strpos( $addon, "load_plugin_textdomain( 'address-autocomplete-for-gravity-forms', false, dirname( GFGEO_BASENAME ) . '/languages/' );" );

// Keep direct-access guards recognizable to Plugin Check as well as PHP.
$source_files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root . '/src', FilesystemIterator::SKIP_DOTS ) );
foreach ( $source_files as $source_file ) {
	if ( 'php' === $source_file->getExtension() ) {
		$relative = substr( $source_file->getPathname(), strlen( $root ) + 1 );
		$checks[ 'direct-access guard: ' . $relative ] = false !== strpos( file_get_contents( $source_file->getPathname() ), "if ( ! defined( 'ABSPATH' ) ) {" );
	}
}

$package = json_decode( file_get_contents( $root . '/package.json' ), true, 512, JSON_THROW_ON_ERROR );
$lock    = json_decode( file_get_contents( $root . '/package-lock.json' ), true, 512, JSON_THROW_ON_ERROR );
$checks['npm identity and lock agree'] = 'address-autocomplete-for-gravity-forms' === $package['name']
	&& $package['name'] === $lock['name'] && $package['name'] === $lock['packages']['']['name'];

foreach ( [ 'free', 'location', 'directions', 'toolkit', 'search', 'allaccess' ] as $package_type ) {
	foreach ( [ 'allowed', 'denied' ] as $permission ) {
		$output = [];
		exec( escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( __DIR__ . '/distribution-conflict.php' )
			. ' ' . escapeshellarg( $package_type ) . ' ' . escapeshellarg( $permission ) . ' ' . escapeshellarg( $entry ) . ' 2>&1', $output, $status );
		$checks[ 'guard: ' . $package_type . '/' . $permission ] = 0 === $status;
	}
}

foreach ( [ 'es_ES', 'he_IL', 'pl_PL' ] as $locale ) {
	$checks[ 'translation identity: ' . $locale ] = is_file( $root . '/languages/address-autocomplete-for-gravity-forms-' . $locale . '.mo' );
}

echo json_encode( $checks, JSON_PRETTY_PRINT ) . PHP_EOL;
exit( in_array( false, $checks, true ) ? 1 : 0 );
