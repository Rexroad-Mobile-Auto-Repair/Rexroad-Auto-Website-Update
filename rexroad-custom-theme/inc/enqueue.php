<?php
/**
 * Theme styles and scripts.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

function rexroad_custom_asset_version( string $relative_path ): string {
    $absolute_path = get_template_directory() . $relative_path;

    if ( file_exists( $absolute_path ) ) {
        return (string) filemtime( $absolute_path );
    }

    return (string) wp_get_theme()->get( 'Version' );
}

function rexroad_custom_assets(): void {

    /*
     * Homepage:
     * Load one bundled stylesheet instead of multiple separate CSS files.
     */
    if ( is_front_page() ) {
        wp_enqueue_style(
            'rexroad-custom-homepage-bundle',
            get_template_directory_uri() . '/assets/css/homepage-bundle.css',
            array(),
            rexroad_custom_asset_version( '/assets/css/homepage-bundle.css' )
        );
    } else {

        /*
         * Non-homepage pages keep using the existing individual stylesheets.
         */
        $styles = array(
            'rexroad-custom-base'    => '/assets/css/base.css',
            'rexroad-custom-content' => '/assets/css/content.css',
            'rexroad-custom-header'  => '/assets/css/header.css',
            'rexroad-custom-footer'  => '/assets/css/footer.css',
        );

        if ( is_page( 'about-us' ) ) {
            $styles['rexroad-custom-about'] = '/assets/css/about.css';
        }

        $services_page = get_page_by_path( 'services' );

        if (
            $services_page instanceof WP_Post
            && is_page()
            && in_array(
                (int) $services_page->ID,
                array_map( 'intval', get_post_ancestors( get_queried_object_id() ) ),
                true
            )
        ) {
            $styles['rexroad-custom-service-detail'] = '/assets/css/service-detail.css';
        }

        $styles['rexroad-custom-services']   = '/assets/css/services.css';
        $styles['rexroad-custom-blog']       = '/assets/css/blog.css';
        $styles['rexroad-custom-forms']      = '/assets/css/forms.css';

        if ( is_page_template( 'page-vehicles.php' ) || is_page_template( 'page-vehicle-make.php' ) || is_page_template( 'page-vehicle-model.php' ) ) {
            $styles['rexroad-custom-vehicles'] = '/assets/css/vehicles.css';
        }

        $styles['rexroad-custom-responsive'] = '/assets/css/responsive.css';

        foreach ( $styles as $handle => $relative_path ) {
            wp_enqueue_style(
                $handle,
                get_template_directory_uri() . $relative_path,
                array(),
                rexroad_custom_asset_version( $relative_path )
            );
        }
    }

    wp_enqueue_script(
        'rexroad-custom-navigation',
        get_template_directory_uri() . '/assets/js/navigation.js',
        array(),
        rexroad_custom_asset_version( '/assets/js/navigation.js' ),
        true
    );

    if ( is_front_page() ) {
        wp_enqueue_script(
            'rexroad-custom-homepage',
            get_template_directory_uri() . '/assets/js/homepage.js',
            array(),
            rexroad_custom_asset_version( '/assets/js/homepage.js' ),
            true
        );
    }

    if ( is_page_template( 'page-vehicles.php' ) ) {
        wp_enqueue_script(
            'rexroad-custom-vehicles',
            get_template_directory_uri() . '/assets/js/vehicles.js',
            array(),
            rexroad_custom_asset_version( '/assets/js/vehicles.js' ),
            true
        );
    }
}

add_action( 'wp_enqueue_scripts', 'rexroad_custom_assets' );