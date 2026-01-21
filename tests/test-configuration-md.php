<?php
/**
 * Tests for CONFIGURATION.md scenarios.
 *
 * @package Betterlytics
 */

require_once dirname( __FILE__ ) . '/class-betterlytics-test-case.php';

/**
 * Test the scenarios documented in CONFIGURATION.md.
 */
class Test_Configuration_MD extends Betterlytics_Test_Case {

	/**
	 * Test the Full JSON Structure documented in CONFIGURATION.md.
	 */
	public function test_full_json_configuration() {
		// This array mirrors the JSON example in CONFIGURATION.md
		$config = array(
			'site_id'          => 'your-site-id',
			'server_url'       => 'https://betterlytics.io/event',
			'script_url'       => 'https://betterlytics.io/analytics.js',
			'enabled'          => true,
			'track_web_vitals' => false,

			// Browser Events
			'track_404'        => array( 'enabled' => false ),
			'track_search'     => array( 'enabled' => false ),
			'track_outbound'   => array( 'mode' => 'domain' ),
			'track_downloads'  => array( 'enabled' => false ),
			'track_css_events' => array( 'enabled' => false ),
		);

		// Simulate applying this configuration via update_option
		update_option( 'betterlytics_options', $config );

		// Verify the options are stored and retrievable via our class
		$stored = Betterlytics_Options::get_options();

		$this->assertSame( 'your-site-id', $stored['site_id'] );
		$this->assertTrue( $stored['enabled'] );
	}

	/**
	 * Test the WP-CLI single value update scenario.
	 */
	public function test_variable_update_flow() {
		// 1. Initial State
		$initial_config = array(
			'site_id' => 'abc1234',
			'enabled' => true,
		);
		update_option( 'betterlytics_options', $initial_config );

		// 2. "Get" current options (simulating `wp option get`)
		$current = get_option( 'betterlytics_options' );
		$this->assertSame( 'abc1234', $current['site_id'] );

		// 3. Modify one value (simulating `jq`)
		$current['site_id'] = 'new-site-id';

		// 4. Update back (simulating `wp option update`)
		update_option( 'betterlytics_options', $current );

		// Verify result
		$final = Betterlytics_Options::get_options();
		$this->assertSame( 'new-site-id', $final['site_id'] );
		$this->assertTrue( $final['enabled'] );
	}

	/**
	 * Test the Environment Variables Logic (Environment Configuration Pattern).
	 *
	 * This tests the PHP logic provided in the "Environment Variables" section
	 * to ensure it correctly constructs a valid configuration array.
	 */
	public function test_environment_variables_logic() {
		// Simulate variables being available (via local vars instead of getenv for test stability)
		$mock_env = array(
			'BETTERLYTICS_SITE_ID'           => 'env-site-id',
			'BETTERLYTICS_SERVER_URL'        => 'https://env-server.com',
			'BETTERLYTICS_SCRIPT_URL'        => 'https://env-server.com/js',
			'BETTERLYTICS_ENABLED'           => 'true',
			'BETTERLYTICS_TRACK_LOGGED_IN'   => 'true',
		);

		// The logic from CONFIGURATION.md adapted for test
		$config = array(
			'site_id'         => $mock_env['BETTERLYTICS_SITE_ID'],
			'server_url'      => $mock_env['BETTERLYTICS_SERVER_URL'],
			'script_url'      => $mock_env['BETTERLYTICS_SCRIPT_URL'],
			'enabled'         => filter_var( $mock_env['BETTERLYTICS_ENABLED'], FILTER_VALIDATE_BOOLEAN ),
		);

		update_option( 'betterlytics_options', $config );

		$stored = Betterlytics_Options::get_options();
		$this->assertSame( 'env-site-id', $stored['site_id'] );
		$this->assertSame( 'https://env-server.com', $stored['server_url'] );
		$this->assertTrue( $stored['enabled'] );
	}
}
