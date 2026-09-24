<?php
/**
 * Deterministic slug generation shared by the vehicle data build and
 * validation scripts. No WordPress dependency.
 */

declare( strict_types=1 );

/**
 * Convert a make or model name into a URL-safe slug.
 *
 * Matches WordPress's own sanitize_title_with_dashes() semantics for
 * this catalog's character set: disallowed characters (anything
 * outside [a-z0-9-] once whitespace has become "-") are STRIPPED, not
 * substituted with a hyphen. This matters because
 * rexroad_vehicle_get_published_child_page_map() matches catalog
 * slugs against the slug WordPress itself auto-generates when an
 * editor creates a Page — e.g. "C/K 2500" -> "ck-2500" in both
 * systems, not "c-k-2500".
 *
 * Rules (applied in order):
 * 1. Lowercase.
 * 2. "." becomes "-" (matches WordPress's own explicit period-to-dash
 *    step in sanitize_title_with_dashes(), applied before its generic
 *    strip — e.g. a future name like "R.S. Turbo" -> "r-s-turbo" in
 *    both systems). No current catalog name contains a period; this
 *    is a no-op today and only matters for future catalog entries.
 * 3. Any run of whitespace becomes a single "-".
 * 4. Anything outside [a-z0-9-] is stripped outright (e.g. "/" is
 *    deleted, not hyphenated — "C/K 2500" -> "ck-2500").
 * 5. Repeated "-" collapse to one; leading/trailing "-" trimmed.
 *
 * @param string $name Source name.
 * @return string
 */
function rexroad_vehicle_slugify( string $name ): string {
	$slug = strtolower( $name );
	$slug = str_replace( '.', '-', $slug );
	$slug = preg_replace( '/\s+/', '-', $slug ) ?? $slug;
	$slug = preg_replace( '/[^a-z0-9-]/', '', $slug ) ?? $slug;
	$slug = preg_replace( '/-+/', '-', $slug ) ?? $slug;

	return trim( $slug, '-' );
}
