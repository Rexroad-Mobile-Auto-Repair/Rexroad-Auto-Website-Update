<?php
/**
 * Dependency-free tests for rexroad-custom-theme/inc/schema-vehicles.php.
 *
 * Exercises the real schema function directly. The business/website
 * schema helpers (rexroad_custom_get_website_schema /
 * rexroad_custom_get_base_business_schema) are stubbed to minimal fixed
 * arrays here, deliberately — this test is about schema-vehicles.php's
 * OWN assembly/gating logic, not about re-verifying the existing
 * business schema (that belongs to inc/structured-data.php).
 *
 * Usage: php tests/vehicles/test-page-schema.php
 */

declare( strict_types=1 );

$root = dirname( __DIR__, 2 );

require __DIR__ . '/lib/wp-stubs.php';

function get_template_directory(): string {
	global $root;
	return $root . '/rexroad-custom-theme';
}

function rexroad_custom_get_website_schema(): array {
	return array( '@type' => 'WebSite', '@id' => 'https://example.test/#website' );
}

function rexroad_custom_get_base_business_schema(): array {
	return array( '@type' => 'AutoRepair', '@id' => 'https://example.test/#auto-repair' );
}

require $root . '/rexroad-custom-theme/inc/vehicles/vehicles.php';
require $root . '/rexroad-custom-theme/inc/schema-service.php';
require $root . '/rexroad-custom-theme/inc/schema-vehicles.php';

$failures = 0;

function rexroad_test_check( string $label, bool $condition ): void {
	global $failures;
	echo ( $condition ? 'PASS' : 'FAIL' ) . ": {$label}\n";
	if ( ! $condition ) {
		++$failures;
	}
}

// --- On the vehicles page: schema should render, restrained -----------

$GLOBALS['rexroad_test_current_template'] = 'page-vehicles.php';

ob_start();
rexroad_custom_vehicles_directory_schema();
$schema_html = (string) ob_get_clean();

rexroad_test_check( 'schema script tag is emitted on the vehicles page', false !== strpos( $schema_html, '<script type="application/ld+json">' ) );
rexroad_test_check( 'graph includes WebSite', false !== strpos( $schema_html, '"@type":"WebSite"' ) );
rexroad_test_check( 'graph includes CollectionPage', false !== strpos( $schema_html, '"@type":"CollectionPage"' ) );
rexroad_test_check( 'graph includes BreadcrumbList', false !== strpos( $schema_html, '"@type":"BreadcrumbList"' ) );
rexroad_test_check( 'graph includes the existing AutoRepair business node', false !== strpos( $schema_html, '"@type":"AutoRepair"' ) );

foreach ( array( 'Vehicle', 'Product', 'Offer', 'Review', 'ItemList' ) as $forbidden_type ) {
	rexroad_test_check(
		"graph does NOT include a \"{$forbidden_type}\" node",
		false === strpos( $schema_html, '"@type":"' . $forbidden_type . '"' )
	);
}

// --- Off the vehicles page: schema should not render at all -----------

$GLOBALS['rexroad_test_current_template'] = 'page-vehicle-make.php';

ob_start();
rexroad_custom_vehicles_directory_schema();
$off_page_html = (string) ob_get_clean();

rexroad_test_check( 'no vehicles-directory schema output when on a different template', '' === $off_page_html );

// --- Vehicle Make Page schema: render, restrained, breadcrumb --------

$GLOBALS['rexroad_test_pages'] = array(
	1 => array( 'ID' => 1, 'post_name' => 'vehicles', 'post_parent' => 0, 'post_title' => 'Cars, Trucks & SUVs We Service', 'post_status' => 'publish', 'template' => 'page-vehicles.php' ),
	// Editor typo/variant: WP Page title differs from the catalog name
	// ("Ford") on purpose — schema naming must still say "Ford" (HARDEN 2).
	2 => array( 'ID' => 2, 'post_name' => 'ford', 'post_parent' => 1, 'post_title' => 'Ford Cars', 'post_status' => 'publish' ),
);
$GLOBALS['rexroad_test_current_post_id']  = 2;
$GLOBALS['rexroad_test_current_template'] = 'page-vehicle-make.php';

ob_start();
rexroad_custom_vehicle_make_page_schema();
$make_schema_html = (string) ob_get_clean();

rexroad_test_check( 'make-page schema script tag is emitted', false !== strpos( $make_schema_html, '<script type="application/ld+json">' ) );
rexroad_test_check( 'make-page graph includes WebPage (not CollectionPage)', false !== strpos( $make_schema_html, '"@type":"WebPage"' ) );
rexroad_test_check( 'make-page graph includes BreadcrumbList', false !== strpos( $make_schema_html, '"@type":"BreadcrumbList"' ) );
rexroad_test_check( 'make-page graph includes the existing AutoRepair business node', false !== strpos( $make_schema_html, '"@type":"AutoRepair"' ) );
rexroad_test_check(
	'make-page breadcrumb is Home -> Vehicles hub title -> Ford',
	false !== strpos( $make_schema_html, '"name":"Home"' )
	&& false !== strpos( $make_schema_html, '"name":"Cars, Trucks & SUVs We Service"' )
	&& false !== strpos( $make_schema_html, '"name":"Ford"' )
);
rexroad_test_check(
	'HARDEN 2: WebPage "name" and breadcrumb use the catalog name ("Ford"), not the differently-worded WP Page title ("Ford Cars")',
	false === strpos( $make_schema_html, 'Ford Cars' )
);

// Wrong-parent case: a page slugged "toyota" whose parent is NOT the
// real Vehicles hub must not emit catalog-driven make naming either.
$GLOBALS['rexroad_test_pages'][3]        = array( 'ID' => 3, 'post_name' => 'unrelated', 'post_parent' => 0, 'post_title' => 'Unrelated', 'post_status' => 'publish' );
$GLOBALS['rexroad_test_pages'][4]        = array( 'ID' => 4, 'post_name' => 'toyota', 'post_parent' => 3, 'post_title' => 'Toyota Page', 'post_status' => 'publish' );
$GLOBALS['rexroad_test_current_post_id'] = 4;

ob_start();
rexroad_custom_vehicle_make_page_schema();
$make_wrong_parent_html = (string) ob_get_clean();

rexroad_test_check(
	'HARDEN 1: wrong-parent page schema falls back to its own WP title ("Toyota Page"), not fabricated catalog naming',
	false !== strpos( $make_wrong_parent_html, '"name":"Toyota Page"' )
);

$GLOBALS['rexroad_test_current_post_id'] = 2;

foreach ( array( 'Vehicle', 'Product', 'Offer', 'Review', 'ItemList', 'CollectionPage' ) as $forbidden_type ) {
	rexroad_test_check(
		"make-page graph does NOT include a \"{$forbidden_type}\" node",
		false === strpos( $make_schema_html, '"@type":"' . $forbidden_type . '"' )
	);
}

$GLOBALS['rexroad_test_current_template'] = 'page-vehicles.php';

ob_start();
rexroad_custom_vehicle_make_page_schema();
$make_off_page_html = (string) ob_get_clean();

rexroad_test_check( 'no make-page schema output when on a different template', '' === $make_off_page_html );

// --- Vehicle Model Page schema: render, restrained, 4-level breadcrumb

$GLOBALS['rexroad_test_pages'][5]        = array( 'ID' => 5, 'post_name' => 'f-150', 'post_parent' => 2, 'post_title' => 'F-150 Page Title', 'post_status' => 'publish' );
$GLOBALS['rexroad_test_current_post_id'] = 5;
$GLOBALS['rexroad_test_current_template'] = 'page-vehicle-model.php';

ob_start();
rexroad_custom_vehicle_model_page_schema();
$model_schema_html = (string) ob_get_clean();

rexroad_test_check( 'model-page schema script tag is emitted', false !== strpos( $model_schema_html, '<script type="application/ld+json">' ) );
rexroad_test_check( 'model-page graph includes WebPage', false !== strpos( $model_schema_html, '"@type":"WebPage"' ) );
rexroad_test_check( 'model-page graph includes BreadcrumbList', false !== strpos( $model_schema_html, '"@type":"BreadcrumbList"' ) );
rexroad_test_check( 'model-page graph includes the existing AutoRepair business node', false !== strpos( $model_schema_html, '"@type":"AutoRepair"' ) );
rexroad_test_check(
	'model-page breadcrumb is Home -> Vehicles hub -> Ford -> F-150 (catalog names, not the differently-worded WP title)',
	false !== strpos( $model_schema_html, '"name":"Home"' )
	&& false !== strpos( $model_schema_html, '"name":"Cars, Trucks & SUVs We Service"' )
	&& false !== strpos( $model_schema_html, '"name":"Ford"' )
	&& false !== strpos( $model_schema_html, '"name":"F-150"' )
	&& false === strpos( $model_schema_html, 'F-150 Page Title' )
);

foreach ( array( 'Vehicle', 'Product', 'Offer', 'Review', 'ItemList', 'CollectionPage' ) as $forbidden_type ) {
	rexroad_test_check(
		"model-page graph does NOT include a \"{$forbidden_type}\" node",
		false === strpos( $model_schema_html, '"@type":"' . $forbidden_type . '"' )
	);
}

// Unresolvable model page (e.g. wrong parent) must not emit misleading
// model-specific naming — falls back to its own WP title only.
$GLOBALS['rexroad_test_pages'][6]        = array( 'ID' => 6, 'post_name' => 'orphan-model', 'post_parent' => 0, 'post_title' => 'Orphan Model Page', 'post_status' => 'publish' );
$GLOBALS['rexroad_test_current_post_id'] = 6;

ob_start();
rexroad_custom_vehicle_model_page_schema();
$model_orphan_html = (string) ob_get_clean();

rexroad_test_check(
	'unresolvable model page schema falls back to its own WP title, no fabricated make/model naming',
	false !== strpos( $model_orphan_html, '"name":"Orphan Model Page"' )
);

$GLOBALS['rexroad_test_current_template'] = 'page-vehicle-make.php';

ob_start();
rexroad_custom_vehicle_model_page_schema();
$model_off_page_html = (string) ob_get_clean();

rexroad_test_check( 'no model-page schema output when on a different template', '' === $model_off_page_html );

// --- Third-party SEO suite suppression (checked LAST: defining the ----
//     constant is irreversible for the rest of this process) -----------

$GLOBALS['rexroad_test_current_template'] = 'page-vehicles.php';
define( 'WPSEO_VERSION', '99.0' );

ob_start();
rexroad_custom_vehicles_directory_schema();
$suppressed_html = (string) ob_get_clean();

rexroad_test_check( 'vehicles-directory schema is suppressed when a third-party SEO suite (WPSEO_VERSION) is active', '' === $suppressed_html );

$GLOBALS['rexroad_test_current_template'] = 'page-vehicle-make.php';

ob_start();
rexroad_custom_vehicle_make_page_schema();
$make_suppressed_html = (string) ob_get_clean();

rexroad_test_check( 'make-page schema is also suppressed when a third-party SEO suite is active', '' === $make_suppressed_html );

$GLOBALS['rexroad_test_current_post_id']  = 5;
$GLOBALS['rexroad_test_current_template'] = 'page-vehicle-model.php';

ob_start();
rexroad_custom_vehicle_model_page_schema();
$model_suppressed_html = (string) ob_get_clean();

rexroad_test_check( 'model-page schema is also suppressed when a third-party SEO suite is active', '' === $model_suppressed_html );

if ( $failures > 0 ) {
	fwrite( STDERR, "\n{$failures} schema test(s) failed.\n" );
	exit( 1 );
}

echo "\nAll schema-vehicles.php tests passed.\n";
exit( 0 );
