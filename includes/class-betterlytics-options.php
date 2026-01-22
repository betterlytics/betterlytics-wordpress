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
		'site_id'          => '',
		'server_url'       => 'https://betterlytics.io/event',
		'script_url'       => 'https://betterlytics.io/analytics.js',
		'enabled'          => false,
		'track_web_vitals' => false,
		'track_404'        => [
			'enabled'  => false,
			'metadata' => [],
		],
		'track_search'     => [
			'enabled'     => false,
			'include_url' => false,
			'metadata'    => [],
		],
		'track_outbound'   => [
			'mode'     => 'domain',
			'metadata' => [],
		],
		'track_downloads'  => [
			'enabled'  => false,
			'metadata' => [],
		],
		'track_css_events' => [
			'enabled'  => false,
			'metadata' => [],
		],
	];

	/**
	 * Get all plugin options.
	 *
	 * @since  1.0.0
	 * @return array Plugin options merged with defaults.
	 */
	public static function get_options() {
		$options = get_option( self::OPTION_NAME, [] );
		$options = wp_parse_args( $options, self::DEFAULTS );

		// Normalize legacy boolean values to object format.
		$event_keys = [
			'track_404',
			'track_search',
			'track_downloads',
			'track_css_events',
		];

		foreach ( $event_keys as $key ) {
			if ( isset( $options[ $key ] ) && ! is_array( $options[ $key ] ) ) {
				$options[ $key ] = [
					'enabled'  => (bool) $options[ $key ],
					'metadata' => [],
				];
			}
		}

		return $options;
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

		if ( ! $options['enabled'] || empty( $options['site_id'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if setup is complete (site ID configured and enabled).
	 *
	 * @since  1.0.0
	 * @return bool True if setup is complete.
	 */
	public static function is_setup_complete() {
		$options = self::get_options();
		return ! empty( $options['site_id'] ) && ! empty( $options['enabled'] );
	}
}
