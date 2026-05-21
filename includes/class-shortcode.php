<?php
/**
 * Shortcode handler.
 *
 * @package TikTokProfileEmbed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers [tiktok_profile] shortcode.
 */
class TPE_Shortcode {

	/**
	 * Register shortcode.
	 */
	public static function register() {
		add_shortcode( 'tiktok_profile', array( __CLASS__, 'render' ) );
	}

	/**
	 * Render shortcode output.
	 *
	 * @param array<string, string>|string $atts Shortcode attributes.
	 * @return string
	 */
	public static function render( $atts ) {
		$atts = shortcode_atts(
			array(
				'username' => '',
				'height'   => '',
			),
			$atts,
			'tiktok_profile'
		);

		$settings = TPE_Settings::get_settings();
		$height   = $atts['height'] !== '' ? (int) $atts['height'] : (int) $settings['default_height'];

		return TPE_OEmbed_Client::render_profile(
			$atts['username'],
			array(
				'height'      => $height,
				'show_errors' => true,
				'lazy_load'   => ! empty( $settings['lazy_load'] ),
			)
		);
	}
}
