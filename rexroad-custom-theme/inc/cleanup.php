<?php
/**
 * Small theme filters and cleanup functions.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

function rexroad_custom_excerpt_more(): string {
    return '&hellip;';
}
add_filter( 'excerpt_more', 'rexroad_custom_excerpt_more' );
