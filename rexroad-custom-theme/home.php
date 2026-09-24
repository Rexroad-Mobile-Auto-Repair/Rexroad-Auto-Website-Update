<?php
/**
 * Posts index / Vehicle Informer archive.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="site-main">

	<div class="rr-container">

		<div class="entry-content rr-article rr-service-detail rr-vehicle-informer">

			<h1 class="page-title">
				<?php echo esc_html( single_post_title( '', false ) ); ?>
			</h1>

			<div class="rr-page-intro">

				<p class="rr-eyebrow">
					REXROAD MOBILE AUTO REPAIR BLOG
				</p>

				<p class="rr-lead">
					Practical automotive repair, diagnostic, and maintenance information from Rexroad Mobile Auto Repair in Frisco, Texas. Learn what common vehicle symptoms may mean, what warning signs deserve attention, and when professional diagnosis may be the right next step.
				</p>

			</div>

			<?php if ( have_posts() ) : ?>

				<section class="rr-content-section">

					<div class="rr-section-heading">

						<p class="rr-eyebrow">
							AUTOMOTIVE GUIDES
						</p>

						<h2>
							Practical answers for common vehicle problems
						</h2>

						<p>
							Browse guides covering warning lights, starting and charging problems, brakes, overheating, A/C concerns, batteries, suspension noise, wheel bearings, maintenance, and other common automotive symptoms.
						</p>

					</div>

					<div class="rr-service-grid">

						<?php
						$vehicle_informer_index = 0;

						while ( have_posts() ) :
							the_post();

							$vehicle_informer_index++;

							$excerpt = get_the_excerpt();

							if ( empty( $excerpt ) ) {
								$excerpt = wp_strip_all_tags( get_the_content() );
							}

							$card_classes = array(
								'rr-service-card',
							);

							if ( 1 === $vehicle_informer_index ) {
								$card_classes[] = 'rr-service-card--featured';
							}
							?>

							<article
								<?php
								post_class(
									implode(
										' ',
										array_map(
											'sanitize_html_class',
											$card_classes
										)
									)
								);
								?>
							>

								<p class="rr-eyebrow">
									<?php echo esc_html( get_the_date( 'M j, Y' ) ); ?>
								</p>

								<h3>
									<?php the_title(); ?>
								</h3>

								<p>
									<?php
									echo esc_html(
										wp_trim_words(
											$excerpt,
											24,
											'…'
										)
									);
									?>
								</p>

								<p>
									<a
										class="rr-service-card__link"
										href="<?php the_permalink(); ?>"
									>
										Read Guide →
									</a>
								</p>

							</article>

						<?php endwhile; ?>

					</div>

				</section>

			<?php else : ?>

				<section class="rr-content-section">

					<div class="rr-section-heading">

						<p class="rr-eyebrow">
							VEHICLE INFORMER
						</p>

						<h2>
							No automotive guides found
						</h2>

						<p>
							New automotive repair, diagnostic, and maintenance guides will appear here when published.
						</p>

					</div>

				</section>

			<?php endif; ?>

			<section class="rr-content-section rr-split">

				<div>

					<p class="rr-eyebrow">
						DIAGNOSIS FIRST
					</p>

					<h2>
						Understand the symptom before replacing parts
					</h2>

					<p>
						A warning light, no-start condition, strange noise, overheating problem, vibration, or electrical issue can have several possible causes.
					</p>

					<p>
						Rexroad Mobile Auto Repair tests the vehicle and explains what was found before recommending a repair. That helps avoid replacing parts based only on symptoms, assumptions, or a trouble-code description.
					</p>

					<p>
						<a
							class="rr-button"
							href="<?php echo esc_url( home_url( '/services/advanced-diagnostics/' ) ); ?>"
						>
							Advanced Diagnostics
						</a>
					</p>

				</div>

				<aside class="rr-info-card">

					<p class="rr-eyebrow">
						NOT SURE WHERE TO START?
					</p>

					<h3>
						Describe what the vehicle is doing
					</h3>

					<p>
						Include the year, make, model, warning lights, noises, when the symptom occurs, whether the vehicle starts and drives, and where the vehicle is located.
					</p>

					<p>
						<a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">
							Request service
						</a>
						or
						<a href="tel:+14694694521">
							call 469-469-4521
						</a>.
					</p>

				</aside>

			</section>

			<section class="rr-content-section">

				<div class="rr-section-heading">

					<p class="rr-eyebrow">
						EXPLORE BY VEHICLE SYSTEM
					</p>

					<h2>
						Start with the area giving you trouble
					</h2>

					<p>
						If you already have a general idea which vehicle system is involved, these service categories can help you find the right information and next step.
					</p>

				</div>

				<div class="rr-service-grid">

					<article class="rr-service-card rr-service-card--featured">
						<p class="rr-eyebrow">DIAGNOSTICS</p>
						<h3>Check Engine Light &amp; Advanced Diagnostics</h3>
						<p>Warning lights, misfires, rough running, hesitation, no-start conditions, electrical faults, and drivability problems.</p>
						<p>
							<a class="rr-service-card__link" href="<?php echo esc_url( home_url( '/services/advanced-diagnostics/' ) ); ?>">
								Explore advanced diagnostics →
							</a>
						</p>
					</article>

					<article class="rr-service-card">
						<p class="rr-eyebrow">STARTING &amp; CHARGING</p>
						<h3>Battery, Starter &amp; Alternator Problems</h3>
						<p>Slow cranking, dead batteries, clicking, charging warnings, starter problems, and alternator symptoms.</p>
						<p>
							<a class="rr-service-card__link" href="<?php echo esc_url( home_url( '/services/alternator-and-starter-repair/' ) ); ?>">
								Explore starting &amp; charging →
							</a>
						</p>
					</article>

					<article class="rr-service-card">
						<p class="rr-eyebrow">STOPPING POWER</p>
						<h3>Brake Problems</h3>
						<p>Squealing, grinding, vibration, pulling, unusual pedal feel, and brake-system warning lights.</p>
						<p>
							<a class="rr-service-card__link" href="<?php echo esc_url( home_url( '/services/brakes-service/' ) ); ?>">
								Explore brake service →
							</a>
						</p>
					</article>

					<article class="rr-service-card">
						<p class="rr-eyebrow">CLIMATE CONTROL</p>
						<h3>A/C Problems</h3>
						<p>Warm air, intermittent cooling, compressor problems, refrigerant leaks, and airflow concerns.</p>
						<p>
							<a class="rr-service-card__link" href="<?php echo esc_url( home_url( '/services/auto-air-condition-service/' ) ); ?>">
								Explore auto A/C service →
							</a>
						</p>
					</article>

					<article class="rr-service-card">
						<p class="rr-eyebrow">ENGINE PROTECTION</p>
						<h3>Overheating &amp; Cooling Problems</h3>
						<p>Coolant leaks, thermostat problems, cooling-fan issues, water pumps, radiators, and overheating.</p>
						<p>
							<a class="rr-service-card__link" href="<?php echo esc_url( home_url( '/services/cooling-system-service/' ) ); ?>">
								Explore cooling-system service →
							</a>
						</p>
					</article>

					<article class="rr-service-card">
						<p class="rr-eyebrow">RIDE &amp; CONTROL</p>
						<h3>Suspension, Steering &amp; Wheel Noise</h3>
						<p>Clunks, squeaks, loose steering, vibration, wheel-bearing noise, CV axle symptoms, and suspension concerns.</p>
						<p>
							<a class="rr-service-card__link" href="<?php echo esc_url( home_url( '/services/suspension-and-steering/' ) ); ?>">
								Explore suspension &amp; steering →
							</a>
						</p>
					</article>

				</div>

			</section>

			<section class="rr-content-section">

				<div class="rr-section-heading">

					<p class="rr-eyebrow">
						MOBILE MECHANIC IN FRISCO, TEXAS
					</p>

					<h2>
						Automotive information backed by real diagnostic service
					</h2>

					<p>
						Vehicle Informer is designed to help you understand common automotive symptoms without assuming which part has failed. Rexroad Mobile Auto Repair provides mobile diagnostics and eligible repairs at homes, workplaces, driveways, garages, and other suitable locations in Frisco and nearby communities.
					</p>

					<p>
						If you already know which repair your vehicle needs, visit
						<a href="<?php echo esc_url( home_url( '/services/' ) ); ?>">
							Rexroad's mobile auto repair services
						</a>.
						If you are not sure where to begin, use the
						<a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">
							service request form
						</a>
						and describe what the vehicle is doing.
					</p>

				</div>

			</section>

			<section class="rr-cta-panel">

				<div>

					<p class="rr-eyebrow">
						NEED HELP WITH YOUR VEHICLE?
					</p>

					<h2>
						Start with the symptoms.
					</h2>

					<p>
						Tell Rexroad what your vehicle is doing, and I can help determine the appropriate next step.
					</p>

				</div>

				<p>

					<a
						class="rr-button"
						href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>"
					>
						Request Service
					</a>

					<a
						class="rr-button"
						href="tel:+14694694521"
					>
						Call 469-469-4521
					</a>

				</p>

			</section>

		</div>

	</div>

</main>

<?php
get_footer();