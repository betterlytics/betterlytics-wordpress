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
	}

	/**
	 * Test that tracking script is not output when disabled.
	 */
	public function test_script_not_output_when_disabled() {
		$this->set_options(
			array(
				'enabled' => false,
				'site_id' => 'test-site',
			)
		);

		ob_start();
		$this->public->inject_tracking_script();
		$output = ob_get_clean();

		$this->assertStringNotContainsString( '<script', $output );
	}

	/**
	 * Test that tracking script is not output without site_id.
	 */
	public function test_script_not_output_without_site_id() {
		$this->set_options(
			array(
				'enabled' => true,
				'site_id' => '',
			)
		);

		ob_start();
		$this->public->inject_tracking_script();
		$output = ob_get_clean();

		$this->assertStringNotContainsString( '<script', $output );
	}

	/**
	 * Test that tracking script is output when properly configured.
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

		ob_start();
		$this->public->inject_tracking_script();
		$output = ob_get_clean();

		// Check that the script contains expected elements.
		$this->assertStringContainsString( 'window.betterlytics', $output );
		$this->assertStringContainsString( 'data-site-id="my-test-site"', $output );
		$this->assertStringContainsString( 'data-server-url="https://analytics.example.com/event"', $output );
		$this->assertStringContainsString( 'src="https://analytics.example.com/script.js"', $output );
		$this->assertStringContainsString( 'async', $output );
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

		ob_start();
		$this->public->inject_tracking_script();
		$output = ob_get_clean();

		// Check for the queue initialization code.
		$this->assertStringContainsString( 'window.betterlytics.q', $output );
		$this->assertStringContainsString( 'push(arguments)', $output );
	}

	/**
	 * Test queued events are output correctly.
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

		ob_start();
		$this->public->output_queued_events();
		$output = ob_get_clean();

		$this->assertStringContainsString( "betterlytics.event('test-event'", $output );
		$this->assertStringContainsString( '"wp_hook":"test_hook"', $output );
		$this->assertStringContainsString( '"value":123', $output );
	}

	/**
	 * Test no output when no queued events.
	 */
	public function test_no_output_when_no_queued_events() {
		global $betterlytics_queued_events;
		$betterlytics_queued_events = array();

		ob_start();
		$this->public->output_queued_events();
		$output = ob_get_clean();

		$this->assertEmpty( $output );
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

		ob_start();
		$this->public->output_queued_events();
		$output = ob_get_clean();

		$this->assertStringContainsString( "betterlytics.event('event-one'", $output );
		$this->assertStringContainsString( "betterlytics.event('event-two'", $output );
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

		ob_start();
		$this->public->inject_tracking_script();
		$output = ob_get_clean();

		$this->assertStringContainsString( 'data-web-vitals="true"', $output );
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

		ob_start();
		$this->public->inject_tracking_script();
		$output = ob_get_clean();

		$this->assertStringContainsString( 'data-web-vitals="false"', $output );
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

		ob_start();
		$this->public->inject_tracking_script();
		$output = ob_get_clean();

		$this->assertStringContainsString( 'data-outbound-links="domain"', $output );
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

		ob_start();
		$this->public->inject_tracking_script();
		$output = ob_get_clean();

		$this->assertStringContainsString( 'data-outbound-links="full"', $output );
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

		ob_start();
		$this->public->inject_tracking_script();
		$output = ob_get_clean();

		$this->assertStringContainsString( 'data-outbound-links="off"', $output );
	}
}
