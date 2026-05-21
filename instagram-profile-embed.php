<?php
/**
 * Plugin Name:       Instagram Profile Embed
 * Plugin URI:        https://ugcsuomi.fi
 * Description:       Upottaa Instagram-käyttäjän profiilin shortcodella tai Gutenberg-lohkolla.
 * Version:           1.0.1
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            UGC Suomi
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       instagram-profile-embed
 *
 * @package InstagramProfileEmbed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'IPE_VERSION', '1.0.1' );
define( 'IPE_PLUGIN_FILE', __FILE__ );
define( 'IPE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'IPE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once IPE_PLUGIN_DIR . 'includes/class-embed-renderer.php';
require_once IPE_PLUGIN_DIR . 'includes/class-shortcode.php';
require_once IPE_PLUGIN_DIR . 'includes/class-block.php';
require_once IPE_PLUGIN_DIR . 'includes/class-settings.php';

/**
 * Initialize plugin components.
 */
function ipe_init() {
	IPE_Shortcode::register();
	IPE_Block::register();
	IPE_Settings::register();
}
add_action( 'plugins_loaded', 'ipe_init' );
