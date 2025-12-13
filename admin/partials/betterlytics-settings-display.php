<?php
/**
 * Settings page view for the plugin.
 *
 * @package    Betterlytics
 * @subpackage Betterlytics/admin/partials
 * @since      1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<div class="wrap betterlytics-settings">
	<h1><?php esc_html_e( 'Settings', 'betterlytics' ); ?></h1>

	<form action="options.php" method="post">
		<?php
		settings_fields( 'betterlytics_settings' );
		do_settings_sections( 'betterlytics-settings' );
		submit_button( __( 'Save Settings', 'betterlytics' ) );
		?>
	</form>
</div>
