<?php
/**
 * Read-only accessor layer for the generated vehicle catalog.
 *
 * Every model is stored internally under a "model:{slug}" array key to
 * avoid PHP's automatic casting of purely-numeric string keys (e.g.
 * "1500") to int. Callers must always use the explicit "slug" field
 * returned in each model/make array — never assume the storage key.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

/**
 * Load (and memoize for this request) the generated vehicle catalog.
 *
 * @return array
 */
function rexroad_vehicle_data(): array {
	static $data = null;

	if ( null === $data ) {
		$file = get_template_directory() . '/inc/vehicles/vehicle-data.php';
		$data = file_exists( $file ) ? require $file : array( 'makes' => array() );
	}

	return $data;
}

/**
 * All makes in catalog (source) order.
 *
 * @return array<int, array{slug: string, name: string, model_count: int}>
 */
function rexroad_vehicle_get_makes(): array {
	$data  = rexroad_vehicle_data();
	$makes = array();

	foreach ( $data['makes'] ?? array() as $make ) {
		$makes[] = array(
			'slug'        => $make['slug'],
			'name'        => $make['name'],
			'model_count' => count( $make['models'] ?? array() ),
		);
	}

	return $makes;
}

/**
 * A single make record by slug.
 *
 * @param string $make_slug Make slug, e.g. "ford".
 * @return array{slug: string, name: string}|null
 */
function rexroad_vehicle_get_make( string $make_slug ): ?array {
	$data = rexroad_vehicle_data();
	$make = $data['makes'][ $make_slug ] ?? null;

	if ( null === $make ) {
		return null;
	}

	return array(
		'slug' => $make['slug'],
		'name' => $make['name'],
	);
}

/**
 * All models for a given make, in catalog (source) order.
 *
 * @param string $make_slug Make slug, e.g. "ram".
 * @return array<int, array{slug: string, name: string, years: array<int, array{0:int,1:int}>}>
 */
function rexroad_vehicle_get_models( string $make_slug ): array {
	$data  = rexroad_vehicle_data();
	$make  = $data['makes'][ $make_slug ] ?? null;
	$models = array();

	if ( null === $make ) {
		return $models;
	}

	foreach ( $make['models'] as $model ) {
		$models[] = array(
			'slug'  => $model['slug'],
			'name'  => $model['name'],
			'years' => $model['years'],
		);
	}

	return $models;
}

/**
 * A single model record by make slug + model slug.
 *
 * @param string $make_slug  Make slug, e.g. "ram".
 * @param string $model_slug Model slug, e.g. "1500".
 * @return array{slug: string, name: string, years: array<int, array{0:int,1:int}>}|null
 */
function rexroad_vehicle_get_model( string $make_slug, string $model_slug ): ?array {
	$data  = rexroad_vehicle_data();
	$model = $data['makes'][ $make_slug ]['models'][ 'model:' . $model_slug ] ?? null;

	if ( null === $model ) {
		return null;
	}

	return array(
		'slug'  => $model['slug'],
		'name'  => $model['name'],
		'years' => $model['years'],
	);
}

/**
 * Normalize a free-text search term for matching: lowercase, strip
 * everything except letters and digits. This makes "F-150", "F150",
 * and "f 150" all compare equal, and "CR-V"/"CRV" likewise.
 *
 * @param string $term Raw search input.
 * @return string
 */
function rexroad_vehicle_normalize_search_term( string $term ): string {
	$term = strtolower( $term );
	return preg_replace( '/[^a-z0-9]/', '', $term ) ?? '';
}

/**
 * Search makes and models by normalized substring match.
 *
 * Does not consult any curated alias list — that is a separate, not-yet
 * -built config layer. This is direct make/model name matching only.
 *
 * @param string $query Raw search input.
 * @return array<int, array{type: string, make_slug: string, make_name: string, model_slug: string|null, model_name: string|null}>
 */
function rexroad_vehicle_search( string $query ): array {
	$needle = rexroad_vehicle_normalize_search_term( $query );

	if ( '' === $needle ) {
		return array();
	}

	$results = array();
	$data    = rexroad_vehicle_data();

	foreach ( $data['makes'] ?? array() as $make ) {
		$make_haystack = rexroad_vehicle_normalize_search_term( $make['name'] );

		if ( false !== strpos( $make_haystack, $needle ) ) {
			$results[] = array(
				'type'       => 'make',
				'make_slug'  => $make['slug'],
				'make_name'  => $make['name'],
				'model_slug' => null,
				'model_name' => null,
			);
		}

		foreach ( $make['models'] as $model ) {
			$model_haystack = rexroad_vehicle_normalize_search_term( $model['name'] );

			if ( false !== strpos( $model_haystack, $needle ) ) {
				$results[] = array(
					'type'       => 'model',
					'make_slug'  => $make['slug'],
					'make_name'  => $make['name'],
					'model_slug' => $model['slug'],
					'model_name' => $model['name'],
				);
			}
		}
	}

	return $results;
}

/**
 * Load (and memoize for this request) the curated make-grouping config
 * (Domestic / Asian / European). Presentation-only; see
 * inc/vehicles/vehicle-groups.php for the confirmed assignment.
 *
 * @return array<string, array{name: string, makes: array<int, string>}>
 */
function rexroad_vehicle_groups(): array {
	static $groups = null;

	if ( null === $groups ) {
		$file   = get_template_directory() . '/inc/vehicles/vehicle-groups.php';
		$groups = file_exists( $file ) ? require $file : array();
	}

	return $groups;
}

/**
 * Load (and memoize for this request) the curated featured-make display
 * order. Presentation-only; see inc/vehicles/vehicle-featured.php.
 *
 * @return array<int, string> Make slugs, most prominent first.
 */
function rexroad_vehicle_featured_makes(): array {
	static $featured = null;

	if ( null === $featured ) {
		$file     = get_template_directory() . '/inc/vehicles/vehicle-featured.php';
		$featured = file_exists( $file ) ? require $file : array();
	}

	return $featured;
}

/**
 * Format a model's year-range segments for display, preserving gaps
 * exactly (e.g. "2000–2001, 2023–2026"). A single-year range renders as
 * just that year (e.g. "2013").
 *
 * @param array<int, array{0:int,1:int}> $years Year-range pairs.
 * @return string
 */
function rexroad_vehicle_format_year_ranges( array $years ): string {
	$parts = array();

	foreach ( $years as $range ) {
		[ $start, $end ] = $range;
		$parts[] = $start === $end ? (string) $start : $start . "\xe2\x80\x93" . $end;
	}

	return implode( ', ', $parts );
}

/**
 * Return a slug => permalink map of a parent page's published, direct
 * child Pages (any template). One get_pages() call — never one lookup
 * per catalog make/model. Used to decide whether a catalog make/model
 * has a real, editor-published SEO landing page; the catalog itself
 * never implies a page exists.
 *
 * @param int $parent_id Parent page ID.
 * @return array<string, string> slug => permalink.
 */
function rexroad_vehicle_get_published_child_page_map( int $parent_id ): array {
	if ( $parent_id <= 0 ) {
		return array();
	}

	$children = get_pages(
		array(
			'parent'      => $parent_id,
			'post_status' => 'publish',
			'sort_column' => 'menu_order',
		)
	);

	$map = array();
	foreach ( $children as $child ) {
		$permalink = get_permalink( $child );
		if ( $permalink ) {
			$map[ $child->post_name ] = $permalink;
		}
	}

	return $map;
}

/**
 * Resolve the validated catalog make for a GIVEN post, or null.
 *
 * A make only resolves when BOTH are true:
 *  - the post's own slug matches a catalog make, AND
 *  - the post's direct parent is the real Vehicles hub page (identified
 *    by its template, not a hardcoded slug/ID — so this survives the
 *    hub page being renamed or its slug changed).
 *
 * This is the single source of truth for "is this really a make page".
 * page-vehicle-make.php, page-vehicle-model.php, and schema-vehicles.php
 * all call it (directly or via the two wrappers below) so their notion
 * of validity can never drift apart. Never fabricates data: a matching
 * slug under the wrong (or no) parent still returns null.
 *
 * @param WP_Post|null $post Post to resolve; null returns null (no
 *                            "current post" guessing — callers pass an
 *                            explicit post so this stays a pure function).
 * @return array{slug: string, name: string}|null
 */
function rexroad_vehicle_resolve_make_from_page( ?WP_Post $post ): ?array {
	if ( ! $post instanceof WP_Post || ! $post->post_parent ) {
		return null;
	}

	$parent = get_post( $post->post_parent );

	if ( ! $parent instanceof WP_Post || 'page-vehicles.php' !== get_page_template_slug( $parent->ID ) ) {
		return null;
	}

	return rexroad_vehicle_get_make( $post->post_name );
}

/**
 * Convenience wrapper: resolve the validated catalog make for the
 * CURRENT post. See rexroad_vehicle_resolve_make_from_page().
 *
 * @return array{slug: string, name: string}|null
 */
function rexroad_vehicle_resolve_make_from_current_page(): ?array {
	return rexroad_vehicle_resolve_make_from_page( get_post() );
}

/**
 * Resolve the validated catalog make + model for the CURRENT post, or
 * null.
 *
 * A model only resolves when the FULL three-level hierarchy is valid:
 *  - the current post's direct parent resolves as a valid make page
 *    (rexroad_vehicle_resolve_make_from_page() — which itself requires
 *    THAT page's parent to be the real Vehicles hub), AND
 *  - the current post's own slug matches a model under that specific
 *    resolved make.
 *
 * A model slug that exists under a DIFFERENT make, or a make page that
 * isn't itself validly hung off the Vehicles hub, or no parent at all,
 * all return null rather than fabricating a relationship. Never
 * fabricates data.
 *
 * @return array{make: array{slug: string, name: string}, model: array{slug: string, name: string, years: array<int, array{0:int,1:int}>}}|null
 */
function rexroad_vehicle_resolve_model_from_current_page(): ?array {
	$post = get_post();

	if ( ! $post instanceof WP_Post || ! $post->post_parent ) {
		return null;
	}

	$make_post = get_post( $post->post_parent );
	$make      = rexroad_vehicle_resolve_make_from_page( $make_post );

	if ( null === $make ) {
		return null;
	}

	$model = rexroad_vehicle_get_model( $make['slug'], $post->post_name );

	if ( null === $model ) {
		return null;
	}

	return array(
		'make'  => $make,
		'model' => $model,
	);
}
