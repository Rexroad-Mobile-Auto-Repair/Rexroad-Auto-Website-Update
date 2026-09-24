<?php
/**
 * "Problems we diagnose" links block.
 *
 * Reused on Vehicle Make Page and Vehicle Model Page templates. This
 * is deliberately NOT a claim that any specific make/model commonly
 * suffers from these issues — no such data source exists in this
 * theme (see inc/vehicle informer / schema-service.php: only a blog
 * and the service registry exist, nothing model-specific). It links
 * to real, existing diagnostic-relevant service pages under a
 * neutral "what we diagnose" framing, universal to any vehicle.
 *
 * Self-contained (defines its own label map) and cross-checks every
 * slug against the canonical service registry
 * (inc/schema-service.php) so a future rename there can't silently
 * produce a dead link here. Only renders once at least one linked
 * service actually exists in that registry.
 *
 * Required $args (WP 5.5+ get_template_part third argument):
 *   'heading' => e.g. "Common Issues We Diagnose on Ford Vehicles" or
 *                "Problems We Diagnose on Ford F-150 Vehicles". A
 *                heading claiming a specific "common problem" for a
 *                make/model must come from verified editorial
 *                content (the_content()), never from this partial.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

$rexroad_vehicle_problem_links = array(
	'advanced-diagnostics'          => 'Check Engine & Warning Lights',
	'auto-battery-replacement'      => 'Battery & Charging Issues',
	'alternator-and-starter-repair' => 'No-Start / Starting Problems',
	'brakes-service'                => 'Brake Noise & Performance',
	'cooling-system-service'        => 'Overheating & Cooling Issues',
	'auto-air-condition-service'    => 'A/C Not Cooling',
	'suspension-and-steering'       => 'Suspension & Steering Noise',
);

$rexroad_vehicle_known_service_slugs = function_exists( 'rexroad_custom_get_service_slugs' )
	? rexroad_custom_get_service_slugs()
	: array();

$rexroad_vehicle_problem_items = array();
foreach ( $rexroad_vehicle_problem_links as $rexroad_vehicle_problem_slug => $rexroad_vehicle_problem_label ) {
	if ( in_array( $rexroad_vehicle_problem_slug, $rexroad_vehicle_known_service_slugs, true ) ) {
		$rexroad_vehicle_problem_items[ $rexroad_vehicle_problem_slug ] = $rexroad_vehicle_problem_label;
	}
}

if ( empty( $rexroad_vehicle_problem_items ) ) {
	return;
}

$rexroad_vehicle_problems_heading = ( isset( $args['heading'] ) && '' !== $args['heading'] )
	? (string) $args['heading']
	: 'Common Issues We Diagnose';
?>
<section class="rr-content-section rr-vehicles-problems">
	<div class="rr-section-heading">
		<p class="rr-eyebrow">Diagnostics</p>
		<h2><?php echo esc_html( $rexroad_vehicle_problems_heading ); ?></h2>
		<p>These are general vehicle systems we diagnose and repair &mdash; not a claim that this vehicle is unusually prone to any of them.</p>
	</div>
	<ul class="rr-check-list">
		<?php foreach ( $rexroad_vehicle_problem_items as $rexroad_vehicle_problem_slug => $rexroad_vehicle_problem_label ) : ?>
			<li>
				<a href="<?php echo esc_url( home_url( '/services/' . $rexroad_vehicle_problem_slug . '/' ) ); ?>">
					<?php echo esc_html( $rexroad_vehicle_problem_label ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
