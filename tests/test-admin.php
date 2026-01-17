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
			'server_url' => 'https://betterlytics.io/track',
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
	 * Test sanitize_hooks removes empty hooks.
	 */
	public function test_sanitize_hooks_removes_empty() {
		$hooks = array(
			array(
				'wp_hook'    => 'valid_hook',
				'event_name' => 'valid-event',
				'enabled'    => true,
			),
			array(
				'wp_hook'    => '',
				'event_name' => 'no-hook',
				'enabled'    => true,
			),
			array(
				'wp_hook'    => 'no_event',
				'event_name' => '',
				'enabled'    => true,
			),
		);

		$result = $this->admin->sanitize_hooks( $hooks );

		$this->assertCount( 1, $result );
		$this->assertSame( 'valid_hook', $result[0]['wp_hook'] );
	}

	/**
	 * Test sanitize_hooks sanitizes values.
	 */
	public function test_sanitize_hooks_sanitizes_values() {
		$hooks = array(
			array(
				'wp_hook'    => 'hook<script>alert(1)</script>',
				'event_name' => '<b>event</b>',
				'enabled'    => '1',
			),
		);

		$result = $this->admin->sanitize_hooks( $hooks );

		$this->assertSame( 'hook', $result[0]['wp_hook'] );
		$this->assertSame( 'event', $result[0]['event_name'] );
		$this->assertTrue( $result[0]['enabled'] );
	}


}
