<?php
/**
 * Shortcode handler.
 *
 * @package InstagramProfileEmbed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers [instagram_profile] shortcode.
 */
class IPE_Shortcode {

	/**
	 * Register shortcode.
	 */
	public static function register() {
		add_shortcode( 'instagram_profile', array( __CLASS__, 'render' ) );
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
			'instagram_profile'
		);

		$settings = IPE_Settings::get_settings();
		$height   = $atts['height'] !== '' ? (int) $atts['height'] : (int) $settings['default_height'];

		return IPE_Embed_Renderer::render_profile(
			$atts['username'],
			array(
				'height'      => $height,
				'show_errors' => true,
				'lazy_load'   => ! empty( $settings['lazy_load'] ),
			)
		);
	}
}
