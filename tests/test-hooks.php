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
}
