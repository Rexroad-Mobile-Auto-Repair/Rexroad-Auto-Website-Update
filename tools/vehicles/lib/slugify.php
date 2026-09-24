<?php
/**
 * Deterministic slug generation shared by the vehicle data build and
 * validation scripts. No WordPress dependency.
 */

declare( strict_types=1 );

/**
 * Convert a make or model name into a URL-safe slug.
 *
 * Rules (applied in order):
 * 1. Lowercase.
 * 2. "/" becomes "-" (e.g. "C/K 2500" -> "c-k-2500").
 * 3. Any run of whitespace becomes a single "-".
 * 4. Anything outside [a-z0-9-] is stripped.
 * 5. Repeated "-" collapse to one; leading/trailing "-" trimmed.
 *
 * @param string $name Source name.
 * @return string
 */
function rexroad_vehicle_slugify( string $name ): string {
	$slug = strtolower( $name );
	$slug = str_replace( '/', '-', $slug );
	$slug = preg_replace( '/\s+/', '-', $slug ) ?? $slug;
	$slug = preg_replace( '/[^a-z0-9-]/', '', $slug ) ?? $slug;
	$slug = preg_replace( '/-+/', '-', $slug ) ?? $slug;

	return trim( $slug, '-' );
}
