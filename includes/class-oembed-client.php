<?php
/**
 * TikTok oEmbed client and embed renderer.
 *
 * @package TikTokProfileEmbed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fetches and renders TikTok profile embeds.
 */
class TPE_OEmbed_Client {

	const OEMBED_ENDPOINT = 'https://www.tiktok.com/oembed';

	/**
	 * Whether frontend assets should load on the current request.
	 *
	 * @var bool
	 */
	private static $enqueue_assets = false;

	/**
	 * Normalize and validate a TikTok username.
	 *
	 * @param string $username Raw username input.
	 * @return string|WP_Error
	 */
	public static function normalize_username( $username ) {
		$username = trim( (string) $username );
		$username = ltrim( $username, '@' );

		if ( $username === '' ) {
			return new WP_Error( 'tpe_empty_username', __( 'TikTok-käyttäjänimi puuttuu.', 'tiktok-profile-embed' ) );
		}

		if ( ! preg_match( '/^[a-zA-Z0-9._]{1,24}$/', $username ) ) {
			return new WP_Error( 'tpe_invalid_username', __( 'TikTok-käyttäjänimi on virheellinen.', 'tiktok-profile-embed' ) );
		}

		return $username;
	}

	/**
	 * Build public TikTok profile URL.
	 *
	 * @param string $username Normalized username.
	 * @return string
	 */
	public static function get_profile_url( $username ) {
		return 'https://www.tiktok.com/@' . rawurlencode( $username );
	}

	/**
	 * Mark that embed assets are needed on this page.
	 */
	public static function mark_assets_needed() {
		self::$enqueue_assets = true;
	}

	/**
	 * Check if embed assets should be enqueued.
	 *
	 * @return bool
	 */
	public static function should_enqueue_assets() {
		return self::$enqueue_assets;
	}

	/**
	 * Fetch embed HTML from TikTok oEmbed API with caching.
	 *
	 * @param string $username Normalized username.
	 * @return string|WP_Error
	 */
	public static function fetch_embed_html( $username ) {
		$cached = TPE_Cache::get( $username );
		if ( null !== $cached ) {
			return $cached;
		}

		$settings = TPE_Settings::get_settings();
		$url      = add_query_arg(
			'url',
			self::get_profile_url( $username ),
			self::OEMBED_ENDPOINT
		);

		$response = wp_remote_get(
			$url,
			array(
				'timeout' => 15,
				'headers' => array(
					'Accept' => 'application/json',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return self::fallback_or_error( $username, $response );
		}

		$status_code = (int) wp_remote_retrieve_response_code( $response );
		$body        = wp_remote_retrieve_body( $response );
		$data        = json_decode( $body, true );

		if ( $status_code < 200 || $status_code >= 300 || ! is_array( $data ) || empty( $data['html'] ) ) {
			$error = new WP_Error(
				'tpe_oembed_failed',
				__( 'TikTok-profiilia ei voitu ladata.', 'tiktok-profile-embed' )
			);

			return self::fallback_or_error( $username, $error );
		}

		$html = self::sanitize_embed_html( $data['html'] );
		TPE_Cache::set( $username, $html, (int) $settings['cache_hours'] );

		return $html;
	}

	/**
	 * Return stale cache if available, otherwise propagate the error.
	 *
	 * @param string         $username Normalized username.
	 * @param string|WP_Error $error   Original error.
	 * @return string|WP_Error
	 */
	private static function fallback_or_error( $username, $error ) {
		$stale = TPE_Cache::get_stale( $username );

		if ( null !== $stale ) {
			return $stale;
		}

		return $error;
	}

	/**
	 * Sanitize oEmbed HTML and strip script tags (loaded separately).
	 *
	 * @param string $html Raw oEmbed HTML.
	 * @return string
	 */
	public static function sanitize_embed_html( $html ) {
		$html = preg_replace( '#<script\b[^>]*>.*?</script>#is', '', $html );

		$allowed = array(
			'blockquote' => array(
				'class'           => true,
				'cite'            => true,
				'data-unique-id'  => true,
				'data-embed-from' => true,
				'style'           => true,
			),
			'section'    => array(
				'style' => true,
			),
			'iframe'     => array(
				'src'             => true,
				'width'           => true,
				'height'          => true,
				'frameborder'     => true,
				'allow'           => true,
				'allowfullscreen' => true,
				'scrolling'       => true,
				'title'           => true,
				'style'           => true,
				'name'            => true,
			),
			'a'          => array(
				'href'   => true,
				'target' => true,
				'rel'    => true,
				'title'  => true,
			),
		);

		return wp_kses( $html, $allowed );
	}

	/**
	 * Render a TikTok profile embed.
	 *
	 * @param string $username Raw username.
	 * @param array  $args     Optional render arguments.
	 * @return string
	 */
	public static function render_profile( $username, $args = array() ) {
		$defaults = array(
			'height'      => TPE_Settings::get_settings()['default_height'],
			'show_errors' => false,
			'lazy_load'   => (bool) TPE_Settings::get_settings()['lazy_load'],
		);

		$args     = wp_parse_args( $args, $defaults );
		$username = self::normalize_username( $username );

		if ( is_wp_error( $username ) ) {
			return self::render_error( $username, (bool) $args['show_errors'] );
		}

		$html = self::fetch_embed_html( $username );

		if ( is_wp_error( $html ) ) {
			return self::render_error( $html, (bool) $args['show_errors'] );
		}

		self::mark_assets_needed();

		$height = max( 200, (int) $args['height'] );
		$lazy   = ! empty( $args['lazy_load'] );

		$wrapper_attrs = array(
			'class'             => 'tpe-profile-embed',
			'data-tpe-username' => esc_attr( $username ),
			'style'             => '--tpe-embed-height: ' . $height . 'px;',
		);

		if ( $lazy ) {
			$wrapper_attrs['class'] .= ' tpe-profile-embed--lazy';
		}

		$attr_string = '';

		foreach ( $wrapper_attrs as $key => $value ) {
			$attr_string .= sprintf( ' %s="%s"', esc_attr( $key ), esc_attr( $value ) );
		}

		return sprintf(
			'<div%s><div class="tpe-profile-embed__inner">%s</div></div>',
			$attr_string,
			$html
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
			'<div class="tpe-profile-embed tpe-profile-embed--error" role="alert"><p>%s</p></div>',
			esc_html( $error->get_error_message() )
		);
	}
}
