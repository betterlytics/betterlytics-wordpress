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

	<h2 class="text-[22px] font-bold mb-6 flex items-center gap-2 text-foreground tracking-tight">
		<?php esc_html_e( 'General Settings', 'betterlytics' ); ?>
		<?php echo Betterlytics_Admin_Controller::get_help_link( '', 'is-blue' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</h2>

	<form action="options.php" method="post">
		<?php settings_fields( 'betterlytics_settings' ); ?>

		<div class="betterlytics-table-container">
			<table class="form-table w-full m-0" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<span class="flex items-center gap-1.5">
								<?php esc_html_e( 'Enable Tracking', 'betterlytics' ); ?>
								<?php echo Betterlytics_Admin_Controller::get_help_link( '', 'is-blue' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</span>
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
							<span class="flex items-center gap-1.5">
								<?php esc_html_e( 'Site ID', 'betterlytics' ); ?>
								<?php echo Betterlytics_Admin_Controller::get_help_link( '', 'is-blue' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</span>
						</th>
						<td>
							<div class="relative max-w-[450px]">
								<input type="text" name="betterlytics_options[site_id]" value="<?php echo esc_attr( $betterlytics_options['site_id'] ); ?>" class="regular-text !w-full" placeholder="your-site-id">
							</div>
							<p class="description mt-2 text-xs text-muted-foreground">
								<?php esc_html_e( 'Your unique Site ID from the Betterlytics dashboard.', 'betterlytics' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<span>
								<?php esc_html_e( 'Server URL', 'betterlytics' ); ?>
							</span>
						</th>
						<td>
							<div class="relative max-w-[450px]">
								<input type="url" name="betterlytics_options[server_url]" value="<?php echo esc_attr( $betterlytics_options['server_url'] ); ?>" class="regular-text !w-full" placeholder="https://betterlytics.io/track">
							</div>
							<p class="description mt-2 text-xs text-muted-foreground">
								<?php esc_html_e( 'The tracking server URL. Default is the Betterlytics cloud. Change this if you are self-hosting.', 'betterlytics' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<span>
								<?php esc_html_e( 'Script URL', 'betterlytics' ); ?>
							</span>
						</th>
						<td>
							<div class="relative max-w-[450px]">
								<input type="url" name="betterlytics_options[script_url]" value="<?php echo esc_attr( $betterlytics_options['script_url'] ); ?>" class="regular-text !w-full" placeholder="https://betterlytics.io/analytics.js">
							</div>
							<p class="description mt-2 text-xs text-muted-foreground">
								<?php esc_html_e( 'The tracking script URL. Default is the Betterlytics cloud. Change this if you are self-hosting.', 'betterlytics' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<span>
								<?php esc_html_e( 'Track Logged-in Users', 'betterlytics' ); ?>
							</span>
						</th>
						<td>
							<label>
								<input type="checkbox" name="betterlytics_options[track_logged_in]" value="1" <?php checked( $betterlytics_options['track_logged_in'] ); ?>>
								<?php esc_html_e( 'Track logged-in users (disable to exclude admins/editors from analytics)', 'betterlytics' ); ?>
							</label>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="betterlytics-sticky-footer">
			<?php submit_button( __( 'Save Settings', 'betterlytics' ), 'primary', 'submit', false, [ 'class' => 'button-primary !py-2.5 !px-10 !h-auto !text-base !font-bold' ] ); ?>
		</div>
	</form>
