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
	 * Register all configured WordPress hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_configured_hooks() {
		if ( ! Betterlytics_Options::is_tracking_enabled() ) {
			return;
		}

		$options = Betterlytics_Options::get_options();
		$hooks   = isset( $options['hooks'] ) ? $options['hooks'] : array();

		foreach ( $hooks as $hook_config ) {
			if ( empty( $hook_config['enabled'] ) ) {
				continue;
			}

			$wp_hook    = $hook_config['wp_hook'];
			$event_name = $hook_config['event_name'];

			// Register the WordPress hook.
			add_action(
				$wp_hook,
				function ( ...$args ) use ( $wp_hook, $event_name ) {
					$this->handle_hook_fired( $wp_hook, $event_name, $args );
				},
				10,
				10
			); // Accept up to 10 args.
		}
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
		$properties = array(
			'wp_hook' => $wp_hook,
		);

		// Add hook-specific properties (no PII).
		switch ( $wp_hook ) {
			case 'wp_login':
				$properties['action'] = 'login';
				break;

			case 'user_register':
				$properties['action'] = 'registration';
				break;

			case 'comment_post':
				if ( isset( $args[0] ) ) {
					$properties['comment_id'] = (int) $args[0];
				}
				break;

			case 'woocommerce_thankyou':
				if ( isset( $args[0] ) ) {
					$properties['order_id'] = (int) $args[0];
				}
				break;

			case 'woocommerce_add_to_cart':
				if ( isset( $args[1] ) ) {
					$properties['product_id'] = (int) $args[1];
				}
				if ( isset( $args[2] ) ) {
					$properties['quantity'] = (int) $args[2];
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
	 * Queue an event for output in the footer.
	 *
	 * @since 1.0.0
	 * @param string $event_name The event name.
	 * @param array  $properties The event properties.
	 */
	private function queue_event( $event_name, $properties ) {
		global $betterlytics_queued_events;

		if ( ! isset( $betterlytics_queued_events ) ) {
			$betterlytics_queued_events = array();
		}

		$betterlytics_queued_events[] = array(
			'name'       => $event_name,
			'properties' => $properties,
		);
	}
}
