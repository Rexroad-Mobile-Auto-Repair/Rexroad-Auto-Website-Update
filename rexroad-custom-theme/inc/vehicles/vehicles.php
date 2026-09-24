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
