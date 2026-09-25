<?php
/**
 * Vehicles Directory Structured Data.
 *
 * Outputs a WebSite + CollectionPage + BreadcrumbList + AutoRepair graph
 * for the /vehicles/ directory page only. Deliberately does not emit a
 * Vehicle/Product/Offer/Review node per catalog entry — the page
 * represents Rexroad's own service coverage, not 579 individual
 * product listings.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

/**
 * Output the Vehicles Directory JSON-LD schema graph.
 *
 * @return void
 */
function rexroad_custom_vehicles_directory_schema(): void {

	if ( ! is_page_template( 'page-vehicles.php' ) ) {
		return;
	}

	// Reuses the same third-party SEO suite suppression check as the
	// services hub (inc/schema-service.php) — no service-specific logic
	// inside it, just the WPSEO_VERSION/RANK_MATH_VERSION/etc. guard.
	if ( function_exists( 'rexroad_custom_service_schema_has_third_party_owner' ) && rexroad_custom_service_schema_has_third_party_owner() ) {
		return;
	}

	if (
		! function_exists( 'rexroad_custom_get_website_schema' ) ||
		! function_exists( 'rexroad_custom_get_base_business_schema' )
	) {
		return;
	}

	$page = get_post();

	if ( ! $page instanceof WP_Post ) {
		return;
	}

	$home_url     = trailingslashit( home_url( '/' ) );
	$vehicles_url = get_permalink( $page );

	if ( ! $vehicles_url ) {
		return;
	}

	$vehicles_url = trailingslashit( $vehicles_url );

	$website  = rexroad_custom_get_website_schema();
	$business = rexroad_custom_get_base_business_schema();

	$collection_page = array(
		'@type'       => 'CollectionPage',
		'@id'         => $vehicles_url . '#webpage',
		'url'         => $vehicles_url,
		'name'        => get_the_title( $page ),
		'description' => get_the_excerpt( $page ) ?: 'Cars, trucks, and SUVs Rexroad Mobile Auto Repair services across domestic, Asian, and European makes.',
		'isPartOf'    => array(
			'@id' => $home_url . '#website',
		),
		'about'       => array(
			'@id' => $home_url . '#auto-repair',
		),
		'breadcrumb'  => array(
			'@id' => $vehicles_url . '#breadcrumb',
		),
	);

	$breadcrumb = array(
		'@type'           => 'BreadcrumbList',
		'@id'             => $vehicles_url . '#breadcrumb',
		'itemListElement' => array(
			array(
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => 'Home',
				'item'     => $home_url,
			),
			array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => get_the_title( $page ),
				'item'     => $vehicles_url,
			),
		),
	);

	$graph = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			$website,
			$collection_page,
			$breadcrumb,
			$business,
		),
	);

	echo "\n" .
		'<script type="application/ld+json">' .
		wp_json_encode(
			$graph,
			JSON_UNESCAPED_SLASHES |
			JSON_UNESCAPED_UNICODE
		) .
		'</script>' .
		"\n";
}
add_action( 'wp_head', 'rexroad_custom_vehicles_directory_schema', 30 );

/**
 * Output the Vehicle Make Page JSON-LD schema graph.
 *
 * Restrained to WebPage + BreadcrumbList + the existing AutoRepair node
 * — no Vehicle/Product/Offer/Review node, whether or not the page's
 * slug resolves to a real catalog make.
 *
 * @return void
 */
function rexroad_custom_vehicle_make_page_schema(): void {

	if ( ! is_page_template( 'page-vehicle-make.php' ) ) {
		return;
	}

	if ( function_exists( 'rexroad_custom_service_schema_has_third_party_owner' ) && rexroad_custom_service_schema_has_third_party_owner() ) {
		return;
	}

	if (
		! function_exists( 'rexroad_custom_get_website_schema' ) ||
		! function_exists( 'rexroad_custom_get_base_business_schema' )
	) {
		return;
	}

	$page = get_post();

	if ( ! $page instanceof WP_Post ) {
		return;
	}

	$home_url = trailingslashit( home_url( '/' ) );
	$page_url = get_permalink( $page );

	if ( ! $page_url ) {
		return;
	}

	$page_url = trailingslashit( $page_url );

	$parent_post  = $page->post_parent ? get_post( $page->post_parent ) : null;
	$parent_url   = $parent_post instanceof WP_Post ? get_permalink( $parent_post ) : false;
	$parent_url   = $parent_url ? trailingslashit( $parent_url ) : trailingslashit( home_url( '/vehicles/' ) );
	$parent_title = $parent_post instanceof WP_Post ? get_the_title( $parent_post ) : 'Vehicles';

	/*
	 * Same validated-make resolution as page-vehicle-make.php (own slug
	 * + direct child of the real Vehicles hub, identified by template —
	 * never a hardcoded slug/ID). Keeps the schema's naming identical to
	 * the visible H1/breadcrumb even if the WP Page title was typed
	 * differently; falls back to the WP title for pages that don't
	 * resolve to a real catalog make, same as the visible template.
	 */
	$rexroad_vehicle_make    = function_exists( 'rexroad_vehicle_resolve_make_from_current_page' )
		? rexroad_vehicle_resolve_make_from_current_page()
		: null;
	$rexroad_vehicle_label   = null !== $rexroad_vehicle_make ? $rexroad_vehicle_make['name'] : get_the_title( $page );

	$website  = rexroad_custom_get_website_schema();
	$business = rexroad_custom_get_base_business_schema();

	$webpage_node = array(
		'@type'      => 'WebPage',
		'@id'        => $page_url . '#webpage',
		'url'        => $page_url,
		'name'       => $rexroad_vehicle_label,
		'isPartOf'   => array(
			'@id' => $home_url . '#website',
		),
		'about'      => array(
			'@id' => $home_url . '#auto-repair',
		),
		'breadcrumb' => array(
			'@id' => $page_url . '#breadcrumb',
		),
	);

	$breadcrumb = array(
		'@type'           => 'BreadcrumbList',
		'@id'             => $page_url . '#breadcrumb',
		'itemListElement' => array(
			array(
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => 'Home',
				'item'     => $home_url,
			),
			array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => $parent_title,
				'item'     => $parent_url,
			),
			array(
				'@type'    => 'ListItem',
				'position' => 3,
				'name'     => $rexroad_vehicle_label,
				'item'     => $page_url,
			),
		),
	);

	$graph = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			$website,
			$webpage_node,
			$breadcrumb,
			$business,
		),
	);

	echo "\n" .
		'<script type="application/ld+json">' .
		wp_json_encode(
			$graph,
			JSON_UNESCAPED_SLASHES |
			JSON_UNESCAPED_UNICODE
		) .
		'</script>' .
		"\n";
}
add_action( 'wp_head', 'rexroad_custom_vehicle_make_page_schema', 30 );

/**
 * Output the Vehicle Model Page JSON-LD schema graph.
 *
 * Restrained to WebPage + BreadcrumbList + the existing AutoRepair node
 * — no Vehicle/Product/Offer/Review node, whether or not the page's
 * hierarchy resolves to a real catalog make+model.
 *
 * @return void
 */
function rexroad_custom_vehicle_model_page_schema(): void {

	if ( ! is_page_template( 'page-vehicle-model.php' ) ) {
		return;
	}

	if ( function_exists( 'rexroad_custom_service_schema_has_third_party_owner' ) && rexroad_custom_service_schema_has_third_party_owner() ) {
		return;
	}

	if (
		! function_exists( 'rexroad_custom_get_website_schema' ) ||
		! function_exists( 'rexroad_custom_get_base_business_schema' )
	) {
		return;
	}

	$page = get_post();

	if ( ! $page instanceof WP_Post ) {
		return;
	}

	$home_url = trailingslashit( home_url( '/' ) );
	$page_url = get_permalink( $page );

	if ( ! $page_url ) {
		return;
	}

	$page_url = trailingslashit( $page_url );

	$make_post = $page->post_parent ? get_post( $page->post_parent ) : null;
	$hub_post  = ( $make_post instanceof WP_Post && $make_post->post_parent ) ? get_post( $make_post->post_parent ) : null;

	$hub_url   = $hub_post instanceof WP_Post ? get_permalink( $hub_post ) : false;
	$hub_url   = $hub_url ? trailingslashit( $hub_url ) : trailingslashit( home_url( '/vehicles/' ) );
	$hub_title = $hub_post instanceof WP_Post ? get_the_title( $hub_post ) : 'Vehicles';

	$make_url = $make_post instanceof WP_Post ? get_permalink( $make_post ) : false;
	$make_url = $make_url ? trailingslashit( $make_url ) : $hub_url;

	/*
	 * Same validated make+model resolution as page-vehicle-model.php —
	 * keeps schema naming identical to the visible H1/breadcrumb even
	 * if either WP Page title was typed differently; falls back to WP
	 * titles for pages that don't resolve to a real catalog
	 * make+model relationship, same as the visible template. Never
	 * emits model-specific naming for an unresolved page.
	 */
	$rexroad_vehicle_resolved   = function_exists( 'rexroad_vehicle_resolve_model_from_current_page' )
		? rexroad_vehicle_resolve_model_from_current_page()
		: null;
	$rexroad_vehicle_make_label = null !== $rexroad_vehicle_resolved
		? $rexroad_vehicle_resolved['make']['name']
		: ( $make_post instanceof WP_Post ? get_the_title( $make_post ) : 'Make' );
	$rexroad_vehicle_label      = null !== $rexroad_vehicle_resolved ? $rexroad_vehicle_resolved['model']['name'] : get_the_title( $page );

	$website  = rexroad_custom_get_website_schema();
	$business = rexroad_custom_get_base_business_schema();

	$webpage_node = array(
		'@type'      => 'WebPage',
		'@id'        => $page_url . '#webpage',
		'url'        => $page_url,
		'name'       => $rexroad_vehicle_label,
		'isPartOf'   => array(
			'@id' => $home_url . '#website',
		),
		'about'      => array(
			'@id' => $home_url . '#auto-repair',
		),
		'breadcrumb' => array(
			'@id' => $page_url . '#breadcrumb',
		),
	);

	$breadcrumb = array(
		'@type'           => 'BreadcrumbList',
		'@id'             => $page_url . '#breadcrumb',
		'itemListElement' => array(
			array(
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => 'Home',
				'item'     => $home_url,
			),
			array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => $hub_title,
				'item'     => $hub_url,
			),
			array(
				'@type'    => 'ListItem',
				'position' => 3,
				'name'     => $rexroad_vehicle_make_label,
				'item'     => $make_url,
			),
			array(
				'@type'    => 'ListItem',
				'position' => 4,
				'name'     => $rexroad_vehicle_label,
				'item'     => $page_url,
			),
		),
	);

	$graph = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			$website,
			$webpage_node,
			$breadcrumb,
			$business,
		),
	);

	echo "\n" .
		'<script type="application/ld+json">' .
		wp_json_encode(
			$graph,
			JSON_UNESCAPED_SLASHES |
			JSON_UNESCAPED_UNICODE
		) .
		'</script>' .
		"\n";
}
add_action( 'wp_head', 'rexroad_custom_vehicle_model_page_schema', 30 );
