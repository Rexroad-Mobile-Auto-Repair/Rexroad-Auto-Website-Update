<?php
/**
 * Presentation-only grouping of makes for future browse-by-group UI
 * (Domestic / Asian / European), inspired by Auros Auto's visual
 * organization. Confirmed by Aaron Rexroad on 2026-09-23.
 *
 * This file is hand-maintained. It is never touched by
 * tools/vehicles/build-vehicle-data.php and has no bearing on:
 *  - whether a make/model exists in the catalog (see vehicle-data.php)
 *  - service eligibility
 *  - SEO-page eligibility (see the future vehicle-seo-pages.php)
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

return array(
	'domestic' => array(
		'name'  => 'Domestic',
		'makes' => array(
			'buick',
			'cadillac',
			'chevrolet',
			'chrysler',
			'dodge',
			'ford',
			'gmc',
			'hummer',
			'jeep',
			'lincoln',
			'mercury',
			'oldsmobile',
			'plymouth',
			'pontiac',
			'ram',
			'saturn',
		),
	),
	'asian'    => array(
		'name'  => 'Asian',
		'makes' => array(
			'acura',
			'genesis',
			'honda',
			'hyundai',
			'infiniti',
			'isuzu',
			'kia',
			'lexus',
			'mazda',
			'mitsubishi',
			'nissan',
			'scion',
			'subaru',
			'toyota',
		),
	),
	'european' => array(
		'name'  => 'European',
		'makes' => array(
			'alfa-romeo',
			'audi',
			'bmw',
			'jaguar',
			'land-rover',
			'mercedes-benz',
		),
	),
);
