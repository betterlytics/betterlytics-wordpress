<?php
/**
 * Plugin options handler.
 *
 * @package    Betterlytics
 * @subpackage Betterlytics/includes
 * @since      1.0.0
 */

/**
 * Plugin options handler.
 *
 * Provides static methods for getting and updating plugin options.
 *
 * @since 1.0.0
 */
class Betterlytics_Options {

	/**
	 * Option name in the database.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'betterlytics_options';

	/**
	 * Default option values.
	 *
	 * @var array
	 */
	const DEFAULTS = [
		'site_id'         => '',
		'server_url'      => 'https://betterlytics.io/track',
		'script_url'      => 'https://betterlytics.io/analytics.js',
		'enabled'         => false,
		'track_logged_in' => true,
		'hooks'           => [],

		// Event tracking options.
		'track_404'        => false,
		'track_search'     => false,
		'track_outbound'   => false,
		'track_downloads'  => false,
		'track_css_events' => false,

		// WooCommerce options.
		'woo_add_to_cart' => false,
		'woo_checkout'    => false,
		'woo_purchase'    => false,
	];

	/**
	 * Get all plugin options.
	 *
	 * @since  1.0.0
	 * @return array Plugin options merged with defaults.
	 */
	public static function get_options() {
		$options = get_option( self::OPTION_NAME, [] );
		return wp_parse_args( $options, self::DEFAULTS );
	}

	/**
	 * Get a specific option value.
	 *
	 * @since  1.0.0
	 * @param  string $key           The option key.
	 * @param  mixed  $default_value Optional. Default value if key doesn't exist.
	 * @return mixed The option value.
	 */
	public static function get( $key, $default_value = null ) {
		$options = self::get_options();

		if ( isset( $options[ $key ] ) ) {
			return $options[ $key ];
		}

		if ( null !== $default_value ) {
			return $default_value;
		}

		return isset( self::DEFAULTS[ $key ] ) ? self::DEFAULTS[ $key ] : null;
	}

	/**
	 * Update plugin options.
	 *
	 * @since  1.0.0
	 * @param  array $options The options to update.
	 * @return bool True if updated, false otherwise.
	 */
	public static function update_options( $options ) {
		return update_option( self::OPTION_NAME, $options );
	}

	/**
	 * Update a specific option value.
	 *
	 * @since  1.0.0
	 * @param  string $key   The option key.
	 * @param  mixed  $value The option value.
	 * @return bool True if updated, false otherwise.
	 */
	public static function set( $key, $value ) {
		$options         = self::get_options();
		$options[ $key ] = $value;
		return self::update_options( $options );
	}

	/**
	 * Check if tracking is enabled and properly configured.
	 *
	 * @since  1.0.0
	 * @return bool True if tracking should be active.
	 */
	public static function is_tracking_enabled() {
		$options = self::get_options();

		// Must be enabled and have a site ID.
		if ( ! $options['enabled'] || empty( $options['site_id'] ) ) {
			return false;
		}

		// Check if we should track logged-in users.
		if ( ! $options['track_logged_in'] && is_user_logged_in() ) {
			return false;
		}

		return true;
	}
}
