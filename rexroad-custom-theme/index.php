<?php
/**
 * Main template.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="site-main" id="primary">
    <div class="rr-container">
        <header class="page-header">
            <h1 class="page-title"><?php echo esc_html( is_home() ? single_post_title( '', false ) : get_bloginfo( 'name' ) ); ?></h1>
        </header>

        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : ?>
                <?php the_post(); ?>
                <?php get_template_part( 'template-parts/content', get_post_type() ); ?>
            <?php endwhile; ?>

            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <?php get_template_part( 'template-parts/content', 'none' ); ?>
        <?php endif; ?>
    </div>
</main>
<?php
get_footer();
