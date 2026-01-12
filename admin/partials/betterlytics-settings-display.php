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

$betterlytics_options = Betterlytics_Options::get_options();
?>

	<h1>
		<?php esc_html_e( 'General Settings', 'betterlytics' ); ?>
		<?php echo Betterlytics_Admin_Controller::get_help_link( '', 'is-blue' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</h1>

	<form action="options.php" method="post">
		<?php settings_fields( 'betterlytics_settings' ); ?>

		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row">
						<?php esc_html_e( 'Enable Tracking', 'betterlytics' ); ?>
						<?php echo Betterlytics_Admin_Controller::get_help_link( '', 'is-blue' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</th>
					<td>
						<label>
							<input type="checkbox" name="betterlytics_options[enabled]" value="1" <?php checked( $betterlytics_options['enabled'] ); ?>>
							<?php esc_html_e( 'Enable Betterlytics tracking on this site', 'betterlytics' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php esc_html_e( 'Site ID', 'betterlytics' ); ?>
						<?php echo Betterlytics_Admin_Controller::get_help_link( '', 'is-blue' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</th>
					<td>
						<input type="text" name="betterlytics_options[site_id]" value="<?php echo esc_attr( $betterlytics_options['site_id'] ); ?>" class="regular-text" placeholder="your-site-id">
						<p class="description">
							<?php esc_html_e( 'Your unique Site ID from the Betterlytics dashboard.', 'betterlytics' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Server URL', 'betterlytics' ); ?></th>
					<td>
						<input type="url" name="betterlytics_options[server_url]" value="<?php echo esc_attr( $betterlytics_options['server_url'] ); ?>" class="regular-text" placeholder="https://betterlytics.io/track">
						<p class="description">
							<?php esc_html_e( 'The tracking server URL. Default is the Betterlytics cloud. Change this if you are self-hosting.', 'betterlytics' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Script URL', 'betterlytics' ); ?></th>
					<td>
						<input type="url" name="betterlytics_options[script_url]" value="<?php echo esc_attr( $betterlytics_options['script_url'] ); ?>" class="regular-text" placeholder="https://betterlytics.io/analytics.js">
						<p class="description">
							<?php esc_html_e( 'The tracking script URL. Default is the Betterlytics cloud. Change this if you are self-hosting.', 'betterlytics' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Track Logged-in Users', 'betterlytics' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="betterlytics_options[track_logged_in]" value="1" <?php checked( $betterlytics_options['track_logged_in'] ); ?>>
							<?php esc_html_e( 'Track logged-in users (disable to exclude admins/editors from analytics)', 'betterlytics' ); ?>
						</label>
					</td>
				</tr>
			</tbody>
		</table>

		<?php submit_button( __( 'Save Settings', 'betterlytics' ) ); ?>
	</form>
