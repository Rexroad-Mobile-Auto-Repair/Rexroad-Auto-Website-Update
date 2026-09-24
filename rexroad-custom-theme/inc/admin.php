<?php
/**
 * WordPress administrator guidance for theme-managed pages.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

/**
 * Explain where the custom homepage content is managed.
 *
 * @param WP_Post $post Current post object.
 */
function rexroad_custom_front_page_editor_notice( WP_Post $post ): void {
    $front_page_id = (int) get_option( 'page_on_front' );

    if ( $front_page_id < 1 || $front_page_id !== (int) $post->ID || ! current_user_can( 'edit_theme_options' ) ) {
        return;
    }

    $customize_url = add_query_arg(
        array(
            'autofocus[section]' => 'rexroad_homepage_settings',
            'url'                => get_permalink( $post ),
        ),
        admin_url( 'customize.php' )
    );
    ?>
    <div class="notice notice-info inline">
        <p>
            <strong><?php esc_html_e( 'Rexroad custom homepage', 'rexroad-custom' ); ?></strong><br>
            <?php esc_html_e( 'The homepage layout is supplied by the theme. Edit its main headline, description, button, and service-truck image in Rexroad Homepage settings.', 'rexroad-custom' ); ?>
            <a href="<?php echo esc_url( $customize_url ); ?>"><?php esc_html_e( 'Open Rexroad Homepage settings', 'rexroad-custom' ); ?></a>
        </p>
    </div>
    <?php
}
add_action( 'edit_form_after_title', 'rexroad_custom_front_page_editor_notice' );
