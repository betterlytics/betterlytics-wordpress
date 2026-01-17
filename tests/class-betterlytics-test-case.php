<?php
/**
 * Base test case class for Betterlytics tests.
 *
 * @package Betterlytics
 */

/**
 * Base test case class.
 */
class Betterlytics_Test_Case extends WP_UnitTestCase {

	/**
	 * Set up test fixtures.
	 */
	public function set_up() {
		parent::set_up();

		// Reset plugin options before each test.
		delete_option( 'betterlytics_options' );
	}

	/**
	 * Tear down test fixtures.
	 */
	public function tear_down() {
		// Clean up after each test.
		delete_option( 'betterlytics_options' );

		parent::tear_down();
	}

	/**
	 * Set plugin options for testing.
	 *
	 * @param array $options Options to set.
	 */
	protected function set_options( $options ) {
		$defaults = array(
			'site_id'         => '',
			'server_url'      => 'https://betterlytics.io/event',
			'script_url'      => 'https://betterlytics.io/analytics.js',
			'enabled'         => false,
			'track_logged_in' => true,
			'hooks'           => array(),
		);

		update_option( 'betterlytics_options', wp_parse_args( $options, $defaults ) );
	}
}
