<?php
/**
 * QA check (optional polish item): compares our
 * tools/vehicles/lib/slugify.php output against WordPress's real
 * sanitize_title_with_dashes() algorithm, for every make and model
 * name in the catalog.
 *
 * This matters because rexroad_vehicle_get_published_child_page_map()
 * matches catalog slugs against $child->post_name — the slug WordPress
 * itself assigns when an editor creates a Page. If our slugify() ever
 * disagrees with WordPress's own algorithm for a given name, an editor
 * creating that model's Page with the default WP-generated slug would
 * silently never link, even though the page exists and is published.
 *
 * The WP algorithm below is a faithful port of
 * sanitize_title_with_dashes() from wp-includes/formatting.php for the
 * ASCII-only, no-accented-character case our catalog is limited to
 * (strip_tags/remove_accents/entity-stripping are no-ops on this data).
 *
 * Usage: php tests/vehicles/test-slug-wp-compatibility.php
 */

declare( strict_types=1 );

$root = dirname( __DIR__, 2 );

require $root . '/tools/vehicles/lib/vehicle-catalog-builder.php';

/**
 * Faithful port of WordPress's sanitize_title_with_dashes() for plain
 * ASCII input (no HTML tags, no entities, no accented characters —
 * true for every name in this catalog).
 */
function rexroad_wp_sanitize_title_with_dashes( string $title ): string {
	$title = str_replace( '.', '-', $title );
	// WP strips (does NOT hyphenate) anything outside [%a-zA-Z0-9 _-].
	$title = preg_replace( '/[^%a-zA-Z0-9 _-]/', '', $title ) ?? $title;
	$title = strtolower( $title );
	$title = preg_replace( '/\s+/', '-', $title ) ?? $title;
	$title = preg_replace( '/-+/', '-', $title ) ?? $title;
	return trim( $title, '-' );
}

define( 'ABSPATH', true );

$raw    = (string) file_get_contents( $root . '/tools/vehicles/source/us-market-vehicles-2000-2026.txt' );
$parsed = rexroad_vehicle_parse_source( $raw );

$mismatches = array();
$checked    = 0;

foreach ( $parsed['makes'] as $make ) {
	++$checked;
	$wp_slug = rexroad_wp_sanitize_title_with_dashes( $make['name'] );
	if ( $wp_slug !== $make['slug'] ) {
		$mismatches[] = "MAKE \"{$make['name']}\": ours=\"{$make['slug']}\" vs WP=\"{$wp_slug}\"";
	}

	foreach ( $make['models'] as $model ) {
		++$checked;
		$wp_slug = rexroad_wp_sanitize_title_with_dashes( $model['name'] );
		if ( $wp_slug !== $model['slug'] ) {
			$mismatches[] = "MODEL \"{$model['name']}\" ({$make['name']}): ours=\"{$model['slug']}\" vs WP=\"{$wp_slug}\"";
		}
	}
}

echo "Checked {$checked} catalog names (36 makes + 579 models) against WordPress's sanitize_title_with_dashes().\n\n";

if ( count( $mismatches ) > 0 ) {
	echo 'MISMATCHES FOUND: ' . count( $mismatches ) . "\n";
	foreach ( $mismatches as $m ) {
		echo "  - {$m}\n";
	}
	echo "\nThese names contain a character our slugify() converts to a\n";
	echo "hyphen but WordPress's own sanitize_title_with_dashes() strips\n";
	echo "outright ('/' being the only such character in this catalog).\n";
	echo "An editor creating that model's Page with WP's default\n";
	echo "auto-generated slug would get a DIFFERENT slug than our catalog\n";
	echo "uses, so rexroad_vehicle_get_published_child_page_map() would\n";
	echo "never match it and the link would silently never appear.\n";
	echo "Report this as a Phase 2B BLOCKER: either the affected Pages\n";
	echo "must have their slug set manually to match our catalog, or the\n";
	echo "model-page lookup needs an explicit alias for these cases.\n";
	exit( 1 );
}

echo "No mismatches. rexroad_vehicle_slugify() output matches WordPress's\n";
echo "own sanitize_title_with_dashes() for every current catalog name.\n";
echo "Slug generation left unchanged.\n";
exit( 0 );
