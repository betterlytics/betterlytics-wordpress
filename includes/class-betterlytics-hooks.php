<?php
/**
 * WordPress hooks integration for Betterlytics.
 *
 * Maps WordPress actions to Betterlytics events.
 *
 * @package    Betterlytics
 * @subpackage Betterlytics/includes
 * @since      1.0.0
 */

/**
 * WordPress hooks integration for Betterlytics.
 *
 * @since 1.0.0
 */
class Betterlytics_Hooks {

	/**
	 * Built-in hook mappings: option_key => [ wp_hook, event_name ].
	 *
	 * @var array
	 */
	const BUILTIN_HOOKS = [];

	/**
	 * Register all configured WordPress hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_configured_hooks() {
		if ( ! Betterlytics_Options::is_tracking_enabled() ) {
			return;
		}

		$options = Betterlytics_Options::get_options();

		// Track registered hooks to prevent duplicates.
		$registered_hooks = [];

		// Register custom hooks.
		$hooks = isset( $options['hooks'] ) ? $options['hooks'] : [];

		foreach ( $hooks as $hook_config ) {
			if ( ! empty( $hook_config['enabled'] ) ) {
				$this->register_hook( $hook_config['wp_hook'], $hook_config['event_name'] );
				$registered_hooks[] = $hook_config['wp_hook'];
			}
		}

		// Register built-in hooks (if enabled and not already registered).
		foreach ( self::BUILTIN_HOOKS as $option_key => list( $wp_hook, $event_name ) ) {
			$enabled = false;
			if ( isset( $options[ $option_key ] ) ) {
				if ( is_array( $options[ $option_key ] ) && ! empty( $options[ $option_key ]['enabled'] ) ) {
					$enabled = true;
				} elseif ( ! is_array( $options[ $option_key ] ) && ! empty( $options[ $option_key ] ) ) {
					$enabled = true;
				}
			}

			if ( $enabled && ! in_array( $wp_hook, $registered_hooks, true ) ) {
				$this->register_hook( $wp_hook, $event_name );
			}
		}
	}
	/**
	 * Register page-level event hooks (404, search).
	 *
	 * @since 1.0.0
	 */
	public function register_page_hooks() {
		if ( ! Betterlytics_Options::is_tracking_enabled() ) {
			return;
		}
		add_action( 'template_redirect', [ $this, 'track_page_events' ] );
	}

	/**
	 * Track page-level events (404, search).
	 *
	 * @since 1.0.0
	 */
	public function track_page_events() {
		$options = Betterlytics_Options::get_options();

		if ( ! empty( $options['track_404'] ) && is_404() ) {
			global $wp;
			$this->queue_event(
				'404',
				[
					'path' => home_url( $wp->request ),
				]
			);
		}

		if ( ! empty( $options['track_search'] ) && is_search() ) {
			$this->queue_event(
				'search',
				[
					'query' => get_search_query(),
				]
			);
		}
	}

	/**
	 * Register a single WordPress hook.
	 *
	 * @since 1.0.0
	 * @param string $wp_hook    The WordPress hook name.
	 * @param string $event_name The Betterlytics event name.
	 */
	private function register_hook( $wp_hook, $event_name ) {
		add_action(
			$wp_hook,
			function ( ...$args ) use ( $wp_hook, $event_name ) {
				$this->handle_hook_fired( $wp_hook, $event_name, $args );
			},
			10,
			10
		);
	}

	/**
	 * Handle when a configured WordPress hook fires.
	 *
	 * @since 1.0.0
	 * @param string $wp_hook    The WordPress hook that fired.
	 * @param string $event_name The Betterlytics event name.
	 * @param array  $args       The hook arguments.
	 */
	private function handle_hook_fired( $wp_hook, $event_name, $args ) {
		$properties = $this->build_event_properties( $wp_hook, $args );
		$this->queue_event( $event_name, $properties );
	}

	/**
	 * Build event properties based on the WordPress hook and its arguments.
	 *
	 * @since  1.0.0
	 * @param  string $wp_hook The WordPress hook name.
	 * @param  array  $args    The hook arguments.
	 * @return array Event properties.
	 */
	private function build_event_properties( $wp_hook, $args ) {
		$properties = [
			'wp_hook' => $wp_hook,
		];

		// Add custom configured metadata.
		$options = Betterlytics_Options::get_options();
		if ( isset( $options['hooks'] ) ) {
			foreach ( $options['hooks'] as $hook_config ) {
				if ( $hook_config['wp_hook'] === $wp_hook && ! empty( $hook_config['metadata'] ) ) {
					foreach ( $hook_config['metadata'] as $meta ) {
						$properties[ $meta['key'] ] = $this->resolve_value( $meta['value'], $args );
					}
					// If we found the matching config, we can stop searching.
					break;
				}
			}
		}

		// Add hook-specific properties (no PII).
		switch ( $wp_hook ) {
			// Removed wp_login and user_register cases.

			case 'comment_post':
				if ( isset( $args[0] ) ) {
					$properties['comment_id'] = (int) $args[0];
				}
				break;

			case 'wpcf7_mail_sent':
				if ( isset( $args[0] ) && is_object( $args[0] ) && method_exists( $args[0], 'id' ) ) {
					$properties['form_id'] = (int) $args[0]->id();
				}
				break;

			case 'gform_after_submission':
				if ( isset( $args[1]['id'] ) ) {
					$properties['form_id'] = (int) $args[1]['id'];
				}
				break;

			case 'wpforms_process_complete':
				if ( isset( $args[2]['id'] ) ) {
					$properties['form_id'] = (int) $args[2]['id'];
				}
				break;
		}

		return $properties;
	}

	/**
	 * Resolve a value pattern against hook arguments.
	 *
	 * Supports:
	 * - {0} - Argument at index 0
	 * - {0->prop} - Property of object at index 0
	 * - {0[key]} - specific key of array at index 0
	 *
	 * @since 1.0.0
	 * @param string $pattern The pattern string.
	 * @param array  $args    The hook arguments.
	 * @return string Resolved value.
	 */
	private function resolve_value( $pattern, $args ) {
		return preg_replace_callback(
			'/\{(\d+)(?:->([a-zA-Z0-9_]+)|\[([a-zA-Z0-9_]+)\])?\}/',
			function ( $matches ) use ( $args ) {
				$index = (int) $matches[1];
				$value = isset( $args[ $index ] ) ? $args[ $index ] : null;

				if ( ! $value ) {
					return '';
				}

				// Handle property access ->.
				if ( ! empty( $matches[2] ) ) {
					$prop = $matches[2];
					return isset( $value->$prop ) ? $value->$prop : '';
				}

				// Handle array access [].
				if ( ! empty( $matches[3] ) ) {
					$key = $matches[3];
					return isset( $value[ $key ] ) ? $value[ $key ] : '';
				}

				// Return the direct value (if scalar).
				if ( is_scalar( $value ) ) {
					return $value;
				}

				// If it's an object/array but no accessor specified, try to cast to string or nothing.
				if ( is_object( $value ) && method_exists( $value, '__toString' ) ) {
					return (string) $value;
				}

				return '';
			},
			$pattern
		);
	}

	/**
	 * Queue an event for output in the footer.
	 *
	 * @since 1.0.0
	 * @param string $event_name The event name.
	 * @param array  $properties The event properties.
	 */
	private function queue_event( $event_name, $properties ) {
		global $betterlytics_queued_events;

		if ( ! isset( $betterlytics_queued_events ) ) {
			$betterlytics_queued_events = [];
		}

		$betterlytics_queued_events[] = [
			'name'       => $event_name,
			'properties' => $properties,
		];
	}
}
