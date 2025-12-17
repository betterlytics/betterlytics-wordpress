<?php
/**
 * Admin page controller for Betterlytics.
 *
 * Handles menu registration and page rendering.
 *
 * @package    Betterlytics
 * @subpackage Betterlytics/admin
 * @since      1.0.0
 */

/**
 * Admin page controller.
 *
 * @since 1.0.0
 */
class Betterlytics_Admin_Controller {

	/**
	 * Menu slug.
	 *
	 * @var string
	 */
	const MENU_SLUG = 'betterlytics';

	/**
	 * Admin pages configuration.
	 *
	 * @var array
	 */
	const PAGES = [
		'home'     => [
			'title' => 'Home',
			'slug'  => 'betterlytics',
		],
		'events'   => [
			'title' => 'Events',
			'slug'  => 'betterlytics-events',
		],
		'settings' => [
			'title' => 'Settings',
			'slug'  => 'betterlytics-settings',
		],
	];

	/**
	 * Register admin menu and pages.
	 *
	 * @since 1.0.0
	 */
	public function register_menu() {
		global $submenu;

		$icon = $this->get_menu_icon();

		// Main menu page.
		add_menu_page(
			__( 'Betterlytics', 'betterlytics' ),
			__( 'Betterlytics', 'betterlytics' ),
			'manage_options',
			self::MENU_SLUG,
			[ $this, 'render' ],
			$icon,
			58
		);

		// Home submenu (replaces default).
		add_submenu_page(
			self::MENU_SLUG,
			__( 'Home', 'betterlytics' ),
			__( 'Home', 'betterlytics' ),
			'manage_options',
			self::PAGES['home']['slug'],
			[ $this, 'render' ]
		);

		// External dashboard link.
		$options       = Betterlytics_Options::get_options();
		$dashboard_url = 'https://www.betterlytics.io/dashboard';
		if ( ! empty( $options['site_id'] ) ) {
			$dashboard_url .= '/' . $options['site_id'];
		}

		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Required to add external link.
		$submenu[ self::MENU_SLUG ][] = [
			__( 'Dashboard', 'betterlytics' ),
			'manage_options',
			$dashboard_url,
		];

		// Events submenu.
		add_submenu_page(
			self::MENU_SLUG,
			__( 'Events', 'betterlytics' ),
			__( 'Events', 'betterlytics' ),
			'manage_options',
			self::PAGES['events']['slug'],
			[ $this, 'render' ]
		);

		// Settings submenu.
		add_submenu_page(
			self::MENU_SLUG,
			__( 'Settings', 'betterlytics' ),
			__( 'Settings', 'betterlytics' ),
			'manage_options',
			self::PAGES['settings']['slug'],
			[ $this, 'render' ]
		);
	}

	/**
	 * Render the current admin page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$page = $this->get_current_page();
		$file = BETTERLYTICS_PLUGIN_DIR . "admin/partials/betterlytics-{$page}-display.php";

		if ( file_exists( $file ) ) {
			include $file;
		}
	}

	/**
	 * Get the current page name from the request.
	 *
	 * @since  1.0.0
	 * @return string Page name (home, events, settings).
	 */
	private function get_current_page() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Just reading page parameter.
		$slug = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : self::MENU_SLUG;

		foreach ( self::PAGES as $page => $config ) {
			if ( $config['slug'] === $slug ) {
				return $page;
			}
		}

		return 'home';
	}

	/**
	 * Get the menu icon as base64 SVG.
	 *
	 * @since  1.0.0
	 * @return string Base64 encoded SVG icon.
	 */
	private function get_menu_icon() {
		$svg_path = BETTERLYTICS_PLUGIN_DIR . 'public/logo/betterlytics-logo-light-simple.svg';
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local file.
		$svg = file_get_contents( $svg_path );
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Required for data URI.
		return 'data:image/svg+xml;base64,' . base64_encode( $svg );
	}

	/**
	 * Get a page slug by name.
	 *
	 * @since  1.0.0
	 * @param  string $page Page name.
	 * @return string Page slug.
	 */
	public static function get_slug( $page ) {
		return self::PAGES[ $page ]['slug'] ?? self::MENU_SLUG;
	}
}
