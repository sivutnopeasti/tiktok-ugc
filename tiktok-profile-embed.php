<?php
/**
 * Plugin Name:       TikTok Profile Embed
 * Plugin URI:        https://ugcsuomi.fi
 * Description:       Upottaa TikTok-käyttäjän profiilin ja videot shortcodella tai Gutenberg-lohkolla.
 * Version:           1.0.0
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

define( 'TPE_VERSION', '1.0.0' );
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
 * Enqueue frontend assets when an embed is present on the page.
 */
function tpe_enqueue_frontend_assets() {
	if ( ! TPE_OEmbed_Client::should_enqueue_assets() ) {
		return;
	}

	wp_enqueue_style(
		'tpe-frontend',
		TPE_PLUGIN_URL . 'assets/css/frontend.css',
		array(),
		TPE_VERSION
	);

	$settings = TPE_Settings::get_settings();

	if ( ! empty( $settings['lazy_load'] ) ) {
		wp_enqueue_script(
			'tpe-lazy-load',
			TPE_PLUGIN_URL . 'assets/js/lazy-load.js',
			array(),
			TPE_VERSION,
			true
		);
	} else {
		wp_enqueue_script(
			'tpe-tiktok-embed',
			'https://www.tiktok.com/embed.js',
			array(),
			null,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'tpe_enqueue_frontend_assets' );
