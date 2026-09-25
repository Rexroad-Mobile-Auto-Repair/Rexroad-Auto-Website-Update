<?php
/**
 * Dependency-free render test for
 * rexroad-custom-theme/page-vehicle-make.php.
 *
 * Stubs WordPress core glue (see lib/wp-stubs.php) but exercises the
 * REAL template and the REAL vehicle catalog accessors.
 *
 * Usage: php tests/vehicles/test-page-vehicle-make-render.php
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
 * Render page-vehicle-make.php for a given "current" fixture post ID.
 *
 * @param int $post_id Fixture post ID to render as the current page.
 * @return string
 */
function rexroad_render_vehicle_make_page( int $post_id ): string {
	global $root;

	$GLOBALS['rexroad_test_current_post_id']  = $post_id;
	$GLOBALS['rexroad_test_have_posts_remaining'] = 1;
	$GLOBALS['rexroad_test_get_pages_calls']  = 0;

	ob_start();
	require $root . '/rexroad-custom-theme/page-vehicle-make.php';
	return (string) ob_get_clean();
}

// Fixture: Vehicles hub (1, using page-vehicles.php — this is what
// makes it "the real hub" for parent validation, not its slug) ->
// Ford (2) -> f-150 (3, published), mustang (4, draft — stays plain
// text). Page 5 has a slug that doesn't match any catalog make (the
// invalid-slug fallback case). Page 6 is an unrelated top-level page
// NOT using the Vehicles hub template. Page 7 ("toyota") sits under
// that unrelated page — a valid catalog slug with the WRONG parent.
// Page 8 ("honda") is top-level — a valid catalog slug with NO parent.
$GLOBALS['rexroad_test_pages'] = array(
	1 => array( 'ID' => 1, 'post_name' => 'vehicles', 'post_parent' => 0, 'post_title' => 'Cars, Trucks & SUVs We Service', 'post_status' => 'publish', 'template' => 'page-vehicles.php' ),
	2 => array( 'ID' => 2, 'post_name' => 'ford', 'post_parent' => 1, 'post_title' => 'Ford', 'post_status' => 'publish' ),
	3 => array( 'ID' => 3, 'post_name' => 'f-150', 'post_parent' => 2, 'post_title' => 'F-150', 'post_status' => 'publish' ),
	4 => array( 'ID' => 4, 'post_name' => 'mustang', 'post_parent' => 2, 'post_title' => 'Mustang', 'post_status' => 'draft' ),
	5 => array( 'ID' => 5, 'post_name' => 'not-a-real-make', 'post_parent' => 1, 'post_title' => 'Not A Real Make', 'post_status' => 'publish' ),
	6 => array( 'ID' => 6, 'post_name' => 'unrelated-page', 'post_parent' => 0, 'post_title' => 'Unrelated Page', 'post_status' => 'publish' ),
	7  => array( 'ID' => 7, 'post_name' => 'toyota', 'post_parent' => 6, 'post_title' => 'Toyota', 'post_status' => 'publish' ),
	8  => array( 'ID' => 8, 'post_name' => 'honda', 'post_parent' => 0, 'post_title' => 'Honda', 'post_status' => 'publish' ),
	// Chevrolet make page + a "C/K 2500" child page created with
	// WordPress's own native auto-generated slug ("ck-2500", not the
	// old "c-k-2500") — proves hierarchy linking works end-to-end with
	// the WordPress-compatible slug scheme.
	9  => array( 'ID' => 9, 'post_name' => 'chevrolet', 'post_parent' => 1, 'post_title' => 'Chevrolet', 'post_status' => 'publish' ),
	10 => array( 'ID' => 10, 'post_name' => 'ck-2500', 'post_parent' => 9, 'post_title' => 'C/K 2500', 'post_status' => 'publish' ),
	// Nissan: a valid make with ZERO child pages at all — every catalog
	// model must still render as plain text, page must still render fully.
	11 => array( 'ID' => 11, 'post_name' => 'nissan', 'post_parent' => 1, 'post_title' => 'Nissan', 'post_status' => 'publish' ),
	// A private "GT" child page under Ford — private status must stay
	// unlinked, same as draft.
	12 => array( 'ID' => 12, 'post_name' => 'gt', 'post_parent' => 2, 'post_title' => 'GT', 'post_status' => 'private' ),
);

// --- Valid make slug: Ford --------------------------------------------

$html = rexroad_render_vehicle_make_page( 2 );

rexroad_test_check( 'render produced non-empty output', strlen( $html ) > 500 );
rexroad_test_check( 'exactly one H1', 1 === substr_count( $html, '<h1' ) );
rexroad_test_check( 'H1 reads "Ford Mobile Mechanic Service"', false !== strpos( $html, '<h1>Ford Mobile Mechanic Service</h1>' ) );
rexroad_test_check(
	'breadcrumb reads Home -> Vehicles hub title -> Ford',
	1 === preg_match(
		'~<nav class="rr-vehicle-breadcrumb"[^>]*>.*?Home</a>.*?Cars, Trucks &amp; SUVs We Service</a>.*?aria-current="page">Ford~s',
		$html
	)
);

$rexroad_ford_models = rexroad_vehicle_get_models( 'ford' );
rexroad_test_check( 'Ford has more than 8 models in the catalog (so the 8-cap is meaningful)', count( $rexroad_ford_models ) > 8 );

rexroad_test_check( 'renamed "Models We Service" heading present (no popularity claim implied)', false !== strpos( $html, '<h2>Models We Service</h2>' ) );
rexroad_test_check( 'old popularity-implying heading ("Featured...") is gone', false === strpos( $html, 'Featured Ford Models' ) );

preg_match( '/Models We Service.*?<ul class="rr-vehicle-model-list">(.*?)<\/ul>/s', $html, $featured_block );
$featured_item_count = isset( $featured_block[1] ) ? substr_count( $featured_block[1], 'class="rr-vehicle-model"' ) : -1;
rexroad_test_check( '"Models We Service" section shows at most 8 models', 8 === $featured_item_count );

rexroad_test_check(
	'full model directory renders every Ford model exactly once',
	count( $rexroad_ford_models ) === substr_count( $html, 'class="rr-vehicle-model"' ) - $featured_item_count
);

rexroad_test_check(
	'F-150 (published child page) is linked to /vehicles/ford/f-150/',
	false !== strpos( $html, '<a class="rr-vehicle-model__name" href="https://example.test/vehicles/ford/f-150/">F-150</a>' )
);
rexroad_test_check(
	'Mustang (draft child page — not published) stays plain text, not linked',
	false === strpos( $html, 'href="https://example.test/vehicles/ford/mustang/"' )
);
rexroad_test_check(
	'GT (private child page — not published) stays plain text, not linked',
	false === strpos( $html, 'href="https://example.test/vehicles/ford/gt/"' )
	&& false !== strpos( $html, '<span class="rr-vehicle-model__name">GT</span>' )
);
rexroad_test_check(
	'exactly one get_pages() lookup resolves all model-page links (no N+1 per model)',
	1 === $GLOBALS['rexroad_test_get_pages_calls']
);

rexroad_test_check( 'service-links partial rendered with context-aware heading ("Common Ford Services")', false !== strpos( $html, '<h2>Common Ford Services</h2>' ) );
rexroad_test_check( 'problem-links partial rendered with neutral, non-fabricated heading', false !== strpos( $html, '<h2>Common Issues We Diagnose on Ford Vehicles</h2>' ) );
rexroad_test_check(
	'problem section explicitly disclaims any "unusually prone" implication',
	false !== strpos( $html, 'not a claim that this vehicle is unusually prone to any of them' )
);
rexroad_test_check( 'service-area section present, reusing existing footer service-area configuration', false !== strpos( $html, 'Frisco' ) && false !== strpos( $html, 'Mobile Service, Wherever You Are' ) );
rexroad_test_check( 'CTA panel present', false !== strpos( $html, 'Request Service for Your Ford' ) );
// Strip HTML comments (developer notes, never visible to a customer)
// before checking customer-FACING wording specifically.
$rexroad_html_visible_only = preg_replace( '/<!--.*?-->/s', '', $html );
rexroad_test_check(
	'customer-facing wording avoids database-sounding language ("catalog", "eligibility")',
	false === stripos( $rexroad_html_visible_only, 'eligibility' ) && false === stripos( $rexroad_html_visible_only, 'catalog' )
);

rexroad_test_check( 'no internal "model:" storage-key prefix leaked into output', false === strpos( $html, 'model:' ) );
rexroad_test_check( 'no source hash leaked into output', false === stripos( $html, 'source_hash' ) && false === stripos( $html, 'sha256' ) );

// --- Invalid make slug: graceful fallback, no fatal, no fabricated ----
//     vehicle data.

$html_invalid = rexroad_render_vehicle_make_page( 5 );

rexroad_test_check( 'invalid-slug page renders without fatal error', strlen( $html_invalid ) > 100 );
rexroad_test_check( 'invalid-slug page still has exactly one H1 (its own title)', 1 === substr_count( $html_invalid, '<h1' ) );
rexroad_test_check( 'invalid-slug page does NOT render a "Featured ... Models" section', false === strpos( $html_invalid, 'Featured' ) );
rexroad_test_check( 'invalid-slug page does NOT render a model directory', false === strpos( $html_invalid, 'Model Directory' ) );
rexroad_test_check( 'invalid-slug page does NOT fabricate any model rows', false === strpos( $html_invalid, 'class="rr-vehicle-model"' ) );
rexroad_test_check( 'invalid-slug page still shows Request Service (safe fallback, not a dead end)', false !== strpos( $html_invalid, 'Request Service' ) );

// --- HARDEN 1: parent-hierarchy validation -----------------------------

$html_wrong_parent = rexroad_render_vehicle_make_page( 7 );
rexroad_test_check(
	'valid make slug ("toyota") under the WRONG parent falls back gracefully, no fabricated data',
	false === strpos( $html_wrong_parent, 'Featured Toyota Models' )
	&& false === strpos( $html_wrong_parent, 'class="rr-vehicle-model"' )
	&& false !== strpos( $html_wrong_parent, '<h1>Toyota</h1>' ) // its own WP title, not "Toyota Mobile Mechanic Service"
);

$html_no_parent = rexroad_render_vehicle_make_page( 8 );
rexroad_test_check(
	'valid make slug ("honda") with NO parent falls back gracefully, no fabricated data',
	false === strpos( $html_no_parent, 'Featured Honda Models' )
	&& false === strpos( $html_no_parent, 'class="rr-vehicle-model"' )
);

rexroad_test_check(
	'valid slug + correct parent still produces the real /vehicles/ford/ permalink',
	false !== strpos( $html, 'https://example.test/vehicles/ford/' )
);

// --- HARDEN 2: catalog name is authoritative, not the WP Page title ----

$GLOBALS['rexroad_test_pages'][2]['post_title'] = 'Ford Cars'; // editor typo/variant
$html_title_mismatch = rexroad_render_vehicle_make_page( 2 );

rexroad_test_check(
	'H1 uses the catalog name ("Ford"), ignoring a differently-worded WP Page title',
	false !== strpos( $html_title_mismatch, '<h1>Ford Mobile Mechanic Service</h1>' )
);
rexroad_test_check(
	'visible breadcrumb current-page label uses the catalog name ("Ford"), not "Ford Cars"',
	1 === preg_match( '~aria-current="page">Ford</li>~', $html_title_mismatch )
	&& false === strpos( $html_title_mismatch, 'aria-current="page">Ford Cars' )
);

// --- Slug-compatibility fix: hierarchy linking with a WordPress- ------
//     native child-page slug ("ck-2500", not the old "c-k-2500") -------

$html_chevrolet = rexroad_render_vehicle_make_page( 9 );
rexroad_test_check(
	'"C/K 2500" (WordPress-native slug "ck-2500") links correctly on the Chevrolet make page',
	false !== strpos( $html_chevrolet, '<a class="rr-vehicle-model__name" href="https://example.test/vehicles/chevrolet/ck-2500/">C/K 2500</a>' )
);

// --- Make page with ZERO published child pages: renders fully, every --
//     catalog model stays plain text (no fabricated/broken links) ------

$html_nissan          = rexroad_render_vehicle_make_page( 11 );
$rexroad_nissan_models = rexroad_vehicle_get_models( 'nissan' );

rexroad_test_check(
	'Nissan (no child pages at all) still renders its H1 and full model directory',
	false !== strpos( $html_nissan, '<h1>Nissan Mobile Mechanic Service</h1>' )
	&& count( $rexroad_nissan_models ) > 0
);
$rexroad_nissan_expected_spans = min( 8, count( $rexroad_nissan_models ) ) + count( $rexroad_nissan_models ); // featured (<=8) + full directory
rexroad_test_check(
	'every Nissan model renders as plain text, none linked, with zero published children',
	0 === substr_count( $html_nissan, '<a class="rr-vehicle-model__name"' )
	&& $rexroad_nissan_expected_spans === substr_count( $html_nissan, '<span class="rr-vehicle-model__name">' )
);

// --- In-body upward navigation (HARDEN fix) ----------------------------

rexroad_test_check(
	'make page renders an in-body "Browse All Vehicles" link to the real hub permalink',
	1 === substr_count( $html, '<a href="https://example.test/vehicles/">&larr; Browse All Vehicles</a>' )
);
rexroad_test_check(
	'invalid-slug fallback page does NOT render the in-body upward vehicle-navigation link',
	false === strpos( $html_invalid, 'Browse All Vehicles' )
);
rexroad_test_check(
	'wrong-parent fallback page does NOT render the in-body upward vehicle-navigation link',
	false === strpos( $html_wrong_parent, 'Browse All Vehicles' )
);

// --- Editorial checklist contains the explicit non-publishable rule ---

$rexroad_checklist = (string) file_get_contents( $root . '/rexroad-custom-theme/inc/vehicles/EDITORIAL-CHECKLIST.md' );
rexroad_test_check(
	'EDITORIAL-CHECKLIST.md states the boilerplate-only page is not publishable',
	false !== stripos( $rexroad_checklist, 'is not publishable' )
);
rexroad_test_check(
	'EDITORIAL-CHECKLIST.md states an explicit ~150-word minimum for both page types',
	2 === substr_count( $rexroad_checklist, '150 words' )
);

if ( $failures > 0 ) {
	fwrite( STDERR, "\n{$failures} render test(s) failed.\n" );
	exit( 1 );
}

echo "\nAll page-vehicle-make.php render tests passed.\n";
exit( 0 );
