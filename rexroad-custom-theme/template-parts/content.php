<?php
/**
 * Default loop content.
 *
 * @package Rexroad_Custom
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
    <h2 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <div class="post-card__excerpt">
        <?php the_excerpt(); ?>
    </div>
</article>
