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

<div class="animate-in fade-in duration-500 pb-24">
	<div class="flex items-center justify-between mb-8">
		<div>
			<h2 class="text-3xl font-bold text-foreground tracking-tight mb-2">
				<?php esc_html_e( 'General Settings', 'betterlytics' ); ?>
			</h2>
			<p class="text-muted-foreground font-medium">
				<?php esc_html_e( 'Configure your Betterlytics integration and tracking preferences.', 'betterlytics' ); ?>
			</p>
		</div>
		<div class="shrink-0">
			<?php echo Betterlytics_Admin_Controller::get_help_link( '', 'is-blue scale-125' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>

	<form action="options.php" method="post">
		<?php settings_fields( 'betterlytics_settings' ); ?>

		<div class="bg-card border border-border rounded-2xl shadow-sm flex flex-col min-h-[600px] mb-8">
			<!-- Card Header -->
			<div class="bg-muted/30 border-b border-border p-6 flex items-center justify-between">
				<div class="flex items-center gap-3">
					<div class="w-2.5 h-2.5 rounded-full bg-primary animate-pulse"></div>
					<span class="text-sm font-bold text-foreground uppercase tracking-wider"><?php esc_html_e( 'Configuration', 'betterlytics' ); ?></span>
				</div>
				<div class="text-xs font-bold text-muted-foreground/40 uppercase tracking-widest hidden sm:block">
					<?php esc_html_e( 'Plugin Settings', 'betterlytics' ); ?>
				</div>
			</div>

			<!-- Card Body -->
			<div class="flex-1 p-10 relative">
				<div class="mb-8">
					<h1 class="text-2xl font-bold text-foreground tracking-tight mb-2">
						<?php esc_html_e( 'Plugin Configuration', 'betterlytics' ); ?>
					</h1>
					<h4 class="text-sm text-muted-foreground font-medium max-w-[850px] leading-relaxed">
						<?php esc_html_e( 'Connect your site to Betterlytics and manage core tracking behavior.', 'betterlytics' ); ?>
					</h4>
				</div>

				<div class="bg-card border border-border rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.05)] overflow-hidden">
					<div class="divide-y divide-border">
						<!-- Enable Tracking -->
						<div class="group hover:bg-muted/5 transition-all duration-200">
							<div class="flex items-center justify-between py-6 px-10">
								<div class="flex flex-col gap-1 min-w-0 flex-1">
									<div class="flex items-center gap-2">
										<span class="text-base font-bold text-foreground truncate">
											<?php esc_html_e( 'Enable Tracking', 'betterlytics' ); ?>
										</span>
										<?php echo Betterlytics_Admin_Controller::get_help_link( '', 'is-blue' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
									<span class="text-sm text-muted-foreground/80 font-medium truncate">
										<?php esc_html_e( 'Enable Betterlytics tracking on this site', 'betterlytics' ); ?>
									</span>
								</div>
								<div class="flex items-center gap-6 shrink-0 ml-8">
									<label class="relative flex items-center cursor-pointer">
										<input type="checkbox" name="betterlytics_options[enabled]" value="1" <?php checked( $betterlytics_options['enabled'] ); ?> class="w-6 h-6 rounded-lg border-2 border-border text-primary focus:ring-primary/30 transition-all cursor-pointer">
									</label>
								</div>
							</div>
						</div>

						<!-- Site ID -->
						<div class="group hover:bg-muted/5 transition-all duration-200">
							<div class="flex items-center justify-between py-8 px-10">
								<div class="flex flex-col gap-1 min-w-0 flex-1">
									<div class="flex items-center gap-2">
										<span class="text-base font-bold text-foreground truncate">
											<?php esc_html_e( 'Site ID', 'betterlytics' ); ?>
										</span>
										<?php echo Betterlytics_Admin_Controller::get_help_link( '', 'is-blue' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
									<span class="text-sm text-muted-foreground/80 font-medium truncate">
										<?php esc_html_e( 'Your unique Site ID from the Betterlytics dashboard.', 'betterlytics' ); ?>
									</span>
								</div>
								<div class="flex items-center gap-6 shrink-0 ml-8 w-64">
									<input type="text" name="betterlytics_options[site_id]" value="<?php echo esc_attr( $betterlytics_options['site_id'] ); ?>" class="!w-full !p-2.5 !text-sm font-bold bg-card border-border rounded-xl shadow-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="your-site-id">
								</div>
							</div>
						</div>

						<!-- Server URL -->
						<div class="group hover:bg-muted/5 transition-all duration-200">
							<div class="flex items-center justify-between py-8 px-10">
								<div class="flex flex-col gap-1 min-w-0 flex-1">
									<div class="flex items-center gap-2">
										<span class="text-base font-bold text-foreground truncate">
											<?php esc_html_e( 'Server URL', 'betterlytics' ); ?>
										</span>
									</div>
									<span class="text-sm text-muted-foreground/80 font-medium truncate">
										<?php esc_html_e( 'The tracking server URL (Default: Betterlytics cloud).', 'betterlytics' ); ?>
									</span>
								</div>
								<div class="flex items-center gap-6 shrink-0 ml-8 w-64">
									<input type="url" name="betterlytics_options[server_url]" value="<?php echo esc_attr( $betterlytics_options['server_url'] ); ?>" class="!w-full !p-2.5 !text-sm font-bold bg-card border-border rounded-xl shadow-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="https://betterlytics.io/track">
								</div>
							</div>
						</div>

						<!-- Script URL -->
						<div class="group hover:bg-muted/5 transition-all duration-200">
							<div class="flex items-center justify-between py-8 px-10">
								<div class="flex flex-col gap-1 min-w-0 flex-1">
									<div class="flex items-center gap-2">
										<span class="text-base font-bold text-foreground truncate">
											<?php esc_html_e( 'Script URL', 'betterlytics' ); ?>
										</span>
									</div>
									<span class="text-sm text-muted-foreground/80 font-medium truncate">
										<?php esc_html_e( 'The tracking script URL (Default: Betterlytics cloud).', 'betterlytics' ); ?>
									</span>
								</div>
								<div class="flex items-center gap-6 shrink-0 ml-8 w-64">
									<input type="url" name="betterlytics_options[script_url]" value="<?php echo esc_attr( $betterlytics_options['script_url'] ); ?>" class="!w-full !p-2.5 !text-sm font-bold bg-card border-border rounded-xl shadow-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="https://betterlytics.io/analytics.js">
								</div>
							</div>
						</div>

						<!-- Track Logged-in Users -->
						<div class="group hover:bg-muted/5 transition-all duration-200">
							<div class="flex items-center justify-between py-6 px-10">
								<div class="flex flex-col gap-1 min-w-0 flex-1">
									<div class="flex items-center gap-2">
										<span class="text-base font-bold text-foreground truncate">
											<?php esc_html_e( 'Track Logged-in Users', 'betterlytics' ); ?>
										</span>
									</div>
									<span class="text-sm text-muted-foreground/80 font-medium truncate">
										<?php esc_html_e( 'Enable to include admins/editors in your analytics.', 'betterlytics' ); ?>
									</span>
								</div>
								<div class="flex items-center gap-6 shrink-0 ml-8">
									<label class="relative flex items-center cursor-pointer">
										<input type="checkbox" name="betterlytics_options[track_logged_in]" value="1" <?php checked( $betterlytics_options['track_logged_in'] ); ?> class="w-6 h-6 rounded-lg border-2 border-border text-primary focus:ring-primary/30 transition-all cursor-pointer">
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Global Sticky Footer -->
		<div class="betterlytics-sticky-footer">
			<?php
			submit_button(
				__( 'Save Settings', 'betterlytics' ),
				'primary',
				'submit',
				false,
				[
					'id'    => 'betterlytics-save-settings',
					'class' => 'button-primary !py-3.5 !px-16 !h-auto !text-base !font-bold rounded-xl shadow-lg transition-all hover:scale-[1.02] active:scale-[0.98]',
				]
			);
			?>
		</div>
	</form>
</div>
