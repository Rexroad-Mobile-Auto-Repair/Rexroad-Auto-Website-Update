<?php
/**
 * About Page Structured Data Graph Output.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

/**
 * Output AboutPage JSON-LD schema graph.
 */
function rexroad_custom_about_page_schema(): void {

	if ( ! is_page( 'about-us' ) && ! is_page( 'about' ) ) {
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

	$website  = rexroad_custom_get_website_schema();
	$business = rexroad_custom_get_base_business_schema();

	$about_page = array(
		'@type'       => 'AboutPage',
		'@id'         => $page_url . '#webpage',
		'url'         => $page_url,
		'name'        => get_the_title(),
		'description' => get_the_excerpt() ?: get_bloginfo( 'description' ),
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
			$about_page,
			$business,
		),
	);

	echo "\n" . '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}

add_action( 'wp_head', 'rexroad_custom_about_page_schema', 30 );