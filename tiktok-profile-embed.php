<?php
/**
 * Plugin Name:       TikTok Profile Embed
 * Plugin URI:        https://ugcsuomi.fi
 * Description:       Upottaa TikTok-käyttäjän profiilin ja videot shortcodella tai Gutenberg-lohkolla.
 * Version:           1.0.1
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            UGC Suomi
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tiktok-profile-embed
 *
 * @package TikTokProfileEmbed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TPE_VERSION', '1.0.1' );
define( 'TPE_PLUGIN_FILE', __FILE__ );
define( 'TPE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'TPE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once TPE_PLUGIN_DIR . 'includes/class-cache.php';
require_once TPE_PLUGIN_DIR . 'includes/class-oembed-client.php';
require_once TPE_PLUGIN_DIR . 'includes/class-shortcode.php';
require_once TPE_PLUGIN_DIR . 'includes/class-block.php';
require_once TPE_PLUGIN_DIR . 'includes/class-settings.php';

/**
 * Initialize plugin components.
 */
function tpe_init() {
	TPE_Shortcode::register();
	TPE_Block::register();
	TPE_Settings::register();
}
add_action( 'plugins_loaded', 'tpe_init' );

/**
 * Flush embed cache after plugin updates so sanitized HTML is refreshed.
 */
function tpe_maybe_upgrade() {
	$stored_version = get_option( 'tpe_version', '' );

	if ( $stored_version === TPE_VERSION ) {
		return;
	}

	TPE_Cache::flush_all();
	update_option( 'tpe_version', TPE_VERSION );
}
add_action( 'plugins_loaded', 'tpe_maybe_upgrade', 20 );
