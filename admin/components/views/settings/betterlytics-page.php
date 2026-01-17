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
	<!-- Header -->
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

		<div class="bg-card border border-border rounded-2xl shadow-sm flex flex-col mb-8">
			<!-- Card Body -->
			<div class="flex-1 p-10 relative">
				<?php
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

				// 3. Core Web Vitals
				Betterlytics_Admin_Controller::render_component(
					'ui/setting-row',
					[
						'label'       => __( 'Core Web Vitals', 'betterlytics' ),
						'name'        => 'betterlytics_options[track_web_vitals]',
						'checked'     => ! empty( $betterlytics_options['track_web_vitals'] ),
						'description' => __( 'Track Core Web Vitals performance metrics', 'betterlytics' ),
						'help_path'   => 'integration/web-vitals',
					]
				);

				// 4. Outbound Links
				Betterlytics_Admin_Controller::render_component(
					'ui/setting-row',
					[
						'label'       => __( 'Outbound Links', 'betterlytics' ),
						'name'        => 'betterlytics_options[track_outbound][mode]',
						'type'        => 'select',
						'value'       => $betterlytics_options['track_outbound']['mode'] ?? 'domain',
						'options'     => [
							'off'    => __( 'Off', 'betterlytics' ),
							'domain' => __( 'Domain only', 'betterlytics' ),
							'full'   => __( 'Full URL', 'betterlytics' ),
						],
						'description' => __( 'Track clicks on links to external websites', 'betterlytics' ),
						'help_path'   => 'integration/outbound-links',
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
