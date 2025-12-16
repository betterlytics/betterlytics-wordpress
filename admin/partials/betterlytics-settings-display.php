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

$options           = Betterlytics_Options::get_options();
$setup_incomplete  = empty( $options['site_id'] ) || empty( $options['enabled'] );
$banner_dismissed  = Betterlytics_Admin::is_setup_banner_dismissed();
$show_setup_banner = $setup_incomplete && ! $banner_dismissed;
?>

<?php if ( $show_setup_banner ) : ?>
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
		<?php
		settings_fields( 'betterlytics_settings' );
		do_settings_sections( 'betterlytics-settings' );
		submit_button( __( 'Save Settings', 'betterlytics' ) );
		?>
	</form>
</div>
