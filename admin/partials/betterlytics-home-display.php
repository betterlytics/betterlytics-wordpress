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

	<div class="betterlytics-setup-steps bg-card border border-border p-6 rounded-md shadow-sm space-y-0">

		<div class="betterlytics-step flex gap-4 py-4 border-b border-muted last:border-b-0 first:pt-0 last:pb-0 <?php echo $betterlytics_has_site_id ? 'opacity-100' : 'opacity-100'; // Keep opacity for now, will handle logic below ?>">
			<?php
			$step1_status = $betterlytics_has_site_id ? 'completed' : 'current';
			$step1_bg     = $betterlytics_has_site_id ? 'bg-[#00a32a]' : 'bg-primary';
			$step1_text   = 'text-white';
			?>
			<div class="step-number w-8 h-8 rounded-full <?php echo esc_attr( $step1_bg . ' ' . $step1_text ); ?> font-semibold flex items-center justify-center shrink-0">1</div>
			<div class="step-content">
				<h3 class="text-sm font-semibold m-0 mb-1"><?php esc_html_e( 'Get your Site ID', 'betterlytics' ); ?></h3>
				<p class="m-0 text-muted-foreground">
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
				<p class="step-status mt-2 text-[#00a32a] font-medium flex items-center gap-1">
					<span class="dashicons dashicons-yes-alt !text-base !w-4 !h-4 !align-bottom"></span>
					<?php esc_html_e( 'Site ID configured', 'betterlytics' ); ?>
				</p>
				<?php endif; ?>
			</div>
		</div>

		<div class="betterlytics-step flex gap-4 py-4 border-b border-muted last:border-b-0 first:pt-0 last:pb-0 <?php echo $betterlytics_has_site_id ? 'opacity-100' : 'opacity-50'; ?>">
			<?php
			$step2_active = $betterlytics_has_site_id;
			$step2_done   = $betterlytics_is_enabled;
			$step2_bg     = $step2_done ? 'bg-[#00a32a]' : ( $step2_active ? 'bg-primary' : 'bg-[#dcdcde]' );
			$step2_text   = ( $step2_done || $step2_active ) ? 'text-white' : 'text-[#50575e]';
			?>
			<div class="step-number w-8 h-8 rounded-full <?php echo esc_attr( $step2_bg . ' ' . $step2_text ); ?> font-semibold flex items-center justify-center shrink-0">2</div>
			<div class="step-content">
				<h3 class="text-sm font-semibold m-0 mb-1"><?php esc_html_e( 'Enable tracking', 'betterlytics' ); ?></h3>
				<p class="m-0 text-muted-foreground"><?php esc_html_e( 'Turn on tracking to start collecting analytics data.', 'betterlytics' ); ?></p>
				<?php if ( $betterlytics_has_site_id && ! $betterlytics_is_enabled ) : ?>
				<p class="mt-2">
					<a href="<?php echo esc_url( admin_url( 'options-general.php?page=betterlytics&tab=settings' ) ); ?>" class="button button-primary">
						<?php esc_html_e( 'Enable Tracking', 'betterlytics' ); ?>
					</a>
				</p>
				<?php elseif ( $betterlytics_is_enabled ) : ?>
				<p class="step-status mt-2 text-[#00a32a] font-medium flex items-center gap-1">
					<span class="dashicons dashicons-yes-alt !text-base !w-4 !h-4 !align-bottom"></span>
					<?php esc_html_e( 'Tracking enabled', 'betterlytics' ); ?>
				</p>
				<?php endif; ?>
			</div>
		</div>

		<div class="betterlytics-step flex gap-4 py-4 border-b border-muted last:border-b-0 first:pt-0 last:pb-0 <?php echo ( $betterlytics_has_site_id && $betterlytics_is_enabled ) ? 'opacity-100' : 'opacity-50'; ?>">
			<?php
			$step3_active = $betterlytics_has_site_id && $betterlytics_is_enabled;
			$step3_bg     = $step3_active ? 'bg-primary' : 'bg-[#dcdcde]';
			$step3_text   = $step3_active ? 'text-white' : 'text-[#50575e]';
			?>
			<div class="step-number w-8 h-8 rounded-full <?php echo esc_attr( $step3_bg . ' ' . $step3_text ); ?> font-semibold flex items-center justify-center shrink-0">3</div>
			<div class="step-content">
				<h3 class="text-sm font-semibold m-0 mb-1"><?php esc_html_e( 'Configure events (optional)', 'betterlytics' ); ?></h3>
				<p class="m-0 text-muted-foreground"><?php esc_html_e( 'Set up event tracking for downloads, outbound links, WooCommerce, and more.', 'betterlytics' ); ?></p>
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

	<div class="betterlytics-resources mt-8">
		<h2 class="text-lg font-semibold mb-4 pb-2 border-b border-border"><?php esc_html_e( 'Resources', 'betterlytics' ); ?></h2>
		<ul class="bg-card border border-border rounded-md divide-y divide-border list-none p-0 m-0">
			<li>
				<a href="https://www.betterlytics.io/docs" target="_blank" class="flex items-center gap-2 px-4 py-3 no-underline text-foreground hover:bg-muted transition-colors">
					<span class="dashicons dashicons-book text-muted-foreground"></span>
					<?php esc_html_e( 'Documentation', 'betterlytics' ); ?>
				</a>
			</li>
			<li>
				<a href="https://www.betterlytics.io/dashboards" target="_blank" class="flex items-center gap-2 px-4 py-3 no-underline text-foreground hover:bg-muted transition-colors">
					<span class="dashicons dashicons-chart-bar text-muted-foreground"></span>
					<?php esc_html_e( 'Betterlytics Dashboard', 'betterlytics' ); ?>
				</a>
			</li>
			<li>
				<a href="https://github.com/betterlytics/betterlytics-wordpress" target="_blank" class="flex items-center gap-2 px-4 py-3 no-underline text-foreground hover:bg-muted transition-colors">
					<span class="dashicons dashicons-editor-code text-muted-foreground"></span>
					<?php esc_html_e( 'GitHub Repository', 'betterlytics' ); ?>
				</a>
			</li>
		</ul>
	</div>

