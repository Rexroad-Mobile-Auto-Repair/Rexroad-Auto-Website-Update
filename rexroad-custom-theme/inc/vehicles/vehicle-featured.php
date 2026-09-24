<?php
/**
 * Presentation-only display-priority ordering for future "featured
 * makes" emphasis on /vehicles/. Confirmed by Aaron Rexroad on
 * 2026-09-23.
 *
 * This file is hand-maintained. It is never touched by
 * tools/vehicles/build-vehicle-data.php and has no bearing on:
 *  - whether a make/model exists in the catalog (see vehicle-data.php)
 *  - service eligibility
 *  - SEO-page eligibility (see the future vehicle-seo-pages.php)
 *  - group membership (see vehicle-groups.php)
 *
 * Order is the intended display priority, most prominent first. Makes
 * not listed here are still fully present in the catalog and browsable
 * — this list only controls visual emphasis/ordering.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

return array(
	'ford',
	'chevrolet',
	'gmc',
	'ram',
	'dodge',
	'jeep',
	'honda',
	'toyota',
	'nissan',
	'hyundai',
	'kia',
	'subaru',
);
