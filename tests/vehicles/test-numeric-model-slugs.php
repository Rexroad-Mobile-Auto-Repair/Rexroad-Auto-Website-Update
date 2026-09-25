<?php
/**
 * Dependency-free tests for rexroad_vehicle_page_slug_for_model() and the
 * numeric-model-slug fix in rexroad_vehicle_resolve_model_from_current_page().
 *
 * WordPress does not reliably allow a purely-numeric hierarchical Page
 * slug (wp_unique_post_slug() mutates it, and a trailing all-digit URL
 * segment collides with WordPress's own page-pagination rewrite rule).
 * These tests cover every current catalog model whose slug is digits
 * only, plus confirm ordinary (non-numeric) models are unaffected.
 *
 * Usage: php tests/vehicles/test-numeric-model-slugs.php
 */

declare( strict_types=1 );

$root = dirname( __DIR__, 2 );

require __DIR__ . '/lib/wp-stubs.php';

function get_template_directory(): string {
	global $root;
	return $root . '/rexroad-custom-theme';
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

// --- The 7 real catalog models with a purely-numeric slug ---
$numeric_models = array(
	array( 'chrysler', '200' ),
	array( 'chrysler', '300' ),
	array( 'mazda', '626' ),
	array( 'ram', '1500' ),
	array( 'ram', '2500' ),
	array( 'ram', '3500' ),
	array( 'toyota', '86' ),
);

foreach ( $numeric_models as [ $make_slug, $model_slug ] ) {
	$catalog_model = rexroad_vehicle_get_model( $make_slug, $model_slug );
	rexroad_test_check(
		"catalog still has {$make_slug}/{$model_slug} under its original, unchanged model slug",
		null !== $catalog_model && $model_slug === $catalog_model['slug']
	);

	$page_slug = rexroad_vehicle_page_slug_for_model( $make_slug, $model_slug );
	rexroad_test_check(
		"page_slug_for_model({$make_slug}, {$model_slug}) === \"{$make_slug}-{$model_slug}\"",
		"{$make_slug}-{$model_slug}" === $page_slug
	);
}

// --- Ordinary (non-numeric) models are never rewritten ---
$ordinary_models = array(
	array( 'ford', 'f-150' ),
	array( 'toyota', 'camry' ),
	array( 'honda', 'civic' ),
	array( 'chevrolet', 'silverado-1500' ), // contains digits but is not PURELY numeric
	array( 'ram', '1500-classic' ),          // "1500 Classic" -> not purely numeric either
);
foreach ( $ordinary_models as [ $make_slug, $model_slug ] ) {
	rexroad_test_check(
		"page_slug_for_model({$make_slug}, {$model_slug}) is unchanged (not purely numeric)",
		$model_slug === rexroad_vehicle_page_slug_for_model( $make_slug, $model_slug )
	);
}

// --- Full-hierarchy resolution: a Page whose post_name is the "{make}-{model}"
//     convention resolves to the ORIGINAL numeric catalog model slug ---
$GLOBALS['rexroad_test_pages'] = array(
	1 => array( 'ID' => 1, 'post_name' => 'vehicles', 'post_parent' => 0, 'post_title' => 'Cars, Trucks & SUVs We Service', 'post_status' => 'publish', 'template' => 'page-vehicles.php' ),
	2 => array( 'ID' => 2, 'post_name' => 'ram', 'post_parent' => 1, 'post_title' => 'Ram', 'post_status' => 'publish', 'template' => 'page-vehicle-make.php' ),
	3 => array( 'ID' => 3, 'post_name' => 'ram-1500', 'post_parent' => 2, 'post_title' => '1500', 'post_status' => 'publish' ),
);

$GLOBALS['rexroad_test_current_post_id'] = 3;
$resolved = rexroad_vehicle_resolve_model_from_current_page();
rexroad_test_check(
	'a Page with post_name "ram-1500" under Ram resolves to catalog model "1500"',
	null !== $resolved && '1500' === $resolved['model']['slug'] && 'Ram' === $resolved['make']['name']
);

// --- The RAW numeric slug ("1500") must NOT resolve as the canonical model URL ---
$GLOBALS['rexroad_test_pages'][4] = array( 'ID' => 4, 'post_name' => '1500', 'post_parent' => 2, 'post_title' => '1500 (wrong slug)', 'post_status' => 'publish' );
$GLOBALS['rexroad_test_current_post_id'] = 4;
$resolved_raw = rexroad_vehicle_resolve_model_from_current_page();
rexroad_test_check(
	'a Page with the RAW numeric post_name "1500" does NOT resolve (not the expected convention)',
	null === $resolved_raw
);

// --- WordPress's own "-2" collision-avoidance suffix must NOT resolve either ---
$GLOBALS['rexroad_test_pages'][5] = array( 'ID' => 5, 'post_name' => '1500-2', 'post_parent' => 2, 'post_title' => '1500', 'post_status' => 'publish' );
$GLOBALS['rexroad_test_current_post_id'] = 5;
$resolved_wp_suffix = rexroad_vehicle_resolve_model_from_current_page();
rexroad_test_check(
	'a Page with WordPress\'s own "-2" suffix ("1500-2") does NOT resolve (no reliance on that suffix)',
	null === $resolved_wp_suffix
);

// --- Ordinary model resolution is completely unaffected ---
$GLOBALS['rexroad_test_pages'][6] = array( 'ID' => 6, 'post_name' => 'f-150', 'post_parent' => 7, 'post_title' => 'F-150', 'post_status' => 'publish' );
$GLOBALS['rexroad_test_pages'][7] = array( 'ID' => 7, 'post_name' => 'ford', 'post_parent' => 1, 'post_title' => 'Ford', 'post_status' => 'publish', 'template' => 'page-vehicle-make.php' );
$GLOBALS['rexroad_test_current_post_id'] = 6;
$resolved_f150 = rexroad_vehicle_resolve_model_from_current_page();
rexroad_test_check(
	'an ordinary model (Ford F-150) still resolves normally, unaffected by the numeric-slug fix',
	null !== $resolved_f150 && 'f-150' === $resolved_f150['model']['slug']
);

if ( $failures > 0 ) {
	fwrite( STDERR, "\n{$failures} numeric-model-slug test(s) failed.\n" );
	exit( 1 );
}

echo "\nAll numeric-model-slug tests passed.\n";
exit( 0 );
