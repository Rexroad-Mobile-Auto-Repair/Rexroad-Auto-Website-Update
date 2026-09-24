<?php
/**
 * Shared parse/validate/render functions for the vehicle catalog build
 * and validation scripts. No WordPress dependency.
 */

declare( strict_types=1 );

require_once __DIR__ . '/slugify.php';

const REXROAD_VEHICLE_MIN_YEAR = 2000;
const REXROAD_VEHICLE_MAX_YEAR = 2026;

/**
 * Parse the source text into an ordered make/model/year structure.
 *
 * @param string $text Raw source file contents.
 * @return array{makes: array<int, array{name: string, slug: string, declared_count: int, models: array<int, array{name: string, slug: string, years: array<int, array{0:int,1:int}>}>}>, declared_model_count: int, declared_make_count: int}
 */
function rexroad_vehicle_parse_source( string $text ): array {
	$lines = preg_split( '/\r\n|\r|\n/', $text );
	if ( false === $lines ) {
		throw new RuntimeException( 'Failed to split source file into lines.' );
	}

	$declared_model_count = 0;
	$declared_make_count  = 0;

	foreach ( $lines as $line ) {
		if ( preg_match( '/^(\d+)\s+selected entries\s*\/\s*(\d+)\s+makes\s*$/', trim( $line ), $m ) ) {
			$declared_model_count = (int) $m[1];
			$declared_make_count  = (int) $m[2];
			break;
		}
	}

	$makes          = array();
	$current_make   = null;
	$expect_dashes  = false;
	$pending_header = null;

	$count = count( $lines );
	for ( $i = 0; $i < $count; $i++ ) {
		$line = rtrim( $lines[ $i ] );

		if ( $expect_dashes ) {
			$expect_dashes = false;
			if ( preg_match( '/^-+$/', trim( $line ) ) && null !== $pending_header ) {
				if ( null !== $current_make ) {
					$makes[] = $current_make;
				}
				$current_make   = $pending_header;
				$pending_header = null;
				continue;
			}
			// Not actually a make header (dashes line missing) — fall through.
			$pending_header = null;
		}

		if ( '' === trim( $line ) ) {
			continue;
		}

		// Candidate make header: "Name (N)" with no "|" in the line.
		if ( false === strpos( $line, '|' ) && preg_match( '/^(.+?)\s\((\d+)\)\s*$/', trim( $line ), $m ) ) {
			$name = trim( $m[1] );
			$pending_header = array(
				'name'           => $name,
				'slug'           => rexroad_vehicle_slugify( $name ),
				'declared_count' => (int) $m[2],
				'models'         => array(),
			);
			$expect_dashes = true;
			continue;
		}

		// Model line: "Name | year-ranges".
		if ( null !== $current_make && false !== strpos( $line, '|' ) ) {
			[ $model_name_raw, $years_raw ] = array_map( 'trim', explode( '|', $line, 2 ) );

			$years = array();
			foreach ( explode( ',', $years_raw ) as $segment ) {
				$segment = trim( $segment );
				if ( '' === $segment ) {
					continue;
				}
				if ( preg_match( '/^(\d{4})-(\d{4})$/', $segment, $ym ) ) {
					$years[] = array( (int) $ym[1], (int) $ym[2] );
				} elseif ( preg_match( '/^(\d{4})$/', $segment, $ym ) ) {
					$years[] = array( (int) $ym[1], (int) $ym[1] );
				} else {
					throw new RuntimeException( "Unparseable year segment \"{$segment}\" on line: {$line}" );
				}
			}

			$current_make['models'][] = array(
				'name'  => $model_name_raw,
				'slug'  => rexroad_vehicle_slugify( $model_name_raw ),
				'years' => $years,
			);
			continue;
		}
	}

	if ( null !== $current_make ) {
		$makes[] = $current_make;
	}

	return array(
		'makes'                => $makes,
		'declared_model_count' => $declared_model_count,
		'declared_make_count'  => $declared_make_count,
	);
}

/**
 * Validate parsed data for slug collisions and year-range sanity before
 * it is written out.
 *
 * @param array $parsed Result of rexroad_vehicle_parse_source().
 * @return array{errors: string[], warnings: string[]}
 */
function rexroad_vehicle_check_parsed( array $parsed ): array {
	$errors   = array();
	$warnings = array();

	$seen_make_slugs = array();

	foreach ( $parsed['makes'] as $make ) {
		if ( isset( $seen_make_slugs[ $make['slug'] ] ) ) {
			$errors[] = "Duplicate make slug \"{$make['slug']}\" ({$make['name']} collides with {$seen_make_slugs[ $make['slug'] ]}).";
		}
		$seen_make_slugs[ $make['slug'] ] = $make['name'];

		if ( count( $make['models'] ) !== $make['declared_count'] ) {
			$errors[] = "Make \"{$make['name']}\" declares {$make['declared_count']} models but " . count( $make['models'] ) . ' were parsed.';
		}

		$seen_model_slugs = array();
		foreach ( $make['models'] as $model ) {
			if ( isset( $seen_model_slugs[ $model['slug'] ] ) ) {
				$errors[] = "Duplicate model slug \"{$model['slug']}\" within make \"{$make['name']}\" ({$model['name']} collides with {$seen_model_slugs[ $model['slug'] ]}).";
			}
			$seen_model_slugs[ $model['slug'] ] = $model['name'];

			if ( ! preg_match( '/^[a-z0-9]+(-[a-z0-9]+)*$/', $model['slug'] ) ) {
				$errors[] = "Invalid slug format \"{$model['slug']}\" for model \"{$model['name']}\" in make \"{$make['name']}\".";
			}

			if ( 0 === count( $model['years'] ) ) {
				$errors[] = "Model \"{$model['name']}\" in make \"{$make['name']}\" has no year ranges.";
			}

			$prev_end = null;
			foreach ( $model['years'] as $range ) {
				[ $start, $end ] = $range;

				if ( $start > $end ) {
					$errors[] = "Model \"{$model['name']}\" ({$make['name']}) has an inverted range {$start}-{$end}.";
				}

				if ( $start < REXROAD_VEHICLE_MIN_YEAR || $end > REXROAD_VEHICLE_MAX_YEAR ) {
					$errors[] = "Model \"{$model['name']}\" ({$make['name']}) has a range {$start}-{$end} outside the " . REXROAD_VEHICLE_MIN_YEAR . '-' . REXROAD_VEHICLE_MAX_YEAR . ' boundary.';
				}

				if ( null !== $prev_end && $start <= $prev_end ) {
					$warnings[] = "Model \"{$model['name']}\" ({$make['name']}) has overlapping/out-of-order ranges near {$start}-{$end}.";
				}
				$prev_end = $end;
			}
		}

		if ( ! preg_match( '/^[a-z0-9]+(-[a-z0-9]+)*$/', $make['slug'] ) ) {
			$errors[] = "Invalid slug format \"{$make['slug']}\" for make \"{$make['name']}\".";
		}
	}

	$total_models = array_sum( array_map( static fn( $m ) => count( $m['models'] ), $parsed['makes'] ) );
	if ( $total_models !== $parsed['declared_model_count'] ) {
		$errors[] = "Declared total model count {$parsed['declared_model_count']} does not match parsed total {$total_models}.";
	}
	if ( count( $parsed['makes'] ) !== $parsed['declared_make_count'] ) {
		$errors[] = "Declared make count {$parsed['declared_make_count']} does not match parsed count " . count( $parsed['makes'] ) . '.';
	}

	return array(
		'errors'   => $errors,
		'warnings' => $warnings,
	);
}

/**
 * Render the parsed structure as a deterministic PHP source file.
 *
 * Every model is keyed as "model:{slug}" to avoid PHP's automatic
 * casting of purely-numeric string array keys (e.g. "1500") to int.
 * The explicit "slug" field inside each model entry is the value all
 * accessors and future URLs must use.
 *
 * @param array  $parsed      Result of rexroad_vehicle_parse_source().
 * @param string $source_hash sha256 hash of the raw source file.
 * @return string
 */
function rexroad_vehicle_render_php( array $parsed, string $source_hash ): string {
	$export_string = static function ( string $value ): string {
		return "'" . str_replace( "'", "\\'", $value ) . "'";
	};

	$total_models = array_sum( array_map( static fn( $m ) => count( $m['models'] ), $parsed['makes'] ) );

	$out  = "<?php\n";
	$out .= "/**\n";
	$out .= " * GENERATED FILE \xe2\x80\x94 do not hand-edit.\n";
	$out .= " *\n";
	$out .= " * Source: tools/vehicles/source/us-market-vehicles-2000-2026.txt\n";
	$out .= " * Source hash: sha256:{$source_hash}\n";
	$out .= " * Regenerate with: php tools/vehicles/build-vehicle-data.php\n";
	$out .= " *\n";
	$out .= " * @package Rexroad_Custom\n";
	$out .= " */\n\n";
	$out .= "defined( 'ABSPATH' ) || exit;\n\n";
	$out .= "return array(\n";
	$out .= "\t'source_hash' => 'sha256:{$source_hash}',\n";
	$out .= "\t'make_count'  => " . count( $parsed['makes'] ) . ",\n";
	$out .= "\t'model_count' => {$total_models},\n";
	$out .= "\t'makes'       => array(\n";

	foreach ( $parsed['makes'] as $make ) {
		$out .= "\t\t" . $export_string( $make['slug'] ) . " => array(\n";
		$out .= "\t\t\t'slug'   => " . $export_string( $make['slug'] ) . ",\n";
		$out .= "\t\t\t'name'   => " . $export_string( $make['name'] ) . ",\n";
		$out .= "\t\t\t'models' => array(\n";

		foreach ( $make['models'] as $model ) {
			$out .= "\t\t\t\t" . $export_string( 'model:' . $model['slug'] ) . " => array(\n";
			$out .= "\t\t\t\t\t'slug'  => " . $export_string( $model['slug'] ) . ",\n";
			$out .= "\t\t\t\t\t'name'  => " . $export_string( $model['name'] ) . ",\n";
			$out .= "\t\t\t\t\t'years' => array(\n";
			foreach ( $model['years'] as $range ) {
				$out .= "\t\t\t\t\t\tarray( {$range[0]}, {$range[1]} ),\n";
			}
			$out .= "\t\t\t\t\t),\n";
			$out .= "\t\t\t\t),\n";
		}

		$out .= "\t\t\t),\n";
		$out .= "\t\t),\n";
	}

	$out .= "\t),\n";
	$out .= ");\n";

	return $out;
}
