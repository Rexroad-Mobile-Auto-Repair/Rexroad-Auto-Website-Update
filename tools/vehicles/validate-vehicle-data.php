<?php
/**
 * Validates the committed generated vehicle catalog against the
 * committed authoritative source.
 *
 * Checks:
 *  - The source file's own declared header counts match what actually
 *    parses out of it.
 *  - No slug collisions (make-level global, model-level per-make).
 *  - Every year range is well-formed and within the 2000-2026 boundary.
 *  - The committed generated file is byte-for-byte identical to what
 *    regenerating from the committed source would produce right now
 *    (catches stale/hand-edited generated output).
 *
 * Usage:
 *   php tools/vehicles/validate-vehicle-data.php [source-file] [generated-file]
 *
 * Exits non-zero on any hard failure.
 */

declare( strict_types=1 );

require __DIR__ . '/lib/vehicle-catalog-builder.php';

// --- CLI entry point -------------------------------------------------

if ( PHP_SAPI !== 'cli' ) {
	fwrite( STDERR, "This script must be run from the command line.\n" );
	exit( 1 );
}

$source_path    = $argv[1] ?? __DIR__ . '/source/us-market-vehicles-2000-2026.txt';
$generated_path = $argv[2] ?? __DIR__ . '/../../rexroad-custom-theme/inc/vehicles/vehicle-data.php';

$failures = array();

if ( ! is_file( $source_path ) ) {
	fwrite( STDERR, "Source file not found: {$source_path}\n" );
	exit( 1 );
}

if ( ! is_file( $generated_path ) ) {
	fwrite( STDERR, "Generated file not found: {$generated_path}\n" );
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
	fwrite( STDOUT, 'PARSE ERROR: ' . $e->getMessage() . "\n" );
	exit( 1 );
}

$check = rexroad_vehicle_check_parsed( $parsed );

$total_models = array_sum( array_map( static fn( $m ) => count( $m['models'] ), $parsed['makes'] ) );

fwrite( STDOUT, "Source sha256: {$source_hash}\n" );
fwrite( STDOUT, "Declared: {$parsed['declared_make_count']} makes / {$parsed['declared_model_count']} models\n" );
fwrite( STDOUT, "Parsed:   " . count( $parsed['makes'] ) . " makes / {$total_models} models\n" );

foreach ( $check['warnings'] as $warning ) {
	fwrite( STDOUT, "WARNING: {$warning}\n" );
}

if ( count( $check['errors'] ) > 0 ) {
	foreach ( $check['errors'] as $error ) {
		fwrite( STDOUT, "ERROR: {$error}\n" );
	}
	$failures[] = count( $check['errors'] ) . ' structural error(s)';
}

// Regenerate in-memory and compare against the committed generated file.
define( 'ABSPATH', true );
$expected_php = rexroad_vehicle_render_php( $parsed, $source_hash );
$actual_php   = file_get_contents( $generated_path );

if ( $expected_php === $actual_php ) {
	fwrite( STDOUT, "Deterministic rebuild: MATCH (generated file is in sync with source)\n" );
} else {
	fwrite( STDOUT, "Deterministic rebuild: MISMATCH (generated file is stale or hand-edited)\n" );
	$failures[] = 'generated file does not match a fresh rebuild from source';
}

if ( count( $failures ) > 0 ) {
	fwrite( STDOUT, "\nVALIDATION FAILED: " . implode( '; ', $failures ) . "\n" );
	exit( 1 );
}

fwrite( STDOUT, "\nVALIDATION PASSED\n" );
exit( 0 );
