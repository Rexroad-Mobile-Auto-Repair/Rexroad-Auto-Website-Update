<?php
/**
 * Compact "related services" links block.
 *
 * Reused on /vehicles/ and Vehicle Make Page templates. Self-contained
 * (defines its own label map) and cross-checks every slug against the
 * canonical service registry (inc/schema-service.php) so a future
 * rename there can't silently produce a dead link here.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

$rexroad_vehicle_service_links = array(
	'advanced-diagnostics'          => 'Diagnostics',
	'brakes-service'                => 'Brakes',
	'auto-battery-replacement'      => 'Battery & Electrical',
	'alternator-and-starter-repair' => 'Starter & Alternator',
	'cooling-system-service'        => 'Cooling System',
	'auto-air-condition-service'    => 'A/C Service',
	'suspension-and-steering'       => 'Suspension & Steering',
	'fuel-pump-replacement'         => 'Fuel System',
);

$rexroad_vehicle_known_service_slugs = function_exists( 'rexroad_custom_get_service_slugs' )
	? rexroad_custom_get_service_slugs()
	: array();
?>
<section class="rr-content-section rr-vehicles-services">
	<div class="rr-section-heading">
		<p class="rr-eyebrow">Related Services</p>
		<h2>Common Services for These Vehicles</h2>
	</div>
	<ul class="rr-vehicle-service-links">
		<?php foreach ( $rexroad_vehicle_service_links as $rexroad_vehicle_service_slug => $rexroad_vehicle_service_label ) : ?>
			<?php if ( ! in_array( $rexroad_vehicle_service_slug, $rexroad_vehicle_known_service_slugs, true ) ) : ?>
				<?php continue; ?>
			<?php endif; ?>
			<li>
				<a href="<?php echo esc_url( home_url( '/services/' . $rexroad_vehicle_service_slug . '/' ) ); ?>">
					<?php echo esc_html( $rexroad_vehicle_service_label ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
