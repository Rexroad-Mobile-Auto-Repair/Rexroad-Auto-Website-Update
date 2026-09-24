<?php
/**
 * Homepage: How It Works section.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

$rexroad_phone        = (string) get_theme_mod( 'rexroad_header_phone', '469-469-4521' );
$rexroad_phone_href   = function_exists( 'rexroad_custom_phone_href' )
	? rexroad_custom_phone_href( $rexroad_phone )
	: preg_replace( '/[^0-9+]/', '', $rexroad_phone );
$rexroad_schedule_url = (string) get_theme_mod( 'rexroad_schedule_url', home_url( '/contact-us/' ) );
?>

<section class="rr-process" aria-labelledby="rr-process-title">

	<div class="rr-process__accent rr-process__accent--one" aria-hidden="true"></div>
	<div class="rr-process__accent rr-process__accent--two" aria-hidden="true"></div>

	<div class="rr-container rr-process__inner">

		<div class="rr-section-heading rr-process__heading">

			<p class="rr-section-heading__eyebrow">
				How Mobile Auto Repair Works
			</p>

			<h2 class="rr-section-heading__title" id="rr-process-title">
				Professional auto repair in <span>four simple steps.</span>
			</h2>

			<p class="rr-section-heading__description">
				From your first service request to the completed repair, I keep the process clear, convenient, and centered around your schedule.
			</p>

		</div>

		<ol class="rr-process__timeline" aria-label="Rexroad mobile auto repair process">

			<li class="rr-process-card rr-reveal">

				<div class="rr-process-card__top">

					<span class="rr-process-card__number" aria-hidden="true">
						01
					</span>

					<span class="rr-process-card__icon" aria-hidden="true">
						<svg viewBox="0 0 64 64" focusable="false">
							<rect x="12" y="14" width="40" height="38" rx="6"/>
							<path d="M21 10v10M43 10v10M12 25h40"/>
							<path d="m23 38 6 6 13-15"/>
						</svg>
					</span>

				</div>

				<h3>Schedule Service</h3>

				<p>
					Book online or call me and tell me what your vehicle is doing, where it is located, and when service works best.
				</p>

				<span class="rr-process-card__detail">
					Online or by phone
				</span>

			</li>

			<li class="rr-process-card rr-reveal">

				<div class="rr-process-card__top">

					<span class="rr-process-card__number" aria-hidden="true">
						02
					</span>

					<span class="rr-process-card__icon" aria-hidden="true">
						<svg viewBox="0 0 64 64" focusable="false">
							<path d="M8 38h48v12H8z"/>
							<path d="M14 26h25l8 12H14z"/>
							<path d="M47 30h7l4 8H47z"/>
							<circle cx="20" cy="50" r="6"/>
							<circle cx="48" cy="50" r="6"/>
							<path d="M19 18h18v8H19z"/>
						</svg>
					</span>

				</div>

				<h3>I Come to You</h3>

				<p>
					I arrive at your home, workplace, or other suitable location with professional tools and equipment for mobile auto repair.
				</p>

				<span class="rr-process-card__detail">
					Home or workplace
				</span>

			</li>

			<li class="rr-process-card rr-reveal">

				<div class="rr-process-card__top">

					<span class="rr-process-card__number" aria-hidden="true">
						03
					</span>

					<span class="rr-process-card__icon" aria-hidden="true">
						<svg viewBox="0 0 64 64" focusable="false">
							<rect x="13" y="9" width="32" height="40" rx="5"/>
							<rect x="19" y="15" width="20" height="14" rx="2"/>
							<path d="M22 24l5-5 4 4 6-7"/>
							<path d="M25 49v6M35 49v6"/>
							<path d="M42 38l9 9M47 33l5 5-10 10-5-5z"/>
						</svg>
					</span>

				</div>

				<h3>Diagnose &amp; Repair</h3>

				<p>
					I identify the root cause, explain the findings, provide clear recommendations, and complete approved repairs.
				</p>

				<span class="rr-process-card__detail">
					No unnecessary parts
				</span>

			</li>

			<li class="rr-process-card rr-reveal">

				<div class="rr-process-card__top">

					<span class="rr-process-card__number" aria-hidden="true">
						04
					</span>

					<span class="rr-process-card__icon" aria-hidden="true">
						<svg viewBox="0 0 64 64" focusable="false">
							<path d="M32 7 52 15v14c0 13-8 23-20 29C20 52 12 42 12 29V15z"/>
							<path d="m22 33 7 7 14-16"/>
						</svg>
					</span>

				</div>

				<h3>Drive with Confidence</h3>

				<p>
					With the work completed and clearly explained, you can get back on the road without the repair-shop hassle.
				</p>

				<span class="rr-process-card__detail">
					Service done right
				</span>

			</li>

		</ol>

		<div class="rr-process__cta rr-reveal">

			<div>

				<p class="rr-process__cta-kicker">
					Ready when you are
				</p>

				<h3>
					Tell me what your vehicle needs.
				</h3>

				<p>
					Schedule professional mobile auto repair in Frisco and surrounding North Texas communities.
				</p>

			</div>

			<div class="rr-process__actions">

				<a class="rr-button" href="<?php echo esc_url( $rexroad_schedule_url ); ?>">
					Schedule Service
				</a>

				<?php if ( '' !== $rexroad_phone_href ) : ?>

					<a
						class="rr-button rr-button--outline"
						href="tel:<?php echo esc_attr( $rexroad_phone_href ); ?>"
					>
						Call <?php echo esc_html( $rexroad_phone ); ?>
					</a>

				<?php endif; ?>

			</div>

		</div>

	</div>

</section>