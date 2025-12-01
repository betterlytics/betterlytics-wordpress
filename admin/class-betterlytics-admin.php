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
	 * The settings page slug.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string $page_slug The settings page slug.
	 */
	private $page_slug = 'betterlytics';

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
		if ( 'settings_page_betterlytics' !== $hook ) {
			return;
		}

		wp_enqueue_style(
			$this->plugin_name,
			BETTERLYTICS_PLUGIN_URL . 'admin/css/betterlytics-admin.css',
			array(),
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
		if ( 'settings_page_betterlytics' !== $hook ) {
			return;
		}

		wp_enqueue_script(
			$this->plugin_name,
			BETTERLYTICS_PLUGIN_URL . 'admin/js/betterlytics-admin.js',
			array( 'jquery' ),
			$this->version,
			true
		);

		wp_localize_script(
			$this->plugin_name,
			'betterlyticsAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'betterlytics_admin' ),
				'hooks'   => Betterlytics_Options::get( 'hooks', array() ),
				'strings' => array(
					'confirmDelete' => __( 'Are you sure you want to delete this hook?', 'betterlytics' ),
					'saved'         => __( 'Settings saved.', 'betterlytics' ),
					'error'         => __( 'An error occurred. Please try again.', 'betterlytics' ),
				),
			)
		);
	}

	/**
	 * Add admin menu page.
	 *
	 * @since 1.0.0
	 */
	public function add_admin_menu() {
		add_options_page(
			__( 'Betterlytics Settings', 'betterlytics' ),
			__( 'Betterlytics', 'betterlytics' ),
			'manage_options',
			$this->page_slug,
			array( $this, 'render_settings_page' )
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
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_options' ),
			)
		);

		// General Settings Section.
		add_settings_section(
			'betterlytics_general',
			__( 'General Settings', 'betterlytics' ),
			array( $this, 'render_general_section' ),
			$this->page_slug
		);

		add_settings_field(
			'enabled',
			__( 'Enable Tracking', 'betterlytics' ),
			array( $this, 'render_enabled_field' ),
			$this->page_slug,
			'betterlytics_general'
		);

		add_settings_field(
			'site_id',
			__( 'Site ID', 'betterlytics' ),
			array( $this, 'render_site_id_field' ),
			$this->page_slug,
			'betterlytics_general'
		);

		add_settings_field(
			'server_url',
			__( 'Server URL', 'betterlytics' ),
			array( $this, 'render_server_url_field' ),
			$this->page_slug,
			'betterlytics_general'
		);

		add_settings_field(
			'script_url',
			__( 'Script URL', 'betterlytics' ),
			array( $this, 'render_script_url_field' ),
			$this->page_slug,
			'betterlytics_general'
		);

		add_settings_field(
			'track_logged_in',
			__( 'Track Logged-in Users', 'betterlytics' ),
			array( $this, 'render_track_logged_in_field' ),
			$this->page_slug,
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

		$options['enabled']         = ! empty( $input['enabled'] );
		$options['site_id']         = sanitize_text_field( isset( $input['site_id'] ) ? $input['site_id'] : '' );
		$options['server_url']      = esc_url_raw( isset( $input['server_url'] ) ? $input['server_url'] : 'https://betterlytics.io/track' );
		$options['script_url']      = esc_url_raw( isset( $input['script_url'] ) ? $input['script_url'] : 'https://betterlytics.io/analytics.js' );
		$options['track_logged_in'] = ! empty( $input['track_logged_in'] );

		// Preserve hooks - they're saved separately via AJAX.
		if ( isset( $input['hooks'] ) && is_array( $input['hooks'] ) ) {
			$options['hooks'] = $this->sanitize_hooks( $input['hooks'] );
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
		$sanitized = array();

		foreach ( $hooks as $hook ) {
			if ( empty( $hook['wp_hook'] ) || empty( $hook['event_name'] ) ) {
				continue;
			}

			$sanitized[] = array(
				'wp_hook'    => sanitize_text_field( $hook['wp_hook'] ),
				'event_name' => sanitize_text_field( $hook['event_name'] ),
				'enabled'    => ! empty( $hook['enabled'] ),
			);
		}

		return $sanitized;
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

		include BETTERLYTICS_PLUGIN_DIR . 'admin/partials/betterlytics-admin-display.php';
	}

	/**
	 * Render general section description.
	 *
	 * @since 1.0.0
	 */
	public function render_general_section() {
		echo '<p>' . esc_html__( 'Configure your Betterlytics tracking settings. You can find your Site ID in the Betterlytics dashboard under Integration.', 'betterlytics' ) . '</p>';
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
	 * AJAX handler for saving hooks.
	 *
	 * @since 1.0.0
	 */
	public function ajax_save_hooks() {
		check_ajax_referer( 'betterlytics_admin', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'betterlytics' ) ) );
		}

		$hooks = array();
		if ( isset( $_POST['hooks'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized in sanitize_hooks().
			$hooks = json_decode( wp_unslash( $_POST['hooks'] ), true );
		}

		if ( ! is_array( $hooks ) ) {
			$hooks = array();
		}

		$options          = Betterlytics_Options::get_options();
		$options['hooks'] = $this->sanitize_hooks( $hooks );
		Betterlytics_Options::update_options( $options );

		wp_send_json_success( array( 'message' => __( 'Hooks saved successfully.', 'betterlytics' ) ) );
	}
}
