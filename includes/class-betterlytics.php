<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @package    Betterlytics
 * @subpackage Betterlytics/includes
 * @since      1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Core plugin class.
 */
class Betterlytics {

	/**
	 * Hook loader.
	 *
	 * @var Betterlytics_Loader
	 */
	protected $loader;

	/**
	 * Plugin name.
	 *
	 * @var string
	 */
	protected $plugin_name;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->version     = defined( 'BETTERLYTICS_VERSION' ) ? BETTERLYTICS_VERSION : '1.0.0';
		$this->plugin_name = 'betterlytics';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters.
		 */
		require_once BETTERLYTICS_PLUGIN_DIR . 'includes/class-betterlytics-loader.php';

		/**
		 * The class responsible for plugin options.
		 */
		require_once BETTERLYTICS_PLUGIN_DIR . 'includes/class-betterlytics-options.php';

		/**
		 * The class responsible for admin page routing and rendering.
		 */
		require_once BETTERLYTICS_PLUGIN_DIR . 'admin/class-betterlytics-admin-controller.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once BETTERLYTICS_PLUGIN_DIR . 'admin/class-betterlytics-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once BETTERLYTICS_PLUGIN_DIR . 'public/class-betterlytics-public.php';

		/**
		 * The class responsible for WordPress hooks integration.
		 */
		require_once BETTERLYTICS_PLUGIN_DIR . 'includes/class-betterlytics-hooks.php';

		$this->loader = new Betterlytics_Loader();
	}

	/**
	 * Register all of the hooks related to the admin area functionality.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function define_admin_hooks() {
		$admin_controller = new Betterlytics_Admin_Controller();
		$plugin_admin     = new Betterlytics_Admin( $this->get_plugin_name(), $this->get_version() );

		// Page controller.
		$this->loader->add_action( 'admin_menu', $admin_controller, 'register_menu' );

		// Admin functionality.
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
		$this->loader->add_action( 'admin_head', $plugin_admin, 'hide_admin_notices' );
		$this->loader->add_action( 'admin_print_footer_scripts', $plugin_admin, 'dashboard_link_script' );
		$this->loader->add_action( 'wp_ajax_betterlytics_dismiss_setup_banner', $plugin_admin, 'ajax_dismiss_setup_banner' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function define_public_hooks() {
		$plugin_public = new Betterlytics_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'inject_tracking_script', 1 );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'output_queued_events', 99 );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_event_scripts' );

		// Initialize custom hooks integration.
		$plugin_hooks = new Betterlytics_Hooks();

		$this->loader->add_action( 'init', $plugin_hooks, 'register_page_hooks' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it.
	 *
	 * @since  1.0.0
	 * @return string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since  1.0.0
	 * @return string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
