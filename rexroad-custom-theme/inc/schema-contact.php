<?php
/**
 * Contact Page Structured Data Graph Output.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

/**
 * Output ContactPage JSON-LD schema graph.
 */
function rexroad_custom_contact_page_schema(): void {

	if ( ! is_page( 'contact-us' ) && ! is_page( 'contact' ) ) {
		return;
	}

	if (
		defined( 'WPSEO_VERSION' ) ||
		defined( 'RANK_MATH_VERSION' ) ||
		defined( 'AIOSEO_VERSION' ) ||
		defined( 'SEOPRESS_VERSION' )
	) {
		return;
	}

	if ( ! function_exists( 'rexroad_custom_get_website_schema' ) || ! function_exists( 'rexroad_custom_get_base_business_schema' ) ) {
		return;
	}

	$home_url = home_url( '/' );
	$page_url = get_permalink();

	if ( ! $page_url ) {
		return;
	}

	$city  = (string) get_theme_mod( 'rexroad_business_city', 'Frisco' );
	$state = (string) get_theme_mod( 'rexroad_business_state', 'TX' );

	$fallback_description = sprintf(
		'Contact Rexroad Mobile Auto Repair for mobile diagnostics and auto repair services in %s, %s and surrounding communities.',
		$city,
		$state
	);

	$website  = rexroad_custom_get_website_schema();
	$business = rexroad_custom_get_base_business_schema();

	$contact_page = array(
		'@type'       => 'ContactPage',
		'@id'         => $page_url . '#webpage',
		'url'         => $page_url,
		'name'        => get_the_title(),
		'description' => get_the_excerpt() ?: $fallback_description,
		'isPartOf'    => array(
			'@id' => $home_url . '#website',
		),
		'about'       => array(
			'@id' => $home_url . '#auto-repair',
		),
		'mainEntity'  => array(
			'@id' => $home_url . '#auto-repair',
		),
	);

	$graph = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			$website,
			$contact_page,
			$business,
		),
	);

	echo "\n" . '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}

add_action( 'wp_head', 'rexroad_custom_contact_page_schema', 30 );