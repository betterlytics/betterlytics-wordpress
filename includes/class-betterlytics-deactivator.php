<?php
/**
 * Fired during plugin deactivation.
 *
 * @package    Betterlytics
 * @subpackage Betterlytics/includes
 * @since      1.0.0
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since 1.0.0
 */
class Betterlytics_Deactivator {

	/**
	 * Deactivate the plugin.
	 *
	 * Currently does nothing. Options are preserved so users can reactivate
	 * without losing their configuration. Use uninstall.php for cleanup.
	 *
	 * @since 1.0.0
	 */
	public static function deactivate() {
		// Nothing to do on deactivation.
		// Options are cleaned up in uninstall.php.
	}
}
