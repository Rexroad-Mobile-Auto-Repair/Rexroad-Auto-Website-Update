<?php
/**
 * Dependency-free tests for rexroad-custom-theme/inc/vehicles/vehicles.php.
 *
 * Stubs the one WordPress function the accessor layer calls
 * (get_template_directory) so it can run outside a WordPress bootstrap.
 *
 * Usage: php tests/vehicles/test-accessors.php
 */

declare( strict_types=1 );

define( 'ABSPATH', true );

$root = dirname( __DIR__, 2 );

if ( ! function_exists( 'get_template_directory' ) ) {
	function get_template_directory(): string {
		global $root;
		return $root . '/rexroad-custom-theme';
	}
}

require $root . '/rexroad-custom-theme/inc/vehicles/vehicles.php';

$failures = 0;

function rexroad_test_check( string $label, bool $condition ): void {
	global $failures;
	echo ( $condition ? 'PASS' : 'FAIL' ) . ": {$label}\n";
	if ( ! $condition ) {
		++$failures;
	}
}

$makes = rexroad_vehicle_get_makes();
rexroad_test_check( 'get_makes() returns 36 makes', 36 === count( $makes ) );

$ford = rexroad_vehicle_get_make( 'ford' );
rexroad_test_check( 'get_make("ford") found with correct name', null !== $ford && 'Ford' === $ford['name'] );

rexroad_test_check( 'get_make() returns null for unknown slug', null === rexroad_vehicle_get_make( 'not-a-real-make' ) );

$ram_models = rexroad_vehicle_get_models( 'ram' );
$ram_slugs  = array_column( $ram_models, 'slug' );
rexroad_test_check( 'get_models("ram") includes model slug "1500"', in_array( '1500', $ram_slugs, true ) );

$slug_1500_index = array_search( '1500', $ram_slugs, true );
rexroad_test_check(
	'ram model slug "1500" is a genuine string, not int',
	false !== $slug_1500_index && is_string( $ram_slugs[ $slug_1500_index ] )
);

rexroad_test_check( 'get_models() returns empty array for unknown make', array() === rexroad_vehicle_get_models( 'not-a-real-make' ) );

$dodge_ram1500 = rexroad_vehicle_get_model( 'dodge', 'ram-1500' );
$ram_1500      = rexroad_vehicle_get_model( 'ram', '1500' );

rexroad_test_check(
	'get_model("dodge","ram-1500") and get_model("ram","1500") resolve to distinct records',
	null !== $dodge_ram1500
	&& null !== $ram_1500
	&& $dodge_ram1500['name'] !== $ram_1500['name']
	&& array( array( 2000, 2010 ) ) === $dodge_ram1500['years']
	&& array( array( 2011, 2026 ) ) === $ram_1500['years']
);

rexroad_test_check( 'get_model() returns null for unknown model', null === rexroad_vehicle_get_model( 'ford', 'not-a-real-model' ) );

// WordPress-slug-compatibility fix: model lookup by the new,
// WP-native "/" -> stripped (not hyphenated) slugs must work.
$ck_2500 = rexroad_vehicle_get_model( 'chevrolet', 'ck-2500' );
$ck_3500 = rexroad_vehicle_get_model( 'chevrolet', 'ck-3500' );
rexroad_test_check(
	'get_model("chevrolet","ck-2500") resolves to "C/K 2500" (WordPress-native slug)',
	null !== $ck_2500 && 'C/K 2500' === $ck_2500['name']
);
rexroad_test_check(
	'get_model("chevrolet","ck-3500") resolves to "C/K 3500" (WordPress-native slug)',
	null !== $ck_3500 && 'C/K 3500' === $ck_3500['name']
);
rexroad_test_check(
	'the old "c-k-2500" / "c-k-3500" slugs no longer resolve',
	null === rexroad_vehicle_get_model( 'chevrolet', 'c-k-2500' )
	&& null === rexroad_vehicle_get_model( 'chevrolet', 'c-k-3500' )
);

rexroad_test_check(
	'normalize_search_term collapses "F-150" and "F150" to the same value',
	rexroad_vehicle_normalize_search_term( 'F-150' ) === rexroad_vehicle_normalize_search_term( 'F150' )
);

rexroad_test_check(
	'normalize_search_term collapses "CR-V" and "CRV" to the same value',
	rexroad_vehicle_normalize_search_term( 'CR-V' ) === rexroad_vehicle_normalize_search_term( 'CRV' )
);

$search_dash = rexroad_vehicle_search( 'F-150' );
$search_nodash = rexroad_vehicle_search( 'F150' );
rexroad_test_check(
	'search("F-150") and search("F150") return the same matches',
	array_column( $search_dash, 'model_slug' ) === array_column( $search_nodash, 'model_slug' )
);
rexroad_test_check(
	'search("F-150") finds Ford F-150',
	count( array_filter(
		$search_dash,
		static fn( $r ) => 'ford' === $r['make_slug'] && 'f-150' === $r['model_slug']
	) ) > 0
);

$search_crv = rexroad_vehicle_search( 'CRV' );
rexroad_test_check(
	'search("CRV") finds Honda CR-V',
	count( array_filter(
		$search_crv,
		static fn( $r ) => 'honda' === $r['make_slug'] && 'cr-v' === $r['model_slug']
	) ) > 0
);

rexroad_test_check( 'search("") returns empty array', array() === rexroad_vehicle_search( '' ) );
rexroad_test_check( 'search("   ") returns empty array', array() === rexroad_vehicle_search( '   ' ) );

if ( $failures > 0 ) {
	fwrite( STDERR, "\n{$failures} accessor test(s) failed.\n" );
	exit( 1 );
}

echo "\nAll accessor tests passed.\n";
exit( 0 );
