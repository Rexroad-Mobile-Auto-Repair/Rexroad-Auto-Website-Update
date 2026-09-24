<?php
/**
 * Rexroad Custom Theme functions and definitions.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

$rexroad_custom_includes = array(
	'/inc/setup.php',
	'/inc/customizer.php',
	'/inc/enqueue.php',
	'/inc/menus.php',
	'/inc/custom-post-types.php',
	'/inc/vehicle-informer.php',
	'/inc/structured-data.php',
	'/inc/schema-home.php',
	'/inc/schema-about.php',
	'/inc/schema-contact.php',
	'/inc/schema-service.php',
	'/inc/schema-breadcrumb.php',
	'/inc/schema-blog.php',
	'/inc/analytics.php',
	'/inc/cleanup.php',
	'/inc/admin.php',
);

foreach ( $rexroad_custom_includes as $rexroad_custom_file ) {
	require_once get_template_directory() . $rexroad_custom_file;
}

/**
 * Keep one semantic H1 per singular view by demoting H1 tags stored inside
 * post and page content. The template's entry title remains the page H1.
 *
 * @param string $content Filtered post content.
 * @return string
 */
function rexroad_custom_normalize_content_headings( string $content ): string {
	if ( is_singular() && in_the_loop() && is_main_query() ) {
		$content = (string) preg_replace(
			array( '/<h1(\s[^>]*)?>/i', '/<\/h1>/i' ),
			array( '<h2$1>', '</h2>' ),
			$content
		);
	}

	return $content;
}
add_filter( 'the_content', 'rexroad_custom_normalize_content_headings', 20 );