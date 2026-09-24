<?php
/**
 * Template Name: Vehicles Directory
 * Template Post Type: page
 *
 * Cars, Trucks & SUVs We Service — /vehicles/
 *
 * Entirely server-rendered from the read-only vehicle catalog accessors
 * (inc/vehicles/vehicles.php). No per-model database queries, no AJAX,
 * no runtime network requests. JavaScript (assets/js/vehicles.js) only
 * enhances the already-rendered directory with live client-side
 * filtering; the full directory is complete and usable with JS
 * disabled.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

get_header();

$rexroad_vehicle_makes = rexroad_vehicle_get_makes();

$rexroad_vehicle_makes_by_slug = array();
foreach ( $rexroad_vehicle_makes as $rexroad_vehicle_make ) {
	$rexroad_vehicle_makes_by_slug[ $rexroad_vehicle_make['slug'] ] = $rexroad_vehicle_make;
}

$rexroad_vehicle_groups   = rexroad_vehicle_groups();
$rexroad_vehicle_featured = rexroad_vehicle_featured_makes();

/*
 * A-Z quick nav: only letters actually present among the 36 makes, each
 * mapped to the FIRST make with that initial in document render order
 * (Domestic, then Asian, then European, each group's makes in the order
 * vehicle-groups.php lists them). The directory itself is grouped by
 * region rather than alphabetically, so a letter can legitimately have
 * more than one make (e.g. "A" covers both Acura in Asian and Alfa
 * Romeo in European) — the nav jumps to the first, not every, match.
 */
$rexroad_vehicle_letter_anchors = array();
foreach ( $rexroad_vehicle_groups as $rexroad_vehicle_group ) {
	foreach ( $rexroad_vehicle_group['makes'] as $rexroad_vehicle_make_slug ) {
		$rexroad_vehicle_make_record = $rexroad_vehicle_makes_by_slug[ $rexroad_vehicle_make_slug ] ?? null;
		if ( null === $rexroad_vehicle_make_record ) {
			continue;
		}
		$rexroad_vehicle_letter = strtoupper( substr( $rexroad_vehicle_make_record['name'], 0, 1 ) );
		if ( ctype_alpha( $rexroad_vehicle_letter ) && ! isset( $rexroad_vehicle_letter_anchors[ $rexroad_vehicle_letter ] ) ) {
			$rexroad_vehicle_letter_anchors[ $rexroad_vehicle_letter ] = $rexroad_vehicle_make_slug;
		}
	}
}
ksort( $rexroad_vehicle_letter_anchors );

/*
 * Server-side search (progressive enhancement baseline). Works with a
 * plain GET request and no JavaScript; assets/js/vehicles.js layers
 * live client-side filtering of the already-rendered directory on top.
 */
$rexroad_vehicle_search_query   = isset( $_GET['vehicle_search'] ) ? sanitize_text_field( wp_unslash( $_GET['vehicle_search'] ) ) : '';
$rexroad_vehicle_search_results = '' !== $rexroad_vehicle_search_query ? rexroad_vehicle_search( $rexroad_vehicle_search_query ) : array();

$rexroad_phone         = (string) get_theme_mod( 'rexroad_header_phone', '469-469-4521' );
$rexroad_phone_href    = rexroad_custom_phone_href( $rexroad_phone );
$rexroad_schedule_url  = (string) get_theme_mod( 'rexroad_schedule_url', home_url( '/contact-us/' ) );
?>

<main class="site-main site-main--vehicles" id="primary">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<?php
		/*
		 * Direct, published child Pages of THIS Vehicles hub page —
		 * i.e. real make SEO landing pages an editor has deliberately
		 * created (e.g. a "Ford" Page using the Vehicle Make Page
		 * template). One query, not one per catalog make. A make name
		 * only becomes a link when it appears in this map; the catalog
		 * itself never implies a page exists.
		 */
		$rexroad_vehicle_make_pages = rexroad_vehicle_get_published_child_page_map( get_the_ID() );
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<div class="rr-container">

				<!-- 1. Hero / introduction -->
				<section class="rr-page-intro rr-vehicles-intro">
					<div>
						<p class="rr-eyebrow">Vehicle Coverage</p>
						<h1><?php echo esc_html( get_the_title() ?: 'Cars, Trucks & SUVs We Service' ); ?></h1>
					</div>
					<div>
						<p class="rr-lead">
							Rexroad Mobile Auto Repair services a wide range of supported U.S.-market cars, trucks, and SUVs across domestic, Asian, and European makes &mdash; right at your home or workplace.
						</p>
						<p>
							Service availability depends on your vehicle, its condition, and the repair needed. We do not service every trim, engine configuration, electric or plug-in hybrid vehicle, or repair type. Submit your year, make, model, and issue through Request Service and we&rsquo;ll confirm what we can do.
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

				<!-- 2. Featured makes -->
				<section class="rr-content-section rr-vehicles-featured">
					<div class="rr-section-heading">
						<p class="rr-eyebrow">Most Requested</p>
						<h2>Featured Makes We Service</h2>
						<p>A quick look at some of the makes we work on most. Every make we support is listed in the full directory below.</p>
					</div>

					<div class="rr-vehicle-make-grid">
						<?php foreach ( $rexroad_vehicle_featured as $rexroad_vehicle_featured_slug ) : ?>
							<?php
							$rexroad_vehicle_featured_make = $rexroad_vehicle_makes_by_slug[ $rexroad_vehicle_featured_slug ] ?? null;
							if ( null === $rexroad_vehicle_featured_make ) {
								continue;
							}
							$rexroad_vehicle_featured_models = rexroad_vehicle_get_models( $rexroad_vehicle_featured_slug );
							$rexroad_vehicle_featured_sample = array_slice( $rexroad_vehicle_featured_models, 0, 8 );
							$rexroad_vehicle_featured_remaining = count( $rexroad_vehicle_featured_models ) - count( $rexroad_vehicle_featured_sample );
							?>
							<?php $rexroad_vehicle_featured_make_url = $rexroad_vehicle_make_pages[ $rexroad_vehicle_featured_slug ] ?? null; ?>
							<div class="rr-vehicle-make-card">
								<h3>
									<?php if ( null !== $rexroad_vehicle_featured_make_url ) : ?>
										<a href="<?php echo esc_url( $rexroad_vehicle_featured_make_url ); ?>"><?php echo esc_html( $rexroad_vehicle_featured_make['name'] ); ?></a>
									<?php else : ?>
										<?php echo esc_html( $rexroad_vehicle_featured_make['name'] ); ?>
									<?php endif; ?>
								</h3>
								<p class="rr-vehicle-make-card__models">
									<?php echo esc_html( implode( ' · ', wp_list_pluck( $rexroad_vehicle_featured_sample, 'name' ) ) ); ?>
									<?php if ( $rexroad_vehicle_featured_remaining > 0 ) : ?>
										&nbsp;· +<?php echo (int) $rexroad_vehicle_featured_remaining; ?> more
									<?php endif; ?>
								</p>
								<a class="rr-vehicle-make-card__link" href="#make-<?php echo esc_attr( $rexroad_vehicle_featured_slug ); ?>">
									View all <?php echo esc_html( $rexroad_vehicle_featured_make['name'] ); ?> vehicles ↓
								</a>
							</div>
						<?php endforeach; ?>
					</div>
				</section>

				<!-- 3. Vehicle search -->
				<section class="rr-content-section rr-vehicles-search" id="vehicle-search">
					<div class="rr-section-heading">
						<p class="rr-eyebrow">Find Your Vehicle</p>
						<h2>Search Your Make or Model</h2>
						<p>Works with or without JavaScript. Try things like &ldquo;F150&rdquo;, &ldquo;F-150&rdquo;, &ldquo;CRV&rdquo;, or &ldquo;Silverado&rdquo;.</p>
					</div>

					<form class="rr-vehicle-search-form" method="get" action="<?php echo esc_url( get_permalink() ); ?>">
						<label class="screen-reader-text" for="rexroad-vehicle-search-input">Search your make or model</label>
						<input
							type="search"
							id="rexroad-vehicle-search-input"
							name="vehicle_search"
							value="<?php echo esc_attr( $rexroad_vehicle_search_query ); ?>"
							placeholder="Search your make or model&hellip;"
							autocomplete="off"
						>
						<button type="submit" class="rr-button">Search</button>
					</form>

					<p id="rexroad-vehicle-search-status" class="rr-vehicle-search-status" aria-live="polite">
						<?php if ( '' !== $rexroad_vehicle_search_query ) : ?>
							<?php
							echo esc_html(
								sprintf(
									/* translators: 1: result count, 2: search term */
									_n( '%1$d result for "%2$s"', '%1$d results for "%2$s"', count( $rexroad_vehicle_search_results ), 'rexroad-custom-theme' ),
									count( $rexroad_vehicle_search_results ),
									$rexroad_vehicle_search_query
								)
							);
							?>
						<?php endif; ?>
					</p>

					<?php if ( '' !== $rexroad_vehicle_search_query ) : ?>
						<div class="rr-vehicle-search-results" id="vehicle-search-results">
							<?php if ( empty( $rexroad_vehicle_search_results ) ) : ?>
								<p>
									No matching vehicles found. That doesn&rsquo;t necessarily mean we can&rsquo;t help &mdash;
									<a href="<?php echo esc_url( $rexroad_schedule_url ); ?>">request service</a> and we&rsquo;ll confirm.
								</p>
							<?php else : ?>
								<ul class="rr-check-list">
									<?php foreach ( array_slice( $rexroad_vehicle_search_results, 0, 25 ) as $rexroad_vehicle_result ) : ?>
										<li>
											<a href="#make-<?php echo esc_attr( $rexroad_vehicle_result['make_slug'] ); ?>">
												<?php
												echo esc_html(
													'model' === $rexroad_vehicle_result['type']
														? $rexroad_vehicle_result['make_name'] . ' ' . $rexroad_vehicle_result['model_name']
														: $rexroad_vehicle_result['make_name']
												);
												?>
											</a>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</section>

				<!-- 4. A-Z make navigation -->
				<nav class="rr-vehicle-az-nav" aria-label="Jump to a make by letter">
					<ul>
						<?php foreach ( $rexroad_vehicle_letter_anchors as $rexroad_vehicle_letter => $rexroad_vehicle_letter_slug ) : ?>
							<li>
								<a href="#make-<?php echo esc_attr( $rexroad_vehicle_letter_slug ); ?>"><?php echo esc_html( $rexroad_vehicle_letter ); ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>

				<!-- 5. Full vehicle directory -->
				<section class="rr-content-section rr-vehicles-directory" id="vehicle-directory">
					<div class="rr-section-heading">
						<p class="rr-eyebrow">Full Coverage</p>
						<h2>Complete Vehicle Directory</h2>
						<p>Every make and model Rexroad Mobile Auto Repair supports, grouped by origin, with U.S. model years shown for each.</p>
					</div>

					<?php foreach ( $rexroad_vehicle_groups as $rexroad_vehicle_group ) : ?>
						<div class="rr-vehicle-group">
							<h3 class="rr-vehicle-group__title"><?php echo esc_html( $rexroad_vehicle_group['name'] ); ?> Makes</h3>

							<div class="rr-vehicle-make-list">
								<?php foreach ( $rexroad_vehicle_group['makes'] as $rexroad_vehicle_group_make_slug ) : ?>
									<?php
									$rexroad_vehicle_group_make = $rexroad_vehicle_makes_by_slug[ $rexroad_vehicle_group_make_slug ] ?? null;
									if ( null === $rexroad_vehicle_group_make ) {
										continue;
									}
									$rexroad_vehicle_group_models = rexroad_vehicle_get_models( $rexroad_vehicle_group_make_slug );
									?>
									<div
										class="rr-vehicle-make"
										id="make-<?php echo esc_attr( $rexroad_vehicle_group_make_slug ); ?>"
										data-search-make="<?php echo esc_attr( rexroad_vehicle_normalize_search_term( $rexroad_vehicle_group_make['name'] ) ); ?>"
									>
										<?php $rexroad_vehicle_group_make_url = $rexroad_vehicle_make_pages[ $rexroad_vehicle_group_make_slug ] ?? null; ?>
										<h4 class="rr-vehicle-make__title">
											<?php if ( null !== $rexroad_vehicle_group_make_url ) : ?>
												<a href="<?php echo esc_url( $rexroad_vehicle_group_make_url ); ?>"><?php echo esc_html( $rexroad_vehicle_group_make['name'] ); ?></a>
											<?php else : ?>
												<?php echo esc_html( $rexroad_vehicle_group_make['name'] ); ?>
											<?php endif; ?>
											<span class="rr-vehicle-make__count">(<?php echo count( $rexroad_vehicle_group_models ); ?>)</span>
										</h4>

										<ul class="rr-vehicle-model-list">
											<?php foreach ( $rexroad_vehicle_group_models as $rexroad_vehicle_model ) : ?>
												<li
													class="rr-vehicle-model"
													data-search-model="<?php echo esc_attr( rexroad_vehicle_normalize_search_term( $rexroad_vehicle_model['name'] ) ); ?>"
												>
													<span class="rr-vehicle-model__name"><?php echo esc_html( $rexroad_vehicle_model['name'] ); ?></span>
													<span class="rr-vehicle-model__years"><?php echo esc_html( rexroad_vehicle_format_year_ranges( $rexroad_vehicle_model['years'] ) ); ?></span>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</section>

				<!-- 6. Coverage clarification -->
				<section class="rr-content-section rr-vehicles-clarification">
					<div class="rr-info-card">
						<h3>What This Directory Means</h3>
						<p>
							This directory shows the U.S.-market makes, models, and model years we generally work on. Being listed here doesn&rsquo;t guarantee every repair, trim, or configuration is covered for that vehicle.
						</p>
						<p>
							To confirm we can help with your specific vehicle and problem, submit your year, make, model, and issue through Request Service below.
						</p>
					</div>
				</section>

				<!-- 7. Service links -->
				<?php get_template_part( 'template-parts/vehicles/service-links' ); ?>

				<!-- 8. Final CTA -->
				<div class="rr-cta-panel">
					<div>
						<p class="rr-eyebrow">Ready When You Are</p>
						<h2>Request Service for Your Vehicle</h2>
						<p>Tell us your year, make, model, and what&rsquo;s going on &mdash; we&rsquo;ll confirm coverage and get you scheduled.</p>
					</div>
					<a class="rr-button" href="<?php echo esc_url( $rexroad_schedule_url ); ?>">Request Service</a>
				</div>

				<?php if ( get_the_content() ) : ?>
					<div class="entry-content">
						<?php the_content(); ?>
					</div>
				<?php endif; ?>

			</div>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer();
