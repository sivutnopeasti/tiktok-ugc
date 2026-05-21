<?php
/**
 * Gutenberg block registration.
 *
 * @package InstagramProfileEmbed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the Instagram Profile Embed block.
 */
class IPE_Block {

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
			'ipe-block-editor',
			IPE_PLUGIN_URL . 'assets/js/block-editor.js',
			array(
				'wp-blocks',
				'wp-element',
				'wp-block-editor',
				'wp-components',
				'wp-i18n',
				'wp-server-side-render',
			),
			IPE_VERSION,
			true
		);

		register_block_type(
			IPE_PLUGIN_DIR . 'blocks/instagram-profile',
			array(
				'render_callback' => array( __CLASS__, 'render' ),
				'editor_script'   => 'ipe-block-editor',
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
		$settings = IPE_Settings::get_settings();
		$username = isset( $attributes['username'] ) ? (string) $attributes['username'] : '';
		$height   = isset( $attributes['height'] ) && $attributes['height'] !== ''
			? (int) $attributes['height']
			: (int) $settings['default_height'];

		if ( $username === '' ) {
			return '';
		}

		return IPE_Embed_Renderer::render_profile(
			$username,
			array(
				'height'      => $height,
				'show_errors' => is_admin() || current_user_can( 'edit_posts' ),
				'lazy_load'   => ! empty( $settings['lazy_load'] ),
			)
		);
	}
}
