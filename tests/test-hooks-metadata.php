<?php
/**
 * Tests for Betterlytics_Hooks (metadata) class.
 *
 * @package Betterlytics
 */

require_once dirname( __FILE__ ) . '/class-betterlytics-test-case.php';

/**
 * Test the Betterlytics_Hooks class.
 */
class Test_Betterlytics_Hooks_Metadata extends Betterlytics_Test_Case {

	/**
	 * Test that event metadata is correctly resolved from hook arguments.
	 */
	public function test_hook_metadata_resolution() {
		global $betterlytics_queued_events;
		$betterlytics_queued_events = array();

		$this->set_options(
			array(
				'enabled' => true,
				'site_id' => 'test-site',
				'hooks'   => array(
					array(
						'wp_hook'    => 'betterlytics_meta_test_hook',
						'event_name' => 'meta-event',
						'enabled'    => true,
						'metadata'   => array(
							array(
								'key'   => 'arg_zero',
								'value' => '{0}',
							),
							array(
								'key'   => 'obj_prop',
								'value' => '{1->user_email}',
							),
							array(
								'key'   => 'arr_key',
								'value' => '{2[status]}',
							),
						),
					),
				),
			)
		);

		$hooks = new Betterlytics_Hooks();
		$hooks->register_configured_hooks();

		$user = new stdClass();
		$user->user_email = 'test@example.com';
		
		$data = array( 'status' => 'active' );

		// Fire the hook with: 0 -> string, 1 -> object, 2 -> array.
		do_action( 'betterlytics_meta_test_hook', 'simple_string', $user, $data );

		$this->assertNotEmpty( $betterlytics_queued_events );
		$props = $betterlytics_queued_events[0]['properties'];
		
		$this->assertSame( 'simple_string', $props['arg_zero'] );
		$this->assertSame( 'test@example.com', $props['obj_prop'] );
		$this->assertSame( 'active', $props['arr_key'] );
	}
	
	/**
	 * Test that missing arguments resolve to empty strings.
	 */
	public function test_metadata_missing_args() {
		global $betterlytics_queued_events;
		$betterlytics_queued_events = array();

		$this->set_options(
			array(
				'enabled' => true,
				'site_id' => 'test-site',
				'hooks'   => array(
					array(
						'wp_hook'    => 'betterlytics_missing_arg_hook',
						'event_name' => 'missing-arg-event',
						'enabled'    => true,
						'metadata'   => array(
							array(
								'key'   => 'missing_index',
								'value' => '{5}',
							),
							array(
								'key'   => 'missing_prop',
								'value' => '{0->missing_prop}',
							),
						),
					),
				),
			)
		);

		$hooks = new Betterlytics_Hooks();
		$hooks->register_configured_hooks();

		$obj = new stdClass();

		do_action( 'betterlytics_missing_arg_hook', $obj );

		$this->assertNotEmpty( $betterlytics_queued_events );
		$props = $betterlytics_queued_events[0]['properties'];
		
		$this->assertSame( '', $props['missing_index'] );
		$this->assertSame( '', $props['missing_prop'] );
	}
}
