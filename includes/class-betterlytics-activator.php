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
		if ( ! get_option( Betterlytics_Options::OPTION_NAME ) ) {
			add_option( Betterlytics_Options::OPTION_NAME, Betterlytics_Options::DEFAULTS );
		}
	}
}
