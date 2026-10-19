<?php
/**
 * Build one standalone ZIP from an explicit source allowlist.
 *
 * Usage: php tools/package/build.php [output-directory]
 * Run npm run build first. No dependency install, remote action or site mutation.
 *
 * @package GravityGeolocationAutocomplete\Tools
 */

if ( 'cli' !== PHP_SAPI ) {
	exit;
}

$root = dirname( __DIR__, 2 );
$slug = 'wpgeo-address-autocomplete-for-gravity-forms';
$entry_file = 'address-autocomplete-for-gravity-forms.php';

try {
	if ( ! class_exists( 'ZipArchive' ) ) {
		throw new RuntimeException( 'Packaging requires the PHP zip extension.' );
	}
	$main = file_get_contents( $root . '/' . $entry_file );
	if ( preg_match( '/^ \* Tested up to:/mi', $main ) ) {
		throw new RuntimeException( 'Tested up to belongs only in readme.txt, not the PHP plugin header.' );
	}
	if ( ! preg_match( '/^ \* Version:\s*([0-9]+\.[0-9]+\.[0-9]+(?:[-.][A-Za-z0-9.-]+)?)\s*$/m', $main, $match ) ) {
		throw new RuntimeException( 'Cannot read the plugin header version.' );
	}
	$version = $match[1];
	if ( ! preg_match( '/^ \* Text Domain:\s*' . preg_quote( $slug, '/' ) . '\s*$/m', $main ) ) {
		throw new RuntimeException( 'The text domain must match the WordPress.org release slug.' );
	}
	if ( false === strpos( $main, "define( 'GFGEOAC_VERSION', '{$version}' );" ) || false === strpos( $main, "define( 'GFGEOAC_PACKAGE_TYPE', 'free' );" ) ) {
		throw new RuntimeException( 'Plugin constants do not match the standalone header.' );
	}
	$readme = file_get_contents( $root . '/readme.txt' );
	if ( ! preg_match( '/^Stable tag:\s*' . preg_quote( $version, '/' ) . '\s*$/m', $readme ) ) {
		throw new RuntimeException( 'The readme stable tag must match the plugin version.' );
	}

	$required = [
		'src/Admin/Dashboard/Loader.php',
		'src/Admin/Dashboard/Features.php',
		'src/Admin/Dashboard/views/dashboard.php',
		'src/Admin/Dashboard/assets/scss/dashboard.scss',
		'src/Admin/Dashboard/assets/css/dashboard.css',
		'build/js/frontend/address-autocomplete.min.js',
		'build/js/admin/gfgeo-form-editor.min.js',
		'build/css/admin/gfgeo-admin.min.css',
		'src/Admin/Dashboard/assets/css/dashboard.min.css',
		'assets/css/address-autocomplete.css',
		'assets/css/choices.css',
		'assets/licenses/choices.txt',
	];
	foreach ( $required as $path ) {
		if ( ! is_file( $root . '/' . $path ) ) {
			throw new RuntimeException( 'Missing asset: ' . $path . '. Run npm run build first.' );
		}
	}

	$paths = [ $entry_file, 'readme.txt', 'change_log.txt', 'src', 'assets', 'build', 'languages' ];
	$files = [];
	$extensions = [ 'php', 'js', 'css', 'scss', 'svg', 'png', 'jpg', 'jpeg', 'webp', 'gif', 'json', 'map', 'txt', 'po', 'mo', 'pot' ];
	foreach ( $paths as $path ) {
		$source = $root . '/' . $path;
		if ( is_link( $source ) || ! file_exists( $source ) ) {
			throw new RuntimeException( 'Missing or linked package input: ' . $path );
		}
		if ( is_file( $source ) ) {
			$files[ $path ] = $source;
			continue;
		}
		$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $source, FilesystemIterator::SKIP_DOTS ) );
		foreach ( $iterator as $file ) {
			if ( $file->isLink() ) {
				throw new RuntimeException( 'Linked files cannot be packaged: ' . $file->getPathname() );
			}
			if ( $file->isFile() && in_array( strtolower( $file->getExtension() ), $extensions, true ) ) {
				$relative = str_replace( DIRECTORY_SEPARATOR, '/', substr( $file->getPathname(), strlen( $root ) + 1 ) );
				$files[ $relative ] = $file->getPathname();
			}
		}
	}
	ksort( $files );

	$output = $argv[1] ?? $root . '/dist';
	if ( ! is_dir( $output ) && ! mkdir( $output, 0755, true ) ) {
		throw new RuntimeException( 'Cannot create the output directory.' );
	}
	$destination = realpath( $output ) . '/' . $slug . '.' . $version . '.zip';
	$zip = new ZipArchive();
	if ( true !== $zip->open( $destination, ZipArchive::CREATE | ZipArchive::EXCL ) ) {
		throw new RuntimeException( 'Cannot create ZIP; existing artifacts will not be overwritten. Use a fresh output directory.' );
	}
	foreach ( $files as $relative => $source ) {
		if ( ! $zip->addFile( $source, $slug . '/' . $relative ) ) {
			throw new RuntimeException( 'Cannot add package file: ' . $relative );
		}
	}
	if ( ! $zip->close() ) {
		throw new RuntimeException( 'Cannot finish the ZIP.' );
	}
	fwrite( STDOUT, $destination . PHP_EOL . 'SHA-256: ' . hash_file( 'sha256', $destination ) . PHP_EOL );
} catch ( Throwable $error ) {
	fwrite( STDERR, $error->getMessage() . PHP_EOL );
	exit( 1 );
}
