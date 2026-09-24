<?php
/**
 * Front page template.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

$rexroad_phone        = (string) get_theme_mod( 'rexroad_header_phone', '469-469-4521' );
$rexroad_phone_href   = rexroad_custom_phone_href( $rexroad_phone );
$rexroad_schedule_url = (string) get_theme_mod( 'rexroad_schedule_url', home_url( '/contact-us/' ) );

$rexroad_hero_eyebrow       = (string) get_theme_mod( 'rexroad_home_eyebrow', 'Mobile Auto Repair in Frisco, Texas' );
$rexroad_hero_title_primary = (string) get_theme_mod( 'rexroad_home_title_primary', 'Skip the Shop.' );
$rexroad_hero_title_accent  = (string) get_theme_mod( 'rexroad_home_title_accent', 'I’ll Come to You.' );
$rexroad_hero_description   = (string) get_theme_mod(
	'rexroad_home_description',
	'Dealership-quality diagnostics and repairs performed at your home or workplace across Frisco and surrounding communities.'
);
$rexroad_hero_button_label  = (string) get_theme_mod( 'rexroad_home_button_label', 'Schedule Service' );
$rexroad_hero_image         = (string) get_theme_mod(
	'rexroad_home_hero_image',
	get_template_directory_uri() . '/assets/images/rexroad-service-truck.png'
);
$rexroad_hero_image_alt     = (string) get_theme_mod(
	'rexroad_home_hero_image_alt',
	'Rexroad Mobile Auto Repair service truck in Frisco, Texas'
);

get_header();
?>

<main class="site-main site-main--front" id="primary">

	<section class="rr-hero" aria-labelledby="rr-hero-title">

		<div class="rr-hero__accent rr-hero__accent--one" aria-hidden="true"></div>
		<div class="rr-hero__accent rr-hero__accent--two" aria-hidden="true"></div>

		<div class="rr-container rr-hero__layout">

			<div class="rr-hero__content">

				<p class="rr-hero__eyebrow">
					<?php echo esc_html( $rexroad_hero_eyebrow ); ?>
				</p>

				<h1 class="rr-hero__title" id="rr-hero-title">
					<?php echo esc_html( $rexroad_hero_title_primary ); ?><br>
					<span><?php echo esc_html( $rexroad_hero_title_accent ); ?></span>
				</h1>

				<p class="rr-hero__description">
					<?php echo esc_html( $rexroad_hero_description ); ?>
				</p>

				<div class="rr-hero__actions">

					<a class="rr-button rr-hero__primary" href="<?php echo esc_url( $rexroad_schedule_url ); ?>">
						<?php echo esc_html( $rexroad_hero_button_label ); ?>
					</a>

					<?php if ( '' !== $rexroad_phone_href ) : ?>
						<a
							class="rr-button rr-button--outline rr-hero__phone"
							href="tel:<?php echo esc_attr( $rexroad_phone_href ); ?>"
						>
							<svg
								aria-hidden="true"
								viewBox="0 0 24 24"
								width="20"
								height="20"
								focusable="false"
							>
								<path
									fill="currentColor"
									d="M6.62 10.79a15.46 15.46 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.02-.24c1.12.37 2.33.57 3.57.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.61 21 3 13.39 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1.02l-2.2 2.2Z"
								/>
							</svg>

							Call <?php echo esc_html( $rexroad_phone ); ?>
						</a>
					<?php endif; ?>

				</div>

				<ul class="rr-hero__benefits" aria-label="Rexroad service benefits">
					<li><span aria-hidden="true">✓</span> Mobile Service</li>
					<li><span aria-hidden="true">✓</span> Honest Pricing</li>
					<li><span aria-hidden="true">✓</span> Advanced Diagnostics</li>
					<li><span aria-hidden="true">✓</span> European, Asian &amp; Domestic</li>
				</ul>

			</div>

			<div class="rr-hero__visual">

				<div class="rr-hero__image-frame">

					<img
						src="<?php echo esc_url( $rexroad_hero_image ); ?>"
						alt="<?php echo esc_attr( $rexroad_hero_image_alt ); ?>"
						width="1360"
						height="1020"
						fetchpriority="high"
					>

					<div class="rr-hero__image-overlay" aria-hidden="true"></div>

				</div>

				<div class="rr-hero__service-card">
					<span class="rr-hero__service-icon" aria-hidden="true">✓</span>

					<span>
						<strong>Professional mobile service</strong>
						<small>At your home or workplace</small>
					</span>
				</div>

			</div>

		</div>

		<div class="rr-hero__curve" aria-hidden="true"></div>

	</section>


	<section class="rr-why" aria-labelledby="rr-why-title">

		<div class="rr-container">

			<div class="rr-section-heading rr-why__heading">

				<p class="rr-section-heading__eyebrow">
					Why Choose Rexroad
				</p>

				<h2 class="rr-section-heading__title" id="rr-why-title">
					Professional service built around <span>your schedule.</span>
				</h2>

				<p class="rr-section-heading__description">
					I bring professional diagnostics and repair service directly to your home or workplace, without the traditional repair-shop hassle.
				</p>

			</div>

			<div class="rr-why__grid">

				<article class="rr-feature-card">

					<div class="rr-feature-card__icon" aria-hidden="true">
						<svg viewBox="0 0 64 64" focusable="false">
							<path d="M8 38h48v12H8z"/>
							<path d="M14 26h25l8 12H14z"/>
							<path d="M47 30h7l4 8H47z"/>
							<circle cx="20" cy="50" r="6"/>
							<circle cx="48" cy="50" r="6"/>
							<path d="M18 18h18v8H18z"/>
						</svg>
					</div>

					<p class="rr-feature-card__number">01</p>

					<h3>I come to you</h3>

					<p>
						Skip the tow truck and waiting room. I bring professional repairs directly to your home or workplace.
					</p>

				</article>

				<article class="rr-feature-card">

					<div class="rr-feature-card__icon" aria-hidden="true">
						<svg viewBox="0 0 64 64" focusable="false">
							<rect x="15" y="8" width="34" height="43" rx="5"/>
							<rect x="21" y="15" width="22" height="16" rx="2"/>
							<path d="M24 25l5-5 4 4 7-7"/>
							<circle cx="25" cy="40" r="2"/>
							<circle cx="32" cy="40" r="2"/>
							<circle cx="39" cy="40" r="2"/>
							<path d="M26 51v6M38 51v6"/>
						</svg>
					</div>

					<p class="rr-feature-card__number">02</p>

					<h3>Advanced Diagnostics</h3>

					<p>
						I test and diagnose the root cause before recommending repairs, helping prevent wasted time and unnecessary parts.
					</p>

				</article>

				<article class="rr-feature-card">

					<div class="rr-feature-card__icon" aria-hidden="true">
						<svg viewBox="0 0 64 64" focusable="false">
							<path d="M18 9h28v46H18z"/>
							<path d="M24 9V5h16v4"/>
							<path d="M25 22h14M25 30h14M25 38h8"/>
							<circle cx="43" cy="43" r="10"/>
							<path d="m38 43 3 3 6-7"/>
						</svg>
					</div>

					<p class="rr-feature-card__number">03</p>

					<h3>Honest Recommendations</h3>

					<p>
						Clear explanations, transparent estimates, and repairs based on what your vehicle actually needs.
					</p>

				</article>

				<article class="rr-feature-card">

					<div class="rr-feature-card__icon" aria-hidden="true">
						<svg viewBox="0 0 64 64" focusable="false">
							<path d="M32 6 52 14v15c0 13-8 23-20 29C20 52 12 42 12 29V14z"/>
							<path d="m23 33 6 6 13-15"/>
							<path d="M20 18h24"/>
						</svg>
					</div>

					<p class="rr-feature-card__number">04</p>

					<h3>Experienced Service</h3>

					<p>
						Hands-on knowledge across domestic, Asian, and European vehicles, backed by a commitment to doing the job right.
					</p>

				</article>

			</div>

			<div class="rr-why__promise">

				<div class="rr-why__promise-mark" aria-hidden="true">
					R
				</div>

				<div>
					<strong>
						Professional care without the repair-shop hassle.
					</strong>

					<span>
						One owner-operator, one clear point of contact, and service brought directly to you.
					</span>
				</div>

				<a href="<?php echo esc_url( $rexroad_schedule_url ); ?>">
					Schedule your service
					<span aria-hidden="true">→</span>
				</a>

			</div>

		</div>

	</section>


	<section class="rr-services" aria-labelledby="rr-services-title">

		<div class="rr-services__glow rr-services__glow--one" aria-hidden="true"></div>
		<div class="rr-services__glow rr-services__glow--two" aria-hidden="true"></div>

		<div class="rr-container">

			<div class="rr-services__intro">

				<div class="rr-section-heading rr-services__heading">

					<p class="rr-section-heading__eyebrow">
						Popular Mobile Services
					</p>

					<h2 class="rr-section-heading__title rr-services__title" id="rr-services-title">
						The right repair, brought <span>right to you.</span>
					</h2>

				</div>

				<div class="rr-services__intro-copy">

					<p>
						From warning lights and no-start problems to brakes and Texas heat, Rexroad brings professional diagnostics and repair equipment directly to your driveway.
					</p>

					<a class="rr-services__all-link" href="<?php echo esc_url( home_url( '/services/' ) ); ?>">
						Explore all services <span aria-hidden="true">→</span>
					</a>

				</div>

			</div>

			<div class="rr-services__grid">

				<article class="rr-service-card rr-service-card--featured">

					<a
						class="rr-service-card__link"
						href="<?php echo esc_url( home_url( '/services/advanced-diagnostics/' ) ); ?>"
						aria-label="Learn more about advanced diagnostics"
					>

						<div class="rr-service-card__topline">

							<span class="rr-service-card__icon" aria-hidden="true">
								<svg viewBox="0 0 64 64" focusable="false">
									<path d="M14 18h36v30H14z"/>
									<path d="M22 18v-6h20v6M21 29h6l4-7 6 14 4-7h5"/>
									<circle cx="24" cy="40" r="2"/>
									<circle cx="32" cy="40" r="2"/>
									<circle cx="40" cy="40" r="2"/>
								</svg>
							</span>

							<span class="rr-service-card__tag">
								Most Requested
							</span>

						</div>

						<div class="rr-service-card__content">

							<p class="rr-service-card__kicker">
								01 / Diagnostics
							</p>

							<h3>
								Advanced Diagnostics
							</h3>

							<p>
								Warning lights, drivability issues, and electrical faults traced to the root cause before parts are recommended.
							</p>

						</div>

						<span class="rr-service-card__action">
							View diagnostic service <span aria-hidden="true">↗</span>
						</span>

					</a>

				</article>

				<article class="rr-service-card">

					<a
						class="rr-service-card__link"
						href="<?php echo esc_url( home_url( '/services/auto-air-condition-service/' ) ); ?>"
						aria-label="Learn more about auto air conditioning repair"
					>

						<div class="rr-service-card__topline">

							<span class="rr-service-card__icon" aria-hidden="true">
								<svg viewBox="0 0 64 64" focusable="false">
									<circle cx="32" cy="32" r="9"/>
									<path d="M32 7v16M32 41v16M7 32h16M41 32h16M14 14l11 11M39 39l11 11M50 14 39 25M25 39 14 50"/>
								</svg>
							</span>

							<span class="rr-service-card__number">
								02
							</span>

						</div>

						<div class="rr-service-card__content">

							<p class="rr-service-card__kicker">
								Climate Control
							</p>

							<h3>
								A/C Repair
							</h3>

							<p>
								Leak detection, recharge, component testing, and repairs to get cold air moving again.
							</p>

						</div>

						<span class="rr-service-card__action">
							View A/C service <span aria-hidden="true">↗</span>
						</span>

					</a>

				</article>

				<article class="rr-service-card">

					<a
						class="rr-service-card__link"
						href="<?php echo esc_url( home_url( '/services/brakes-service/' ) ); ?>"
						aria-label="Learn more about mobile brake service"
					>

						<div class="rr-service-card__topline">

							<span class="rr-service-card__icon" aria-hidden="true">
								<svg viewBox="0 0 64 64" focusable="false">
									<circle cx="32" cy="32" r="22"/>
									<circle cx="32" cy="32" r="9"/>
									<path d="M17 20h12v24H17c-4-7-4-17 0-24Z"/>
									<path d="M45 18l6-6M47 46l6 6"/>
								</svg>
							</span>

							<span class="rr-service-card__number">
								03
							</span>

						</div>

						<div class="rr-service-card__content">

							<p class="rr-service-card__kicker">
								Stopping Power
							</p>

							<h3>
								Brake Service
							</h3>

							<p>
								Brake pads, rotors, fluid service, inspections, and repairs performed safely at your location.
							</p>

						</div>

						<span class="rr-service-card__action">
							View brake service <span aria-hidden="true">↗</span>
						</span>

					</a>

				</article>

				<article class="rr-service-card">

					<a
						class="rr-service-card__link"
						href="<?php echo esc_url( home_url( '/services/auto-battery-replacement/' ) ); ?>"
						aria-label="Learn more about battery and electrical service"
					>

						<div class="rr-service-card__topline">

							<span class="rr-service-card__icon" aria-hidden="true">
								<svg viewBox="0 0 64 64" focusable="false">
									<rect x="10" y="18" width="44" height="32" rx="4"/>
									<path d="M20 18v-6h8v6M38 18v-6h8v6M20 34h10M25 29v10M39 34h8"/>
								</svg>
							</span>

							<span class="rr-service-card__number">
								04
							</span>

						</div>

						<div class="rr-service-card__content">

							<p class="rr-service-card__kicker">
								Starting &amp; Charging
							</p>

							<h3>
								Battery &amp; Electrical
							</h3>

							<p>
								No-start testing, battery replacement, charging-system checks, and electrical troubleshooting.
							</p>

						</div>

						<span class="rr-service-card__action">
							View electrical service <span aria-hidden="true">↗</span>
						</span>

					</a>

				</article>

				<article class="rr-service-card">

					<a
						class="rr-service-card__link"
						href="<?php echo esc_url( home_url( '/services/cooling-system-service/' ) ); ?>"
						aria-label="Learn more about cooling system service"
					>

						<div class="rr-service-card__topline">

							<span class="rr-service-card__icon" aria-hidden="true">
								<svg viewBox="0 0 64 64" focusable="false">
									<path d="M25 9h14v30a14 14 0 1 1-14 0Z"/>
									<path d="M32 18v27"/>
									<path d="M39 24h8M39 32h8"/>
								</svg>
							</span>

							<span class="rr-service-card__number">
								05
							</span>

						</div>

						<div class="rr-service-card__content">

							<p class="rr-service-card__kicker">
								Engine Protection
							</p>

							<h3>
								Cooling Systems
							</h3>

							<p>
								Overheating diagnosis, leak testing, coolant service, water pumps, thermostats, and hoses.
							</p>

						</div>

						<span class="rr-service-card__action">
							View cooling service <span aria-hidden="true">↗</span>
						</span>

					</a>

				</article>

				<article class="rr-service-card rr-service-card--cta">

					<a
						class="rr-service-card__link"
						href="<?php echo esc_url( home_url( '/services/' ) ); ?>"
						aria-label="View all mobile auto repair services"
					>

						<div class="rr-service-card__cta-mark" aria-hidden="true">
							R
						</div>

						<div class="rr-service-card__content">

							<p class="rr-service-card__kicker">
								More Than a Short List
							</p>

							<h3>
								Need Something Else?
							</h3>

							<p>
								Explore the complete service lineup or tell me what your vehicle is doing. I will help you find the right next step.
							</p>

						</div>

						<span class="rr-service-card__action">
							See all services <span aria-hidden="true">→</span>
						</span>

					</a>

				</article>

			</div>

		</div>

	</section>


	<?php get_template_part( 'template-parts/home/how-it-works' ); ?>

	<?php get_template_part( 'template-parts/home/legacy-story' ); ?>

	<?php get_template_part( 'template-parts/home/reviews' ); ?>

</main>

<?php
get_footer();