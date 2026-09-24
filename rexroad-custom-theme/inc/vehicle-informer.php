<?php
/**
 * Vehicle Informer query behavior.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

/**
 * Show all published posts on the Vehicle Informer posts index.
 *
 * @param WP_Query $query Main WordPress query.
 * @return void
 */
function rexroad_custom_vehicle_informer_show_all_posts( $query ) {
	if (
		is_admin()
		|| ! $query->is_main_query()
		|| ! $query->is_home()
	) {
		return;
	}

	$query->set( 'posts_per_page', -1 );
}
add_action( 'pre_get_posts', 'rexroad_custom_vehicle_informer_show_all_posts' );