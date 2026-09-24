<?php
/**
 * Site header.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

$rexroad_phone        = (string) get_theme_mod( 'rexroad_header_phone', '469-469-4521' );
$rexroad_phone_href   = rexroad_custom_phone_href( $rexroad_phone );
$rexroad_schedule_url = (string) get_theme_mod( 'rexroad_schedule_url', home_url( '/contact-us/' ) );
$rexroad_hours         = (string) get_theme_mod( 'rexroad_business_hours', 'Open 24 Hours' );
$rexroad_service_area  = (string) get_theme_mod( 'rexroad_service_area', 'Serving Frisco, McKinney, Prosper & Celina' );
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'rexroad-custom' ); ?></a>
<header class="site-header" id="masthead">
    <div class="header-topbar">
        <div class="rr-container header-topbar__inner">
            <?php if ( '' !== $rexroad_phone_href ) : ?>
                <a class="header-topbar__item header-topbar__phone" href="tel:<?php echo esc_attr( $rexroad_phone_href ); ?>">
                    <svg aria-hidden="true" viewBox="0 0 24 24" width="16" height="16" focusable="false">
                        <path d="M6.62 10.79a15.46 15.46 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.02-.24c1.12.37 2.33.57 3.57.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.61 21 3 13.39 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1.02l-2.2 2.2Z" fill="currentColor"/>
                    </svg>
                    <span><?php echo esc_html( $rexroad_phone ); ?></span>
                </a>
            <?php endif; ?>

            <?php if ( '' !== trim( $rexroad_hours ) ) : ?>
                <span class="header-topbar__item">
                    <svg aria-hidden="true" viewBox="0 0 24 24" width="16" height="16" focusable="false">
                        <path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm1 5v4.59l3.2 3.2-1.41 1.42L11 12.41V7h2Z" fill="currentColor"/>
                    </svg>
                    <span><?php echo esc_html( $rexroad_hours ); ?></span>
                </span>
            <?php endif; ?>

            <?php if ( '' !== trim( $rexroad_service_area ) ) : ?>
                <span class="header-topbar__item header-topbar__service-area">
                    <svg aria-hidden="true" viewBox="0 0 24 24" width="16" height="16" focusable="false">
                        <path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5Z" fill="currentColor"/>
                    </svg>
                    <span><?php echo esc_html( $rexroad_service_area ); ?></span>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <div class="header-main">
        <div class="rr-container site-header__inner">
            <div class="site-branding">
                <?php if ( has_custom_logo() ) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                        <span class="site-title__rex">RE</span><span class="site-title__x">X</span><span class="site-title__road">ROAD</span>
                        <span class="site-title__tagline"><?php esc_html_e( 'Mobile Auto Repair', 'rexroad-custom' ); ?></span>
                    </a>
                <?php endif; ?>
            </div>

            <button class="menu-toggle" type="button" aria-controls="header-navigation-panel" aria-expanded="false">
                <span class="menu-toggle__icon" aria-hidden="true">
                    <span></span><span></span><span></span>
                </span>
                <span class="screen-reader-text"><?php esc_html_e( 'Open navigation menu', 'rexroad-custom' ); ?></span>
            </button>

            <div class="header-navigation-panel" id="header-navigation-panel">
                <nav class="primary-navigation" id="site-navigation" aria-label="<?php esc_attr_e( 'Primary navigation', 'rexroad-custom' ); ?>">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'primary',
                            'menu_id'        => 'primary-menu',
                            'container'      => false,
                            'fallback_cb'    => 'wp_page_menu',
                        )
                    );
                    ?>
                </nav>

                <div class="header-actions">
                    <?php if ( '' !== $rexroad_phone_href ) : ?>
                        <a class="header-phone" href="tel:<?php echo esc_attr( $rexroad_phone_href ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Call Rexroad at %s', 'rexroad-custom' ), $rexroad_phone ) ); ?>">
                            <span class="header-phone__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" width="22" height="22" focusable="false">
                                    <path d="M6.62 10.79a15.46 15.46 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.02-.24c1.12.37 2.33.57 3.57.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.61 21 3 13.39 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1.02l-2.2 2.2Z" fill="currentColor"/>
                                </svg>
                            </span>
                            <span class="header-phone__number"><?php echo esc_html( $rexroad_phone ); ?></span>
                        </a>
                    <?php endif; ?>

                    <a class="rr-button header-schedule-button" href="<?php echo esc_url( $rexroad_schedule_url ); ?>">
                        <?php esc_html_e( 'Schedule Service', 'rexroad-custom' ); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
