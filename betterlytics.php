<?php
/**
 * Betterlytics - Privacy-first analytics for WordPress.
 *
 * @package           Betterlytics
 * @author            Betterlytics
 * @copyright         2024 Betterlytics
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Betterlytics
 * Plugin URI:        https://github.com/betterlytics/betterlytics-wordpress
 * Description:       Privacy-first analytics for WordPress. Automatically adds the Betterlytics tracking script and provides easy WordPress action hooks integration.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Betterlytics
 * Author URI:        https://betterlytics.io
 * Text Domain:       betterlytics
 * Domain Path:       /languages
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

/**
 * The code that runs during plugin deactivation.
 */
function betterlytics_deactivate() {
	require_once BETTERLYTICS_PLUGIN_DIR . 'includes/class-betterlytics-deactivator.php';
	Betterlytics_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'betterlytics_activate' );
register_deactivation_hook( __FILE__, 'betterlytics_deactivate' );

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
