<?php
/**
 * Inspect a temporary artifact from the actual standalone ZIP builder.
 *
 * This checks archive contents, not installed WordPress acceptance.
 * Temporary artifacts remain available for inspection after the test.
 *
 * @package GravityGeolocationAutocomplete\Tests
 */

$root = dirname( __DIR__, 2 );
$output = sys_get_temp_dir() . '/gfgeoac-package-' . bin2hex( random_bytes( 6 ) );
$command = escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $root . '/tools/package/build.php' ) . ' ' . escapeshellarg( $output );
exec( $command . ' 2>&1', $messages, $status );
if ( 0 !== $status ) {
	fwrite( STDERR, implode( PHP_EOL, $messages ) . PHP_EOL );
	exit( 1 );
}

$zip = new ZipArchive();
if ( true !== $zip->open( $messages[0] ) ) {
	fwrite( STDERR, 'Cannot inspect the generated ZIP.' . PHP_EOL );
	exit( 1 );
}
$prefix = 'address-autocomplete-for-gravity-forms/';
$files = [];
for ( $index = 0; $index < $zip->numFiles; ++$index ) {
	$files[] = $zip->getNameIndex( $index );
}
$checks = [];
$checks['one standalone root'] = ! array_filter( $files, static function ( $file ) use ( $prefix ) { return 0 !== strpos( $file, $prefix ); } );
foreach ( [
	'address-autocomplete-for-gravity-forms.php', 'readme.txt', 'change_log.txt',
	'src/Common/Form/Fields/Registrar.php', 'src/Features/Form/Fields/Address/Field.php',
	'build/js/frontend/address-autocomplete.min.js', 'build/js/admin/gfgeo-form-editor.min.js',
	'assets/js/autocomplete/services/address-autocomplete.js', 'assets/js/autocomplete/services/compat/places-autocomplete.js',
	'assets/js/autocomplete/native-address.js', 'assets/js/editor/form-editor.js', 'assets/js/editor/vendor/choices.js',
	'assets/licenses/choices.txt', 'assets/scss/admin/gfgeo-product-dashboard.scss', 'build.js', 'package.json', 'package-lock.json',
] as $file ) {
	$checks[ 'includes ' . $file ] = in_array( $prefix . $file, $files, true );
}
$checks['no development or private directories'] = ! array_filter( $files, static function ( $file ) use ( $prefix ) {
	return preg_match( '#^(?:\.git|\.agents|\.codex|\.vscode|docs|tests|tools|vendor|node_modules|dist)/#', substr( $file, strlen( $prefix ) ) );
} );
$checks['no removed premium/runtime scaffolding'] = ! array_filter( $files, static function ( $file ) {
	return preg_match( '#/(?:Licensing|Migrations|Distribution|Services|Features/Search)/|/Features/Form/Provider\.php$|/Common/Form/Admin/#', $file );
} );
$main = $zip->getFromName( $prefix . 'address-autocomplete-for-gravity-forms.php' );
$readme = $zip->getFromName( $prefix . 'readme.txt' );
for ( $number = 1; $number <= 6; ++$number ) {
	$file = 'assets/screenshots/screenshot-' . $number . '.png';
	$checks[ 'includes ' . $file ] = in_array( $prefix . $file, $files, true );
}
preg_match( '/== Screenshots ==\s*(.*?)\s*== Changelog ==/s', $readme, $screenshot_section );
$checks['six numbered screenshot captions'] = isset( $screenshot_section[1] ) && 6 === preg_match_all( '/^[1-6]\. .+$/m', $screenshot_section[1] );
$checks['free identity retained'] = false !== strpos( $main, "define( 'GFGEOAC_PACKAGE_TYPE', 'free' );" );
$checks['standalone readme has no account SDK claims'] = false === strpos( $readme, 'Connect Account' ) && false === strpos( $readme, '@VERSION@' );
$map = json_decode( $zip->getFromName( $prefix . 'build/js/frontend/address-autocomplete.min.js.map' ), true );
$checks['frontend sources only belong to this plugin'] = ! empty( $map['sources'] ) && ! array_filter( $map['sources'], static function ( $source ) { return 0 !== strpos( $source, '../../../assets/js/autocomplete/' ); } );
$zip->close();

$artifact = $messages[0];
$checksum = hash_file( 'sha256', $artifact );
exec( $command . ' 2>&1', $retry_messages, $retry_status );
$checks['existing artifacts are not overwritten'] = 0 !== $retry_status && $checksum === hash_file( 'sha256', $artifact );

echo json_encode( $checks, JSON_PRETTY_PRINT ) . PHP_EOL;
echo 'Test artifact: ' . $artifact . PHP_EOL;
exit( in_array( false, $checks, true ) ? 1 : 0 );
