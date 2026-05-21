<?php
/**
 * Gutenberg block registration.
 *
 * @package TikTokProfileEmbed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the TikTok Profile Embed block.
 */
class TPE_Block {

	/**
	 * Register block and editor assets.
	 */
	public static function register() {
		add_action( 'init', array( __CLASS__, 'register_block' ) );
	}

	/**
	 * Register dynamic block type.
	 */
	public static function register_block() {
		wp_register_script(
			'tpe-block-editor',
			TPE_PLUGIN_URL . 'assets/js/block-editor.js',
			array(
				'wp-blocks',
				'wp-element',
				'wp-block-editor',
				'wp-components',
				'wp-i18n',
				'wp-server-side-render',
			),
			TPE_VERSION,
			true
		);

		register_block_type(
			TPE_PLUGIN_DIR . 'blocks/tiktok-profile',
			array(
				'render_callback' => array( __CLASS__, 'render' ),
				'editor_script'   => 'tpe-block-editor',
			)
		);
	}

	/**
	 * Server-side block render callback.
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 * @return string
	 */
	public static function render( $attributes ) {
		$settings = TPE_Settings::get_settings();
		$username = isset( $attributes['username'] ) ? (string) $attributes['username'] : '';
		$height   = isset( $attributes['height'] ) && $attributes['height'] !== ''
			? (int) $attributes['height']
			: (int) $settings['default_height'];

		if ( $username === '' ) {
			return '';
		}

		return TPE_OEmbed_Client::render_profile(
			$username,
			array(
				'height'      => $height,
				'show_errors' => is_admin() || current_user_can( 'edit_posts' ),
				'lazy_load'   => ! empty( $settings['lazy_load'] ),
			)
		);
	}
}
