<?php
/**
 * Homepage: Legacy business story and service philosophy.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

$rexroad_schedule_url = (string) get_theme_mod(
	'rexroad_schedule_url',
	home_url( '/contact-us/' )
);
?>

<section class="rr-story" aria-labelledby="rr-story-title">

	<div class="rr-story__accent" aria-hidden="true"></div>

	<div class="rr-container rr-story__layout">

		<div class="rr-story__brand-panel rr-reveal">

			<div class="rr-story__mark" aria-hidden="true">
				R
			</div>

			<p class="rr-story__panel-kicker">
				Rexroad Mobile Auto Repair
			</p>

			<h2 id="rr-story-title">
				Straightforward advice. <span>Work done right.</span>
			</h2>

			<p>
				Rexroad has helped drivers in Frisco and surrounding North Texas communities keep their cars and trucks running reliably since 2021.
			</p>

			<dl class="rr-story__facts">

				<div>
					<dt>Service model</dt>
					<dd>Professional mobile auto repair</dd>
				</div>

				<div>
					<dt>Vehicle coverage</dt>
					<dd>Domestic, Asian, and European</dd>
				</div>

				<div>
					<dt>Primary area</dt>
					<dd>Frisco and surrounding communities</dd>
				</div>

			</dl>

		</div>

		<div class="rr-story__content">

			<div class="rr-section-heading rr-story__heading rr-reveal">

				<p class="rr-section-heading__eyebrow">
					Built on Honest Service
				</p>

				<h2 class="rr-section-heading__title">
					A mobile mechanic who treats your vehicle like it <span>actually matters.</span>
				</h2>

				<p class="rr-section-heading__description">
					Rexroad combines professional diagnostics, clear communication, and service at your location so you can make informed decisions without the repair-shop runaround.
				</p>

			</div>

			<div class="rr-story__principles">

				<article class="rr-story-card rr-reveal">

					<span class="rr-story-card__number" aria-hidden="true">
						01
					</span>

					<div>

						<h3>
							Straightforward Advice
						</h3>

						<p>
							I explain what I find, answer your questions, and help you decide what your vehicle needs now and what can wait.
						</p>

					</div>

				</article>

				<article class="rr-story-card rr-reveal">

					<span class="rr-story-card__number" aria-hidden="true">
						02
					</span>

					<div>

						<h3>
							Personalized Maintenance
						</h3>

						<p>
							Your driving habits, vehicle needs, and expectations shape the maintenance plan instead of forcing every customer into the same schedule.
						</p>

					</div>

				</article>

				<article class="rr-story-card rr-reveal">

					<span class="rr-story-card__number" aria-hidden="true">
						03
					</span>

					<div>

						<h3>
							Long-Term Relationships
						</h3>

						<p>
							The goal is not a one-time repair. It is a trust-based relationship built through honest recommendations, quality work, and consistent communication.
						</p>

					</div>

				</article>

			</div>

			<div class="rr-story__footer rr-reveal">

				<p>
					Whether you need mobile diagnostics, repair, or maintenance near Frisco, Rexroad focuses on safety, reliability, and completing the job as efficiently as possible.
				</p>

				<div class="rr-story__actions">

					<a class="rr-button" href="<?php echo esc_url( $rexroad_schedule_url ); ?>">
						Schedule Service
					</a>

					<a class="rr-story__about-link" href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>">
						Meet Aaron <span aria-hidden="true">→</span>
					</a>

				</div>

			</div>

		</div>

	</div>

</section>