<?php
/**
 * Template Name: Vehicle Model Page
 * Template Post Type: page
 *
 * A single model's SEO landing page (e.g. /vehicles/ford/f-150/). This
 * URL only exists because an editor deliberately created a real
 * WordPress Page as a child of a valid Vehicle Make Page and selected
 * this template — the vehicle catalog itself never implies a page
 * exists. The model resolves structurally, not just from its own
 * slug: its direct parent must itself resolve as a valid make page
 * (whose own parent must be the real Vehicles hub), and its own slug
 * must match a model under that specific make. Any broken link in
 * that chain — wrong parent, no parent, invalid make, invalid model
 * slug, or a model slug that belongs to a DIFFERENT make — falls back
 * to minimal graceful rendering rather than fabricating vehicle data.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

get_header();

$rexroad_vehicle_model_post = get_post();

/*
 * See rexroad_vehicle_resolve_model_from_current_page() in
 * inc/vehicles/vehicles.php — this is the single source of truth for
 * "is this really a model page", reused by schema-vehicles.php too so
 * the two can never disagree.
 */
$rexroad_vehicle_resolved = rexroad_vehicle_resolve_model_from_current_page();

$rexroad_vehicle_make_post = ( $rexroad_vehicle_model_post instanceof WP_Post && $rexroad_vehicle_model_post->post_parent )
	? get_post( $rexroad_vehicle_model_post->post_parent )
	: null;
$rexroad_vehicle_hub_post  = ( $rexroad_vehicle_make_post instanceof WP_Post && $rexroad_vehicle_make_post->post_parent )
	? get_post( $rexroad_vehicle_make_post->post_parent )
	: null;

$rexroad_vehicle_hub_url   = $rexroad_vehicle_hub_post instanceof WP_Post ? get_permalink( $rexroad_vehicle_hub_post ) : home_url( '/vehicles/' );
$rexroad_vehicle_hub_label = $rexroad_vehicle_hub_post instanceof WP_Post ? get_the_title( $rexroad_vehicle_hub_post ) : 'Vehicles';

$rexroad_vehicle_make_url = $rexroad_vehicle_make_post instanceof WP_Post ? get_permalink( $rexroad_vehicle_make_post ) : $rexroad_vehicle_hub_url;

/*
 * Catalog names are authoritative for a validated model page (H1,
 * breadcrumb, schema all agree even if either WP Page title was typed
 * differently) — WP titles are only used as fallbacks for pages that
 * don't resolve to a real catalog make+model relationship.
 */
$rexroad_vehicle_make_label  = null !== $rexroad_vehicle_resolved
	? $rexroad_vehicle_resolved['make']['name']
	: ( $rexroad_vehicle_make_post instanceof WP_Post ? get_the_title( $rexroad_vehicle_make_post ) : 'Make' );
$rexroad_vehicle_model_label = null !== $rexroad_vehicle_resolved ? $rexroad_vehicle_resolved['model']['name'] : get_the_title();

$rexroad_phone        = (string) get_theme_mod( 'rexroad_header_phone', '469-469-4521' );
$rexroad_phone_href   = rexroad_custom_phone_href( $rexroad_phone );
$rexroad_schedule_url = (string) get_theme_mod( 'rexroad_schedule_url', home_url( '/contact-us/' ) );
?>

<main class="site-main site-main--vehicles" id="primary">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<div class="rr-container">

				<nav class="rr-vehicle-breadcrumb" aria-label="Breadcrumb">
					<ol>
						<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
						<li><a href="<?php echo esc_url( $rexroad_vehicle_hub_url ); ?>"><?php echo esc_html( $rexroad_vehicle_hub_label ); ?></a></li>
						<li><a href="<?php echo esc_url( $rexroad_vehicle_make_url ); ?>"><?php echo esc_html( $rexroad_vehicle_make_label ); ?></a></li>
						<li aria-current="page"><?php echo esc_html( $rexroad_vehicle_model_label ); ?></li>
					</ol>
				</nav>

				<?php if ( null === $rexroad_vehicle_resolved ) : ?>

					<!--
						This page's hierarchy or slug doesn't resolve to a
						real catalog make+model relationship. Graceful
						minimal fallback: no fabricated vehicle data.
					-->
					<header class="rr-page-intro rr-vehicles-intro">
						<div>
							<h1><?php echo esc_html( get_the_title() ?: 'Vehicles' ); ?></h1>
						</div>
					</header>

					<?php if ( get_the_content() ) : ?>
						<div class="entry-content">
							<?php the_content(); ?>
						</div>
					<?php endif; ?>

					<div class="rr-cta-panel">
						<div>
							<p class="rr-eyebrow">Ready When You Are</p>
							<h2>Request Service</h2>
							<p>Tell us your year, make, model, and what&rsquo;s going on &mdash; we&rsquo;ll confirm coverage and get you scheduled.</p>
						</div>
						<a class="rr-button" href="<?php echo esc_url( $rexroad_schedule_url ); ?>">Request Service</a>
					</div>

				<?php else : ?>

					<?php
					$rexroad_vehicle_make_name  = $rexroad_vehicle_resolved['make']['name'];
					$rexroad_vehicle_model_name = $rexroad_vehicle_resolved['model']['name'];
					$rexroad_vehicle_years      = $rexroad_vehicle_resolved['model']['years'];
					?>

					<!-- Hero / introduction -->
					<section class="rr-page-intro rr-vehicles-intro">
						<div>
							<p class="rr-eyebrow">Vehicle Coverage</p>
							<h1><?php echo esc_html( $rexroad_vehicle_make_name . ' ' . $rexroad_vehicle_model_name ); ?> Mobile Mechanic Service</h1>
						</div>
						<div>
							<p class="rr-lead">
								Rexroad Mobile Auto Repair services the <?php echo esc_html( $rexroad_vehicle_make_name . ' ' . $rexroad_vehicle_model_name ); ?> across our Frisco-area service area &mdash; right at your home or workplace.
							</p>
							<p>
								Supported model years: <strong><?php echo esc_html( rexroad_vehicle_format_year_ranges( $rexroad_vehicle_years ) ); ?></strong>. Actual service eligibility depends on your specific vehicle and the repair requested. Submit your year and issue through Request Service and we&rsquo;ll confirm what we can do.
							</p>
							<div class="rr-vehicles-intro__actions">
								<a class="rr-button" href="<?php echo esc_url( $rexroad_schedule_url ); ?>">Request Service</a>
								<?php if ( '' !== $rexroad_phone_href ) : ?>
									<a class="rr-button rr-button--outline" href="tel:<?php echo esc_attr( $rexroad_phone_href ); ?>">
										Call <?php echo esc_html( $rexroad_phone ); ?>
									</a>
								<?php endif; ?>
							</div>
						</div>
					</section>

					<!-- Editorial content: unique model-specific copy lives here, not auto-generated. -->
					<?php if ( get_the_content() ) : ?>
						<div class="entry-content">
							<?php the_content(); ?>
						</div>
					<?php endif; ?>

					<?php get_template_part( 'template-parts/vehicles/service-links' ); ?>

					<!-- Final CTA -->
					<div class="rr-cta-panel">
						<div>
							<p class="rr-eyebrow">Ready When You Are</p>
							<h2>Request Service for Your <?php echo esc_html( $rexroad_vehicle_make_name . ' ' . $rexroad_vehicle_model_name ); ?></h2>
							<p>Tell us your year and what&rsquo;s going on &mdash; we&rsquo;ll confirm coverage and get you scheduled.</p>
						</div>
						<a class="rr-button" href="<?php echo esc_url( $rexroad_schedule_url ); ?>">Request Service</a>
					</div>

				<?php endif; ?>

			</div>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer();
