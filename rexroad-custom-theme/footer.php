<?php
/**
 * Site footer.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

$rexroad_footer_phone       = (string) get_theme_mod( 'rexroad_header_phone', '469-469-4521' );
$rexroad_footer_phone_href  = rexroad_custom_phone_href( $rexroad_footer_phone );
$rexroad_footer_email       = (string) get_theme_mod( 'rexroad_business_email', 'aaron@rexroadauto.com' );
$rexroad_footer_hours = (string) get_theme_mod( 'rexroad_business_hours', 'Open 24 Hours' );
$rexroad_footer_street      = (string) get_theme_mod( 'rexroad_business_street', '15922 Eldorado Parkway, Suite 500' );
$rexroad_footer_city        = (string) get_theme_mod( 'rexroad_business_city', 'Frisco' );
$rexroad_footer_state       = (string) get_theme_mod( 'rexroad_business_state', 'TX' );
$rexroad_footer_postal_code = (string) get_theme_mod( 'rexroad_business_postal_code', '75035' );
$rexroad_footer_areas_raw   = (string) get_theme_mod( 'rexroad_footer_service_areas', "Frisco\nProsper\nLittle Elm\nThe Colony\nCelina\nMcKinney\nPlano\nAllen" );
$rexroad_footer_areas       = array_values( array_filter( array_map( 'trim', preg_split( '/[\r\n,]+/', $rexroad_footer_areas_raw ) ?: array() ) ) );
$rexroad_footer_address     = implode(
    ', ',
    array_filter(
        array(
            trim( $rexroad_footer_street ),
            trim( $rexroad_footer_city ),
            trim( $rexroad_footer_state . ' ' . $rexroad_footer_postal_code ),
        )
    )
);
$rexroad_footer_map_url     = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $rexroad_footer_address );
?>
<footer class="site-footer" id="colophon">
    <div class="rr-container">
        <div class="site-footer__grid">
            <section>
                <h2 class="site-footer__title"><?php bloginfo( 'name' ); ?></h2>
                <p><?php bloginfo( 'description' ); ?></p>
                <p class="site-footer__contact">
                    <?php if ( '' !== $rexroad_footer_address ) : ?>
                        <a class="site-footer__address" href="<?php echo esc_url( $rexroad_footer_map_url ); ?>" target="_blank" rel="noopener noreferrer">
                            <span><?php echo esc_html( $rexroad_footer_street ); ?></span><br>
                            <span><?php echo esc_html( trim( $rexroad_footer_city . ', ' . $rexroad_footer_state . ' ' . $rexroad_footer_postal_code ) ); ?></span>
                        </a><br>
                    <?php endif; ?>
                    <?php if ( '' !== $rexroad_footer_phone_href ) : ?>
                        <a href="tel:<?php echo esc_attr( $rexroad_footer_phone_href ); ?>"><?php echo esc_html( $rexroad_footer_phone ); ?></a><br>
                    <?php endif; ?>
                    <?php if ( '' !== $rexroad_footer_email ) : ?>
                        <a href="mailto:<?php echo esc_attr( $rexroad_footer_email ); ?>"><?php echo esc_html( antispambot( $rexroad_footer_email ) ); ?></a><br>
                    <?php endif; ?>
                    <?php if ( '' !== trim( $rexroad_footer_hours ) ) : ?>
                        <span><?php esc_html_e( 'Hours:', 'rexroad-custom-theme' ); ?> <?php echo esc_html( $rexroad_footer_hours ); ?></span>
                    <?php endif; ?>
                </p>
            </section>

            <section>
                <h2 class="site-footer__title"><?php esc_html_e( 'Quick Links', 'rexroad-custom-theme' ); ?></h2>
                <ul class="site-footer__list">
                    <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'rexroad-custom-theme' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>"><?php esc_html_e( 'About Us', 'rexroad-custom-theme' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><?php esc_html_e( 'Services', 'rexroad-custom-theme' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>"><?php esc_html_e( 'Contact Us', 'rexroad-custom-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'rexroad-custom-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/terms-and-conditions/' ) ); ?>"><?php esc_html_e( 'Terms & Conditions', 'rexroad-custom-theme' ); ?></a></li>
                </ul>
            </section>

            <section>
                <h2 class="site-footer__title"><?php esc_html_e( 'Service Areas', 'rexroad-custom-theme' ); ?></h2>
                <?php if ( ! empty( $rexroad_footer_areas ) ) : ?>
                    <ul class="site-footer__list site-footer__areas">
                        <?php foreach ( $rexroad_footer_areas as $rexroad_footer_area ) : ?>
                            <li><?php echo esc_html( $rexroad_footer_area ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

            <section>
                <h2 class="site-footer__title"><?php esc_html_e( 'Follow Us', 'rexroad-custom-theme' ); ?></h2>
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'footer',
                        'container'      => false,
                        'fallback_cb'    => false,
                        'depth'          => 1,
                    )
                );
                ?>
            </section>
        </div>

        <div class="site-footer__bottom">
            <p>
                &copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>.
                <?php esc_html_e( 'All rights reserved.', 'rexroad-custom-theme' ); ?>
            </p>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
