<?php
/**
 * Rexroad analytics events.
 *
 * Fires a GA4 generate_lead event only after Gravity Forms
 * Form #7 has successfully submitted.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add GA4 lead tracking to the successful confirmation for Form #7.
 *
 * @param string|array $confirmation Confirmation content.
 * @param array        $form         Gravity Forms form object.
 * @param array        $entry        Gravity Forms entry.
 * @param bool         $ajax         Whether AJAX is enabled.
 *
 * @return string|array
 */
function rexroad_track_service_request_lead( $confirmation, $form, $entry, $ajax ) {

	if ( ! is_string( $confirmation ) ) {
		return $confirmation;
	}

	$tracking = <<<'HTML'
<script>
window.dataLayer = window.dataLayer || [];
window.gtag = window.gtag || function(){dataLayer.push(arguments);};

gtag('event', 'generate_lead', {
	form_id: '7',
	form_name: 'rexroad_service_request'
});
</script>
HTML;

	return $confirmation . $tracking;
}

add_filter(
	'gform_confirmation_7',
	'rexroad_track_service_request_lead',
	10,
	4
);