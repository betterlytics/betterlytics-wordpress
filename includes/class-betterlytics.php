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

/**
 * The core plugin class.
 *
 * @since 1.0.0
 */
class Betterlytics {

	/**
	 * The loader that's responsible for maintaining and registering all hooks.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    Betterlytics_Loader $loader Maintains and registers all hooks.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $version The current version of the plugin.
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
		$this->set_locale();
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
		 * The class responsible for defining internationalization functionality.
		 */
		require_once BETTERLYTICS_PLUGIN_DIR . 'includes/class-betterlytics-i18n.php';

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
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function set_locale() {
		$plugin_i18n = new Betterlytics_I18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
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
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
		$this->loader->add_action( 'admin_head', $plugin_admin, 'hide_admin_notices' );
		$this->loader->add_action( 'admin_print_footer_scripts', $plugin_admin, 'dashboard_link_script' );
		$this->loader->add_action( 'wp_ajax_betterlytics_save_hooks', $plugin_admin, 'ajax_save_hooks' );
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

		$this->loader->add_action( 'wp_head', $plugin_public, 'inject_tracking_script', 1 );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'output_queued_events', 99 );

		// Initialize custom hooks integration.
		$plugin_hooks = new Betterlytics_Hooks();
		$this->loader->add_action( 'init', $plugin_hooks, 'register_configured_hooks' );
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
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since  1.0.0
	 * @return Betterlytics_Loader Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
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
