<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Betterlytics
 * @subpackage Betterlytics/admin
 * @since      1.0.0
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for the admin area.
 *
 * @since 1.0.0
 */
class Betterlytics_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The main menu slug.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string $menu_slug The main menu slug.
	 */
	private $menu_slug = 'betterlytics';

	/**
	 * The events page slug.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string $events_slug The events page slug.
	 */
	private $events_slug = 'betterlytics-events';

	/**
	 * The settings page slug.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string $settings_slug The settings page slug.
	 */
	private $settings_slug = 'betterlytics-settings';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.0.0
	 * @param string $hook The current admin page hook.
	 */
	public function enqueue_styles( $hook ) {
		if ( strpos( $hook, 'betterlytics' ) === false ) {
			return;
		}

		wp_enqueue_style(
			$this->plugin_name . '-banner',
			BETTERLYTICS_PLUGIN_URL . 'admin/css/components/setup-banner.css',
			[],
			$this->version,
			'all'
		);

		wp_enqueue_style(
			$this->plugin_name,
			BETTERLYTICS_PLUGIN_URL . 'admin/css/betterlytics-admin.css',
			[ $this->plugin_name . '-banner' ],
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 * @param string $hook The current admin page hook.
	 */
	public function enqueue_scripts( $hook ) {
		// Only load JS on Events page (for tabs and hooks management).
		if ( 'betterlytics_page_betterlytics-events' !== $hook ) {
			return;
		}

		wp_enqueue_script(
			$this->plugin_name,
			BETTERLYTICS_PLUGIN_URL . 'admin/js/betterlytics-admin.js',
			[ 'jquery' ],
			$this->version,
			true
		);

		wp_localize_script(
			$this->plugin_name,
			'betterlyticsAdmin',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'betterlytics_admin' ),
				'hooks'   => Betterlytics_Options::get( 'hooks', [] ),
				'strings' => [
					'confirmDelete' => __( 'Are you sure you want to delete this hook?', 'betterlytics' ),
					'saved'         => __( 'Settings saved.', 'betterlytics' ),
					'error'         => __( 'An error occurred. Please try again.', 'betterlytics' ),
				],
			]
		);
	}

	/**
	 * Add admin menu and subpages.
	 *
	 * @since 1.0.0
	 */
	public function add_admin_menu() {
		global $submenu;

		$icon_svg = file_get_contents( BETTERLYTICS_PLUGIN_DIR . 'public/logo/betterlytics-logo-light-simple.svg' );
		$icon     = 'data:image/svg+xml;base64,' . base64_encode( $icon_svg );

		// Add main menu page (Home).
		// Position 58 places it after WooCommerce (55-56), near analytics items.
		add_menu_page(
			__( 'Betterlytics', 'betterlytics' ),
			__( 'Betterlytics', 'betterlytics' ),
			'manage_options',
			$this->menu_slug,
			[ $this, 'render_home_page' ],
			$icon,
			58
		);

		// Add Home submenu (replaces default submenu).
		add_submenu_page(
			$this->menu_slug,
			__( 'Home', 'betterlytics' ),
			__( 'Home', 'betterlytics' ),
			'manage_options',
			$this->menu_slug,
			[ $this, 'render_home_page' ]
		);

		// Add Dashboard external link.
		$options       = Betterlytics_Options::get_options();
		$dashboard_url = 'https://www.betterlytics.io/dashboard';
		if ( ! empty( $options['site_id'] ) ) {
			$dashboard_url .= '/' . $options['site_id'];
		}

		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Required to add external link to submenu.
		$submenu[ $this->menu_slug ][] = [
			__( 'Dashboard', 'betterlytics' ),
			'manage_options',
			$dashboard_url,
		];

		// Add Events submenu.
		add_submenu_page(
			$this->menu_slug,
			__( 'Events', 'betterlytics' ),
			__( 'Events', 'betterlytics' ),
			'manage_options',
			$this->events_slug,
			[ $this, 'render_events_page' ]
		);

		// Add Settings submenu.
		add_submenu_page(
			$this->menu_slug,
			__( 'Settings', 'betterlytics' ),
			__( 'Settings', 'betterlytics' ),
			'manage_options',
			$this->settings_slug,
			[ $this, 'render_settings_page' ]
		);
	}

	/**
	 * Register plugin settings.
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {
		register_setting(
			'betterlytics_settings',
			'betterlytics_options',
			[
				'type'              => 'array',
				'sanitize_callback' => [ $this, 'sanitize_options' ],
			]
		);

		register_setting(
			'betterlytics_events',
			'betterlytics_options',
			[
				'type'              => 'array',
				'sanitize_callback' => [ $this, 'sanitize_options' ],
			]
		);

		// General Settings Section.
		add_settings_section(
			'betterlytics_general',
			'',
			'__return_empty_string',
			$this->settings_slug
		);

		add_settings_field(
			'enabled',
			__( 'Enable Tracking', 'betterlytics' ),
			[ $this, 'render_enabled_field' ],
			$this->settings_slug,
			'betterlytics_general'
		);

		add_settings_field(
			'site_id',
			__( 'Site ID', 'betterlytics' ),
			[ $this, 'render_site_id_field' ],
			$this->settings_slug,
			'betterlytics_general'
		);

		add_settings_field(
			'server_url',
			__( 'Server URL', 'betterlytics' ),
			[ $this, 'render_server_url_field' ],
			$this->settings_slug,
			'betterlytics_general'
		);

		add_settings_field(
			'script_url',
			__( 'Script URL', 'betterlytics' ),
			[ $this, 'render_script_url_field' ],
			$this->settings_slug,
			'betterlytics_general'
		);

		add_settings_field(
			'track_logged_in',
			__( 'Track Logged-in Users', 'betterlytics' ),
			[ $this, 'render_track_logged_in_field' ],
			$this->settings_slug,
			'betterlytics_general'
		);
	}

	/**
	 * Sanitize options before saving.
	 *
	 * @since  1.0.0
	 * @param  array $input The input options.
	 * @return array Sanitized options.
	 */
	public function sanitize_options( $input ) {
		$options = Betterlytics_Options::get_options();

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified by settings API.
		$page = isset( $_POST['option_page'] ) ? sanitize_text_field( wp_unslash( $_POST['option_page'] ) ) : '';

		// Settings page fields.
		if ( 'betterlytics_settings' === $page ) {
			$options['enabled']         = ! empty( $input['enabled'] );
			$options['site_id']         = sanitize_text_field( $input['site_id'] ?? '' );
			$options['server_url']      = esc_url_raw( $input['server_url'] ?? 'https://betterlytics.io/track' );
			$options['script_url']      = esc_url_raw( $input['script_url'] ?? 'https://betterlytics.io/analytics.js' );
			$options['track_logged_in'] = ! empty( $input['track_logged_in'] );
		}

		// Events page fields.
		if ( 'betterlytics_events' === $page ) {
			$options['track_404']        = ! empty( $input['track_404'] );
			$options['track_search']     = ! empty( $input['track_search'] );
			$options['track_outbound']   = ! empty( $input['track_outbound'] );
			$options['track_downloads']  = ! empty( $input['track_downloads'] );
			$options['track_css_events'] = ! empty( $input['track_css_events'] );
			$options['woo_add_to_cart']  = ! empty( $input['woo_add_to_cart'] );
			$options['woo_checkout']     = ! empty( $input['woo_checkout'] );
			$options['woo_purchase']     = ! empty( $input['woo_purchase'] );

			if ( isset( $input['hooks'] ) && is_array( $input['hooks'] ) ) {
				$options['hooks'] = $this->sanitize_hooks( $input['hooks'] );
			}
		}

		return $options;
	}

	/**
	 * Sanitize hooks array.
	 *
	 * @since  1.0.0
	 * @param  array $hooks The hooks to sanitize.
	 * @return array Sanitized hooks.
	 */
	public function sanitize_hooks( $hooks ) {
		$sanitized = [];

		foreach ( $hooks as $hook ) {
			if ( empty( $hook['wp_hook'] ) || empty( $hook['event_name'] ) ) {
				continue;
			}

			$sanitized[] = [
				'wp_hook'    => sanitize_text_field( $hook['wp_hook'] ),
				'event_name' => sanitize_text_field( $hook['event_name'] ),
				'enabled'    => ! empty( $hook['enabled'] ),
			];
		}

		return $sanitized;
	}

	/**
	 * Render the home page.
	 *
	 * @since 1.0.0
	 */
	public function render_home_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		include BETTERLYTICS_PLUGIN_DIR . 'admin/partials/betterlytics-home-display.php';
	}

	/**
	 * Render the events page.
	 *
	 * @since 1.0.0
	 */
	public function render_events_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		include BETTERLYTICS_PLUGIN_DIR . 'admin/partials/betterlytics-events-display.php';
	}

	/**
	 * Render the settings page.
	 *
	 * @since 1.0.0
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		include BETTERLYTICS_PLUGIN_DIR . 'admin/partials/betterlytics-settings-display.php';
	}

	/**
	 * Hide WordPress admin notices on Betterlytics pages for cleaner UI.
	 *
	 * @since 1.0.0
	 */
	public function hide_admin_notices() {
		$screen = get_current_screen();
		if ( $screen && strpos( $screen->id, 'betterlytics' ) !== false ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}

	/**
	 * Add target="_blank" to dashboard link in admin menu.
	 * Runs on all admin pages since the menu is global.
	 *
	 * @since 1.0.0
	 */
	public function dashboard_link_script() {
		?>
		<script>
		(function(){
			function setTarget() {
				var link = document.querySelector('#adminmenu a[href*="betterlytics.io"]');
				if (link) { link.target = '_blank'; link.rel = 'noopener'; }
			}
			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', setTarget);
			} else {
				setTarget();
			}
		})();
		</script>
		<?php
	}

	/**
	 * Render enabled field.
	 *
	 * @since 1.0.0
	 */
	public function render_enabled_field() {
		$options = Betterlytics_Options::get_options();
		?>
		<label>
			<input type="checkbox" name="betterlytics_options[enabled]" value="1" <?php checked( $options['enabled'] ); ?>>
			<?php esc_html_e( 'Enable Betterlytics tracking on this site', 'betterlytics' ); ?>
		</label>
		<?php
	}

	/**
	 * Render site ID field.
	 *
	 * @since 1.0.0
	 */
	public function render_site_id_field() {
		$options = Betterlytics_Options::get_options();
		?>
		<input type="text" name="betterlytics_options[site_id]" value="<?php echo esc_attr( $options['site_id'] ); ?>" class="regular-text" placeholder="your-site-id">
		<p class="description">
			<?php esc_html_e( 'Your unique Site ID from the Betterlytics dashboard.', 'betterlytics' ); ?>
		</p>
		<?php
	}

	/**
	 * Render server URL field.
	 *
	 * @since 1.0.0
	 */
	public function render_server_url_field() {
		$options = Betterlytics_Options::get_options();
		?>
		<input type="url" name="betterlytics_options[server_url]" value="<?php echo esc_attr( $options['server_url'] ); ?>" class="regular-text" placeholder="https://betterlytics.io/track">
		<p class="description">
			<?php esc_html_e( 'The tracking server URL. Default is the Betterlytics cloud. Change this if you are self-hosting.', 'betterlytics' ); ?>
		</p>
		<?php
	}

	/**
	 * Render script URL field.
	 *
	 * @since 1.0.0
	 */
	public function render_script_url_field() {
		$options = Betterlytics_Options::get_options();
		?>
		<input type="url" name="betterlytics_options[script_url]" value="<?php echo esc_attr( $options['script_url'] ); ?>" class="regular-text" placeholder="https://betterlytics.io/analytics.js">
		<p class="description">
			<?php esc_html_e( 'The tracking script URL. Default is the Betterlytics cloud. Change this if you are self-hosting.', 'betterlytics' ); ?>
		</p>
		<?php
	}

	/**
	 * Render track logged-in users field.
	 *
	 * @since 1.0.0
	 */
	public function render_track_logged_in_field() {
		$options = Betterlytics_Options::get_options();
		?>
		<label>
			<input type="checkbox" name="betterlytics_options[track_logged_in]" value="1" <?php checked( $options['track_logged_in'] ); ?>>
			<?php esc_html_e( 'Track logged-in users (disable to exclude admins/editors from analytics)', 'betterlytics' ); ?>
		</label>
		<?php
	}

	/**
	 * AJAX handler for saving hooks (legacy).
	 *
	 * @since 1.0.0
	 */
	public function ajax_save_hooks() {
		check_ajax_referer( 'betterlytics_admin', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied.', 'betterlytics' ) ] );
		}

		$hooks = [];
		if ( isset( $_POST['hooks'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized in sanitize_hooks().
			$hooks = json_decode( wp_unslash( $_POST['hooks'] ), true );
		}

		if ( ! is_array( $hooks ) ) {
			$hooks = [];
		}

		$options          = Betterlytics_Options::get_options();
		$options['hooks'] = $this->sanitize_hooks( $hooks );
		Betterlytics_Options::update_options( $options );

		wp_send_json_success( [ 'message' => __( 'Hooks saved successfully.', 'betterlytics' ) ] );
	}

	/**
	 * AJAX handler for saving events (built-in and custom hooks).
	 *
	 * @since 1.0.1
	 */
	public function ajax_save_events() {
		check_ajax_referer( 'betterlytics_admin', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied.', 'betterlytics' ) ] );
		}

		$options = Betterlytics_Options::get_options();

		// Handle built-in events.
		if ( isset( $_POST['builtin_events'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized below.
			$builtin_events = json_decode( wp_unslash( $_POST['builtin_events'] ), true );

			if ( is_array( $builtin_events ) ) {
				// Map of valid option keys.
				$valid_keys = [
					'track_404',
					'track_search',
					'track_outbound',
					'track_downloads',
					'track_css_events',
					'woo_add_to_cart',
					'woo_checkout',
					'woo_purchase',
				];

				foreach ( $valid_keys as $key ) {
					$options[ $key ] = isset( $builtin_events[ $key ] ) && $builtin_events[ $key ] === true;
				}
			}
		}

		// Handle custom hooks.
		if ( isset( $_POST['custom_hooks'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized in sanitize_hooks().
			$custom_hooks = json_decode( wp_unslash( $_POST['custom_hooks'] ), true );

			if ( ! is_array( $custom_hooks ) ) {
				$custom_hooks = [];
			}

			$options['hooks'] = $this->sanitize_hooks( $custom_hooks );
		}

		Betterlytics_Options::update_options( $options );

		wp_send_json_success( [ 'message' => __( 'Events saved successfully.', 'betterlytics' ) ] );
	}

	/**
	 * AJAX handler for dismissing the setup banner.
	 *
	 * @since 1.0.0
	 */
	public function ajax_dismiss_setup_banner() {
		check_ajax_referer( 'betterlytics_admin', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied.', 'betterlytics' ) ] );
		}

		update_user_meta( get_current_user_id(), 'betterlytics_setup_banner_dismissed', true );

		wp_send_json_success();
	}

	/**
	 * Check if the setup banner has been dismissed by the current user.
	 *
	 * @since 1.0.0
	 * @return bool True if dismissed.
	 */
	public static function is_setup_banner_dismissed() {
		return (bool) get_user_meta( get_current_user_id(), 'betterlytics_setup_banner_dismissed', true );
	}
}
