<?php
/**
 * Deterministic parser/generator for the Rexroad vehicle catalog.
 *
 * Reads the authoritative source text file and writes a generated PHP
 * array file consumed by the theme's read-only accessor layer.
 *
 * Usage:
 *   php tools/vehicles/build-vehicle-data.php [source-file] [output-file]
 *
 * No WordPress bootstrap required.
 */

declare( strict_types=1 );

require __DIR__ . '/lib/vehicle-catalog-builder.php';

if ( PHP_SAPI !== 'cli' ) {
	fwrite( STDERR, "This script must be run from the command line.\n" );
	exit( 1 );
}

$source_path = $argv[1] ?? __DIR__ . '/source/us-market-vehicles-2000-2026.txt';
$output_path = $argv[2] ?? __DIR__ . '/../../rexroad-custom-theme/inc/vehicles/vehicle-data.php';

if ( ! is_file( $source_path ) ) {
	fwrite( STDERR, "Source file not found: {$source_path}\n" );
	exit( 1 );
}

$raw = file_get_contents( $source_path );
if ( false === $raw ) {
	fwrite( STDERR, "Failed to read source file: {$source_path}\n" );
	exit( 1 );
}

$source_hash = hash( 'sha256', $raw );

try {
	$parsed = rexroad_vehicle_parse_source( $raw );
} catch ( RuntimeException $e ) {
	fwrite( STDERR, 'Parse error: ' . $e->getMessage() . "\n" );
	exit( 1 );
}

$check = rexroad_vehicle_check_parsed( $parsed );

foreach ( $check['warnings'] as $warning ) {
	fwrite( STDERR, "WARNING: {$warning}\n" );
}

if ( count( $check['errors'] ) > 0 ) {
	foreach ( $check['errors'] as $error ) {
		fwrite( STDERR, "ERROR: {$error}\n" );
	}
	fwrite( STDERR, 'Build aborted: ' . count( $check['errors'] ) . " error(s).\n" );
	exit( 1 );
}

$php = rexroad_vehicle_render_php( $parsed, $source_hash );

$output_dir = dirname( $output_path );
if ( ! is_dir( $output_dir ) ) {
	mkdir( $output_dir, 0777, true );
}

if ( false === file_put_contents( $output_path, $php ) ) {
	fwrite( STDERR, "Failed to write output file: {$output_path}\n" );
	exit( 1 );
}

$total_models = array_sum( array_map( static fn( $m ) => count( $m['models'] ), $parsed['makes'] ) );

fwrite( STDOUT, "Parsed makes: " . count( $parsed['makes'] ) . "\n" );
fwrite( STDOUT, "Parsed models: {$total_models}\n" );
fwrite( STDOUT, "Declared makes: {$parsed['declared_make_count']}\n" );
fwrite( STDOUT, "Declared models: {$parsed['declared_model_count']}\n" );
fwrite( STDOUT, "Source sha256: {$source_hash}\n" );
fwrite( STDOUT, "Warnings: " . count( $check['warnings'] ) . "\n" );
fwrite( STDOUT, "Written to: {$output_path}\n" );
