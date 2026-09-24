<?php
/**
 * Vehicle Informer Structured Data Graph Output.
 *
 * Outputs:
 * - CollectionPage + BreadcrumbList + ItemList on the Vehicle Informer index.
 * - BlogPosting + WebPage + BreadcrumbList on individual blog posts.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

/**
 * Determine whether another major SEO plugin is managing schema.
 *
 * @return bool
 */
function rexroad_custom_blog_schema_has_third_party_owner(): bool {
	return (
		defined( 'WPSEO_VERSION' ) ||
		defined( 'RANK_MATH_VERSION' ) ||
		defined( 'AIOSEO_VERSION' ) ||
		defined( 'SEOPRESS_VERSION' )
	);
}

/**
 * Output Vehicle Informer landing-page schema.
 *
 * The WordPress Posts Page is used as a custom Vehicle Informer landing page,
 * so it receives CollectionPage, BreadcrumbList, and ItemList schema.
 *
 * @return void
 */
function rexroad_custom_vehicle_informer_schema(): void {

	if ( ! is_home() || is_paged() ) {
		return;
	}

	if ( rexroad_custom_blog_schema_has_third_party_owner() ) {
		return;
	}

	if (
		! function_exists( 'rexroad_custom_get_website_schema' ) ||
		! function_exists( 'rexroad_custom_get_base_business_schema' )
	) {
		return;
	}

	$page_for_posts = (int) get_option( 'page_for_posts' );

	if ( $page_for_posts <= 0 ) {
		return;
	}

	$archive_url = get_permalink( $page_for_posts );

	if ( ! $archive_url ) {
		return;
	}

	$archive_url = trailingslashit( $archive_url );
	$home_url    = trailingslashit( home_url( '/' ) );

	$page_title = get_the_title( $page_for_posts );

	if ( '' === trim( (string) $page_title ) ) {
		$page_title = 'Vehicle Informer';
	}

	$description = 'Practical automotive repair, diagnostic, and maintenance information from Rexroad Mobile Auto Repair in Frisco, Texas. Learn what common vehicle symptoms may mean, what warning signs deserve attention, and when professional diagnosis may be the right next step.';

	$website  = rexroad_custom_get_website_schema();
	$business = rexroad_custom_get_base_business_schema();

	/*
	 * Vehicle Informer CollectionPage node.
	 */
	$collection_page = array(
		'@type'       => 'CollectionPage',
		'@id'         => $archive_url . '#webpage',
		'url'         => $archive_url,
		'name'        => $page_title,
		'description' => $description,
		'inLanguage'  => 'en-US',
		'isPartOf'    => array(
			'@id' => $home_url . '#website',
		),
		'about'       => array(
			'@id' => $home_url . '#auto-repair',
		),
		'breadcrumb'  => array(
			'@id' => $archive_url . '#breadcrumb',
		),
		'mainEntity'  => array(
			'@id' => $archive_url . '#article-list',
		),
	);

	/*
	 * Vehicle Informer breadcrumb.
	 */
	$breadcrumb = array(
		'@type'           => 'BreadcrumbList',
		'@id'             => $archive_url . '#breadcrumb',
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
				'name'     => $page_title,
				'item'     => $archive_url,
			),
		),
	);

	/*
	 * Build an ItemList containing published Vehicle Informer articles.
	 */
	$posts = get_posts(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => 100, // Capped for query memory safety
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	$item_list_elements = array();
	$position           = 0;

	foreach ( $posts as $post ) {

		if ( ! $post instanceof WP_Post ) {
			continue;
		}

		$post_url = get_permalink( $post );

		if ( ! $post_url ) {
			continue;
		}

		$post_url = trailingslashit( $post_url );
		$position++;

		/*
		 * Build the BlogPosting entity used by the Vehicle Informer ItemList.
		 */
		$article_item = array(
			'@type'         => 'BlogPosting',
			'@id'           => $post_url . '#article',
			'url'           => $post_url,
			'headline'      => get_the_title( $post ),
			'datePublished' => get_the_date( DATE_W3C, $post ),
			'dateModified'  => get_the_modified_date( DATE_W3C, $post ),
			'publisher'     => array(
				'@id' => $home_url . '#auto-repair',
			),
		);

		/*
		 * Author.
		 */
		$author_id   = (int) $post->post_author;
		$author_name = trim(
			(string) get_the_author_meta(
				'display_name',
				$author_id
			)
		);

		if ( '' !== $author_name ) {
			$article_item['author'] = array(
				'@type' => 'Person',
				'@id'   => $home_url . '#author-' . $author_id,
				'name'  => $author_name,
			);
		}

		/*
		 * Featured image.
		 */
		$featured_image = get_the_post_thumbnail_url( $post, 'full' );

		if ( $featured_image ) {
			$article_item['image'] = array(
				'@type' => 'ImageObject',
				'url'   => $featured_image,
			);
		}

		/*
		 * Add article to the Vehicle Informer ItemList.
		 */
		$item_list_elements[] = array(
			'@type'    => 'ListItem',
			'position' => $position,
			'name'     => get_the_title( $post ),
			'url'      => $post_url,
			'item'     => $article_item,
		);
	}

	/*
	 * ItemList container node.
	 */
	$item_list = array(
		'@type'           => 'ItemList',
		'@id'             => $archive_url . '#article-list',
		'name'            => 'Vehicle Informer Automotive Guides',
		'itemListOrder'   => 'https://schema.org/ItemListOrderDescending',
		'numberOfItems'   => count( $item_list_elements ),
		'itemListElement' => $item_list_elements,
	);

	/*
	 * Assemble graph outside the loop.
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
 * Output BlogPosting JSON-LD schema graph on single blog posts.
 *
 * @return void
 */
function rexroad_custom_blog_post_schema(): void {

	if ( ! is_singular( 'post' ) ) {
		return;
	}

	if ( rexroad_custom_blog_schema_has_third_party_owner() ) {
		return;
	}

	if (
		! function_exists( 'rexroad_custom_get_website_schema' ) ||
		! function_exists( 'rexroad_custom_get_base_business_schema' )
	) {
		return;
	}

	$post = get_post();

	if ( ! $post instanceof WP_Post ) {
		return;
	}

	$home_url = trailingslashit( home_url( '/' ) );
	$post_url = get_permalink( $post );

	if ( ! $post_url ) {
		return;
	}

	$post_url = trailingslashit( $post_url );

	$website  = rexroad_custom_get_website_schema();
	$business = rexroad_custom_get_base_business_schema();

	/*
	 * Build description.
	 */
	$description = trim( (string) get_the_excerpt( $post ) );

	if ( '' === $description ) {
		$description = wp_trim_words(
			wp_strip_all_tags(
				strip_shortcodes( $post->post_content )
			),
			35,
			'…'
		);
	}

	/*
	 * WebPage node.
	 */
	$webpage_node = array(
		'@type'       => 'WebPage',
		'@id'         => $post_url . '#webpage',
		'url'         => $post_url,
		'name'        => get_the_title( $post ),
		'description' => $description,
		'isPartOf'    => array(
			'@id' => $home_url . '#website',
		),
		'about'       => array(
			'@id' => $home_url . '#auto-repair',
		),
		'mainEntity'  => array(
			'@id' => $post_url . '#article',
		),
	);

	/*
	 * BreadcrumbList node via helper.
	 */
	$breadcrumb_node = function_exists( 'rexroad_custom_get_blog_breadcrumb_schema' )
		? rexroad_custom_get_blog_breadcrumb_schema( $post )
		: array();

	if ( ! empty( $breadcrumb_node ) ) {
		$webpage_node['breadcrumb'] = array(
			'@id' => $post_url . '#breadcrumb',
		);
	}

	/*
	 * BlogPosting node.
	 */
	$article_node = array(
		'@type'            => 'BlogPosting',
		'@id'              => $post_url . '#article',
		'url'              => $post_url,
		'headline'         => get_the_title( $post ),
		'description'      => $description,
		'datePublished'    => get_the_date( DATE_W3C, $post ),
		'dateModified'     => get_the_modified_date( DATE_W3C, $post ),
		'isPartOf'         => array(
			'@id' => $home_url . '#website',
		),
		'publisher'        => array(
			'@id' => $home_url . '#auto-repair',
		),
		'mainEntityOfPage' => array(
			'@id' => $post_url . '#webpage',
		),
	);

	/*
	 * Author.
	 */
	$author_id   = (int) $post->post_author;
	$author_name = trim(
		(string) get_the_author_meta(
			'display_name',
			$author_id
		)
	);

	if ( '' !== $author_name ) {
		$article_node['author'] = array(
			'@type' => 'Person',
			'@id'   => $home_url . '#author-' . $author_id,
			'name'  => $author_name,
		);
	}

	/*
	 * Featured image.
	 */
	$featured_image = get_the_post_thumbnail_url( $post, 'full' );

	if ( $featured_image ) {
		$article_node['image'] = array(
			'@type' => 'ImageObject',
			'url'   => $featured_image,
		);
	}

	/*
	 * Assemble graph.
	 */
	$graph_nodes = array(
		$website,
		$webpage_node,
	);

	if ( ! empty( $breadcrumb_node ) ) {
		$graph_nodes[] = $breadcrumb_node;
	}

	$graph_nodes[] = $article_node;
	$graph_nodes[] = $business;

	$graph = array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph_nodes,
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
 * Vehicle Informer landing page.
 */
add_action(
	'wp_head',
	'rexroad_custom_vehicle_informer_schema',
	30
);

/*
 * Individual Vehicle Informer articles.
 */
add_action(
	'wp_head',
	'rexroad_custom_blog_post_schema',
	30
);