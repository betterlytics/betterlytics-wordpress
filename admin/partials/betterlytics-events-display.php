<?php
/**
 * Events page view for the plugin.
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
$betterlytics_has_woocommerce   = class_exists( 'WooCommerce' );
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
	<h1><?php esc_html_e( 'Events', 'betterlytics' ); ?></h1>

	<p class="betterlytics-page-description">
		<?php
		$betterlytics_dashboard_url = 'https://www.betterlytics.io/dashboard';
		if ( ! empty( $betterlytics_options['site_id'] ) ) {
			$betterlytics_dashboard_url .= '/' . esc_attr( $betterlytics_options['site_id'] );
		}
		printf(
			/* translators: %s: link to Betterlytics dashboard */
			esc_html__( 'Configure which events to track on your site. View your tracked events on your %s.', 'betterlytics' ),
			'<a href="' . esc_url( $betterlytics_dashboard_url ) . '" target="_blank">' . esc_html__( 'Betterlytics dashboard', 'betterlytics' ) . '</a>'
		);
		?>
	</p>

	<nav class="nav-tab-wrapper betterlytics-tabs">
		<a href="#standard" class="nav-tab nav-tab-active" data-tab="standard">
			<?php esc_html_e( 'Standard', 'betterlytics' ); ?>
		</a>
		<a href="#custom" class="nav-tab" data-tab="custom">
			<?php esc_html_e( 'Custom', 'betterlytics' ); ?>
		</a>
	</nav>

	<!-- Standard Events Tab -->
	<div id="tab-standard" class="betterlytics-tab-content active">
		<form action="options.php" method="post">
			<?php settings_fields( 'betterlytics_events' ); ?>

			<h2><?php esc_html_e( 'Page Events', 'betterlytics' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( '404 Error Pages', 'betterlytics' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="betterlytics_options[track_404]" value="1" <?php checked( ! empty( $betterlytics_options['track_404'] ) ); ?>>
								<?php esc_html_e( 'Track when visitors land on pages that don\'t exist', 'betterlytics' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Site Search', 'betterlytics' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="betterlytics_options[track_search]" value="1" <?php checked( ! empty( $betterlytics_options['track_search'] ) ); ?>>
								<?php esc_html_e( 'Track search queries on your site', 'betterlytics' ); ?>
							</label>
						</td>
					</tr>
				</tbody>
			</table>

			<h2><?php esc_html_e( 'Click Events', 'betterlytics' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Outbound Links', 'betterlytics' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="betterlytics_options[track_outbound]" value="1" <?php checked( ! empty( $betterlytics_options['track_outbound'] ) ); ?>>
								<?php esc_html_e( 'Track clicks on links to external websites', 'betterlytics' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'File Downloads', 'betterlytics' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="betterlytics_options[track_downloads]" value="1" <?php checked( ! empty( $betterlytics_options['track_downloads'] ) ); ?>>
								<?php esc_html_e( 'Track downloads of files (.pdf, .zip, .doc, etc.)', 'betterlytics' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'CSS Class Events', 'betterlytics' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="betterlytics_options[track_css_events]" value="1" <?php checked( ! empty( $betterlytics_options['track_css_events'] ) ); ?>>
								<?php
								printf(
									/* translators: %s: CSS class example */
									esc_html__( 'Track clicks on elements with %s class', 'betterlytics' ),
									'<code>betterlytics-event-name=YourEvent</code>'
								);
								?>
							</label>
						</td>
					</tr>
				</tbody>
			</table>

			<?php if ( $betterlytics_has_woocommerce ) : ?>
			<h2><?php esc_html_e( 'WooCommerce', 'betterlytics' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Add to Cart', 'betterlytics' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="betterlytics_options[woo_add_to_cart]" value="1" <?php checked( ! empty( $betterlytics_options['woo_add_to_cart'] ) ); ?>>
								<?php esc_html_e( 'Track when products are added to the cart', 'betterlytics' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Begin Checkout', 'betterlytics' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="betterlytics_options[woo_checkout]" value="1" <?php checked( ! empty( $betterlytics_options['woo_checkout'] ) ); ?>>
								<?php esc_html_e( 'Track when customers start the checkout process', 'betterlytics' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Purchase Complete', 'betterlytics' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="betterlytics_options[woo_purchase]" value="1" <?php checked( ! empty( $betterlytics_options['woo_purchase'] ) ); ?>>
								<?php esc_html_e( 'Track completed purchases', 'betterlytics' ); ?>
							</label>
						</td>
					</tr>
				</tbody>
			</table>
			<?php endif; ?>

			<?php submit_button( __( 'Save Events', 'betterlytics' ) ); ?>
		</form>
	</div>

	<!-- Custom Events Tab -->
	<div id="tab-custom" class="betterlytics-tab-content">
		<p class="description">
			<?php esc_html_e( 'Add custom WordPress hooks to track as events. When the hook fires, Betterlytics will record it.', 'betterlytics' ); ?>
		</p>

		<table class="wp-list-table widefat fixed striped" id="betterlytics-hooks-table">
			<thead>
				<tr>
					<th scope="col" class="column-enabled"><?php esc_html_e( 'Enabled', 'betterlytics' ); ?></th>
					<th scope="col" class="column-wp-hook"><?php esc_html_e( 'WordPress Hook', 'betterlytics' ); ?></th>
					<th scope="col" class="column-event-name"><?php esc_html_e( 'Event Name', 'betterlytics' ); ?></th>
					<th scope="col" class="column-actions"><?php esc_html_e( 'Actions', 'betterlytics' ); ?></th>
				</tr>
			</thead>
			<tbody id="betterlytics-hooks-list">
				<!-- Hooks will be rendered via JavaScript -->
			</tbody>
		</table>

		<p class="betterlytics-hooks-actions">
			<button type="button" class="button button-secondary" id="betterlytics-add-hook">
				<?php esc_html_e( 'Add Hook', 'betterlytics' ); ?>
			</button>
			<button type="button" class="button button-primary" id="betterlytics-save-hooks">
				<?php esc_html_e( 'Save Hooks', 'betterlytics' ); ?>
			</button>
			<span id="betterlytics-hooks-status"></span>
		</p>

		<div class="betterlytics-hooks-help">
			<h3><?php esc_html_e( 'Common WordPress Hooks', 'betterlytics' ); ?></h3>
			<ul>
				<li><code>wp_login</code> - <?php esc_html_e( 'User logs in', 'betterlytics' ); ?></li>
				<li><code>user_register</code> - <?php esc_html_e( 'New user registration', 'betterlytics' ); ?></li>
				<li><code>comment_post</code> - <?php esc_html_e( 'New comment posted', 'betterlytics' ); ?></li>
				<li><code>wpcf7_mail_sent</code> - <?php esc_html_e( 'Contact Form 7 submission', 'betterlytics' ); ?></li>
				<li><code>gform_after_submission</code> - <?php esc_html_e( 'Gravity Forms submission', 'betterlytics' ); ?></li>
				<li><code>wpforms_process_complete</code> - <?php esc_html_e( 'WPForms submission', 'betterlytics' ); ?></li>
			</ul>
		</div>
	</div>
</div>
