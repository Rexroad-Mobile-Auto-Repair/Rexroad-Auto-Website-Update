<?php
/**
 * Theme Customizer settings and controls.
 *
 * @package Rexroad_Custom
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'rexroad_custom_customize_register' ) ) {
	/**
	 * Register Customizer sections, settings, and controls.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
	 */
	function rexroad_custom_customize_register( WP_Customize_Manager $wp_customize ): void {

		/*
		 * ------------------------------------------------------------------
		 * Section: Homepage Settings
		 * ------------------------------------------------------------------
		 */
		$wp_customize->add_section(
			'rexroad_homepage_settings',
			array(
				'title'       => __( 'Rexroad Homepage', 'rexroad-custom' ),
				'description' => __( 'Edit the primary message, button, and service-truck image shown at the top of the homepage.', 'rexroad-custom' ),
				'priority'    => 30,
			)
		);

		$homepage_text_controls = array(
			'rexroad_home_eyebrow'       => array(
				'default' => 'Mobile Auto Repair in Frisco, Texas',
				'label'   => __( 'Location line', 'rexroad-custom' ),
			),
			'rexroad_home_title_primary' => array(
				'default' => 'Skip the Shop.',
				'label'   => __( 'Headline first line', 'rexroad-custom' ),
			),
			'rexroad_home_title_accent'  => array(
				'default' => 'We’ll Come to You.',
				'label'   => __( 'Headline red line', 'rexroad-custom' ),
			),
			'rexroad_home_button_label'  => array(
				'default' => 'Schedule Service',
				'label'   => __( 'Schedule button text', 'rexroad-custom' ),
			),
		);

		foreach ( $homepage_text_controls as $setting_id => $control_args ) {
			$wp_customize->add_setting(
				$setting_id,
				array(
					'default'           => $control_args['default'],
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'refresh',
				)
			);

			$wp_customize->add_control(
				$setting_id,
				array(
					'label'   => $control_args['label'],
					'section' => 'rexroad_homepage_settings',
					'type'    => 'text',
				)
			);
		}

		$wp_customize->add_setting(
			'rexroad_home_description',
			array(
				'default'           => 'Dealership-quality diagnostics and repairs performed at your home or workplace across Frisco and surrounding communities.',
				'sanitize_callback' => 'sanitize_textarea_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'rexroad_home_description',
			array(
				'label'   => __( 'Homepage description', 'rexroad-custom' ),
				'section' => 'rexroad_homepage_settings',
				'type'    => 'textarea',
			)
		);

		$wp_customize->add_setting(
			'rexroad_home_hero_image',
			array(
				'default'           => get_template_directory_uri() . '/assets/images/rexroad-service-truck.png',
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'rexroad_home_hero_image',
				array(
					'label'       => __( 'Service-truck image', 'rexroad-custom' ),
					'description' => __( 'Choose the main image displayed beside the homepage headline.', 'rexroad-custom' ),
					'section'     => 'rexroad_homepage_settings',
				)
			)
		);

		$wp_customize->add_setting(
			'rexroad_home_hero_image_alt',
			array(
				'default'           => 'Rexroad Mobile Auto Repair service truck in Frisco, Texas',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'rexroad_home_hero_image_alt',
			array(
				'label'       => __( 'Service-truck image description', 'rexroad-custom' ),
				'description' => __( 'Briefly describe the image for visitors using screen readers.', 'rexroad-custom' ),
				'section'     => 'rexroad_homepage_settings',
				'type'        => 'text',
			)
		);

		/*
		 * ------------------------------------------------------------------
		 * Section: Business Information
		 * ------------------------------------------------------------------
		 */
		$wp_customize->add_section(
			'rexroad_business_settings',
			array(
				'title'       => __( 'Rexroad Business Information', 'rexroad-custom' ),
				'description' => __( 'Business details displayed in the footer and available for local-business search data.', 'rexroad-custom' ),
				'priority'    => 32,
			)
		);

		$business_text_controls = array(
			'rexroad_business_street'      => array(
				'default' => '15922 Eldorado Parkway, Suite 500',
				'label'   => __( 'Street address', 'rexroad-custom' ),
			),
			'rexroad_business_city'        => array(
				'default' => 'Frisco',
				'label'   => __( 'City', 'rexroad-custom' ),
			),
			'rexroad_business_state'       => array(
				'default' => 'TX',
				'label'   => __( 'State', 'rexroad-custom' ),
			),
			'rexroad_business_postal_code' => array(
				'default' => '75035',
				'label'   => __( 'ZIP code', 'rexroad-custom' ),
			),
			'rexroad_business_price_range' => array(
				'default' => '$$',
				'label'   => __( 'Price range (e.g. $$)', 'rexroad-custom' ),
			),
		);

		foreach ( $business_text_controls as $setting_id => $control_args ) {
			$wp_customize->add_setting(
				$setting_id,
				array(
					'default'           => $control_args['default'],
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'refresh',
				)
			);

			$wp_customize->add_control(
				$setting_id,
				array(
					'label'   => $control_args['label'],
					'section' => 'rexroad_business_settings',
					'type'    => 'text',
				)
			);
		}

		$wp_customize->add_setting(
			'rexroad_business_latitude',
			array(
				'default'           => '33.1755082',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'rexroad_business_latitude',
			array(
				'label'       => __( 'Business latitude', 'rexroad-custom' ),
				'description' => __( 'Latitude used for LocalBusiness structured data.', 'rexroad-custom' ),
				'section'     => 'rexroad_business_settings',
				'type'        => 'text',
			)
		);

		$wp_customize->add_setting(
			'rexroad_business_longitude',
			array(
				'default'           => '-96.7356425',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'rexroad_business_longitude',
			array(
				'label'       => __( 'Business longitude', 'rexroad-custom' ),
				'description' => __( 'Longitude used for LocalBusiness structured data.', 'rexroad-custom' ),
				'section'     => 'rexroad_business_settings',
				'type'        => 'text',
			)
		);

		$wp_customize->add_setting(
			'rexroad_business_email',
			array(
				'default'           => 'aaron@rexroadauto.com',
				'sanitize_callback' => 'sanitize_email',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'rexroad_business_email',
			array(
				'label'   => __( 'Business email', 'rexroad-custom' ),
				'section' => 'rexroad_business_settings',
				'type'    => 'email',
			)
		);

		$wp_customize->add_setting(
			'rexroad_footer_service_areas',
			array(
				'default'           => "Frisco\nProsper\nLittle Elm\nThe Colony\nCelina\nMcKinney\nPlano\nAllen",
				'sanitize_callback' => 'sanitize_textarea_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'rexroad_footer_service_areas',
			array(
				'label'       => __( 'Footer service areas', 'rexroad-custom' ),
				'description' => __( 'Enter one city per line. Keep Frisco first as the primary market.', 'rexroad-custom' ),
				'section'     => 'rexroad_business_settings',
				'type'        => 'textarea',
			)
		);

		$wp_customize->add_setting(
			'rexroad_open_24_hours',
			array(
				'default'           => true,
				'sanitize_callback' => 'rexroad_custom_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'rexroad_open_24_hours',
			array(
				'label'       => __( 'Open 24 hours every day', 'rexroad-custom' ),
				'description' => __( 'Keep this checked while the business is open around the clock. It controls the structured business hours sent to search engines.', 'rexroad-custom' ),
				'section'     => 'rexroad_business_settings',
				'type'        => 'checkbox',
			)
		);

		/*
		 * ------------------------------------------------------------------
		 * Section: Header Settings
		 * ------------------------------------------------------------------
		 */
		$wp_customize->add_section(
			'rexroad_header_settings',
			array(
				'title'       => __( 'Rexroad Header', 'rexroad-custom' ),
				'description' => __( 'Contact information and calls to action displayed in the site header.', 'rexroad-custom' ),
				'priority'    => 35,
			)
		);

		$wp_customize->add_setting(
			'rexroad_header_phone',
			array(
				'default'           => '469-469-4521',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'rexroad_header_phone',
			array(
				'label'   => __( 'Header phone number', 'rexroad-custom' ),
				'section' => 'rexroad_header_settings',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'rexroad_business_hours',
			array(
				'default'           => 'Mon-Fri 8AM-6PM',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'rexroad_business_hours',
			array(
				'label'       => __( 'Business hours label', 'rexroad-custom' ),
				'description' => __( 'Displayed in the thin header information bar. Leave blank to hide it.', 'rexroad-custom' ),
				'section'     => 'rexroad_header_settings',
				'type'        => 'text',
			)
		);

		$wp_customize->add_setting(
			'rexroad_service_area',
			array(
				'default'           => 'Serving Frisco, McKinney, Prosper & Celina',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'rexroad_service_area',
			array(
				'label'       => __( 'Top-bar service area', 'rexroad-custom' ),
				'description' => __( 'Displayed in the thin information bar. Leave blank to hide it.', 'rexroad-custom' ),
				'section'     => 'rexroad_header_settings',
				'type'        => 'text',
			)
		);

		$wp_customize->add_setting(
			'rexroad_schedule_url',
			array(
				'default'           => home_url( '/contact-us/' ),
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'rexroad_schedule_url',
			array(
				'label'       => __( 'Schedule Service URL', 'rexroad-custom' ),
				'description' => __( 'Enter the full URL of the service request or appointment page.', 'rexroad-custom' ),
				'section'     => 'rexroad_header_settings',
				'type'        => 'url',
			)
		);

		/*
		 * ------------------------------------------------------------------
		 * Section: Social Profiles
		 * ------------------------------------------------------------------
		 */
		$wp_customize->add_section(
			'rexroad_social_settings',
			array(
				'title'       => __( 'Rexroad Social Profiles', 'rexroad-custom' ),
				'description' => __( 'Social profile URLs used in theme links and sameAs structured data.', 'rexroad-custom' ),
				'priority'    => 38,
			)
		);

		$social_controls = array(
			'rexroad_social_facebook'  => __( 'Facebook URL', 'rexroad-custom' ),
			'rexroad_social_youtube'   => __( 'YouTube URL', 'rexroad-custom' ),
			'rexroad_social_instagram' => __( 'Instagram URL', 'rexroad-custom' ),
		);

		foreach ( $social_controls as $setting_id => $label ) {
			$wp_customize->add_setting(
				$setting_id,
				array(
					'default'           => '',
					'sanitize_callback' => 'esc_url_raw',
					'transport'         => 'refresh',
				)
			);

			$wp_customize->add_control(
				$setting_id,
				array(
					'label'   => $label,
					'section' => 'rexroad_social_settings',
					'type'    => 'url',
				)
			);
		}
	}
}
add_action( 'customize_register', 'rexroad_custom_customize_register' );

if ( ! function_exists( 'rexroad_custom_sanitize_checkbox' ) ) {
	/**
	 * Sanitize a Customizer checkbox value.
	 *
	 * @param mixed $checked Submitted checkbox value.
	 * @return bool
	 */
	function rexroad_custom_sanitize_checkbox( $checked ): bool {
		return true === $checked || '1' === $checked || 1 === $checked;
	}
}

if ( ! function_exists( 'rexroad_custom_phone_href' ) ) {
	/**
	 * Return a telephone-safe number for a tel link.
	 *
	 * @param string $phone Raw phone string.
	 * @return string
	 */
	function rexroad_custom_phone_href( string $phone ): string {
		return preg_replace( '/[^0-9+]/', '', $phone ) ?: '';
	}
}