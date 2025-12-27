<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @package    Betterlytics
 * @subpackage Betterlytics/public
 * @since      1.0.0
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for the public-facing side.
 *
 * @since 1.0.0
 */
class Betterlytics_Public {

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
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Inject the Betterlytics tracking script.
	 *
	 * @since 1.0.0
	 */
	public function inject_tracking_script() {
		// Debug: Log tracking check.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$options         = Betterlytics_Options::get_options();
			$tracking_status = Betterlytics_Options::is_tracking_enabled() ? 'ENABLED' : 'DISABLED';
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug only.
			error_log( '[Betterlytics] Tracking status: ' . $tracking_status );
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug only.
			error_log( '[Betterlytics] Options: ' . wp_json_encode( $options ) );
		}

		if ( ! Betterlytics_Options::is_tracking_enabled() ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				echo "<!-- Betterlytics: Tracking DISABLED -->\n";
				echo '<!-- Reason: enabled=' . ( $options['enabled'] ? 'true' : 'false' );
				echo ', site_id=' . ( empty( $options['site_id'] ) ? 'EMPTY' : 'set' );
				echo ', track_logged_in=' . ( $options['track_logged_in'] ? 'true' : 'false' );
				echo ', is_user_logged_in=' . ( is_user_logged_in() ? 'true' : 'false' ) . " -->\n";
			}
			return;
		}

		$options    = Betterlytics_Options::get_options();
		$site_id    = esc_attr( $options['site_id'] );
		$server_url = esc_url( $options['server_url'] );
		$script_url = esc_url( $options['script_url'] );

		// Debug: Output configuration as HTML comment.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			echo "<!-- Betterlytics: Tracking ENABLED -->\n";
			echo '<!-- Site ID: ' . esc_html( $site_id ) . " -->\n";
			echo '<!-- Server URL: ' . esc_html( $server_url ) . " -->\n";
			echo '<!-- Script URL: ' . esc_html( $script_url ) . " -->\n";
		}

		// Output async event queue for reliable tracking before script loads.
		?>
<script>
window.betterlytics = window.betterlytics || {
	event: function() {
		(window.betterlytics.q = window.betterlytics.q || []).push(arguments);
	}
};
		<?php if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) : ?>
console.log('[Betterlytics] Script injected', {
	siteId: '<?php echo esc_js( $site_id ); ?>',
	serverUrl: '<?php echo esc_js( $server_url ); ?>',
	scriptUrl: '<?php echo esc_js( $script_url ); ?>'
});
		<?php endif; ?>
</script>
<?php // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -- External analytics script with data attributes cannot use wp_enqueue_script(). ?>
<script async src="<?php echo esc_url( $script_url ); ?>" data-site-id="<?php echo esc_attr( $site_id ); ?>" data-server-url="<?php echo esc_url( $server_url ); ?>"></script>
		<?php
	}

	/**
	 * Enqueue JavaScript for client-side event tracking.
	 *
	 * @since 1.0.3
	 */
	public function enqueue_event_scripts() {
		if ( ! Betterlytics_Options::is_tracking_enabled() ) {
			return;
		}

		$options = Betterlytics_Options::get_options();
		$needs_js = ! empty( $options['track_outbound'] ) || ! empty( $options['track_downloads'] ) || ! empty( $options['track_css_events'] );

		if ( ! $needs_js ) {
			return;
		}

		wp_enqueue_script(
			'betterlytics-events',
			BETTERLYTICS_PLUGIN_URL . 'public/js/betterlytics-events.js',
			array(),
			$this->version,
			true
		);

		wp_localize_script(
			'betterlytics-events',
			'betterlyticsEvents',
			array(
				'trackOutbound'  => ! empty( $options['track_outbound'] ),
				'trackDownloads' => ! empty( $options['track_downloads'] ),
				'trackCssEvents' => ! empty( $options['track_css_events'] ),
			)
		);
	}

	/**
	 * Output queued events as JavaScript.
	 *
	 * @since 1.0.0
	 */
	public function output_queued_events() {
		global $betterlytics_queued_events;

		if ( empty( $betterlytics_queued_events ) ) {
			return;
		}

		echo "<script>\n";
		foreach ( $betterlytics_queued_events as $event ) {
			$name  = esc_js( $event['name'] );
			$props = wp_json_encode( $event['properties'] );
			echo "betterlytics.event('" . esc_js( $name ) . "', " . $props . ");\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo "</script>\n";
	}
}
