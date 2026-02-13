<?php
/**
 * Tests for Betterlytics_Public class.
 *
 * @package Betterlytics
 */

require_once dirname( __FILE__ ) . '/class-betterlytics-test-case.php';

/**
 * Test the Betterlytics_Public class.
 */
class Test_Betterlytics_Public extends Betterlytics_Test_Case {

	/**
	 * The public class instance.
	 *
	 * @var Betterlytics_Public
	 */
	private $public;

	/**
	 * Set up test fixtures.
	 */
	public function set_up() {
		parent::set_up();
		$this->public = new Betterlytics_Public( 'betterlytics', '1.0.0' );

		// Reset WordPress scripts for each test.
		global $wp_scripts;
		$wp_scripts = null;
	}

	/**
	 * Test that tracking script is not enqueued when disabled.
	 */
	public function test_script_not_output_when_disabled() {
		$this->set_options(
			array(
				'enabled' => false,
				'site_id' => 'test-site',
			)
		);

		$this->public->inject_tracking_script();

		$this->assertFalse( wp_script_is( 'betterlytics-tracker', 'enqueued' ) );
	}

	/**
	 * Test that tracking script is not enqueued without site_id.
	 */
	public function test_script_not_output_without_site_id() {
		$this->set_options(
			array(
				'enabled' => true,
				'site_id' => '',
			)
		);

		$this->public->inject_tracking_script();

		$this->assertFalse( wp_script_is( 'betterlytics-tracker', 'enqueued' ) );
	}

	/**
	 * Test that tracking script is enqueued when properly configured.
	 */
	public function test_script_output_when_configured() {
		$this->set_options(
			array(
				'enabled'    => true,
				'site_id'    => 'my-test-site',
				'server_url' => 'https://analytics.example.com/event',
				'script_url' => 'https://analytics.example.com/script.js',
			)
		);

		$this->public->inject_tracking_script();

		// Check that the script is enqueued.
		$this->assertTrue( wp_script_is( 'betterlytics-tracker', 'enqueued' ) );

		// Check the script URL.
		global $wp_scripts;
		$this->assertEquals(
			'https://analytics.example.com/script.js',
			$wp_scripts->registered['betterlytics-tracker']->src
		);

		// Check inline script before.
		$inline_before = $wp_scripts->get_data( 'betterlytics-tracker', 'before' );
		$this->assertNotEmpty( $inline_before );
		$this->assertStringContainsString( 'window.betterlytics', implode( '', $inline_before ) );
	}

	/**
	 * Test that the event queue is properly initialized.
	 */
	public function test_event_queue_initialized() {
		$this->set_options(
			array(
				'enabled' => true,
				'site_id' => 'test-site',
			)
		);

		$this->public->inject_tracking_script();

		global $wp_scripts;
		$inline_before = $wp_scripts->get_data( 'betterlytics-tracker', 'before' );
		$inline_script = implode( '', $inline_before );

		// Check for the queue initialization code.
		$this->assertStringContainsString( 'window.betterlytics.q', $inline_script );
		$this->assertStringContainsString( 'push(arguments)', $inline_script );
	}

	/**
	 * Test queued events are added as inline script.
	 */
	public function test_queued_events_output() {
		global $betterlytics_queued_events;
		$betterlytics_queued_events = array(
			array(
				'name'       => 'test-event',
				'properties' => array(
					'wp_hook' => 'test_hook',
					'value'   => 123,
				),
			),
		);

		// First enqueue the tracker script.
		$this->set_options(
			array(
				'enabled' => true,
				'site_id' => 'test-site',
			)
		);
		$this->public->inject_tracking_script();

		// Now output queued events.
		$this->public->output_queued_events();

		global $wp_scripts;
		$inline_after = $wp_scripts->get_data( 'betterlytics-tracker', 'after' );
		$inline_script = implode( '', $inline_after );

		$this->assertStringContainsString( "betterlytics.event('test-event'", $inline_script );
		$this->assertStringContainsString( '"wp_hook":"test_hook"', $inline_script );
		$this->assertStringContainsString( '"value":123', $inline_script );
	}

	/**
	 * Test no inline script added when no queued events.
	 */
	public function test_no_output_when_no_queued_events() {
		global $betterlytics_queued_events;
		$betterlytics_queued_events = array();

		// First enqueue the tracker script.
		$this->set_options(
			array(
				'enabled' => true,
				'site_id' => 'test-site',
			)
		);
		$this->public->inject_tracking_script();

		// Now output queued events (should do nothing).
		$this->public->output_queued_events();

		global $wp_scripts;
		$inline_after = $wp_scripts->get_data( 'betterlytics-tracker', 'after' );

		$this->assertEmpty( $inline_after );
	}

	/**
	 * Test multiple queued events are output.
	 */
	public function test_multiple_queued_events_output() {
		global $betterlytics_queued_events;
		$betterlytics_queued_events = array(
			array(
				'name'       => 'event-one',
				'properties' => array( 'key' => 'value1' ),
			),
			array(
				'name'       => 'event-two',
				'properties' => array( 'key' => 'value2' ),
			),
		);

		// First enqueue the tracker script.
		$this->set_options(
			array(
				'enabled' => true,
				'site_id' => 'test-site',
			)
		);
		$this->public->inject_tracking_script();

		// Now output queued events.
		$this->public->output_queued_events();

		global $wp_scripts;
		$inline_after = $wp_scripts->get_data( 'betterlytics-tracker', 'after' );
		$inline_script = implode( '', $inline_after );

		$this->assertStringContainsString( "betterlytics.event('event-one'", $inline_script );
		$this->assertStringContainsString( "betterlytics.event('event-two'", $inline_script );
	}

	/**
	 * Test that web vitals attribute is output correctly when enabled.
	 */
	public function test_web_vitals_enabled() {
		$this->set_options(
			array(
				'enabled'          => true,
				'site_id'          => 'test-site',
				'track_web_vitals' => true,
			)
		);

		$attr   = array(
			'id'  => 'betterlytics-tracker-js',
			'src' => 'https://analytics.example.com/script.js',
		);
		$result = $this->public->add_tracker_script_attributes( $attr );

		$this->assertEquals( 'true', $result['data-web-vitals'] );
	}

	/**
	 * Test that web vitals attribute is output correctly when disabled.
	 */
	public function test_web_vitals_disabled() {
		$this->set_options(
			array(
				'enabled'          => true,
				'site_id'          => 'test-site',
				'track_web_vitals' => false,
			)
		);

		$attr   = array(
			'id'  => 'betterlytics-tracker-js',
			'src' => 'https://analytics.example.com/script.js',
		);
		$result = $this->public->add_tracker_script_attributes( $attr );

		$this->assertEquals( 'false', $result['data-web-vitals'] );
	}

	/**
	 * Test track_outbound mode 'domain' outputs correctly.
	 */
	public function test_outbound_links_domain_mode() {
		$this->set_options(
			array(
				'enabled'        => true,
				'site_id'        => 'test-site',
				'track_outbound' => array( 'mode' => 'domain' ),
			)
		);

		$attr   = array(
			'id'  => 'betterlytics-tracker-js',
			'src' => 'https://analytics.example.com/script.js',
		);
		$result = $this->public->add_tracker_script_attributes( $attr );

		$this->assertEquals( 'domain', $result['data-outbound-links'] );
	}

	/**
	 * Test track_outbound mode 'full' outputs correctly.
	 */
	public function test_outbound_links_full_mode() {
		$this->set_options(
			array(
				'enabled'        => true,
				'site_id'        => 'test-site',
				'track_outbound' => array( 'mode' => 'full' ),
			)
		);

		$attr   = array(
			'id'  => 'betterlytics-tracker-js',
			'src' => 'https://analytics.example.com/script.js',
		);
		$result = $this->public->add_tracker_script_attributes( $attr );

		$this->assertEquals( 'full', $result['data-outbound-links'] );
	}

	/**
	 * Test track_outbound mode 'off' outputs correctly.
	 */
	public function test_outbound_links_off_mode() {
		$this->set_options(
			array(
				'enabled'        => true,
				'site_id'        => 'test-site',
				'track_outbound' => array( 'mode' => 'off' ),
			)
		);

		$attr   = array(
			'id'  => 'betterlytics-tracker-js',
			'src' => 'https://analytics.example.com/script.js',
		);
		$result = $this->public->add_tracker_script_attributes( $attr );

		$this->assertEquals( 'off', $result['data-outbound-links'] );
	}

	/**
	 * Test that filter doesn't modify other script handles.
	 */
	public function test_filter_ignores_other_handles() {
		$this->set_options(
			array(
				'enabled' => true,
				'site_id' => 'test-site',
			)
		);

		$attr   = array(
			'id'  => 'other-script-js',
			'src' => 'https://example.com/other.js',
		);
		$result = $this->public->add_tracker_script_attributes( $attr );

		// Should return unchanged — no data-* keys added.
		$this->assertArrayNotHasKey( 'data-site-id', $result );
		$this->assertEquals( $attr, $result );
	}
}
