<?php
/**
 * Uninstall handler for TikTok Profile Embed.
 *
 * @package TikTokProfileEmbed
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-cache.php';

delete_option( 'tpe_settings' );
TPE_Cache::flush_all();
