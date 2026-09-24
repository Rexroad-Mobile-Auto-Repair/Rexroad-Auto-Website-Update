<?php
/**
 * Local-business structured data output.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

/**
 * Output one AutoRepair JSON-LD record on the homepage.
 *
 * A future SEO plugin can disable this record with the
 * rexroad_custom_output_local_business_schema filter.
 */
function rexroad_custom_local_business_schema(): void {
    if ( ! is_front_page() || ! apply_filters( 'rexroad_custom_output_local_business_schema', true ) ) {
        return;
    }

    // Avoid duplicate organization markup if a full SEO suite is activated later.
    if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) ) {
        return;
    }

    $phone       = (string) get_theme_mod( 'rexroad_header_phone', '469-469-4521' );
    $phone_href  = rexroad_custom_phone_href( $phone );
    $phone_digits = preg_replace( '/\D+/', '', $phone_href ) ?: '';

    if ( 10 === strlen( $phone_digits ) ) {
        $phone_schema = '+1' . $phone_digits;
    } elseif ( '' !== $phone_digits ) {
        $phone_schema = '+' . $phone_digits;
    } else {
        $phone_schema = '';
    }

    $street      = (string) get_theme_mod( 'rexroad_business_street', '15922 Eldorado Parkway, Suite 500' );
    $city        = (string) get_theme_mod( 'rexroad_business_city', 'Frisco' );
    $state       = (string) get_theme_mod( 'rexroad_business_state', 'TX' );
    $postal_code = (string) get_theme_mod( 'rexroad_business_postal_code', '75035' );
    $email       = (string) get_theme_mod( 'rexroad_business_email', 'aaron@rexroadauto.com' );
    $areas_raw   = (string) get_theme_mod( 'rexroad_footer_service_areas', "Frisco\nProsper\nLittle Elm\nThe Colony\nCelina\nMcKinney\nPlano\nAllen" );
    $areas       = array_values( array_filter( array_map( 'trim', preg_split( '/[\r\n,]+/', $areas_raw ) ?: array() ) ) );
    $home_url    = home_url( '/' );

    $schema = array(
        '@context'    => 'https://schema.org',
        '@type'       => 'AutoRepair',
        '@id'         => $home_url . '#auto-repair',
        'name'        => get_bloginfo( 'name' ),
        'description' => get_bloginfo( 'description' ),
        'url'         => $home_url,
        'address'     => array(
            '@type'           => 'PostalAddress',
            'streetAddress'   => $street,
            'addressLocality' => $city,
            'addressRegion'   => $state,
            'postalCode'      => $postal_code,
            'addressCountry'  => 'US',
        ),
        'areaServed'  => array_map(
            static function ( string $area ): array {
                return array(
                    '@type' => 'City',
                    'name'  => $area . ', Texas',
                );
            },
            $areas
        ),
    );

    if ( '' !== $phone_schema ) {
        $schema['telephone'] = $phone_schema;
    }

    if ( '' !== $email ) {
        $schema['email'] = $email;
    }

    $hero_image = (string) get_theme_mod( 'rexroad_home_hero_image', get_template_directory_uri() . '/assets/images/rexroad-service-truck.png' );
    if ( '' !== $hero_image ) {
        $schema['image'] = $hero_image;
    }

    $custom_logo_id = (int) get_theme_mod( 'custom_logo', 0 );
    $logo_url       = $custom_logo_id > 0 ? wp_get_attachment_image_url( $custom_logo_id, 'full' ) : false;
    if ( false !== $logo_url ) {
        $schema['logo'] = $logo_url;
    }

    if ( (bool) get_theme_mod( 'rexroad_open_24_hours', true ) ) {
        $schema['openingHoursSpecification'] = array(
            '@type'     => 'OpeningHoursSpecification',
            'dayOfWeek' => array(
                'https://schema.org/Monday',
                'https://schema.org/Tuesday',
                'https://schema.org/Wednesday',
                'https://schema.org/Thursday',
                'https://schema.org/Friday',
                'https://schema.org/Saturday',
                'https://schema.org/Sunday',
            ),
            'opens'     => '00:00',
            'closes'    => '23:59',
        );
    }

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
}
add_action( 'wp_head', 'rexroad_custom_local_business_schema', 30 );
