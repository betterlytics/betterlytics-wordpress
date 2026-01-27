<?php
/**
 * Betterlytics - Privacy-first analytics for WordPress.
 *
 * @package           Betterlytics
 * @author            Betterlytics
 * @copyright         2026 Betterlytics
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Betterlytics
 * Plugin URI:        https://github.com/betterlytics/betterlytics-wordpress
 * Description:       Privacy-first analytics for WordPress. Automatically adds the Betterlytics tracking script and provides easy WordPress action hooks integration.
 * Version:           1.0.0
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Author:            Betterlytics
 * Author URI:        https://betterlytics.io
 * Text Domain:       betterlytics
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'BETTERLYTICS_VERSION', '1.0.0' );

/**
 * Plugin base file.
 */
define( 'BETTERLYTICS_PLUGIN_FILE', __FILE__ );

/**
 * Plugin directory path.
 */
define( 'BETTERLYTICS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL.
 */
define( 'BETTERLYTICS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function betterlytics_activate() {
	require_once BETTERLYTICS_PLUGIN_DIR . 'includes/class-betterlytics-activator.php';
	Betterlytics_Activator::activate();
}

register_activation_hook( __FILE__, 'betterlytics_activate' );

/**
 * Add settings link to plugin action links.
 *
 * @param array $links Existing plugin action links.
 * @return array Modified plugin action links.
 */
function betterlytics_plugin_action_links( $links ) {
	$settings_link = '<a href="' . admin_url( 'options-general.php?page=betterlytics&tab=settings' ) . '">' . __( 'Settings', 'betterlytics' ) . '</a>';
	array_unshift( $links, $settings_link );

	// Add dashboard link.
	$dashboard_link = '<a href="https://betterlytics.io/dashboards" target="_blank">' . __( 'Dashboard', 'betterlytics' ) . '</a>';
	array_unshift( $links, $dashboard_link );

	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'betterlytics_plugin_action_links' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require BETTERLYTICS_PLUGIN_DIR . 'includes/class-betterlytics.php';

/**
 * Begins execution of the plugin.
 *
 * @since 1.0.0
 */
function betterlytics_run() {
	$plugin = new Betterlytics();
	$plugin->run();
}
betterlytics_run();
