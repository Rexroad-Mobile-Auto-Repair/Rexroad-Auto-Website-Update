<?php
/**
 * Navigation menu locations.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

function rexroad_custom_register_menus(): void {
    register_nav_menus(
        array(
            'primary' => __( 'Primary Menu', 'rexroad-custom' ),
            'footer'  => __( 'Footer Menu', 'rexroad-custom' ),
        )
    );
}
add_action( 'after_setup_theme', 'rexroad_custom_register_menus' );
