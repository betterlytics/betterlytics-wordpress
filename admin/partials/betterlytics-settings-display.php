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

$betterlytics_options           = Betterlytics_Options::get_options();
$betterlytics_setup_incomplete  = empty( $betterlytics_options['site_id'] ) || empty( $betterlytics_options['enabled'] );
$betterlytics_banner_dismissed  = Betterlytics_Admin::is_setup_banner_dismissed();
$betterlytics_show_setup_banner = $betterlytics_setup_incomplete && ! $betterlytics_banner_dismissed;
?>

<?php if ( $betterlytics_show_setup_banner ) : ?>
<div class="betterlytics-setup-banner" id="betterlytics-setup-banner">
	<span class="dashicons dashicons-info"></span>
	<p>
		<?php
		printf(
			/* translators: %s: link to setup guide */
			esc_html__( 'Setup is not complete. Follow the %s to start tracking.', 'betterlytics' ),
			'<a href="' . esc_url( admin_url( 'admin.php?page=betterlytics' ) ) . '">' . esc_html__( 'setup guide', 'betterlytics' ) . '</a>'
		);
		?>
	</p>
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=betterlytics' ) ); ?>" class="button">
		<?php esc_html_e( 'View Setup', 'betterlytics' ); ?>
	</a>
	<button type="button" class="dismiss" id="betterlytics-dismiss-banner" title="<?php esc_attr_e( 'Dismiss', 'betterlytics' ); ?>">
		<span class="dashicons dashicons-no-alt"></span>
	</button>
</div>
<script>
document.getElementById('betterlytics-dismiss-banner').addEventListener('click', function() {
	var banner = document.getElementById('betterlytics-setup-banner');
	banner.style.display = 'none';
	var xhr = new XMLHttpRequest();
	xhr.open('POST', '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>');
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhr.send('action=betterlytics_dismiss_setup_banner&nonce=<?php echo esc_js( wp_create_nonce( 'betterlytics_admin' ) ); ?>');
});
</script>
<?php endif; ?>

<div class="wrap betterlytics-settings">
	<h1><?php esc_html_e( 'Settings', 'betterlytics' ); ?></h1>

	<form action="options.php" method="post">
		<?php settings_fields( 'betterlytics_settings' ); ?>

		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><?php esc_html_e( 'Enable Tracking', 'betterlytics' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="betterlytics_options[enabled]" value="1" <?php checked( $betterlytics_options['enabled'] ); ?>>
							<?php esc_html_e( 'Enable Betterlytics tracking on this site', 'betterlytics' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Site ID', 'betterlytics' ); ?></th>
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
</div>
