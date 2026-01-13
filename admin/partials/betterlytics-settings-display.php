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
				<table class="form-table w-full m-0" role="presentation">
					<tbody class="divide-y divide-border">
						<tr class="hover:bg-muted/5 transition-colors">
							<th scope="row" class="w-[300px] py-8 px-10 text-left align-top border-none">
								<span class="flex items-center gap-2 font-semibold text-foreground">
									<?php esc_html_e( 'Enable Tracking', 'betterlytics' ); ?>
									<?php echo Betterlytics_Admin_Controller::get_help_link( '', 'is-blue' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</span>
							</th>
							<td class="py-8 px-10 align-top border-none">
								<label class="flex items-center gap-3 cursor-pointer group">
									<input type="checkbox" name="betterlytics_options[enabled]" value="1" <?php checked( $betterlytics_options['enabled'] ); ?> class="w-6 h-6 rounded border-border text-primary focus:ring-primary/30 transition-all">
									<span class="text-base font-medium text-foreground/80 group-hover:text-foreground transition-colors"><?php esc_html_e( 'Enable Betterlytics tracking on this site', 'betterlytics' ); ?></span>
								</label>
							</td>
						</tr>
						<tr class="hover:bg-muted/5 transition-colors">
							<th scope="row" class="w-[300px] py-8 px-10 text-left align-top border-none">
								<span class="flex items-center gap-2 font-semibold text-foreground">
									<?php esc_html_e( 'Site ID', 'betterlytics' ); ?>
									<?php echo Betterlytics_Admin_Controller::get_help_link( '', 'is-blue' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</span>
							</th>
							<td class="py-8 px-10 align-top border-none">
								<div class="relative max-w-[500px]">
									<input type="text" name="betterlytics_options[site_id]" value="<?php echo esc_attr( $betterlytics_options['site_id'] ); ?>" class="!w-full !p-3 !text-base bg-card border-border rounded-xl shadow-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="your-site-id">
								</div>
								<p class="description mt-3 text-sm text-muted-foreground font-medium">
									<?php esc_html_e( 'Your unique Site ID from the Betterlytics dashboard.', 'betterlytics' ); ?>
								</p>
							</td>
						</tr>
						<tr class="hover:bg-muted/5 transition-colors">
							<th scope="row" class="w-[300px] py-8 px-10 text-left align-top border-none">
								<span class="flex items-center gap-2 font-semibold text-foreground">
									<?php esc_html_e( 'Server URL', 'betterlytics' ); ?>
								</span>
							</th>
							<td class="py-8 px-10 align-top border-none">
								<div class="relative max-w-[500px]">
									<input type="url" name="betterlytics_options[server_url]" value="<?php echo esc_attr( $betterlytics_options['server_url'] ); ?>" class="!w-full !p-3 !text-base bg-card border-border rounded-xl shadow-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="https://betterlytics.io/track">
								</div>
								<p class="description mt-3 text-sm text-muted-foreground font-medium">
									<?php esc_html_e( 'The tracking server URL. Default is the Betterlytics cloud. Change this if you are self-hosting.', 'betterlytics' ); ?>
								</p>
							</td>
						</tr>
						<tr class="hover:bg-muted/5 transition-colors">
							<th scope="row" class="w-[300px] py-8 px-10 text-left align-top border-none">
								<span class="flex items-center gap-2 font-semibold text-foreground">
									<?php esc_html_e( 'Script URL', 'betterlytics' ); ?>
								</span>
							</th>
							<td class="py-8 px-10 align-top border-none">
								<div class="relative max-w-[500px]">
									<input type="url" name="betterlytics_options[script_url]" value="<?php echo esc_attr( $betterlytics_options['script_url'] ); ?>" class="!w-full !p-3 !text-base bg-card border-border rounded-xl shadow-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" placeholder="https://betterlytics.io/analytics.js">
								</div>
								<p class="description mt-3 text-sm text-muted-foreground font-medium">
									<?php esc_html_e( 'The tracking script URL. Default is the Betterlytics cloud. Change this if you are self-hosting.', 'betterlytics' ); ?>
								</p>
							</td>
						</tr>
						<tr class="hover:bg-muted/5 transition-colors border-b-0">
							<th scope="row" class="w-[300px] py-8 px-10 text-left align-top border-none">
								<span class="flex items-center gap-2 font-semibold text-foreground">
									<?php esc_html_e( 'Track Logged-in Users', 'betterlytics' ); ?>
								</span>
							</th>
							<td class="py-8 px-10 align-top border-none">
								<label class="flex items-center gap-3 cursor-pointer group">
									<input type="checkbox" name="betterlytics_options[track_logged_in]" value="1" <?php checked( $betterlytics_options['track_logged_in'] ); ?> class="w-6 h-6 rounded border-border text-primary focus:ring-primary/30 transition-all">
									<span class="text-base font-medium text-foreground/80 group-hover:text-foreground transition-colors"><?php esc_html_e( 'Track logged-in users (disable to exclude admins/editors from analytics)', 'betterlytics' ); ?></span>
								</label>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<!-- Global Sticky Footer -->
		<div class="betterlytics-sticky-footer">
			<?php submit_button( __( 'Save Settings', 'betterlytics' ), 'primary', 'submit', false, [ 'id' => 'betterlytics-save-settings', 'class' => 'button-primary !py-3.5 !px-16 !h-auto !text-base !font-bold rounded-xl shadow-lg transition-all hover:scale-[1.02] active:scale-[0.98]' ] ); ?>
		</div>
	</form>
</div>
