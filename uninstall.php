<?php
/**
 * Uninstall handler.
 *
 * @package InstagramProfileEmbed
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'ipe_settings' );
