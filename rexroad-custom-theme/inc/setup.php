<?php
/**
 * Theme setup.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

function rexroad_custom_setup(): void {
    load_theme_textdomain( 'rexroad-custom-theme', get_template_directory() . '/languages' );

    add_theme_support( 'title-tag' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'editor-styles' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'align-wide' );
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    add_theme_support(
        'custom-logo',
        array(
            'height'      => 112,
            'width'       => 420,
            'flex-height' => true,
            'flex-width'  => true,
        )
    );

    add_editor_style( 'assets/css/editor.css' );
}
add_action( 'after_setup_theme', 'rexroad_custom_setup' );
