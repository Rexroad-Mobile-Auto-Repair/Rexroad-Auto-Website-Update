<?php
/**
 * Dependency-free render test for
 * rexroad-custom-theme/page-vehicle-model.php.
 *
 * Stubs WordPress core glue (see lib/wp-stubs.php) but exercises the
 * REAL template and the REAL vehicle catalog accessors.
 *
 * Usage: php tests/vehicles/test-page-vehicle-model-render.php
 */

declare( strict_types=1 );

$root = dirname( __DIR__, 2 );

require __DIR__ . '/lib/wp-stubs.php';

function get_template_directory(): string {
	global $root;
	return $root . '/rexroad-custom-theme';
}

function get_template_directory_uri(): string {
	return 'https://example.test/wp-content/themes/rexroad-custom-theme';
}

require $root . '/rexroad-custom-theme/inc/vehicles/vehicles.php';
require $root . '/rexroad-custom-theme/inc/schema-service.php';

$failures = 0;

function rexroad_test_check( string $label, bool $condition ): void {
	global $failures;
	echo ( $condition ? 'PASS' : 'FAIL' ) . ": {$label}\n";
	if ( ! $condition ) {
		++$failures;
	}
}

/**
 * Render page-vehicle-model.php for a given "current" fixture post ID.
 */
function rexroad_render_vehicle_model_page( int $post_id ): string {
	global $root;

	$GLOBALS['rexroad_test_current_post_id']      = $post_id;
	$GLOBALS['rexroad_test_have_posts_remaining'] = 1;
	$GLOBALS['rexroad_test_get_pages_calls']      = 0;

	ob_start();
	require $root . '/rexroad-custom-theme/page-vehicle-model.php';
	return (string) ob_get_clean();
}

$GLOBALS['rexroad_test_pages'] = array(
	1  => array( 'ID' => 1, 'post_name' => 'vehicles', 'post_parent' => 0, 'post_title' => 'Cars, Trucks & SUVs We Service', 'post_status' => 'publish', 'template' => 'page-vehicles.php' ),
	2  => array( 'ID' => 2, 'post_name' => 'ford', 'post_parent' => 1, 'post_title' => 'Ford', 'post_status' => 'publish', 'template' => 'page-vehicle-make.php' ),
	3  => array( 'ID' => 3, 'post_name' => 'f-150', 'post_parent' => 2, 'post_title' => 'F-150', 'post_status' => 'publish' ), // valid
	4  => array( 'ID' => 4, 'post_name' => 'mustang', 'post_parent' => 2, 'post_title' => 'Mustang', 'post_status' => 'draft' ),
	5  => array( 'ID' => 5, 'post_name' => 'not-a-real-make', 'post_parent' => 1, 'post_title' => 'Not A Real Make', 'post_status' => 'publish', 'template' => 'page-vehicle-make.php' ),
	6  => array( 'ID' => 6, 'post_name' => 'unrelated-page', 'post_parent' => 0, 'post_title' => 'Unrelated Page', 'post_status' => 'publish' ),
	7  => array( 'ID' => 7, 'post_name' => 'toyota', 'post_parent' => 6, 'post_title' => 'Toyota', 'post_status' => 'publish', 'template' => 'page-vehicle-make.php' ), // make attached to wrong grandparent
	8  => array( 'ID' => 8, 'post_name' => 'honda', 'post_parent' => 0, 'post_title' => 'Honda', 'post_status' => 'publish', 'template' => 'page-vehicle-make.php' ),
	9  => array( 'ID' => 9, 'post_name' => 'chevrolet', 'post_parent' => 1, 'post_title' => 'Chevrolet', 'post_status' => 'publish', 'template' => 'page-vehicle-make.php' ),
	10 => array( 'ID' => 10, 'post_name' => 'ck-2500', 'post_parent' => 9, 'post_title' => 'C/K 2500', 'post_status' => 'publish' ), // valid, WP-native slug
	11 => array( 'ID' => 11, 'post_name' => 'some-model', 'post_parent' => 6, 'post_title' => 'Some Model', 'post_status' => 'publish' ), // parent isn't make-shaped at all
	12 => array( 'ID' => 12, 'post_name' => 'orphan-model', 'post_parent' => 0, 'post_title' => 'Orphan Model', 'post_status' => 'publish' ), // no parent
	13 => array( 'ID' => 13, 'post_name' => 'camry', 'post_parent' => 7, 'post_title' => 'Camry', 'post_status' => 'publish' ), // make parent (toyota) not attached to real hub
	14 => array( 'ID' => 14, 'post_name' => 'some-model-2', 'post_parent' => 5, 'post_title' => 'Some Model 2', 'post_status' => 'publish' ), // parent attached to hub but not a real catalog make
	15 => array( 'ID' => 15, 'post_name' => 'not-a-real-model', 'post_parent' => 2, 'post_title' => 'Not A Real Model', 'post_status' => 'publish' ), // valid make, invalid model slug
	16 => array( 'ID' => 16, 'post_name' => 'f-150', 'post_parent' => 9, 'post_title' => 'F-150 Under Chevy', 'post_status' => 'publish' ), // valid model slug, WRONG make
	// A published "Bronco" sibling under Ford (catalog order: Bronco is
	// the very first Ford model) — proves the "Other Ford Models We
	// Service" section links a real published sibling and, since it's
	// the closest excluded-current-model neighbor, appears within the
	// max-6 window.
	17 => array( 'ID' => 17, 'post_name' => 'bronco', 'post_parent' => 2, 'post_title' => 'Bronco', 'post_status' => 'publish' ),
);

// --- Valid: Ford F-150 --------------------------------------------------

$html = rexroad_render_vehicle_model_page( 3 );

rexroad_test_check( 'render produced non-empty output', strlen( $html ) > 500 );
rexroad_test_check( 'exactly one H1', 1 === substr_count( $html, '<h1' ) );
rexroad_test_check( 'H1 reads "Ford F-150 Mobile Mechanic Service"', false !== strpos( $html, '<h1>Ford F-150 Mobile Mechanic Service</h1>' ) );
rexroad_test_check(
	'breadcrumb is Home -> Vehicles hub -> Ford -> F-150',
	1 === preg_match(
		'~Home</a>.*?Cars, Trucks &amp; SUVs We Service</a>.*?>Ford</a>.*?aria-current="page">F-150~s',
		$html
	)
);

$rexroad_f150       = rexroad_vehicle_get_model( 'ford', 'f-150' );
$rexroad_f150_years = htmlspecialchars( rexroad_vehicle_format_year_ranges( $rexroad_f150['years'] ), ENT_QUOTES );
rexroad_test_check(
	'exact supported years rendered, gaps preserved (formatter reused, not re-derived)',
	false !== strpos( $html, $rexroad_f150_years )
);

rexroad_test_check( 'context-aware "Supported Model Years" section present', false !== strpos( $html, '<h3>Supported Model Years</h3>' ) );
rexroad_test_check( 'service-links partial reused with context-aware heading ("Common Ford F-150 Services")', false !== strpos( $html, '<h2>Common Ford F-150 Services</h2>' ) );
rexroad_test_check( 'problem-links partial reused with model-specific, non-fabricated heading', false !== strpos( $html, '<h2>Problems We Diagnose on Ford F-150 Vehicles</h2>' ) );
rexroad_test_check( '"What We Can Diagnose and Repair at Your Location" capability section present', false !== strpos( $html, 'What We Can Diagnose and Repair at Your Location' ) );

rexroad_test_check(
	'"Other Ford Models We Service" section present, capped at 6, current model (F-150) excluded',
	1 === preg_match( '/Other Ford Models We Service.*?<ul class="rr-vehicle-model-list">(.*?)<\/ul>/s', $html, $rexroad_related_block )
	&& 6 === substr_count( $rexroad_related_block[1] ?? '', 'class="rr-vehicle-model"' )
	&& false === strpos( $rexroad_related_block[1] ?? '', '>F-150<' )
);
rexroad_test_check(
	'published sibling "Bronco" links in the related-models section; unpublished siblings stay plain text',
	false !== strpos( $html, '<a class="rr-vehicle-model__name" href="https://example.test/vehicles/ford/bronco/">Bronco</a>' )
);
rexroad_test_check(
	'related-models resolution uses exactly one get_pages() lookup (no N+1)',
	1 === $GLOBALS['rexroad_test_get_pages_calls']
);

rexroad_test_check( 'service-area section present, reusing existing footer configuration', false !== strpos( $html, 'Mobile Service, Wherever You Are' ) && false !== strpos( $html, 'Frisco' ) );
rexroad_test_check( 'CTA panel present', false !== strpos( $html, 'Request Service for Your Ford F-150' ) );
rexroad_test_check( 'no internal "model:" storage-key prefix leaked into output', false === strpos( $html, 'model:' ) );

$rexroad_html_visible_only = preg_replace( '/<!--.*?-->/s', '', $html );
rexroad_test_check(
	'customer-facing wording avoids database-sounding language ("catalog", "eligibility")',
	false === stripos( $rexroad_html_visible_only, 'eligibility' ) && false === stripos( $rexroad_html_visible_only, 'catalog' )
);

// --- C/K 2500 (WordPress-native slug) as a real model page --------------

$html_ck = rexroad_render_vehicle_model_page( 10 );
rexroad_test_check(
	'Chevrolet "C/K 2500" (WP-native slug "ck-2500") renders its own model page correctly',
	false !== strpos( $html_ck, '<h1>Chevrolet C/K 2500 Mobile Mechanic Service</h1>' )
);

// --- In-body upward navigation (HARDEN fix) ----------------------------

rexroad_test_check(
	'model page renders an in-body "View All Ford Models" link to the real make permalink (catalog name used, not WP title)',
	1 === substr_count( $html, '<a href="https://example.test/vehicles/ford/">&larr; View All Ford Models</a>' )
);
rexroad_test_check(
	'model page also renders an in-body "Browse All Vehicles" link to the real hub permalink',
	1 === substr_count( $html, '<a href="https://example.test/vehicles/">Browse All Vehicles</a>' )
);

// --- Hierarchy failure modes: all must fall back gracefully -------------

$cases = array(
	11 => 'parent is not make-shaped at all',
	12 => 'no parent',
	13 => 'make parent not attached to the real Vehicles hub',
	14 => 'make parent attached to hub but slug is not a real catalog make',
	15 => 'valid make, invalid model slug',
	16 => 'valid model slug but under the WRONG make (f-150 under chevrolet)',
);

foreach ( $cases as $id => $label ) {
	$html_case = rexroad_render_vehicle_model_page( $id );
	rexroad_test_check( "graceful fallback (no fatal, no fabricated data): {$label}", strlen( $html_case ) > 50 );
	rexroad_test_check( "fallback has exactly one H1: {$label}", 1 === substr_count( $html_case, '<h1' ) );
	rexroad_test_check( "fallback does not fabricate a 'Mobile Mechanic Service' H1: {$label}", false === strpos( $html_case, 'Mobile Mechanic Service</h1>' ) );
	rexroad_test_check( "fallback still offers Request Service: {$label}", false !== strpos( $html_case, 'Request Service' ) );
	rexroad_test_check(
		"fallback does NOT render misleading in-body upward vehicle navigation: {$label}",
		false === strpos( $html_case, 'View All' ) && false === strpos( $html_case, 'Browse All Vehicles' )
	);
}

if ( $failures > 0 ) {
	fwrite( STDERR, "\n{$failures} render test(s) failed.\n" );
	exit( 1 );
}

echo "\nAll page-vehicle-model.php render tests passed.\n";
exit( 0 );
