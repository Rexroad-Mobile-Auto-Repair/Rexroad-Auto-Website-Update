<?php
/**
 * 404 template.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="site-main" id="primary">
    <div class="rr-container">
        <header class="page-header">
            <h1 class="page-title"><?php esc_html_e( 'Page not found', 'rexroad-custom' ); ?></h1>
        </header>
        <div class="entry-content">
            <p><?php esc_html_e( 'The page you requested could not be found. Use the navigation menu or return to the homepage.', 'rexroad-custom' ); ?></p>
            <p><a class="rr-button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return home', 'rexroad-custom' ); ?></a></p>
        </div>
    </div>
</main>
<?php
get_footer();
