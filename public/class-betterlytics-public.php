<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @package    Betterlytics
 * @subpackage Betterlytics/public
 * @since      1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Public-facing functionality.
 */
class Betterlytics_Public {

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
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Enqueue the Betterlytics tracking script.
	 *
	 * @since 1.0.0
	 */
	public function inject_tracking_script() {
		$options = Betterlytics_Options::get_options();

		if ( ! Betterlytics_Options::is_tracking_enabled() ) {
			return;
		}

		$script_url = $options['script_url'];

		// Validate script URL before enqueueing.
		if ( empty( $script_url ) || ! filter_var( $script_url, FILTER_VALIDATE_URL ) ) {
			return;
		}

		// Register and enqueue the external tracking script with async strategy (WP 6.3+).
		wp_enqueue_script(
			'betterlytics-tracker',
			$script_url,
			[],
			// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- External script URL; version managed by remote server.
			null,
			[
				'strategy'  => 'async',
				'in_footer' => false,
			]
		);

		// Add data-* attributes via wp_script_attributes filter.
		add_filter( 'wp_script_attributes', [ $this, 'add_tracker_script_attributes' ] );

		// Build inline script for event queue buffer.
		$inline_script = 'window.betterlytics = window.betterlytics || { event: function() { (window.betterlytics.q = window.betterlytics.q || []).push(arguments); } };';

		// Add debug logging if WP_DEBUG is enabled.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$site_id        = esc_js( $options['site_id'] );
			$server_url     = esc_js( $options['server_url'] );
			$script_url_js  = esc_js( $script_url );
			$track_outbound = esc_js( $options['track_outbound']['mode'] );

			$inline_script .= "\nconsole.log('[Betterlytics] Script injected', { siteId: '{$site_id}', serverUrl: '{$server_url}', scriptUrl: '{$script_url_js}', trackOutbound: '{$track_outbound}' });";
		}

		// Add event queue buffer as inline script BEFORE the main script.
		wp_add_inline_script( 'betterlytics-tracker', $inline_script, 'before' );
	}

	/**
	 * Add data attributes to the tracking script tag.
	 *
	 * @since 1.0.0
	 * @param array $attr Script tag attributes.
	 * @return array Modified attributes.
	 */
	public function add_tracker_script_attributes( $attr ) {
		if ( ! isset( $attr['id'] ) || 'betterlytics-tracker-js' !== $attr['id'] ) {
			return $attr;
		}

		$options = Betterlytics_Options::get_options();

		$attr['data-site-id']        = esc_attr( $options['site_id'] );
		$attr['data-server-url']     = esc_url( $options['server_url'] );
		$attr['data-outbound-links'] = esc_attr( $options['track_outbound']['mode'] );
		$attr['data-web-vitals']     = ! empty( $options['track_web_vitals'] ) ? 'true' : 'false';

		return $attr;
	}

	/**
	 * Enqueue JavaScript for client-side event tracking.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_event_scripts() {
		if ( ! Betterlytics_Options::is_tracking_enabled() ) {
			return;
		}

		$options  = Betterlytics_Options::get_options();
		$needs_js = ! empty( $options['track_custom_html_attribute']['enabled'] );

		if ( ! $needs_js ) {
			return;
		}

		wp_enqueue_script(
			'betterlytics-events',
			BETTERLYTICS_PLUGIN_URL . 'public/js/betterlytics-events.js',
			[],
			$this->version,
			true
		);

		wp_localize_script(
			'betterlytics-events',
			'betterlyticsEvents',
			[
				'trackCustomHtmlAttr' => ! empty( $options['track_custom_html_attribute']['enabled'] ),
			]
		);
	}
}
