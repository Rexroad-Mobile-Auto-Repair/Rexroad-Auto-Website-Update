<?php
/**
 * Dependency-free unit tests for tools/vehicles/lib/slugify.php.
 *
 * Usage: php tests/vehicles/test-slugify.php
 */

declare( strict_types=1 );

require dirname( __DIR__, 2 ) . '/tools/vehicles/lib/slugify.php';

// A list of [input, expected] pairs — NOT an associative array keyed by
// the input string. Using '1500' or '86' as an array KEY here would hit
// the exact PHP numeric-string-key coercion pitfall this whole slug
// scheme exists to avoid (PHP would silently cast the key to int, and
// the foreach below would receive an int, not a string).
$cases = array(
	array( 'F-150', 'f-150' ),
	array( 'Mercedes-Benz', 'mercedes-benz' ),
	array( 'Land Rover', 'land-rover' ),
	array( 'C/K 2500', 'c-k-2500' ),
	array( 'MX-5 Miata', 'mx-5-miata' ),
	array( '300M', '300m' ),
	array( '4Runner', '4runner' ),
	array( 'Grand Cherokee L', 'grand-cherokee-l' ),
	array( '1500 Classic', '1500-classic' ),
	array( 'INFINITI', 'infiniti' ),
	array( 'Alfa Romeo', 'alfa-romeo' ),
	array( 'Ram 1500', 'ram-1500' ),
	array( '1500', '1500' ),
	array( '86', '86' ),
	array( 'CR-V', 'cr-v' ),
);

$failures = 0;

foreach ( $cases as [ $input, $expected ] ) {
	$actual = rexroad_vehicle_slugify( $input );
	if ( $actual === $expected ) {
		echo "PASS: \"{$input}\" -> \"{$actual}\"\n";
	} else {
		echo "FAIL: \"{$input}\" -> \"{$actual}\" (expected \"{$expected}\")\n";
		++$failures;
	}
}

if ( $failures > 0 ) {
	fwrite( STDERR, "\n{$failures} slug test(s) failed.\n" );
	exit( 1 );
}

echo "\nAll slug tests passed.\n";
exit( 0 );
