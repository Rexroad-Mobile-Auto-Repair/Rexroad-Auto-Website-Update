<?php
/**
 * Core Structured Data Helper Functions.
 *
 * Provides central business and website entity nodes without top-level @context wrappers.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns canonical WebSite entity array for graph inclusion.
 *
 * @return array
 */
function rexroad_custom_get_website_schema(): array {
	$home_url = home_url( '/' );

	return array(
		'@type'     => 'WebSite',
		'@id'       => $home_url . '#website',
		'url'       => $home_url,
		'name'      => wp_strip_all_tags( get_bloginfo( 'name' ) ),
		'publisher' => array(
			'@id' => $home_url . '#auto-repair',
		),
	);
}

/**
 * Returns base AutoRepair entity array for graph inclusion.
 *
 * @return array
 */
function rexroad_custom_get_base_business_schema(): array {
	$phone        = (string) get_theme_mod( 'rexroad_header_phone', '469-469-4521' );
	$phone_href   = function_exists( 'rexroad_custom_phone_href' ) ? rexroad_custom_phone_href( $phone ) : $phone;
	$phone_digits = preg_replace( '/\D+/', '', $phone_href ) ?: '';

	if ( 10 === strlen( $phone_digits ) ) {
		$phone_schema = '+1' . $phone_digits;
	} elseif ( '' !== $phone_digits ) {
		$phone_schema = '+' . $phone_digits;
	} else {
		$phone_schema = '';
	}

	$street      = (string) get_theme_mod( 'rexroad_business_street', '15922 Eldorado Parkway, Suite 500' );
	$city        = (string) get_theme_mod( 'rexroad_business_city', 'Frisco' );
	$state       = (string) get_theme_mod( 'rexroad_business_state', 'TX' );
	$postal_code = (string) get_theme_mod( 'rexroad_business_postal_code', '75035' );
	$email       = trim( (string) get_theme_mod( 'rexroad_business_email', 'aaron@rexroadauto.com' ) );
	$areas_raw   = (string) get_theme_mod(
		'rexroad_footer_service_areas',
		"Frisco\nProsper\nLittle Elm\nThe Colony\nCelina\nMcKinney\nPlano\nAllen"
	);

	$areas = array_values(
		array_filter(
			array_map(
				'trim',
				preg_split( '/[\r\n,]+/', $areas_raw ) ?: array()
			)
		)
	);

	$home_url = home_url( '/' );

	/*
	 * Core Business Entity Node.
	 * Top-level graph output owns @context.
	 */
	$data = array(
		'@type'       => 'AutoRepair',
		'@id'         => $home_url . '#auto-repair',
		'name'        => get_bloginfo( 'name' ),
		'description' => get_bloginfo( 'description' ),
		'url'         => $home_url,
		'address'     => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => $street,
			'addressLocality' => $city,
			'addressRegion'   => $state,
			'postalCode'      => $postal_code,
			'addressCountry'  => 'US',
		),
		'areaServed'  => array_map(
			static function ( string $area ): array {
				return array(
					'@type' => 'City',
					'name'  => $area . ', Texas',
				);
			},
			$areas
		),
	);

	/*
	 * Telephone.
	 */
	if ( '' !== $phone_schema ) {
		$data['telephone'] = $phone_schema;
	}

	/*
	 * Email.
	 */
	if ( '' !== $email ) {
		$data['email'] = $email;
	}

	/*
	 * Price range.
	 */
	$price_range = trim(
		(string) get_theme_mod(
			'rexroad_business_price_range',
			'$$'
		)
	);

	if ( '' !== $price_range ) {
		$data['priceRange'] = $price_range;
	}

	/*
	 * Business identity profiles.
	 *
	 * Google Maps identifies the Google Business Profile location.
	 * Social profile defaults were recovered from the historical
	 * Yoast wpseo_social configuration.
	 */
	$google_maps = 'https://www.google.com/maps?cid=10830740925244583162';

	$facebook = trim(
	rawurldecode(
		(string) get_theme_mod(
			'rexroad_social_facebook',
			'https://www.facebook.com/RexroadAuto'
		)
	)
);

	$instagram = trim(
		(string) get_theme_mod(
			'rexroad_social_instagram',
			'https://www.instagram.com/rexroadmobileautorepair'
		)
	);

	$youtube = trim(
		(string) get_theme_mod(
			'rexroad_social_youtube',
			'https://www.youtube.com/@rexroadauto'
		)
	);

	$linkedin = trim(
		(string) get_theme_mod(
			'rexroad_social_linkedin',
			'https://www.linkedin.com/company/rexroad-auto/'
		)
	);

	$pinterest = trim(
		(string) get_theme_mod(
			'rexroad_social_pinterest',
			'https://www.pinterest.com/rexroadmobileautorepair/'
		)
	);

	$same_as = array_values(
		array_unique(
			array_filter(
				array_map(
					'esc_url',
					array(
						$google_maps,
						$facebook,
						$instagram,
						$youtube,
						$linkedin,
						$pinterest,
					)
				)
			)
		)
	);

	if ( ! empty( $same_as ) ) {
		$data['sameAs'] = $same_as;
	}

	/*
	 * Geographic coordinates.
	 */
	$latitude = preg_replace(
		'/[^\d.-]/',
		'',
		(string) get_theme_mod(
			'rexroad_business_latitude',
			'33.1755082'
		)
	);

	$longitude = preg_replace(
		'/[^\d.-]/',
		'',
		(string) get_theme_mod(
			'rexroad_business_longitude',
			'-96.7356425'
		)
	);

	if ( is_numeric( $latitude ) && is_numeric( $longitude ) ) {
		$data['geo'] = array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => (float) $latitude,
			'longitude' => (float) $longitude,
		);
	}

	/*
	 * Primary business image.
	 */
	$hero_image = (string) get_theme_mod(
		'rexroad_home_hero_image',
		get_template_directory_uri() . '/assets/images/rexroad-service-truck.png'
	);

	if ( '' !== $hero_image ) {
		$data['image'] = $hero_image;
	}

	/*
	 * Business logo.
	 */
	$custom_logo_id = (int) get_theme_mod( 'custom_logo', 0 );
	$logo_url       = $custom_logo_id > 0
		? wp_get_attachment_image_url( $custom_logo_id, 'full' )
		: false;

	if ( false !== $logo_url && '' !== $logo_url ) {
		$data['logo'] = $logo_url;
	}

	/*
	 * Business hours.
	 */
	$is_24_hours = (bool) get_theme_mod(
		'rexroad_open_24_hours',
		true
	);

	if ( $is_24_hours ) {
		$data['openingHoursSpecification'] = array(
			'@type'     => 'OpeningHoursSpecification',
			'dayOfWeek' => array(
				'https://schema.org/Monday',
				'https://schema.org/Tuesday',
				'https://schema.org/Wednesday',
				'https://schema.org/Thursday',
				'https://schema.org/Friday',
				'https://schema.org/Saturday',
				'https://schema.org/Sunday',
			),
			'opens'     => '00:00',
			'closes'    => '23:59',
		);
	} else {
		$open_time = trim(
			(string) get_theme_mod(
				'rexroad_business_opens',
				'08:00'
			)
		);

		$close_time = trim(
			(string) get_theme_mod(
				'rexroad_business_closes',
				'18:00'
			)
		);

		if ( '' !== $open_time && '' !== $close_time ) {
			$data['openingHoursSpecification'] = array(
				'@type'     => 'OpeningHoursSpecification',
				'dayOfWeek' => array(
					'https://schema.org/Monday',
					'https://schema.org/Tuesday',
					'https://schema.org/Wednesday',
					'https://schema.org/Thursday',
					'https://schema.org/Friday',
				),
				'opens'     => $open_time,
				'closes'    => $close_time,
			);
		}
	}

	return apply_filters(
		'rexroad_custom_business_schema_data',
		$data
	);
}