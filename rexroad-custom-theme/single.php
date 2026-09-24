<?php
/**
 * Single post template.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main class="site-main" id="primary">
    <?php while ( have_posts() ) : ?>
        <?php the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="rr-container">

                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>

                    <?php if ( ! is_single( 'frisco-mobile-mechanics' ) ) : ?>
                        <p><?php echo esc_html( get_the_date() ); ?></p>
                    <?php endif; ?>
                </header>

                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="entry-featured-image">
                        <?php the_post_thumbnail( 'full' ); ?>
                    </div>
                <?php endif; ?>

                <div class="entry-content">
                    <?php the_content(); ?>
                    <?php wp_link_pages(); ?>
                </div>

            </div>
        </article>

    <?php endwhile; ?>
</main>

<?php
get_footer();