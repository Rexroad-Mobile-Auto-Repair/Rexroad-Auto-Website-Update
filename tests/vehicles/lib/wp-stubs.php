<?php
/**
 * Minimal WordPress core stand-ins so the real theme template/schema/
 * accessor files can be exercised outside a WordPress bootstrap.
 *
 * These are intentionally thin: only what page-vehicles.php,
 * schema-vehicles.php, and schema-service.php actually call. Anything
 * theme-authored (e.g. rexroad_vehicle_* accessors) is the REAL
 * function from the theme, never stubbed — only WordPress core glue is
 * replaced here.
 */

declare( strict_types=1 );

define( 'ABSPATH', true );

/*
 * Fixture-driven page tree, so get_post()/get_permalink()/get_pages()
 * can model real WordPress page hierarchy (parent/child, publish
 * status) rather than a single hardcoded post. Tests that don't care
 * get a sensible one-page default (id 1, slug "vehicles", published)
 * reproducing the old fixed-string behavior these stubs used to have.
 *
 * Row shape: array{ID:int, post_name:string, post_parent:int, post_title:?string, post_status:string}
 * A null post_title falls back to $GLOBALS['rexroad_test_title'].
 */
$GLOBALS['rexroad_test_pages'] = $GLOBALS['rexroad_test_pages'] ?? array(
	1 => array(
		'ID'          => 1,
		'post_name'   => 'vehicles',
		'post_parent' => 0,
		'post_title'  => null,
		'post_status' => 'publish',
	),
);
$GLOBALS['rexroad_test_current_post_id'] = $GLOBALS['rexroad_test_current_post_id'] ?? 1;
$GLOBALS['rexroad_test_get_pages_calls'] = 0;

if ( ! class_exists( 'WP_Post' ) ) {
	class WP_Post {
		public int $ID;
		public string $post_name;
		public int $post_parent;
		public string $post_title;
		public string $post_status;

		public function __construct( array $data ) {
			$this->ID          = $data['ID'];
			$this->post_name   = $data['post_name'];
			$this->post_parent = $data['post_parent'];
			$this->post_title  = $data['post_title'] ?? ( $GLOBALS['rexroad_test_title'] ?? 'Cars, Trucks & SUVs We Service' );
			$this->post_status = $data['post_status'] ?? 'publish';
		}
	}
}

if ( ! function_exists( 'rexroad_test_make_post' ) ) {
	function rexroad_test_make_post( int $id ): ?WP_Post {
		$rows = $GLOBALS['rexroad_test_pages'] ?? array();
		return isset( $rows[ $id ] ) ? new WP_Post( $rows[ $id ] ) : null;
	}
}

if ( ! function_exists( 'rexroad_test_post_path' ) ) {
	function rexroad_test_post_path( WP_Post $post ): string {
		$segments = array();
		$current  = $post;
		$guard    = 0;
		while ( $current instanceof WP_Post && $guard < 20 ) {
			array_unshift( $segments, $current->post_name );
			if ( 0 === $current->post_parent ) {
				break;
			}
			$current = rexroad_test_make_post( $current->post_parent );
			++$guard;
		}
		return implode( '/', $segments );
	}
}

if ( ! function_exists( 'add_action' ) ) {
	function add_action( ...$args ) {
	}
}

if ( ! function_exists( 'add_filter' ) ) {
	function add_filter( ...$args ) {
	}
}

if ( ! function_exists( 'is_page' ) ) {
	function is_page( $page = null ): bool {
		return false;
	}
}

if ( ! function_exists( 'is_singular' ) ) {
	function is_singular( $post_types = '' ): bool {
		return false;
	}
}

if ( ! function_exists( 'is_page_template' ) ) {
	/**
	 * Compares against $GLOBALS['rexroad_test_current_template'] — set
	 * that to the template filename a test wants to simulate being on
	 * (or leave unset / null for "not on any custom template"). This is
	 * template-aware, unlike a single blanket boolean, so tests that
	 * gate two different templates (e.g. page-vehicles.php vs
	 * page-vehicle-make.php) actually exercise the real distinction.
	 */
	function is_page_template( $template = '' ): bool {
		$current = $GLOBALS['rexroad_test_current_template'] ?? null;
		return null !== $current && $template === $current;
	}
}

if ( ! function_exists( 'get_header' ) ) {
	function get_header( $name = null ) {
	}
}

if ( ! function_exists( 'get_footer' ) ) {
	function get_footer( $name = null ) {
	}
}

if ( ! function_exists( 'have_posts' ) ) {
	function have_posts(): bool {
		if ( ( $GLOBALS['rexroad_test_have_posts_remaining'] ?? 0 ) > 0 ) {
			--$GLOBALS['rexroad_test_have_posts_remaining'];
			return true;
		}
		return false;
	}
}

if ( ! function_exists( 'the_post' ) ) {
	function the_post() {
	}
}

if ( ! function_exists( 'the_ID' ) ) {
	function the_ID() {
		echo $GLOBALS['rexroad_test_current_post_id'] ?? 1;
	}
}

if ( ! function_exists( 'get_the_ID' ) ) {
	function get_the_ID(): int {
		return (int) ( $GLOBALS['rexroad_test_current_post_id'] ?? 1 );
	}
}

if ( ! function_exists( 'post_class' ) ) {
	function post_class( $class = '' ) {
		echo 'post-1';
	}
}

if ( ! function_exists( 'get_the_title' ) ) {
	function get_the_title( $post = null ): string {
		$resolved = null === $post ? get_post() : ( $post instanceof WP_Post ? $post : get_post( (int) $post ) );
		return $resolved instanceof WP_Post ? $resolved->post_title : ( $GLOBALS['rexroad_test_title'] ?? 'Cars, Trucks & SUVs We Service' );
	}
}

if ( ! function_exists( 'get_the_content' ) ) {
	function get_the_content(): string {
		return '';
	}
}

if ( ! function_exists( 'the_content' ) ) {
	function the_content() {
	}
}

if ( ! function_exists( 'get_template_part' ) ) {
	function get_template_part( string $slug, ?string $name = null, array $args = array() ) {
		$path = get_template_directory() . '/' . $slug . '.php';
		if ( is_file( $path ) ) {
			require $path;
		}
	}
}

if ( ! function_exists( 'get_the_excerpt' ) ) {
	function get_the_excerpt( $post = null ): string {
		return '';
	}
}

if ( ! function_exists( 'get_post' ) ) {
	function get_post( $post = null ): ?WP_Post {
		if ( null === $post ) {
			return rexroad_test_make_post( (int) ( $GLOBALS['rexroad_test_current_post_id'] ?? 1 ) );
		}
		if ( $post instanceof WP_Post ) {
			return $post;
		}
		return rexroad_test_make_post( (int) $post );
	}
}

if ( ! function_exists( 'get_page_template_slug' ) ) {
	/**
	 * Reads an optional 'template' key on a fixture row (e.g.
	 * 'page-vehicles.php'); defaults to '' (WordPress's own default-
	 * template convention) when the row doesn't specify one.
	 */
	function get_page_template_slug( $post = null ): string {
		$rows = $GLOBALS['rexroad_test_pages'] ?? array();
		$id   = $post instanceof WP_Post ? $post->ID : (int) $post;
		return (string) ( $rows[ $id ]['template'] ?? '' );
	}
}

if ( ! function_exists( 'get_pages' ) ) {
	/**
	 * Direct-children lookup, mirroring the subset of get_pages() args
	 * rexroad_vehicle_get_published_child_page_map() actually uses
	 * ('parent', 'post_status'). Counts calls in
	 * $GLOBALS['rexroad_test_get_pages_calls'] so tests can assert a
	 * single lookup rather than one per catalog entry.
	 *
	 * @return WP_Post[]
	 */
	function get_pages( array $args = array() ): array {
		++$GLOBALS['rexroad_test_get_pages_calls'];

		$rows   = $GLOBALS['rexroad_test_pages'] ?? array();
		$parent = $args['parent'] ?? null;
		$status = $args['post_status'] ?? 'publish';

		$result = array();
		foreach ( $rows as $id => $row ) {
			if ( null !== $parent && (int) $row['post_parent'] !== (int) $parent ) {
				continue;
			}
			if ( $status && ( $row['post_status'] ?? 'publish' ) !== $status ) {
				continue;
			}
			$result[] = rexroad_test_make_post( $id );
		}
		return $result;
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( $str ): string {
		return trim( (string) $str );
	}
}

if ( ! function_exists( 'wp_unslash' ) ) {
	function wp_unslash( $value ) {
		return $value;
	}
}

if ( ! function_exists( 'get_theme_mod' ) ) {
	function get_theme_mod( $name, $default = false ) {
		return $default;
	}
}

if ( ! function_exists( 'rexroad_custom_phone_href' ) ) {
	// Mirrors the one-line implementation in inc/customizer.php. Stubbed
	// here (rather than loading the full Customizer registration file)
	// to avoid pulling in WP_Customize_Manager and 15 unrelated sections
	// just to reach this single helper.
	function rexroad_custom_phone_href( string $phone ): string {
		return preg_replace( '/[^0-9+]/', '', $phone ) ?: '';
	}
}

if ( ! function_exists( 'home_url' ) ) {
	function home_url( string $path = '' ): string {
		return 'https://example.test' . $path;
	}
}

if ( ! function_exists( 'get_permalink' ) ) {
	function get_permalink( $post = null ): string {
		$resolved = null === $post ? get_post() : ( $post instanceof WP_Post ? $post : get_post( (int) $post ) );
		if ( ! $resolved instanceof WP_Post ) {
			return 'https://example.test/vehicles/';
		}
		return 'https://example.test/' . rexroad_test_post_path( $resolved ) . '/';
	}
}

if ( ! function_exists( 'trailingslashit' ) ) {
	function trailingslashit( string $string ): string {
		return rtrim( $string, '/' ) . '/';
	}
}

if ( ! function_exists( 'esc_html' ) ) {
	function esc_html( $text ): string {
		return htmlspecialchars( (string) $text, ENT_QUOTES );
	}
}

if ( ! function_exists( 'esc_attr' ) ) {
	function esc_attr( $text ): string {
		return htmlspecialchars( (string) $text, ENT_QUOTES );
	}
}

if ( ! function_exists( 'esc_url' ) ) {
	function esc_url( $url ): string {
		return htmlspecialchars( (string) $url, ENT_QUOTES );
	}
}

if ( ! function_exists( '_n' ) ) {
	function _n( string $single, string $plural, int $number, string $domain = 'default' ): string {
		return 1 === $number ? $single : $plural;
	}
}

if ( ! function_exists( 'wp_list_pluck' ) ) {
	function wp_list_pluck( array $list, $field ): array {
		return array_map(
			static function ( $item ) use ( $field ) {
				return is_object( $item ) ? $item->$field : $item[ $field ];
			},
			$list
		);
	}
}

if ( ! function_exists( 'wp_json_encode' ) ) {
	function wp_json_encode( $data, int $flags = 0 ) {
		return json_encode( $data, $flags );
	}
}
