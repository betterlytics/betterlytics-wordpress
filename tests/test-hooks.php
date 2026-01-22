<?php
/**
 * Tests for Betterlytics_Hooks class.
 *
 * @package Betterlytics
 */

require_once dirname( __FILE__ ) . '/class-betterlytics-test-case.php';

/**
 * Test the Betterlytics_Hooks class.
 */
class Test_Betterlytics_Hooks extends Betterlytics_Test_Case {





	/**
	 * Test 404 page hook registration and event queuing.
	 */
	public function test_404_page_event() {
		global $betterlytics_queued_events, $wp_query, $wp;
		$betterlytics_queued_events = array();

		$this->set_options(
			array(
				'enabled'   => true,
				'site_id'   => 'test-site',
				'track_404' => true,
			)
		);

		$hooks = new Betterlytics_Hooks();
		$hooks->register_page_hooks();

		$this->assertNotFalse( has_action( 'template_redirect' ) );

		// Simulate 404.
		$wp_query->is_404 = true;
		$wp->request      = 'nonprofit-page';

		// Remove default WP redirects which cause "headers already sent" errors in tests.
		remove_action( 'template_redirect', 'redirect_canonical' );
		remove_action( 'template_redirect', 'wp_redirect_admin_locations', 1000 );

		// Output buffering to prevent "headers already sent" if template loads.
		ob_start();
		do_action( 'template_redirect' );
		ob_end_clean();

		$this->assertNotEmpty( $betterlytics_queued_events );
		$this->assertSame( '404', $betterlytics_queued_events[0]['name'] );
		$this->assertStringContainsString( 'nonprofit-page', $betterlytics_queued_events[0]['properties']['path'] );
	}

	/**
	 * Test Search page hook registration and event queuing.
	 */
	public function test_search_page_event() {
		global $betterlytics_queued_events, $wp_query;
		$betterlytics_queued_events = array();

		$this->set_options(
			array(
				'enabled'      => true,
				'site_id'      => 'test-site',
				'track_search' => array(
					'enabled'       => true,
					'include_query' => true,
				),
			)
		);

		$hooks = new Betterlytics_Hooks();
		$hooks->register_page_hooks();

		// Simulate Search.
		$wp_query->is_404    = false;
		$wp_query->is_search = true;
		$wp_query->set( 's', 'my search query' );

		// Remove default WP redirects which cause "headers already sent" errors in tests.
		remove_action( 'template_redirect', 'redirect_canonical' );
		remove_action( 'template_redirect', 'wp_redirect_admin_locations', 1000 );

		// Output buffering to prevent "headers already sent" if template loads.
		ob_start();
		do_action( 'template_redirect' );
		ob_end_clean();

		$this->assertNotEmpty( $betterlytics_queued_events );
		$this->assertSame( 'search', $betterlytics_queued_events[0]['name'] );
		$this->assertSame( 'my search query', $betterlytics_queued_events[0]['properties']['query'] );
	}

	/**
	 * Test Search page event excludes query when include_query is disabled.
	 */
	public function test_search_page_event_without_query() {
		global $betterlytics_queued_events, $wp_query;
		$betterlytics_queued_events = array();

		$this->set_options(
			array(
				'enabled'      => true,
				'site_id'      => 'test-site',
				'track_search' => array(
					'enabled'       => true,
					'include_query' => false,
				),
			)
		);

		$hooks = new Betterlytics_Hooks();
		$hooks->register_page_hooks();

		// Simulate Search.
		$wp_query->is_404    = false;
		$wp_query->is_search = true;
		$wp_query->set( 's', 'my search query' );

		// Remove default WP redirects which cause "headers already sent" errors in tests.
		remove_action( 'template_redirect', 'redirect_canonical' );
		remove_action( 'template_redirect', 'wp_redirect_admin_locations', 1000 );

		// Output buffering to prevent "headers already sent" if template loads.
		ob_start();
		do_action( 'template_redirect' );
		ob_end_clean();

		$this->assertNotEmpty( $betterlytics_queued_events );
		$this->assertSame( 'search', $betterlytics_queued_events[0]['name'] );
		$this->assertArrayNotHasKey( 'query', $betterlytics_queued_events[0]['properties'] );
	}
}
