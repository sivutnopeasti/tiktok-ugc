<?php
/**
 * Instagram profile embed renderer.
 *
 * @package InstagramProfileEmbed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds Instagram profile iframe embeds.
 */
class IPE_Embed_Renderer {

	/**
	 * Whether frontend assets should load on the current request.
	 *
	 * @var bool
	 */
	private static $enqueue_assets = false;

	/**
	 * Normalize and validate an Instagram username.
	 *
	 * @param string $username Raw username input.
	 * @return string|WP_Error
	 */
	public static function normalize_username( $username ) {
		$username = trim( (string) $username );
		$username = ltrim( $username, '@' );

		if ( $username === '' ) {
			return new WP_Error( 'ipe_empty_username', __( 'Instagram-käyttäjänimi puuttuu.', 'instagram-profile-embed' ) );
		}

		if ( ! preg_match( '/^[a-zA-Z0-9._]{1,30}$/', $username ) ) {
			return new WP_Error( 'ipe_invalid_username', __( 'Instagram-käyttäjänimi on virheellinen.', 'instagram-profile-embed' ) );
		}

		return $username;
	}

	/**
	 * Build Instagram profile embed URL.
	 *
	 * @param string $username Normalized username.
	 * @return string
	 */
	public static function get_embed_url( $username ) {
		return 'https://www.instagram.com/' . rawurlencode( $username ) . '/embed';
	}

	/**
	 * Register footer assets when an embed is rendered late (Elementor, shortcodes).
	 */
	public static function mark_assets_needed() {
		if ( self::$enqueue_assets ) {
			return;
		}

		self::$enqueue_assets = true;
		add_action( 'wp_footer', array( __CLASS__, 'print_frontend_assets' ), 5 );
	}

	/**
	 * Print frontend CSS and optional lazy-load script.
	 */
	public static function print_frontend_assets() {
		if ( ! self::$enqueue_assets ) {
			return;
		}

		$css_url = IPE_PLUGIN_URL . 'assets/css/frontend.css';
		echo '<link rel="stylesheet" id="ipe-frontend-css" href="' . esc_url( $css_url ) . '?ver=' . esc_attr( IPE_VERSION ) . '" media="all" />' . "\n";

		$settings = IPE_Settings::get_settings();

		if ( ! empty( $settings['lazy_load'] ) ) {
			$js_url = IPE_PLUGIN_URL . 'assets/js/lazy-load.js';
			echo '<script src="' . esc_url( $js_url ) . '?ver=' . esc_attr( IPE_VERSION ) . '"></script>' . "\n";
		}
	}

	/**
	 * Render an Instagram profile embed.
	 *
	 * @param string $username Raw username.
	 * @param array  $args     Optional render arguments.
	 * @return string
	 */
	public static function render_profile( $username, $args = array() ) {
		$settings = IPE_Settings::get_settings();
		$defaults = array(
			'height'      => (int) $settings['default_height'],
			'show_errors' => false,
			'lazy_load'   => ! empty( $settings['lazy_load'] ),
		);

		$args     = wp_parse_args( $args, $defaults );
		$username = self::normalize_username( $username );

		if ( is_wp_error( $username ) ) {
			return self::render_error( $username, (bool) $args['show_errors'] );
		}

		self::mark_assets_needed();

		$height    = max( 200, (int) $args['height'] );
		$embed_url = self::get_embed_url( $username );
		$lazy      = ! empty( $args['lazy_load'] );
		$classes   = 'ipe-profile-embed';

		if ( $lazy ) {
			$classes .= ' ipe-profile-embed--lazy';
		}

		$iframe_attrs = array(
			'class'           => 'ipe-profile-embed__iframe',
			'width'           => '100%',
			'height'          => (string) $height,
			'frameborder'     => '0',
			'scrolling'       => 'no',
			'allowtransparency' => 'true',
			'title'           => sprintf(
				/* translators: %s: Instagram username */
				__( 'Instagram-profiili @%s', 'instagram-profile-embed' ),
				$username
			),
			'style'           => '--ipe-embed-height: ' . $height . 'px;',
		);

		if ( $lazy ) {
			$iframe_attrs['data-ipe-src'] = $embed_url;
			$iframe_attrs['src']          = 'about:blank';
		} else {
			$iframe_attrs['src']    = $embed_url;
			$iframe_attrs['loading'] = 'lazy';
		}

		$attr_string = '';

		foreach ( $iframe_attrs as $key => $value ) {
			$attr_string .= sprintf( ' %s="%s"', esc_attr( $key ), esc_attr( $value ) );
		}

		return sprintf(
			'<div class="%1$s" data-ipe-username="%2$s" style="--ipe-embed-height:%3$dpx;"><div class="ipe-profile-embed__inner"><iframe%4$s></iframe></div></div>',
			esc_attr( $classes ),
			esc_attr( $username ),
			$height,
			$attr_string
		);
	}

	/**
	 * Render an error message.
	 *
	 * @param WP_Error $error       Error object.
	 * @param bool     $show_errors Whether to show the message publicly.
	 * @return string
	 */
	private static function render_error( WP_Error $error, $show_errors ) {
		if ( ! $show_errors && ! current_user_can( 'edit_posts' ) ) {
			return '';
		}

		return sprintf(
			'<div class="ipe-profile-embed ipe-profile-embed--error" role="alert"><p>%s</p></div>',
			esc_html( $error->get_error_message() )
		);
	}
}
