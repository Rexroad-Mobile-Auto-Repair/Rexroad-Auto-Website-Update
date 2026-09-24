<?php
/**
 * Template Name: Vehicle Make Page
 * Template Post Type: page
 *
 * A single make's SEO landing page (e.g. /vehicles/ford/). This URL
 * only exists because an editor deliberately created a real WordPress
 * Page as a child of the Vehicles hub page and selected this template
 * — the vehicle catalog itself never implies a page exists. The make
 * is resolved from this page's OWN slug (get_post()->post_name)
 * against the read-only vehicle catalog accessors; if the slug doesn't
 * match a catalog make, this renders a minimal graceful fallback
 * rather than fabricating vehicle data.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

get_header();

$rexroad_vehicle_make_post = get_post();

/*
 * Resolves only when this page's own slug matches a catalog make AND
 * its direct parent is the real Vehicles hub (by template, not a
 * hardcoded slug/ID) — see rexroad_vehicle_resolve_make_from_current_page()
 * for why. A matching slug under the wrong (or no) parent still falls
 * back to the minimal graceful state below; nothing is fabricated.
 */
$rexroad_vehicle_make = rexroad_vehicle_resolve_make_from_current_page();

$rexroad_vehicle_hub_post   = ( $rexroad_vehicle_make_post instanceof WP_Post && $rexroad_vehicle_make_post->post_parent )
	? get_post( $rexroad_vehicle_make_post->post_parent )
	: null;
$rexroad_vehicle_hub_url    = $rexroad_vehicle_hub_post instanceof WP_Post ? get_permalink( $rexroad_vehicle_hub_post ) : home_url( '/vehicles/' );
$rexroad_vehicle_hub_title  = $rexroad_vehicle_hub_post instanceof WP_Post ? get_the_title( $rexroad_vehicle_hub_post ) : 'Vehicles';

/*
 * The catalog display name is authoritative for a validated make page
 * (H1, breadcrumb, schema all agree even if the WP Page title was
 * typed differently) — the WP title is only used as a fallback for
 * pages that don't resolve to a real catalog make.
 */
$rexroad_vehicle_current_label = null !== $rexroad_vehicle_make ? $rexroad_vehicle_make['name'] : get_the_title();

$rexroad_phone         = (string) get_theme_mod( 'rexroad_header_phone', '469-469-4521' );
$rexroad_phone_href    = rexroad_custom_phone_href( $rexroad_phone );
$rexroad_schedule_url  = (string) get_theme_mod( 'rexroad_schedule_url', home_url( '/contact-us/' ) );

/*
 * Service-area context reuses the SAME theme configuration already
 * shown in the footer — never a new, separately-maintained city list.
 */
$rexroad_vehicle_service_area_raw = (string) get_theme_mod(
	'rexroad_footer_service_areas',
	"Frisco\nProsper\nLittle Elm\nThe Colony\nCelina\nMcKinney\nPlano\nAllen"
);
$rexroad_vehicle_service_areas    = array_values( array_filter( array_map( 'trim', preg_split( '/[\r\n,]+/', $rexroad_vehicle_service_area_raw ) ?: array() ) ) );

if ( null !== $rexroad_vehicle_make ) {
	$rexroad_vehicle_models      = rexroad_vehicle_get_models( $rexroad_vehicle_make['slug'] );
	$rexroad_vehicle_featured    = array_slice( $rexroad_vehicle_models, 0, 8 );
	$rexroad_vehicle_child_pages = rexroad_vehicle_get_published_child_page_map( (int) $rexroad_vehicle_make_post->ID );
}
?>

<main class="site-main site-main--vehicles" id="primary">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<div class="rr-container">

				<nav class="rr-vehicle-breadcrumb" aria-label="Breadcrumb">
					<ol>
						<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
						<li><a href="<?php echo esc_url( $rexroad_vehicle_hub_url ); ?>"><?php echo esc_html( $rexroad_vehicle_hub_title ); ?></a></li>
						<li aria-current="page"><?php echo esc_html( $rexroad_vehicle_current_label ); ?></li>
					</ol>
				</nav>

				<?php if ( null === $rexroad_vehicle_make ) : ?>

					<!--
						This Page's slug doesn't match any catalog make.
						Graceful minimal fallback: no fabricated vehicle
						data, no catalog-driven sections.
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

					<!-- Hero / introduction -->
					<section class="rr-page-intro rr-vehicles-intro">
						<div>
							<p class="rr-eyebrow">Vehicle Coverage</p>
							<h1><?php echo esc_html( $rexroad_vehicle_make['name'] ); ?> Mobile Mechanic Service</h1>
						</div>
						<div>
							<p class="rr-lead">
								Rexroad Mobile Auto Repair services <?php echo esc_html( $rexroad_vehicle_make['name'] ); ?> vehicles across our Frisco-area service area &mdash; right at your home or workplace.
							</p>
							<p>
								Service availability depends on your vehicle, its condition, and the repair needed. Submit your year, model, and issue through Request Service and we&rsquo;ll confirm what we can do.
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

					<!-- Editorial content: unique make-specific copy lives here, not auto-generated. -->
					<?php if ( get_the_content() ) : ?>
						<div class="entry-content">
							<?php the_content(); ?>
						</div>
					<?php endif; ?>

					<!-- Models we service (deterministic catalog order, max 8 shown here — no popularity ranking implied) -->
					<section class="rr-content-section rr-vehicles-featured">
						<div class="rr-section-heading">
							<p class="rr-eyebrow">Coverage</p>
							<h2>Models We Service</h2>
						</div>
						<ul class="rr-vehicle-model-list">
							<?php foreach ( $rexroad_vehicle_featured as $rexroad_vehicle_model ) : ?>
								<?php $rexroad_vehicle_model_url = $rexroad_vehicle_child_pages[ $rexroad_vehicle_model['slug'] ] ?? null; ?>
								<li class="rr-vehicle-model">
									<?php if ( null !== $rexroad_vehicle_model_url ) : ?>
										<a class="rr-vehicle-model__name" href="<?php echo esc_url( $rexroad_vehicle_model_url ); ?>"><?php echo esc_html( $rexroad_vehicle_model['name'] ); ?></a>
									<?php else : ?>
										<span class="rr-vehicle-model__name"><?php echo esc_html( $rexroad_vehicle_model['name'] ); ?></span>
									<?php endif; ?>
									<span class="rr-vehicle-model__years"><?php echo esc_html( rexroad_vehicle_format_year_ranges( $rexroad_vehicle_model['years'] ) ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</section>

					<!-- Full model directory for this make -->
					<section class="rr-content-section rr-vehicles-directory">
						<div class="rr-section-heading">
							<p class="rr-eyebrow">Full Coverage</p>
							<h2>Full <?php echo esc_html( $rexroad_vehicle_make['name'] ); ?> Model Directory</h2>
						</div>
						<ul class="rr-vehicle-model-list">
							<?php foreach ( $rexroad_vehicle_models as $rexroad_vehicle_model ) : ?>
								<?php $rexroad_vehicle_model_url = $rexroad_vehicle_child_pages[ $rexroad_vehicle_model['slug'] ] ?? null; ?>
								<li class="rr-vehicle-model">
									<?php if ( null !== $rexroad_vehicle_model_url ) : ?>
										<a class="rr-vehicle-model__name" href="<?php echo esc_url( $rexroad_vehicle_model_url ); ?>"><?php echo esc_html( $rexroad_vehicle_model['name'] ); ?></a>
									<?php else : ?>
										<span class="rr-vehicle-model__name"><?php echo esc_html( $rexroad_vehicle_model['name'] ); ?></span>
									<?php endif; ?>
									<span class="rr-vehicle-model__years"><?php echo esc_html( rexroad_vehicle_format_year_ranges( $rexroad_vehicle_model['years'] ) ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</section>

					<?php
					get_template_part(
						'template-parts/vehicles/service-links',
						null,
						array( 'heading' => 'Common ' . $rexroad_vehicle_make['name'] . ' Services' )
					);
					get_template_part(
						'template-parts/vehicles/problem-links',
						null,
						array( 'heading' => 'Common Issues We Diagnose on ' . $rexroad_vehicle_make['name'] . ' Vehicles' )
					);
					?>

					<?php if ( ! empty( $rexroad_vehicle_service_areas ) ) : ?>
						<!-- Service-area / mobile-service context (reuses existing footer configuration) -->
						<section class="rr-content-section rr-vehicles-service-area">
							<div class="rr-info-card">
								<h3>Mobile Service, Wherever You Are</h3>
								<p>
									We come to your home or workplace to service your <?php echo esc_html( $rexroad_vehicle_make['name'] ); ?> &mdash; no shop visit required. We currently serve <?php echo esc_html( implode( ', ', $rexroad_vehicle_service_areas ) ); ?> and the surrounding area.
								</p>
							</div>
						</section>
					<?php endif; ?>

					<!-- In-body upward navigation (breadcrumb already links here, but a visible in-content link improves scannability and internal linking beyond the breadcrumb alone). Reuses the hub URL already resolved above — no new query. -->
					<p class="rr-vehicle-back-link">
						<a href="<?php echo esc_url( $rexroad_vehicle_hub_url ); ?>">&larr; Browse All Vehicles</a>
					</p>

					<!-- Final CTA -->
					<div class="rr-cta-panel">
						<div>
							<p class="rr-eyebrow">Ready When You Are</p>
							<h2>Request Service for Your <?php echo esc_html( $rexroad_vehicle_make['name'] ); ?></h2>
							<p>Tell us your year, model, and what&rsquo;s going on &mdash; we&rsquo;ll confirm coverage and get you scheduled.</p>
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
