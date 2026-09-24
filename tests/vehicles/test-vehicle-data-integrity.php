<?php
/**
 * Dependency-free integrity tests for the committed generated vehicle
 * catalog against the committed authoritative source.
 *
 * Usage: php tests/vehicles/test-vehicle-data-integrity.php
 */

declare( strict_types=1 );

define( 'ABSPATH', true );

$root = dirname( __DIR__, 2 );
require $root . '/tools/vehicles/lib/vehicle-catalog-builder.php';

$failures = 0;

function rexroad_test_check( string $label, bool $condition ): void {
	global $failures;
	echo ( $condition ? 'PASS' : 'FAIL' ) . ": {$label}\n";
	if ( ! $condition ) {
		++$failures;
	}
}

$source_path    = $root . '/tools/vehicles/source/us-market-vehicles-2000-2026.txt';
$generated_path = $root . '/rexroad-custom-theme/inc/vehicles/vehicle-data.php';

rexroad_test_check( 'source file exists', is_file( $source_path ) );
rexroad_test_check( 'generated file exists', is_file( $generated_path ) );

$raw         = (string) file_get_contents( $source_path );
$source_hash = hash( 'sha256', $raw );

$parsed = rexroad_vehicle_parse_source( $raw );
$check  = rexroad_vehicle_check_parsed( $parsed );

rexroad_test_check( 'no structural errors', 0 === count( $check['errors'] ) );
foreach ( $check['errors'] as $error ) {
	echo "  ERROR: {$error}\n";
}

rexroad_test_check( 'parsed make count is 36', 36 === count( $parsed['makes'] ) );

$total_models = array_sum( array_map( static fn( $m ) => count( $m['models'] ), $parsed['makes'] ) );
rexroad_test_check( 'parsed model count is 579', 579 === $total_models );

$expected_php = rexroad_vehicle_render_php( $parsed, $source_hash );
$actual_php   = (string) file_get_contents( $generated_path );
rexroad_test_check( 'committed generated file matches a fresh rebuild', $expected_php === $actual_php );

$data = require $generated_path;

rexroad_test_check( 'generated make_count field is 36', 36 === $data['make_count'] );
rexroad_test_check( 'generated model_count field is 579', 579 === $data['model_count'] );
rexroad_test_check( 'generated source_hash matches source file', 'sha256:' . $source_hash === $data['source_hash'] );

rexroad_test_check( 'ford exists in catalog', isset( $data['makes']['ford'] ) );
rexroad_test_check(
	'ford f-150 exists with years 2000-2026',
	array( array( 2000, 2026 ) ) === ( $data['makes']['ford']['models']['model:f-150']['years'] ?? null )
);

rexroad_test_check(
	'dodge "ram-1500" and ram "1500" are distinct models with distinct years',
	'Ram 1500' === ( $data['makes']['dodge']['models']['model:ram-1500']['name'] ?? null )
	&& '1500' === ( $data['makes']['ram']['models']['model:1500']['name'] ?? null )
	&& array( array( 2000, 2010 ) ) === ( $data['makes']['dodge']['models']['model:ram-1500']['years'] ?? null )
	&& array( array( 2011, 2026 ) ) === ( $data['makes']['ram']['models']['model:1500']['years'] ?? null )
);

$ram_model_keys = array_keys( $data['makes']['ram']['models'] );
rexroad_test_check(
	'no purely-numeric model names (1500, 2500, 3500) were coerced to int array keys',
	0 === count( array_filter( $ram_model_keys, 'is_int' ) )
);

rexroad_test_check(
	'ram "1500" slug field is a genuine string',
	is_string( $data['makes']['ram']['models']['model:1500']['slug'] )
	&& '1500' === $data['makes']['ram']['models']['model:1500']['slug']
);

if ( $failures > 0 ) {
	fwrite( STDERR, "\n{$failures} integrity test(s) failed.\n" );
	exit( 1 );
}

echo "\nAll vehicle-data integrity tests passed.\n";
exit( 0 );
