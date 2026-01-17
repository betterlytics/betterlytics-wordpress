<?php
/**
 * Fired during plugin activation.
 *
 * @package    Betterlytics
 * @subpackage Betterlytics/includes
 * @since      1.0.0
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since 1.0.0
 */
class Betterlytics_Activator {

	/**
	 * Activate the plugin.
	 *
	 * Sets up default options when the plugin is first activated.
	 *
	 * @since 1.0.0
	 */
	public static function activate() {
		$default_options = [
			'site_id'         => '',
			'server_url'      => 'https://betterlytics.io/event',
			'script_url'      => 'https://betterlytics.io/analytics.js',
			'enabled'         => false,
			'track_logged_in' => true,
			'hooks'           => [],
		];

		if ( ! get_option( 'betterlytics_options' ) ) {
			add_option( 'betterlytics_options', $default_options );
		}
	}
}
