<?php
/**
 * Tests for Betterlytics_Admin class.
 *
 * @package Betterlytics
 */

require_once dirname( __FILE__ ) . '/class-betterlytics-test-case.php';

/**
 * Test the Betterlytics_Admin class.
 */
class Test_Betterlytics_Admin extends Betterlytics_Test_Case {

	/**
	 * The admin class instance.
	 *
	 * @var Betterlytics_Admin
	 */
	private $admin;

	/**
	 * Set up test fixtures.
	 */
	public function set_up() {
		parent::set_up();
		$this->admin = new Betterlytics_Admin( 'betterlytics', '1.0.0' );
		$_POST['option_page'] = 'betterlytics_settings';
	}

	/**
	 * Test sanitize_options sanitizes site_id.
	 */
	public function test_sanitize_options_site_id() {
		$input = array(
			'site_id'    => '  test-site<script>alert(1)</script>  ',
			'enabled'    => true,
			'server_url' => 'https://betterlytics.io/event',
			'script_url' => 'https://betterlytics.io/analytics.js',
		);

		$result = $this->admin->sanitize_options( $input );

		$this->assertSame( 'test-site', $result['site_id'] );
	}

	/**
	 * Test sanitize_options sanitizes URLs.
	 */
	public function test_sanitize_options_urls() {
		$input = array(
			'site_id'    => 'test',
			'enabled'    => true,
			'server_url' => 'javascript:alert(1)',
			'script_url' => 'https://valid.example.com/script.js',
		);

		$result = $this->admin->sanitize_options( $input );

		// Invalid URL should be sanitized.
		$this->assertSame( '', $result['server_url'] );
		$this->assertSame( 'https://valid.example.com/script.js', $result['script_url'] );
	}

	/**
	 * Test sanitize_options handles enabled checkbox.
	 */
	public function test_sanitize_options_enabled_checkbox() {
		$input_enabled = array(
			'enabled' => '1',
		);

		$input_disabled = array();

		$result_enabled  = $this->admin->sanitize_options( $input_enabled );
		$result_disabled = $this->admin->sanitize_options( $input_disabled );

		$this->assertTrue( $result_enabled['enabled'] );
		$this->assertFalse( $result_disabled['enabled'] );
	}

	/**
	 * Test sanitize_options preserves valid track_outbound modes.
	 */
	public function test_sanitize_options_track_outbound_valid_modes() {
		$valid_modes = array( 'off', 'domain', 'full' );

		foreach ( $valid_modes as $mode ) {
			$input = array(
				'track_outbound' => array( 'mode' => $mode ),
			);

			$result = $this->admin->sanitize_options( $input );

			$this->assertSame( $mode, $result['track_outbound']['mode'] );
		}
	}

	/**
	 * Test sanitize_options falls back to default for invalid track_outbound mode.
	 */
	public function test_sanitize_options_track_outbound_invalid_mode() {
		$input = array(
			'track_outbound' => array( 'mode' => 'invalid_mode' ),
		);

		$result = $this->admin->sanitize_options( $input );

		// Should fall back to default 'domain' mode.
		$this->assertSame( 'domain', $result['track_outbound']['mode'] );
	}

	/**
	 * Test sanitize_options handles track_web_vitals checkbox.
	 */
	public function test_sanitize_options_web_vitals_checkbox() {
		$input_enabled = array(
			'track_web_vitals' => '1',
		);

		$input_disabled = array();

		$result_enabled  = $this->admin->sanitize_options( $input_enabled );
		$result_disabled = $this->admin->sanitize_options( $input_disabled );

		$this->assertTrue( $result_enabled['track_web_vitals'] );
		$this->assertFalse( $result_disabled['track_web_vitals'] );
	}
}
