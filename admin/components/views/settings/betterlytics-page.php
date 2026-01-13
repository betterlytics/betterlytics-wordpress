<?php
/**
 * View: Settings Page
 *
 * @package Betterlytics
 * @subpackage Betterlytics/admin/components/views/settings
 *
 * @var array $args {
 *     @type array $options Plugin options.
 * }
 */

$betterlytics_options = $args['options'];

?>
<div class="animate-in fade-in duration-500 pb-24">
	<div class="flex items-center justify-between mb-8">
		<div>
			<div class="flex items-center gap-3 mb-2">
				<h2 class="text-3xl font-bold text-foreground tracking-tight">
					<?php esc_html_e( 'General Settings', 'betterlytics' ); ?>
				</h2>
				<?php echo Betterlytics_Admin_Controller::get_help_link( '', 'is-blue scale-110 relative top-[1px]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<p class="text-muted-foreground font-medium">
				<?php esc_html_e( 'Configure your Betterlytics integration and tracking preferences.', 'betterlytics' ); ?>
			</p>
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
				<?php
				// Configuration Section.
				ob_start();

				// 1. Enable Tracking
				Betterlytics_Admin_Controller::render_component(
					'ui/setting-row',
					[
						'label'       => __( 'Enable Tracking', 'betterlytics' ),
						'name'        => 'betterlytics_options[enabled]',
						'checked'     => ! empty( $betterlytics_options['enabled'] ),
						'description' => __( 'Enable Betterlytics tracking on this site', 'betterlytics' ),
					]
				);

				// 2. Site ID
				Betterlytics_Admin_Controller::render_component(
					'ui/setting-row',
					[
						'label'       => __( 'Site ID', 'betterlytics' ),
						'name'        => 'betterlytics_options[site_id]',
						'type'        => 'text',
						'value'       => $betterlytics_options['site_id'],
						'placeholder' => 'your-site-id',
						'description' => __( 'Your unique Site ID from the Betterlytics dashboard.', 'betterlytics' ),
					]
				);

				// 3. Server URL
				Betterlytics_Admin_Controller::render_component(
					'ui/setting-row',
					[
						'label'       => __( 'Server URL', 'betterlytics' ),
						'name'        => 'betterlytics_options[server_url]',
						'type'        => 'url',
						'value'       => $betterlytics_options['server_url'],
						'placeholder' => 'https://betterlytics.io/track',
						'description' => __( 'The tracking server URL (Default: Betterlytics cloud).', 'betterlytics' ),
					]
				);

				// 4. Script URL
				Betterlytics_Admin_Controller::render_component(
					'ui/setting-row',
					[
						'label'       => __( 'Script URL', 'betterlytics' ),
						'name'        => 'betterlytics_options[script_url]',
						'type'        => 'url',
						'value'       => $betterlytics_options['script_url'],
						'placeholder' => 'https://betterlytics.io/analytics.js',
						'description' => __( 'The tracking script URL (Default: Betterlytics cloud).', 'betterlytics' ),
					]
				);

				// 5. Track Logged-in Users
				Betterlytics_Admin_Controller::render_component(
					'ui/setting-row',
					[
						'label'       => __( 'Track Logged-in Users', 'betterlytics' ),
						'name'        => 'betterlytics_options[track_logged_in]',
						'checked'     => ! empty( $betterlytics_options['track_logged_in'] ),
						'description' => __( 'Enable to include admins/editors in your analytics.', 'betterlytics' ),
					]
				);

				$betterlytics_fields_html = ob_get_clean();

				Betterlytics_Admin_Controller::render_component(
					'ui/card',
					[
						'title'       => __( 'Plugin Configuration', 'betterlytics' ),
						'description' => __( 'Connect your site to Betterlytics and manage core tracking behavior.', 'betterlytics' ),
						'children'    => $betterlytics_fields_html,
					]
				);
				?>
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
