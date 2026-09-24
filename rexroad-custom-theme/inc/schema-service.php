<?php
/**
 * Service Structured Data Graph Output.
 *
 * Outputs:
 * - CollectionPage + BreadcrumbList + ItemList on the Services hub.
 * - WebPage + Service on individual service pages.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

/**
 * Determine whether another major SEO plugin is managing schema.
 *
 * @return bool
 */
function rexroad_custom_service_schema_has_third_party_owner(): bool {
	return (
		defined( 'WPSEO_VERSION' ) ||
		defined( 'RANK_MATH_VERSION' ) ||
		defined( 'AIOSEO_VERSION' ) ||
		defined( 'SEOPRESS_VERSION' )
	);
}

/**
 * Return the canonical list of Rexroad service slugs.
 *
 * @return array
 */
function rexroad_custom_get_service_slugs(): array {
	return array(
		'advanced-diagnostics',
		'auto-air-condition-service',
		'auto-battery-replacement',
		'brakes-service',
		'cooling-system-service',
		'alternator-and-starter-repair',
		'fuel-pump-replacement',
		'headlight-restoration',
		'oil-change',
		'window-regulator-repair',
		'pre-purchase-inspection',
		'roadside-assistance',
		'suspension-and-steering',
		'tune-up-service',
		'wheel-bearing-and-cv-axle-repair',
	);
}

/**
 * Output Services hub JSON-LD schema graph.
 *
 * @return void
 */
function rexroad_custom_services_hub_schema(): void {

	if ( ! is_page( 'services' ) ) {
		return;
	}

	if ( rexroad_custom_service_schema_has_third_party_owner() ) {
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

	$home_url    = trailingslashit( home_url( '/' ) );
	$services_url = get_permalink( $page );

	if ( ! $services_url ) {
		return;
	}

	$services_url = trailingslashit( $services_url );

	$website  = rexroad_custom_get_website_schema();
	$business = rexroad_custom_get_base_business_schema();

	/*
	 * Services hub CollectionPage.
	 */
	$collection_page = array(
		'@type'       => 'CollectionPage',
		'@id'         => $services_url . '#webpage',
		'url'         => $services_url,
		'name'        => get_the_title( $page ),
		'description' => get_the_excerpt( $page ) ?: 'Mobile auto repair services from Rexroad Mobile Auto Repair in Frisco, Texas.',
		'isPartOf'    => array(
			'@id' => $home_url . '#website',
		),
		'about'       => array(
			'@id' => $home_url . '#auto-repair',
		),
		'breadcrumb'  => array(
			'@id' => $services_url . '#breadcrumb',
		),
		'mainEntity'  => array(
			'@id' => $services_url . '#service-list',
		),
	);

	/*
	 * Breadcrumb.
	 */
	$breadcrumb = array(
		'@type'           => 'BreadcrumbList',
		'@id'             => $services_url . '#breadcrumb',
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
				'item'     => $services_url,
			),
		),
	);

	/*
	 * Build ItemList from published service pages.
	 */
	$item_list_elements = array();
	$position           = 0;

	foreach ( rexroad_custom_get_service_slugs() as $service_slug ) {

		$service_page = get_page_by_path(
			'services/' . $service_slug,
			OBJECT,
			'page'
		);

		if ( ! $service_page instanceof WP_Post ) {
			$service_page = get_page_by_path(
				$service_slug,
				OBJECT,
				'page'
			);
		}

		if (
			! $service_page instanceof WP_Post ||
			'publish' !== $service_page->post_status
		) {
			continue;
		}

		$service_url = get_permalink( $service_page );

		if ( ! $service_url ) {
			continue;
		}

		$service_url = trailingslashit( $service_url );
		$position++;

		$item_list_elements[] = array(
			'@type'    => 'ListItem',
			'position' => $position,
			'name'     => get_the_title( $service_page ),
			'url'      => $service_url,
			'item'     => array(
				'@type'    => 'Service',
				'@id'      => $service_url . '#service',
				'url'      => $service_url,
				'name'     => get_the_title( $service_page ),
				'provider' => array(
					'@id' => $home_url . '#auto-repair',
				),
			),
		);
	}

	/*
	 * Service ItemList.
	 */
	$item_list = array(
		'@type'           => 'ItemList',
		'@id'             => $services_url . '#service-list',
		'name'            => 'Rexroad Mobile Auto Repair Services',
		'itemListOrder'   => 'https://schema.org/ItemListOrderAscending',
		'numberOfItems'   => count( $item_list_elements ),
		'itemListElement' => $item_list_elements,
	);

	/*
	 * Assemble graph.
	 */
	$graph = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			$website,
			$collection_page,
			$breadcrumb,
			$item_list,
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

/**
 * Output Service JSON-LD schema graph on individual service pages.
 *
 * @return void
 */
function rexroad_custom_service_page_schema(): void {

	if ( ! is_singular() ) {
		return;
	}

	if ( rexroad_custom_service_schema_has_third_party_owner() ) {
		return;
	}

	$service_slugs = rexroad_custom_get_service_slugs();

	$post = get_post();

	if ( ! $post instanceof WP_Post ) {
		return;
	}

	$parent_post = $post->post_parent > 0
		? get_post( $post->post_parent )
		: null;

	$is_service_slug     = in_array( $post->post_name, $service_slugs, true );
	$is_service_cpt      = is_singular( 'service' );
	$is_service_template = is_page_template( 'template-service.php' );
	$is_service_child    = is_page()
		&& $parent_post instanceof WP_Post
		&& 'services' === $parent_post->post_name;

	if (
		! (
			$is_service_slug ||
			$is_service_cpt ||
			$is_service_template ||
			$is_service_child
		)
	) {
		return;
	}

	if (
		! function_exists( 'rexroad_custom_get_website_schema' ) ||
		! function_exists( 'rexroad_custom_get_base_business_schema' )
	) {
		return;
	}

	$home_url    = trailingslashit( home_url( '/' ) );
	$service_url = get_permalink( $post );

	if ( ! $service_url ) {
		return;
	}

	$service_url = trailingslashit( $service_url );

	$website  = rexroad_custom_get_website_schema();
	$business = rexroad_custom_get_base_business_schema();

	$webpage_node = array(
		'@type'      => 'WebPage',
		'@id'        => $service_url . '#webpage',
		'url'        => $service_url,
		'name'       => get_the_title( $post ),
		'isPartOf'   => array(
			'@id' => $home_url . '#website',
		),
		'mainEntity' => array(
			'@id' => $service_url . '#service',
		),
	);

	$service_node = array(
		'@type'       => 'Service',
		'@id'         => $service_url . '#service',
		'url'         => $service_url,
		'name'        => get_the_title( $post ),
		'description' => get_the_excerpt( $post ) ?: get_bloginfo( 'description' ),
		'provider'    => array(
			'@id' => $home_url . '#auto-repair',
		),
	);

	$graph = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			$website,
			$webpage_node,
			$service_node,
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

/*
 * Services hub.
 */
add_action(
	'wp_head',
	'rexroad_custom_services_hub_schema',
	30
);

/*
 * Individual service pages.
 */
add_action(
	'wp_head',
	'rexroad_custom_service_page_schema',
	30
);