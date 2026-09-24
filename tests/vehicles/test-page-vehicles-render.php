<?php
/**
 * Dependency-free render test for rexroad-custom-theme/page-vehicles.php.
 *
 * Stubs WordPress core glue (see lib/wp-stubs.php) but exercises the
 * REAL template file and the REAL vehicle catalog accessors — this
 * proves the actual shipped code renders the complete directory
 * correctly, not just a re-implementation of it.
 *
 * Usage: php tests/vehicles/test-page-vehicles-render.php
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
 * Render page-vehicles.php with a given $_GET state and return the
 * captured HTML output.
 *
 * @param array<string, string> $get Superglobal $_GET to simulate.
 * @return string
 */
function rexroad_render_vehicles_page( array $get ): string {
	global $root;

	$_GET = $get;
	$GLOBALS['rexroad_test_have_posts_remaining'] = 1;

	ob_start();
	require $root . '/rexroad-custom-theme/page-vehicles.php';
	return (string) ob_get_clean();
}

// --- Default render (no search query) ---------------------------------

$html = rexroad_render_vehicles_page( array() );

rexroad_test_check( 'render produced non-empty output', strlen( $html ) > 1000 );

$h1_count = substr_count( $html, '<h1' );
rexroad_test_check( 'exactly one H1 on the page', 1 === $h1_count );

$make_count = substr_count( $html, 'class="rr-vehicle-make"' );
rexroad_test_check( 'directory renders exactly 36 makes', 36 === $make_count );

$model_count = substr_count( $html, 'class="rr-vehicle-model"' );
rexroad_test_check( 'directory renders exactly 579 models (each exactly once)', 579 === $model_count );

$domestic_pos = strpos( $html, 'Domestic Makes' );
$asian_pos    = strpos( $html, 'Asian Makes' );
$european_pos = strpos( $html, 'European Makes' );
rexroad_test_check(
	'group order is Domestic -> Asian -> European',
	false !== $domestic_pos && false !== $asian_pos && false !== $european_pos
	&& $domestic_pos < $asian_pos && $asian_pos < $european_pos
);

// Featured makes must render in the confirmed order.
// h3 content may be plain text or wrapped in an <a> (when a real make
// page exists) — capture the whole inner block and strip tags so both
// forms compare equal.
preg_match_all( '/rr-vehicle-make-card">\s*<h3>(.*?)<\/h3>/s', $html, $featured_matches );
$featured_names    = array_map( static fn( $m ) => trim( strip_tags( $m ) ), $featured_matches[1] ?? array() );
$expected_featured = array( 'Ford', 'Chevrolet', 'GMC', 'Ram', 'Dodge', 'Jeep', 'Honda', 'Toyota', 'Nissan', 'Hyundai', 'Kia', 'Subaru' );
rexroad_test_check(
	'featured makes render in the confirmed order',
	$expected_featured === $featured_names
);

// Discontinuous year ranges must render exactly, without flattening gaps.
rexroad_test_check(
	'Acura Integra renders its discontinuous year ranges intact (2000-2001, 2023-2026)',
	1 === preg_match( '/Integra<\/span>\s*<span class="rr-vehicle-model__years">2000\xe2\x80\x932001, 2023\xe2\x80\x932026</', $html )
);

// No fake /vehicles/{make}/ links anywhere (only #make-{slug} anchors and
// the page's own bare permalink are allowed).
rexroad_test_check(
	'no crawlable /vehicles/{make}/ style links were emitted',
	0 === preg_match( '~/vehicles/[a-z][a-z0-9-]*/~', $html )
);

// No internal implementation details leaked into the markup.
rexroad_test_check( 'no internal "model:" storage-key prefix leaked into output', false === strpos( $html, 'model:' ) );
rexroad_test_check( 'no source hash leaked into output', false === stripos( $html, 'source_hash' ) && false === stripos( $html, 'sha256' ) );

// aria-live status region present for the search.
rexroad_test_check( 'search status region has aria-live="polite"', false !== strpos( $html, 'aria-live="polite"' ) );

// Accessible label for the search input.
rexroad_test_check(
	'search input has an associated accessible label',
	false !== strpos( $html, 'for="rexroad-vehicle-search-input"' ) && false !== strpos( $html, 'id="rexroad-vehicle-search-input"' )
);

// A-Z nav is a real <nav> landmark with a label.
rexroad_test_check( 'A-Z nav has an aria-label', false !== strpos( $html, '<nav class="rr-vehicle-az-nav" aria-label=' ) );

// Only the 8 verified service slugs may appear as service links, and
// only ones that exist in the real canonical registry.
$known_slugs = rexroad_custom_get_service_slugs();
foreach ( array( 'advanced-diagnostics', 'brakes-service', 'auto-battery-replacement', 'alternator-and-starter-repair', 'cooling-system-service', 'auto-air-condition-service', 'suspension-and-steering', 'fuel-pump-replacement' ) as $expected_slug ) {
	rexroad_test_check(
		"service link slug \"{$expected_slug}\" exists in the canonical service registry",
		in_array( $expected_slug, $known_slugs, true )
	);
	rexroad_test_check(
		"service link to /services/{$expected_slug}/ is present",
		false !== strpos( $html, '/services/' . $expected_slug . '/' )
	);
}

// --- Search: "F150" should match Ford F-150 (punctuation-insensitive) -

$html_search_f150 = rexroad_render_vehicles_page( array( 'vehicle_search' => 'F150' ) );
rexroad_test_check(
	'search "F150" (no-JS GET fallback) links to Ford in the results',
	1 === preg_match( '/id="vehicle-search-results".*?href="#make-ford"/s', $html_search_f150 )
);
rexroad_test_check(
	'search "F150" still renders the complete 36-make / 579-model directory alongside results',
	36 === substr_count( $html_search_f150, 'class="rr-vehicle-make"' )
	&& 579 === substr_count( $html_search_f150, 'class="rr-vehicle-model"' )
);

// --- Search: "CRV" should match Honda CR-V -----------------------------

$html_search_crv = rexroad_render_vehicles_page( array( 'vehicle_search' => 'CRV' ) );
rexroad_test_check(
	'search "CRV" (no dash) links to Honda in the results',
	1 === preg_match( '/id="vehicle-search-results".*?href="#make-honda"/s', $html_search_crv )
);

// --- Default render with no query still contains the complete directory
//     (the no-JS / "no query-dependent content" requirement).

rexroad_test_check(
	'complete directory is present with zero query parameters (no-JS baseline)',
	36 === substr_count( $html, 'class="rr-vehicle-make"' ) && 579 === substr_count( $html, 'class="rr-vehicle-model"' )
);

// --- Hub make-name linking: only a real published child Page under ----
//     the Vehicles hub turns a make name into a link; a single
//     get_pages() lookup drives both the featured cards and the full
//     directory — never one lookup per catalog make.

$GLOBALS['rexroad_test_pages'] = array(
	1 => array( 'ID' => 1, 'post_name' => 'vehicles', 'post_parent' => 0, 'post_title' => 'Cars, Trucks & SUVs We Service', 'post_status' => 'publish' ),
	2 => array( 'ID' => 2, 'post_name' => 'ford', 'post_parent' => 1, 'post_title' => 'Ford', 'post_status' => 'publish' ),
	3 => array( 'ID' => 3, 'post_name' => 'chevrolet', 'post_parent' => 1, 'post_title' => 'Chevrolet', 'post_status' => 'draft' ),
	// A real, published child of the hub whose slug matches NO catalog
	// make (e.g. an "Our Story" page an editor puts under /vehicles/
	// for unrelated reasons) — must never become a false make link.
	4 => array( 'ID' => 4, 'post_name' => 'our-story', 'post_parent' => 1, 'post_title' => 'Our Story', 'post_status' => 'publish' ),
);
$GLOBALS['rexroad_test_current_post_id'] = 1;
$GLOBALS['rexroad_test_get_pages_calls'] = 0;

$html_linked = rexroad_render_vehicles_page( array() );

rexroad_test_check(
	'exactly one get_pages() lookup drives all make-name linking on the hub',
	1 === $GLOBALS['rexroad_test_get_pages_calls']
);
rexroad_test_check(
	'Ford (published child page) becomes a real link to /vehicles/ford/ in both the featured card and the full directory',
	2 === substr_count( $html_linked, '<a href="https://example.test/vehicles/ford/">Ford</a>' )
);
rexroad_test_check(
	'Chevrolet (draft child page — not published) stays plain text, not linked',
	false === strpos( $html_linked, 'href="https://example.test/vehicles/chevrolet/"' )
);
rexroad_test_check(
	'an unrelated published child ("our-story", not a catalog make) never becomes a false make link',
	false === strpos( $html_linked, 'href="https://example.test/vehicles/our-story/"' )
	&& false === strpos( $html_linked, '>Our Story<' )
);

// Reset the page fixture back to the single-page default for any
// future assertions added after this block.
$GLOBALS['rexroad_test_pages']           = array(
	1 => array( 'ID' => 1, 'post_name' => 'vehicles', 'post_parent' => 0, 'post_title' => null, 'post_status' => 'publish' ),
);
$GLOBALS['rexroad_test_current_post_id'] = 1;

if ( $failures > 0 ) {
	fwrite( STDERR, "\n{$failures} render test(s) failed.\n" );
	exit( 1 );
}

echo "\nAll page-vehicles.php render tests passed.\n";
exit( 0 );
