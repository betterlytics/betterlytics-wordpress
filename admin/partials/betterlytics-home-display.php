<?php
/**
 * Home/Setup page view for the plugin.
 *
 * @package    Betterlytics
 * @subpackage Betterlytics/admin/partials
 * @since      1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$betterlytics_options     = Betterlytics_Options::get_options();
$betterlytics_is_enabled  = ! empty( $betterlytics_options['enabled'] );
$betterlytics_has_site_id = ! empty( $betterlytics_options['site_id'] );
?>

<div class="wrap betterlytics-settings betterlytics-home">
	<h1><?php esc_html_e( 'Betterlytics', 'betterlytics' ); ?></h1>

	<p class="betterlytics-page-description">
		<?php esc_html_e( 'Privacy-first analytics for WordPress. Track your visitors without compromising their privacy.', 'betterlytics' ); ?>
	</p>

	<div class="betterlytics-setup-steps">
		<h2><?php esc_html_e( 'Setup', 'betterlytics' ); ?></h2>

		<div class="betterlytics-step <?php echo $betterlytics_has_site_id ? 'completed' : 'current'; ?>">
			<div class="step-number">1</div>
			<div class="step-content">
				<h3><?php esc_html_e( 'Get your Site ID', 'betterlytics' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: link to Betterlytics dashboard */
						esc_html__( 'Sign up or log in to your %s to get your unique Site ID.', 'betterlytics' ),
						'<a href="https://www.betterlytics.io/dashboard" target="_blank">' . esc_html__( 'Betterlytics dashboard', 'betterlytics' ) . '</a>'
					);
					?>
				</p>
				<?php if ( ! $betterlytics_has_site_id ) : ?>
				<p>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=betterlytics-settings' ) ); ?>" class="button button-primary">
						<?php esc_html_e( 'Enter Site ID', 'betterlytics' ); ?>
					</a>
				</p>
				<?php else : ?>
				<p class="step-status">
					<span class="dashicons dashicons-yes-alt"></span>
					<?php esc_html_e( 'Site ID configured', 'betterlytics' ); ?>
				</p>
				<?php endif; ?>
			</div>
		</div>

		<div class="betterlytics-step <?php echo $betterlytics_has_site_id ? ( $betterlytics_is_enabled ? 'completed' : 'current' ) : ''; ?>">
			<div class="step-number">2</div>
			<div class="step-content">
				<h3><?php esc_html_e( 'Enable tracking', 'betterlytics' ); ?></h3>
				<p><?php esc_html_e( 'Turn on tracking to start collecting analytics data.', 'betterlytics' ); ?></p>
				<?php if ( $betterlytics_has_site_id && ! $betterlytics_is_enabled ) : ?>
				<p>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=betterlytics-settings' ) ); ?>" class="button button-primary">
						<?php esc_html_e( 'Enable Tracking', 'betterlytics' ); ?>
					</a>
				</p>
				<?php elseif ( $betterlytics_is_enabled ) : ?>
				<p class="step-status">
					<span class="dashicons dashicons-yes-alt"></span>
					<?php esc_html_e( 'Tracking enabled', 'betterlytics' ); ?>
				</p>
				<?php endif; ?>
			</div>
		</div>

		<div class="betterlytics-step <?php echo ( $betterlytics_has_site_id && $betterlytics_is_enabled ) ? 'current' : ''; ?>">
			<div class="step-number">3</div>
			<div class="step-content">
				<h3><?php esc_html_e( 'Configure events (optional)', 'betterlytics' ); ?></h3>
				<p><?php esc_html_e( 'Set up event tracking for downloads, outbound links, WooCommerce, and more.', 'betterlytics' ); ?></p>
				<?php if ( $betterlytics_has_site_id && $betterlytics_is_enabled ) : ?>
				<p>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=betterlytics-events' ) ); ?>" class="button">
						<?php esc_html_e( 'Configure Events', 'betterlytics' ); ?>
					</a>
				</p>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="betterlytics-resources">
		<h2><?php esc_html_e( 'Resources', 'betterlytics' ); ?></h2>
		<ul>
			<li>
				<a href="https://www.betterlytics.io/docs" target="_blank">
					<span class="dashicons dashicons-book"></span>
					<?php esc_html_e( 'Documentation', 'betterlytics' ); ?>
				</a>
			</li>
			<li>
				<a href="https://www.betterlytics.io/dashboard" target="_blank">
					<span class="dashicons dashicons-chart-bar"></span>
					<?php esc_html_e( 'Betterlytics Dashboard', 'betterlytics' ); ?>
				</a>
			</li>
			<li>
				<a href="https://github.com/betterlytics/betterlytics-wordpress" target="_blank">
					<span class="dashicons dashicons-editor-code"></span>
					<?php esc_html_e( 'GitHub Repository', 'betterlytics' ); ?>
				</a>
			</li>
		</ul>
	</div>
</div>
