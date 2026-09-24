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

/*
 * Service-area context reuses the SAME theme configuration already
 * shown in the footer — never a new, separately-maintained city list.
 */
$rexroad_vehicle_service_area_raw = (string) get_theme_mod(
	'rexroad_footer_service_areas',
	"Frisco\nProsper\nLittle Elm\nThe Colony\nCelina\nMcKinney\nPlano\nAllen"
);
$rexroad_vehicle_service_areas    = array_values( array_filter( array_map( 'trim', preg_split( '/[\r\n,]+/', $rexroad_vehicle_service_area_raw ) ?: array() ) ) );

if ( null !== $rexroad_vehicle_resolved ) {
	/*
	 * Other models under the SAME already-resolved make, for the
	 * "Other {Make} Models We Service" section — reuses the identical
	 * published-child-page map pattern the make page already uses for
	 * its own directory (one get_pages() call, no N+1), scoped to the
	 * make page's ID (this model's own parent).
	 */
	$rexroad_vehicle_other_models = rexroad_vehicle_get_other_models( $rexroad_vehicle_resolved['make']['slug'], $rexroad_vehicle_resolved['model']['slug'], 6 );
	$rexroad_vehicle_sibling_pages = $rexroad_vehicle_make_post instanceof WP_Post
		? rexroad_vehicle_get_published_child_page_map( (int) $rexroad_vehicle_make_post->ID )
		: array();
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
								Service availability depends on your vehicle, its condition, and the repair needed. Submit your year and issue through Request Service and we&rsquo;ll confirm what we can do.
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

					<!-- Supported model years (exact catalog data, gaps preserved) -->
					<section class="rr-content-section rr-vehicles-years">
						<div class="rr-info-card">
							<h3>Supported Model Years</h3>
							<p>
								We service <?php echo esc_html( $rexroad_vehicle_make_name . ' ' . $rexroad_vehicle_model_name ); ?> model years: <strong><?php echo esc_html( rexroad_vehicle_format_year_ranges( $rexroad_vehicle_years ) ); ?></strong>. Not every repair applies to every year or configuration &mdash; submit your specific year and issue through Request Service to confirm.
							</p>
						</div>
					</section>

					<?php
					get_template_part(
						'template-parts/vehicles/service-links',
						null,
						array( 'heading' => 'Common ' . $rexroad_vehicle_make_name . ' ' . $rexroad_vehicle_model_name . ' Services' )
					);
					get_template_part(
						'template-parts/vehicles/problem-links',
						null,
						array( 'heading' => 'Problems We Diagnose on ' . $rexroad_vehicle_make_name . ' ' . $rexroad_vehicle_model_name . ' Vehicles' )
					);
					?>

					<!-- What mobile service actually covers, in general terms — not model-specific claims -->
					<section class="rr-content-section rr-vehicles-capability">
						<div class="rr-section-heading">
							<p class="rr-eyebrow">How It Works</p>
							<h2>What We Can Diagnose and Repair at Your Location</h2>
						</div>
						<ul class="rr-check-list">
							<li>Full diagnostic scan and visual inspection at your home or workplace</li>
							<li>Most common repairs completed on-site in a single visit</li>
							<li>Transparent pricing before any work begins</li>
							<li>Parts sourced and confirmed ahead of your appointment</li>
						</ul>
					</section>

					<?php if ( ! empty( $rexroad_vehicle_other_models ) ) : ?>
						<!-- Other models from the same make (deterministic catalog order, max 6, linked only when published) -->
						<section class="rr-content-section rr-vehicles-related">
							<div class="rr-section-heading">
								<p class="rr-eyebrow">More Coverage</p>
								<h2>Other <?php echo esc_html( $rexroad_vehicle_make_name ); ?> Models We Service</h2>
							</div>
							<ul class="rr-vehicle-model-list">
								<?php foreach ( $rexroad_vehicle_other_models as $rexroad_vehicle_other_model ) : ?>
									<?php $rexroad_vehicle_other_model_url = $rexroad_vehicle_sibling_pages[ $rexroad_vehicle_other_model['slug'] ] ?? null; ?>
									<li class="rr-vehicle-model">
										<?php if ( null !== $rexroad_vehicle_other_model_url ) : ?>
											<a class="rr-vehicle-model__name" href="<?php echo esc_url( $rexroad_vehicle_other_model_url ); ?>"><?php echo esc_html( $rexroad_vehicle_other_model['name'] ); ?></a>
										<?php else : ?>
											<span class="rr-vehicle-model__name"><?php echo esc_html( $rexroad_vehicle_other_model['name'] ); ?></span>
										<?php endif; ?>
										<span class="rr-vehicle-model__years"><?php echo esc_html( rexroad_vehicle_format_year_ranges( $rexroad_vehicle_other_model['years'] ) ); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						</section>
					<?php endif; ?>

					<?php if ( ! empty( $rexroad_vehicle_service_areas ) ) : ?>
						<!-- Service-area / mobile-service context (reuses existing footer configuration) -->
						<section class="rr-content-section rr-vehicles-service-area">
							<div class="rr-info-card">
								<h3>Mobile Service, Wherever You Are</h3>
								<p>
									We come to your home or workplace to service your <?php echo esc_html( $rexroad_vehicle_make_name . ' ' . $rexroad_vehicle_model_name ); ?> &mdash; no shop visit required. We currently serve <?php echo esc_html( implode( ', ', $rexroad_vehicle_service_areas ) ); ?> and the surrounding area.
								</p>
							</div>
						</section>
					<?php endif; ?>

					<!-- In-body upward navigation (breadcrumb already links here, but a visible in-content link improves scannability and internal linking beyond the breadcrumb alone). Reuses URLs already resolved above — no new query. -->
					<p class="rr-vehicle-back-link">
						<a href="<?php echo esc_url( $rexroad_vehicle_make_url ); ?>">&larr; View All <?php echo esc_html( $rexroad_vehicle_make_name ); ?> Models</a>
						&nbsp;&middot;&nbsp;
						<a href="<?php echo esc_url( $rexroad_vehicle_hub_url ); ?>">Browse All Vehicles</a>
					</p>

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
