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

		if ( ! empty( $options['track_404']['enabled'] ) && is_404() ) {
			global $wp;
			$this->queue_event(
				'404',
				[
					'path' => home_url( $wp->request ),
				]
			);
		}

		if ( ! empty( $options['track_search']['enabled'] ) && is_search() ) {
			$properties = [
				'query' => get_search_query(),
			];

			// Optionally include the search URL.
			if ( ! empty( $options['track_search']['include_url'] ) ) {
				global $wp;
				$properties['url'] = home_url( $wp->request ) . '?' . $wp->query_string;
			}

			$this->queue_event( 'search', $properties );
		}
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
