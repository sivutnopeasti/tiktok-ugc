<?php
/**
 * Transient cache for TikTok oEmbed responses.
 *
 * @package TikTokProfileEmbed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cache helper for embed HTML.
 */
class TPE_Cache {

	const TRANSIENT_PREFIX = 'tpe_embed_';
	const STALE_SUFFIX     = '_stale';

	/**
	 * Build cache key for a username.
	 *
	 * @param string $username Normalized username.
	 * @return string
	 */
	public static function get_key( $username ) {
		return self::TRANSIENT_PREFIX . md5( strtolower( $username ) );
	}

	/**
	 * Get cached embed HTML.
	 *
	 * @param string $username Normalized username.
	 * @return string|null
	 */
	public static function get( $username ) {
		$cached = get_transient( self::get_key( $username ) );

		return is_string( $cached ) && $cached !== '' ? $cached : null;
	}

	/**
	 * Get stale cache used as fallback when TikTok is unavailable.
	 *
	 * @param string $username Normalized username.
	 * @return string|null
	 */
	public static function get_stale( $username ) {
		$cached = get_transient( self::get_key( $username ) . self::STALE_SUFFIX );

		return is_string( $cached ) && $cached !== '' ? $cached : null;
	}

	/**
	 * Store embed HTML in cache.
	 *
	 * @param string $username Normalized username.
	 * @param string $html     Sanitized embed HTML.
	 * @param int    $hours    Cache lifetime in hours.
	 */
	public static function set( $username, $html, $hours ) {
		$key  = self::get_key( $username );
		$ttl  = max( 1, (int) $hours ) * HOUR_IN_SECONDS;

		set_transient( $key, $html, $ttl );
		set_transient( $key . self::STALE_SUFFIX, $html, 30 * DAY_IN_SECONDS );
	}

	/**
	 * Delete all plugin transients.
	 */
	public static function flush_all() {
		global $wpdb;

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
				'_transient_' . self::TRANSIENT_PREFIX . '%',
				'_transient_timeout_' . self::TRANSIENT_PREFIX . '%'
			)
		);
	}
}
