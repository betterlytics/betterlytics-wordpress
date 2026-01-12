<?php
/**
 * Setup Guide partial for the plugin.
 *
 * @package    Betterlytics
 * @subpackage Betterlytics/admin/partials
 * @since      1.0.2
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$betterlytics_options     = Betterlytics_Options::get_options();
$betterlytics_is_enabled  = ! empty( $betterlytics_options['enabled'] );
$betterlytics_has_site_id = ! empty( $betterlytics_options['site_id'] );
?>

<div class="betterlytics-setup-steps bg-card border border-border p-12 rounded-xl shadow-sm space-y-0">

	<!-- Step 1 -->
	<div class="betterlytics-step flex gap-10 py-10 border-b border-border transition-opacity duration-300 opacity-100">
		<div class="step-icon-wrapper shrink-0 pt-1">
			<div class="step-number w-10 h-10 rounded-full bg-primary text-white font-bold text-base flex items-center justify-center shadow-primary/20 shadow-lg">1</div>
		</div>
		<div class="step-content">
			<h3 class="text-foreground tracking-tight m-0 mb-1"><?php esc_html_e( 'Get your Site ID', 'betterlytics' ); ?></h3>
			<p class="m-0 text-foreground/70 font-medium">
				<?php
				printf(
					/* translators: %s: link to Betterlytics dashboard */
					esc_html__( 'Sign up or log in to your %s to get your unique Site ID.', 'betterlytics' ),
					'<a href="https://www.betterlytics.io/dashboards" target="_blank">' . esc_html__( 'Betterlytics dashboard', 'betterlytics' ) . '</a>'
				);
				?>
			</p>
			<?php if ( ! $betterlytics_has_site_id ) : ?>
			<p class="mt-2 text-muted-foreground">
				<a href="<?php echo esc_url( admin_url( 'options-general.php?page=betterlytics&tab=settings' ) ); ?>" class="button button-primary">
					<?php esc_html_e( 'Enter Site ID', 'betterlytics' ); ?>
				</a>
			</p>
			<?php else : ?>
			<p class="step-status mt-3 text-success font-semibold flex items-center gap-1.5 text-sm">
				<span class="dashicons dashicons-yes-alt !text-lg !w-5 !h-5 !leading-none"></span>
				<?php esc_html_e( 'Site ID configured', 'betterlytics' ); ?>
			</p>
			<?php endif; ?>
		</div>
	</div>

	<!-- Step 2 -->
	<div class="betterlytics-step flex gap-10 py-10 border-b border-border transition-opacity duration-300 <?php echo $betterlytics_has_site_id ? 'opacity-100' : 'opacity-70'; ?>">
		<div class="step-icon-wrapper shrink-0 pt-1">
			<div class="step-number w-10 h-10 rounded-full bg-primary text-white font-bold text-base flex items-center justify-center shadow-primary/20 shadow-lg">2</div>
		</div>
		<div class="step-content">
			<h3 class="text-foreground tracking-tight m-0 mb-1"><?php esc_html_e( 'Enable tracking', 'betterlytics' ); ?></h3>
			<p class="m-0 text-foreground/70 font-medium"><?php esc_html_e( 'Turn on tracking to start collecting analytics data.', 'betterlytics' ); ?></p>
			<?php if ( $betterlytics_has_site_id && ! $betterlytics_is_enabled ) : ?>
			<p class="mt-2">
				<a href="<?php echo esc_url( admin_url( 'options-general.php?page=betterlytics&tab=settings' ) ); ?>" class="button button-primary">
					<?php esc_html_e( 'Enable Tracking', 'betterlytics' ); ?>
				</a>
			</p>
			<?php elseif ( $betterlytics_is_enabled ) : ?>
			<p class="step-status mt-3 text-success font-semibold flex items-center gap-1.5 text-sm">
				<span class="dashicons dashicons-yes-alt !text-lg !w-5 !h-5 !leading-none"></span>
				<?php esc_html_e( 'Tracking enabled', 'betterlytics' ); ?>
			</p>
			<?php endif; ?>
		</div>
	</div>

	<!-- Step 3 -->
	<div class="betterlytics-step flex gap-10 py-10 transition-opacity duration-300 <?php echo ( $betterlytics_has_site_id && $betterlytics_is_enabled ) ? 'opacity-100' : 'opacity-70'; ?>">
		<div class="step-icon-wrapper shrink-0 pt-1">
			<div class="step-number w-10 h-10 rounded-full bg-primary text-white font-bold text-base flex items-center justify-center shadow-primary/20 shadow-lg">3</div>
		</div>
		<div class="step-content">
			<h3 class="text-foreground tracking-tight m-0 mb-1"><?php esc_html_e( 'Configure events (optional)', 'betterlytics' ); ?></h3>
			<p class="m-0 text-foreground/70 font-medium"><?php esc_html_e( 'Set up event tracking for downloads, outbound links, WooCommerce, and more.', 'betterlytics' ); ?></p>
			<?php if ( $betterlytics_has_site_id && $betterlytics_is_enabled ) : ?>
			<p class="mt-2">
				<a href="<?php echo esc_url( admin_url( 'options-general.php?page=betterlytics&tab=events' ) ); ?>" class="button">
					<?php esc_html_e( 'Configure Events', 'betterlytics' ); ?>
				</a>
			</p>
			<?php endif; ?>
		</div>
	</div>
</div>
