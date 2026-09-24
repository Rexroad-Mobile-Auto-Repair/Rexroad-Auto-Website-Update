<?php
/**
 * Homepage: Customer reviews.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

$rexroad_schedule_url = (string) get_theme_mod(
	'rexroad_schedule_url',
	home_url( '/contact-us/' )
);

$rexroad_google_reviews_url = function_exists( 'rexroad_ai_seo_google_reviews_url' )
	? rexroad_ai_seo_google_reviews_url()
	: '';
?>

<section class="rr-reviews" aria-labelledby="rr-reviews-title">

	<div class="rr-reviews__accent rr-reviews__accent--one" aria-hidden="true"></div>
	<div class="rr-reviews__accent rr-reviews__accent--two" aria-hidden="true"></div>

	<div class="rr-container">

		<div class="rr-reviews__header rr-reveal">

			<div class="rr-section-heading rr-reviews__heading">

				<p class="rr-section-heading__eyebrow">
					Customer Reviews
				</p>

				<h2 class="rr-section-heading__title" id="rr-reviews-title">
					Trusted by drivers who needed the job <span>done right.</span>
				</h2>

			</div>

			<div class="rr-reviews__summary">

				<div class="rr-reviews__stars" aria-label="Five star customer feedback">
					★★★★★
				</div>

				<p>
					Real feedback from customers who chose Rexroad for mobile diagnostics, repairs, and honest advice.
				</p>

			</div>

		</div>

		<div class="rr-google-reviews-live rr-reveal">

			<div class="rr-google-reviews-live__icon" aria-hidden="true">
				G
			</div>

			<div class="rr-google-reviews-live__content">

				<span class="rr-google-reviews-live__eyebrow">
					Google Reviews
				</span>

				<?php
				/**
				 * Cached Google Reviews summary supplied by Rexroad AI SEO.
				 *
				 * No live Google API request is made during normal page loads.
				 */
				do_action( 'rexroad_ai_seo_homepage_reviews' );
				?>

			</div>

		</div>

		<div class="rr-reviews__grid">

			<article class="rr-review-card rr-reveal">

				<div class="rr-review-card__top">

					<span class="rr-review-card__stars" aria-hidden="true">
						★★★★★
					</span>

					<span class="rr-review-card__source">
						Google Review
					</span>

				</div>

				<blockquote>
					<p>
						“Very talented mechanic. Figured out the problems, repaired them quickly, and provided great customer service.”
					</p>
				</blockquote>

				<footer>

					<span class="rr-review-card__avatar" aria-hidden="true">
						R
					</span>

					<div>
						<strong>Ron M.</strong>
						<span>Local customer</span>
					</div>

				</footer>

			</article>

			<article class="rr-review-card rr-review-card--featured rr-reveal">

				<div class="rr-review-card__quote-mark" aria-hidden="true">
					“
				</div>

				<div class="rr-review-card__top">

					<span class="rr-review-card__stars" aria-hidden="true">
						★★★★★
					</span>

					<span class="rr-review-card__source">
						Google Review
					</span>

				</div>

				<blockquote>
					<p>
						“Friendly and professional. My vehicle was up and running within 20 minutes.”
					</p>
				</blockquote>

				<footer>

					<span class="rr-review-card__avatar" aria-hidden="true">
						R
					</span>

					<div>
						<strong>Richard L.</strong>
						<span>Local customer</span>
					</div>

				</footer>

			</article>

			<article class="rr-review-card rr-reveal">

				<div class="rr-review-card__top">

					<span class="rr-review-card__stars" aria-hidden="true">
						★★★★★
					</span>

					<span class="rr-review-card__source">
						Customer Review
					</span>

				</div>

				<blockquote>
					<p>
						“They go above and beyond. Mr. Rexroad knows what he is doing. Definitely recommended.”
					</p>
				</blockquote>

				<footer>

					<span class="rr-review-card__avatar" aria-hidden="true">
						R
					</span>

					<div>
						<strong>Verified customer</strong>
						<span>Frisco area</span>
					</div>

				</footer>

			</article>

		</div>

		<div class="rr-reviews__cta rr-reveal">

			<div>

				<span class="rr-reviews__cta-kicker">
					Ready for the same level of service?
				</span>

				<h3>
					Tell me what your vehicle needs.
				</h3>

			</div>

			<div class="rr-reviews__actions">

				<a
					class="rr-button"
					href="<?php echo esc_url( $rexroad_schedule_url ); ?>"
				>
					Schedule Service
				</a>

				<?php if ( $rexroad_google_reviews_url ) : ?>

					<a
						class="rr-reviews__secondary-link"
						href="<?php echo esc_url( $rexroad_google_reviews_url ); ?>"
						target="_blank"
						rel="noopener noreferrer"
					>
						Read Google Reviews
						<span aria-hidden="true">↗</span>
					</a>

				<?php endif; ?>

			</div>

		</div>

	</div>

</section>