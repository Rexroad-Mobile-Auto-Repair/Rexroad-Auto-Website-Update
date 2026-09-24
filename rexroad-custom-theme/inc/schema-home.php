<?php
/**
 * Homepage Structured Data Graph Output.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

/**
 * Output JSON-LD schema graph on the homepage.
 */
function rexroad_custom_homepage_schema(): void {
	if ( ! is_front_page() || ! apply_filters( 'rexroad_custom_output_local_business_schema', true ) ) {
		return;
	}

	if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) ) {
		return;
	}

	if ( ! function_exists( 'rexroad_custom_get_website_schema' ) || ! function_exists( 'rexroad_custom_get_base_business_schema' ) ) {
		return;
	}

	$home_url = home_url( '/' );

	// 1. Shared WebSite Node
	$website_node = rexroad_custom_get_website_schema();

	// 2. WebPage Node (Homepage)
	$webpage_node = array(
		'@type'      => 'WebPage',
		'@id'        => $home_url . '#webpage',
		'url'        => $home_url,
		'name'       => get_bloginfo( 'name' ),
		'isPartOf'   => array(
			'@id' => $home_url . '#website',
		),
		'about'      => array(
			'@id' => $home_url . '#auto-repair',
		),
		'mainEntity' => array(
			'@id' => $home_url . '#auto-repair',
		),
	);

	// 3. Base AutoRepair Entity
	$business = rexroad_custom_get_base_business_schema();

	// 4. Explicit Live Service Links
	$services = array(
		array(
			'name' => 'Advanced Diagnostics',
			'url'  => home_url( '/services/advanced-diagnostics/' ),
		),
		array(
			'name' => 'Mobile A/C Service',
			'url'  => home_url( '/services/auto-air-condition-service/' ),
		),
		array(
			'name' => 'Battery & Electrical',
			'url'  => home_url( '/services/auto-battery-replacement/' ),
		),
		array(
			'name' => 'Brake Service',
			'url'  => home_url( '/services/brakes-service/' ),
		),
		array(
			'name' => 'Cooling System Service',
			'url'  => home_url( '/services/cooling-system-service/' ),
		),
		array(
			'name' => 'Alternators & Starters',
			'url'  => home_url( '/services/alternator-and-starter-repair/' ),
		),
		array(
			'name' => 'Fuel Pump Replacement',
			'url'  => home_url( '/services/fuel-pump-replacement/' ),
		),
		array(
			'name' => 'Headlight Restoration',
			'url'  => home_url( '/services/headlight-restoration/' ),
		),
		array(
			'name' => 'Oil Change',
			'url'  => home_url( '/services/oil-change/' ),
		),
		array(
			'name' => 'Window Regulator Repair',
			'url'  => home_url( '/services/window-regulator-repair/' ),
		),
		array(
			'name' => 'Pre-Purchase Inspection',
			'url'  => home_url( '/services/pre-purchase-inspection/' ),
		),
		array(
			'name' => 'Roadside Assistance',
			'url'  => home_url( '/services/roadside-assistance/' ),
		),
		array(
			'name' => 'Suspension & Steering',
			'url'  => home_url( '/services/suspension-and-steering/' ),
		),
		array(
			'name' => 'Tune-Up & Maintenance',
			'url'  => home_url( '/services/tune-up-service/' ),
		),
		array(
			'name' => 'Wheel Bearings & CV Axles',
			'url'  => home_url( '/services/wheel-bearing-and-cv-axle-repair/' ),
		),
	);

	// 5. Build Offers Array with Stable Entity @id values
	$offers = array_map(
		static function ( array $service ) use ( $home_url ): array {
			return array(
				'@type'       => 'Offer',
				'itemOffered' => array(
					'@type'    => 'Service',
					'@id'      => $service['url'] . '#service',
					'name'     => $service['name'],
					'url'      => $service['url'],
					'provider' => array(
						'@id' => $home_url . '#auto-repair',
					),
				),
			);
		},
		$services
	);

	// 6. Enrich Business Node with Offer Catalog & Stable @id
	$business['hasOfferCatalog'] = array(
		'@type'           => 'OfferCatalog',
		'@id'             => $home_url . '#service-catalog',
		'name'            => 'Mobile Auto Repair Services',
		'itemListElement' => $offers,
	);

	// 7. Assemble Complete Graph
	$graph = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			$website_node,
			$webpage_node,
			$business,
		),
	);

	echo "\n" . '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'rexroad_custom_homepage_schema', 30 );