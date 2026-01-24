<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Betterlytics
 * @subpackage Betterlytics/admin
 * @since      1.0.0
 */

/**
 * Admin functionality handler.
 */
class Betterlytics_Admin {

	/**
	 * Plugin name.
	 *
	 * @var string
	 */
	private $plugin_name;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private $version;

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

		// Enqueue Google Fonts (Inter & Inter Tight).
		wp_enqueue_style(
			'betterlytics-fonts',
			'https://fonts.googleapis.com/css2?family=Inter:wght@400..700&family=Inter+Tight:wght@400..700&display=swap',
			[],
			$this->version,
			'all'
		);

		wp_enqueue_style(
			$this->plugin_name,
			BETTERLYTICS_PLUGIN_URL . 'admin/css/betterlytics-admin.tailwind.css',
			[ 'betterlytics-fonts' ],
			$this->version,
			'all'
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

		if ( 'betterlytics_settings' === $page ) {
			$options['enabled']    = ! empty( $input['enabled'] );
			$options['site_id']    = sanitize_text_field( $input['site_id'] ?? '' );

			// Only update URLs if explicitly provided
			if ( ! empty( $input['server_url'] ) ) {
				$options['server_url'] = esc_url_raw( $input['server_url'] );
			}

			if ( ! empty( $input['script_url'] ) ) {
				$options['script_url'] = esc_url_raw( $input['script_url'] );
			}

			$options['track_web_vitals'] = ! empty( $input['track_web_vitals'] );

			$checkbox_keys = [
				'track_404'        => [ 'enabled' ],
				'track_search'     => [ 'enabled', 'include_query' ],
				'track_downloads'  => [ 'enabled' ],
				'track_custom_html_attribute' => [ 'enabled' ],
			];

			foreach ( $checkbox_keys as $key => $subkeys ) {
				foreach ( $subkeys as $subkey ) {
					if ( isset( $input[ $key ][ $subkey ] ) ) {
						$options[ $key ][ $subkey ] = ! empty( $input[ $key ][ $subkey ] );
					} else {
						$options[ $key ][ $subkey ] = false;
					}
				}
			}

			$mode_keys = [
				'track_outbound' => 'domain',
			];

			foreach ( $mode_keys as $key => $default_mode ) {
				$val      = isset( $input[ $key ] ) ? $input[ $key ] : null;
				$mode     = 'off';
				$metadata = [];

				if ( is_array( $val ) ) {
					$mode = isset( $val['mode'] ) ? sanitize_text_field( $val['mode'] ) : $default_mode;
					if ( isset( $val['metadata'] ) && is_array( $val['metadata'] ) ) {
						$metadata = $val['metadata'];
					}
				} elseif ( is_string( $val ) ) {
					$mode = sanitize_text_field( $val );
				}

				$valid_modes = [ 'off', 'domain', 'full' ];
				if ( ! in_array( $mode, $valid_modes, true ) ) {
					$mode = $default_mode;
				}

				$options[ $key ] = [
					'mode'     => $mode,
					'metadata' => $metadata,
				];
			}
		}

		return $options;
	}

	/**
	 * Hide WordPress admin notices on Betterlytics pages for cleaner UI.
	 *
	 * @since 1.0.0
	 */
	public function hide_admin_notices() {
		$screen = get_current_screen();
		if ( $screen && 'settings_page_betterlytics' === $screen->id ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}

	/**
	 * Add target="_blank" to dashboard link in admin menu.
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
