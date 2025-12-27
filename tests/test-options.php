<?php
/**
 * Tests for Betterlytics_Options class.
 *
 * @package Betterlytics
 */

require_once dirname( __FILE__ ) . '/class-betterlytics-test-case.php';

/**
 * Test the Betterlytics_Options class.
 */
class Test_Betterlytics_Options extends Betterlytics_Test_Case {

	/**
	 * Test that default options are returned when no options are set.
	 */
	public function test_get_options_returns_defaults() {
		$options = Betterlytics_Options::get_options();

		$this->assertIsArray( $options );
		$this->assertArrayHasKey( 'site_id', $options );
		$this->assertArrayHasKey( 'server_url', $options );
		$this->assertArrayHasKey( 'script_url', $options );
		$this->assertArrayHasKey( 'enabled', $options );
		$this->assertArrayHasKey( 'track_logged_in', $options );
		$this->assertArrayHasKey( 'hooks', $options );
		$this->assertArrayHasKey( 'woo_remove_from_cart', $options );

		$this->assertSame( '', $options['site_id'] );
		$this->assertSame( 'https://betterlytics.io/track', $options['server_url'] );
		$this->assertSame( 'https://betterlytics.io/analytics.js', $options['script_url'] );
		$this->assertFalse( $options['enabled'] );
		$this->assertTrue( $options['track_logged_in'] );
		$this->assertSame( array(), $options['hooks'] );
	}

	/**
	 * Test that saved options are returned.
	 */
	public function test_get_options_returns_saved_values() {
		$this->set_options(
			array(
				'site_id' => 'test-site-123',
				'enabled' => true,
			)
		);

		$options = Betterlytics_Options::get_options();

		$this->assertSame( 'test-site-123', $options['site_id'] );
		$this->assertTrue( $options['enabled'] );
	}

	/**
	 * Test getting a specific option.
	 */
	public function test_get_single_option() {
		$this->set_options(
			array(
				'site_id' => 'my-site',
			)
		);

		$this->assertSame( 'my-site', Betterlytics_Options::get( 'site_id' ) );
	}

	/**
	 * Test getting a non-existent option returns default.
	 */
	public function test_get_nonexistent_option_returns_default() {
		$result = Betterlytics_Options::get( 'nonexistent_key', 'default_value' );

		$this->assertSame( 'default_value', $result );
	}

	/**
	 * Test updating options.
	 */
	public function test_update_options() {
		$options = array(
			'site_id'         => 'updated-site',
			'server_url'      => 'https://custom.example.com/track',
			'script_url'      => 'https://custom.example.com/analytics.js',
			'enabled'         => true,
			'track_logged_in' => false,
			'hooks'           => array(),
		);

		$result = Betterlytics_Options::update_options( $options );

		$this->assertTrue( $result );
		$this->assertSame( 'updated-site', Betterlytics_Options::get( 'site_id' ) );
		$this->assertSame( 'https://custom.example.com/track', Betterlytics_Options::get( 'server_url' ) );
	}

	/**
	 * Test setting a single option.
	 */
	public function test_set_single_option() {
		Betterlytics_Options::set( 'site_id', 'single-set-test' );

		$this->assertSame( 'single-set-test', Betterlytics_Options::get( 'site_id' ) );
	}

	/**
	 * Test tracking is disabled when not enabled.
	 */
	public function test_is_tracking_enabled_when_disabled() {
		$this->set_options(
			array(
				'enabled' => false,
				'site_id' => 'test-site',
			)
		);

		$this->assertFalse( Betterlytics_Options::is_tracking_enabled() );
	}

	/**
	 * Test tracking is disabled when site_id is empty.
	 */
	public function test_is_tracking_enabled_without_site_id() {
		$this->set_options(
			array(
				'enabled' => true,
				'site_id' => '',
			)
		);

		$this->assertFalse( Betterlytics_Options::is_tracking_enabled() );
	}

	/**
	 * Test tracking is enabled when properly configured.
	 */
	public function test_is_tracking_enabled_when_configured() {
		$this->set_options(
			array(
				'enabled' => true,
				'site_id' => 'test-site',
			)
		);

		$this->assertTrue( Betterlytics_Options::is_tracking_enabled() );
	}

	/**
	 * Test tracking is disabled for logged-in users when configured.
	 */
	public function test_is_tracking_disabled_for_logged_in_users() {
		$this->set_options(
			array(
				'enabled'         => true,
				'site_id'         => 'test-site',
				'track_logged_in' => false,
			)
		);

		// Create and log in a user.
		$user_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $user_id );

		$this->assertFalse( Betterlytics_Options::is_tracking_enabled() );

		// Log out and verify tracking is enabled.
		wp_set_current_user( 0 );
		$this->assertTrue( Betterlytics_Options::is_tracking_enabled() );
	}
}
