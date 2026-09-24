<?php
/**
 * Vehicle Informer BreadcrumbList schema.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

/**
 * Build BreadcrumbList schema for a Vehicle Informer post.
 *
 * @param WP_Post $post Post object.
 * @return array
 */
function rexroad_custom_get_blog_breadcrumb_schema( WP_Post $post ): array {

	$post_url = get_permalink( $post );

	if ( ! $post_url ) {
		return array();
	}

	$post_url = trailingslashit( $post_url );

	return array(
		'@type'           => 'BreadcrumbList',
		'@id'             => $post_url . '#breadcrumb',
		'itemListElement' => array(
			array(
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => 'Home',
				'item'     => home_url( '/' ),
			),
			array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => 'Vehicle Informer',
				'item'     => home_url( '/vehicle-informer/' ),
			),
			array(
				'@type'    => 'ListItem',
				'position' => 3,
				'name'     => get_the_title( $post ),
			),
		),
	);
}