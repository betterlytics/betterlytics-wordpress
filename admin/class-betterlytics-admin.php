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
 * Handles settings, assets, and AJAX handlers for the admin area.
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
		if ( strpos( $hook, Betterlytics_Admin_Controller::MENU_SLUG ) === false ) {
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
		$events_hook = Betterlytics_Admin_Controller::MENU_SLUG . '_page_' . Betterlytics_Admin_Controller::get_slug( 'events' );
		if ( $events_hook !== $hook ) {
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
				// 'ajaxUrl' => admin_url( 'admin-ajax.php' ), // Unused
				// 'nonce'   => wp_create_nonce( 'betterlytics_admin' ), // Unused
				'hooks'   => Betterlytics_Options::get( 'hooks', [] ),
				'builtinHooks' => Betterlytics_Hooks::BUILTIN_HOOKS,
				'strings' => [
					'confirmDelete' => __( 'Are you sure you want to delete this hook?', 'betterlytics' ),
					'saved'         => __( 'Settings saved.', 'betterlytics' ),
					'error'         => __( 'An error occurred. Please try again.', 'betterlytics' ),
				],
			]
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
			// 1. Sanitize Custom Hooks List
			// 'hooks' input comes from the table.
			$hooks = isset( $input['hooks'] ) && is_array( $input['hooks'] ) ? $this->sanitize_hooks( $input['hooks'] ) : [];
			$options['hooks'] = $hooks; // Start with custom hooks

			// 2. Process All Options (Browser + Server + User + Woo)
			$all_keys = [
				'track_404', 'track_search', 'track_outbound', 'track_downloads', 'track_css_events',
				'woo_add_to_cart', 'woo_remove_from_cart', 'woo_checkout', 'woo_purchase',
				'track_wp_login', 'track_wp_logout', 'track_user_register'
			];

			foreach ( $all_keys as $key ) {
				$val = isset( $input[ $key ] ) ? $input[ $key ] : null;
				$enabled = false;
				$metadata = [];

				if ( is_array( $val ) ) {
					$enabled = ! empty( $val['enabled'] );
					if ( isset( $val['metadata'] ) && is_array( $val['metadata'] ) ) {
						$metadata = $val['metadata'];
					}
				} else {
					$enabled = ! empty( $val );
				}

				$options[ $key ] = [
					'enabled'  => $enabled,
					'metadata' => $metadata, 
				];

				// 3. Sync Built-ins to Hooks List
				if ( array_key_exists( $key, Betterlytics_Hooks::BUILTIN_HOOKS ) ) {
					$hook_def = Betterlytics_Hooks::BUILTIN_HOOKS[ $key ];
					$wp_hook    = $hook_def[0];
					$event_name = $hook_def[1];

					if ( $enabled ) {
						// Add if not present
						$found = false;
						foreach ( $options['hooks'] as $h ) {
							if ( $h['wp_hook'] === $wp_hook ) {
								$found = true;
								break;
							}
						}
						if ( ! $found ) {
							$options['hooks'][] = [
								'wp_hook'    => $wp_hook,
								'event_name' => $event_name,
								'enabled'    => true,
								'metadata'   => [],
							];
						}
					} else {
						// Remove if present
						foreach ( $options['hooks'] as $idx => $h ) {
							if ( $h['wp_hook'] === $wp_hook ) {
								unset( $options['hooks'][ $idx ] );
							}
						}
						$options['hooks'] = array_values( $options['hooks'] );
					}
				}
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

			$clean_hook = [
				'wp_hook'    => sanitize_text_field( $hook['wp_hook'] ),
				'event_name' => sanitize_text_field( $hook['event_name'] ),
				'enabled'    => ! empty( $hook['enabled'] ),
				'metadata'   => [],
			];

			if ( isset( $hook['metadata'] ) && is_array( $hook['metadata'] ) ) {
				foreach ( $hook['metadata'] as $meta ) {
					if ( ! empty( $meta['key'] ) && isset( $meta['value'] ) ) {
						$clean_hook['metadata'][] = [
							'key'   => sanitize_key( $meta['key'] ),
							'value' => sanitize_text_field( $meta['value'] ),
						];
					}
				}
			}

			$sanitized[] = $clean_hook;
		}

		return $sanitized;
	}

	/**
	 * Hide WordPress admin notices on Betterlytics pages for cleaner UI.
	 *
	 * @since 1.0.0
	 */
	public function hide_admin_notices() {
		$screen = get_current_screen();
		if ( $screen && strpos( $screen->id, Betterlytics_Admin_Controller::MENU_SLUG ) !== false ) {
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
