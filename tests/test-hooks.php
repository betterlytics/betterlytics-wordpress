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
	 * Test that hooks are not registered when tracking is disabled.
	 */
	public function test_hooks_not_registered_when_disabled() {
		$this->set_options(
			array(
				'enabled' => false,
				'site_id' => 'test-site',
				'hooks'   => array(
					array(
						'wp_hook'    => 'test_hook',
						'event_name' => 'test-event',
						'enabled'    => true,
					),
				),
			)
		);

		$hooks = new Betterlytics_Hooks();
		$hooks->register_configured_hooks();

		// The hook should not be registered.
		$this->assertFalse( has_action( 'test_hook' ) );
	}

	/**
	 * Test that disabled hooks are not registered.
	 */
	public function test_disabled_hooks_not_registered() {
		$this->set_options(
			array(
				'enabled' => true,
				'site_id' => 'test-site',
				'hooks'   => array(
					array(
						'wp_hook'    => 'test_disabled_hook',
						'event_name' => 'test-disabled-event',
						'enabled'    => false,
					),
				),
			)
		);

		$hooks = new Betterlytics_Hooks();
		$hooks->register_configured_hooks();

		// The hook should not be registered.
		$this->assertFalse( has_action( 'test_disabled_hook' ) );
	}

	/**
	 * Test that enabled hooks are registered.
	 */
	public function test_enabled_hooks_are_registered() {
		$this->set_options(
			array(
				'enabled' => true,
				'site_id' => 'test-site',
				'hooks'   => array(
					array(
						'wp_hook'    => 'betterlytics_test_hook',
						'event_name' => 'test-enabled-event',
						'enabled'    => true,
					),
				),
			)
		);

		$hooks = new Betterlytics_Hooks();
		$hooks->register_configured_hooks();

		// The hook should be registered.
		$this->assertNotFalse( has_action( 'betterlytics_test_hook' ) );
	}

	/**
	 * Test that events are queued when hooks fire.
	 */
	public function test_events_are_queued() {
		global $betterlytics_queued_events;
		$betterlytics_queued_events = array();

		$this->set_options(
			array(
				'enabled' => true,
				'site_id' => 'test-site',
				'hooks'   => array(
					array(
						'wp_hook'    => 'betterlytics_queue_test_hook',
						'event_name' => 'queued-event',
						'enabled'    => true,
					),
				),
			)
		);

		$hooks = new Betterlytics_Hooks();
		$hooks->register_configured_hooks();

		// Fire the hook.
		do_action( 'betterlytics_queue_test_hook' );

		// Check that the event was queued.
		$this->assertNotEmpty( $betterlytics_queued_events );
		$this->assertCount( 1, $betterlytics_queued_events );
		$this->assertSame( 'queued-event', $betterlytics_queued_events[0]['name'] );
		$this->assertSame( 'betterlytics_queue_test_hook', $betterlytics_queued_events[0]['properties']['wp_hook'] );
	}

	/**
	 * Test multiple hooks can be registered.
	 */
	public function test_multiple_hooks_registered() {
		$this->set_options(
			array(
				'enabled' => true,
				'site_id' => 'test-site',
				'hooks'   => array(
					array(
						'wp_hook'    => 'betterlytics_multi_test_1',
						'event_name' => 'event-1',
						'enabled'    => true,
					),
					array(
						'wp_hook'    => 'betterlytics_multi_test_2',
						'event_name' => 'event-2',
						'enabled'    => true,
					),
				),
			)
		);

		$hooks = new Betterlytics_Hooks();
		$hooks->register_configured_hooks();

		$this->assertNotFalse( has_action( 'betterlytics_multi_test_1' ) );
		$this->assertNotFalse( has_action( 'betterlytics_multi_test_2' ) );
	}

	/**
	 * Test WooCommerce Remove from Cart hook registration.
	 */
	public function test_woo_remove_from_cart_hook_registered() {
		$this->set_options(
			array(
				'enabled'              => true,
				'site_id'              => 'test-site',
				'woo_remove_from_cart' => true,
			)
		);

		$hooks = new Betterlytics_Hooks();
		$hooks->register_configured_hooks();

		$this->assertNotFalse( has_action( 'woocommerce_cart_item_removed' ) );
	}

	/**
	 * Test WooCommerce Remove from Cart event queuing.
	 */
	public function test_woo_remove_from_cart_event_queued() {
		global $betterlytics_queued_events;
		$betterlytics_queued_events = array();

		$this->set_options(
			array(
				'enabled'              => true,
				'site_id'              => 'test-site',
				'woo_remove_from_cart' => true,
			)
		);

		$hooks = new Betterlytics_Hooks();
		$hooks->register_configured_hooks();

		// Fire hook with cart item key.
		do_action( 'woocommerce_cart_item_removed', 'key_123' );

		$this->assertNotEmpty( $betterlytics_queued_events );
		$this->assertSame( 'remove-from-cart', $betterlytics_queued_events[0]['name'] );
		$this->assertSame( 'key_123', $betterlytics_queued_events[0]['properties']['cart_item_key'] );
	}

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

		do_action( 'template_redirect' );

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
				'track_search' => true,
			)
		);

		$hooks = new Betterlytics_Hooks();
		$hooks->register_page_hooks();

		// Simulate Search.
		$wp_query->is_404    = false;
		$wp_query->is_search = true;
		$wp_query->set( 's', 'my search query' );

		do_action( 'template_redirect' );

		$this->assertNotEmpty( $betterlytics_queued_events );
		$this->assertSame( 'search', $betterlytics_queued_events[0]['name'] );
		$this->assertSame( 'my search query', $betterlytics_queued_events[0]['properties']['query'] );
	}
}
