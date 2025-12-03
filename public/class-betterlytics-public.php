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
		if ( ! Betterlytics_Options::is_tracking_enabled() ) {
			return;
		}

		$options    = Betterlytics_Options::get_options();
		$site_id    = esc_attr( $options['site_id'] );
		$server_url = esc_url( $options['server_url'] );
		$script_url = esc_url( $options['script_url'] );

		// Output async event queue for reliable tracking before script loads.
		?>
<script>
window.betterlytics = window.betterlytics || {
	event: function() {
		(window.betterlytics.q = window.betterlytics.q || []).push(arguments);
	}
};
</script>
<?php // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -- External analytics script with data attributes cannot use wp_enqueue_script(). ?>
<script async src="<?php echo esc_url( $script_url ); ?>" data-site-id="<?php echo esc_attr( $site_id ); ?>" data-server-url="<?php echo esc_url( $server_url ); ?>"></script>
		<?php
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
