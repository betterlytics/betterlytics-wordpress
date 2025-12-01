<?php
/**
 * Provide a admin area view for the plugin.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
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
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<form action="options.php" method="post">
		<?php
		settings_fields( 'betterlytics_settings' );
		do_settings_sections( 'betterlytics' );
		submit_button( __( 'Save Settings', 'betterlytics' ) );
		?>
	</form>

	<hr>

	<h2><?php esc_html_e( 'WordPress Hooks Integration', 'betterlytics' ); ?></h2>
	<p class="description">
		<?php esc_html_e( 'Map WordPress actions to Betterlytics events. When the WordPress action fires, it will automatically send an event to Betterlytics.', 'betterlytics' ); ?>
	</p>

	<div id="betterlytics-hooks-manager">
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

		<p>
			<button type="button" class="button button-secondary" id="betterlytics-add-hook">
				<?php esc_html_e( 'Add New Hook', 'betterlytics' ); ?>
			</button>
			<button type="button" class="button button-primary" id="betterlytics-save-hooks">
				<?php esc_html_e( 'Save Hooks', 'betterlytics' ); ?>
			</button>
			<span id="betterlytics-hooks-status"></span>
		</p>
	</div>

	<div class="betterlytics-hooks-help">
		<h3><?php esc_html_e( 'Common WordPress Hooks', 'betterlytics' ); ?></h3>
		<ul>
			<li><code>wp_login</code> - <?php esc_html_e( 'User logs in', 'betterlytics' ); ?></li>
			<li><code>user_register</code> - <?php esc_html_e( 'New user registration', 'betterlytics' ); ?></li>
			<li><code>comment_post</code> - <?php esc_html_e( 'New comment posted', 'betterlytics' ); ?></li>
			<li><code>woocommerce_thankyou</code> - <?php esc_html_e( 'WooCommerce order complete', 'betterlytics' ); ?></li>
			<li><code>woocommerce_add_to_cart</code> - <?php esc_html_e( 'Product added to cart', 'betterlytics' ); ?></li>
			<li><code>wpcf7_mail_sent</code> - <?php esc_html_e( 'Contact Form 7 submission', 'betterlytics' ); ?></li>
			<li><code>gform_after_submission</code> - <?php esc_html_e( 'Gravity Forms submission', 'betterlytics' ); ?></li>
			<li><code>wpforms_process_complete</code> - <?php esc_html_e( 'WPForms submission', 'betterlytics' ); ?></li>
		</ul>
	</div>
</div>
