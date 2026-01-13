<?php
/**
 * View: Events Page
 *
 * Main entry point for the "Events" tab.
 *
 * @package Betterlytics
 * @subpackage Betterlytics/admin/components/views/events
 *
 * @var array $args {
 *     @type array  $options Plugin options.
 *     @type string $tab     Current tab name ('events').
 * }
 */

$betterlytics_options        = $args['options'];
$betterlytics_current_subtab = isset( $_GET['subtab'] ) ? sanitize_text_field( wp_unslash( $_GET['subtab'] ) ) : 'browser'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

// Determine dashboard text and link.
$betterlytics_dashboard_text = '';
$betterlytics_dashboard_url  = 'https://www.betterlytics.io/dashboards';
if ( ! empty( $betterlytics_options['site_id'] ) ) {
	$betterlytics_dashboard_url .= '/' . esc_attr( $betterlytics_options['site_id'] );
}
$betterlytics_dashboard_link = sprintf(
	'<a href="%s" target="_blank" class="text-primary font-bold hover:underline underline-offset-4 decoration-2">%s</a>',
	esc_url( $betterlytics_dashboard_url ),
	esc_html__( 'Betterlytics dashboard', 'betterlytics' )
);
$betterlytics_dashboard_text = sprintf(
	/* translators: %s: link to Betterlytics dashboard */
	esc_html__( 'Configure which events to track on your site. View your tracked events on your %s.', 'betterlytics' ),
	$betterlytics_dashboard_link
);

?>
<div class="animate-in fade-in duration-500 pb-24">

	<!-- Header -->
	<div class="flex items-center justify-between mb-8">
		<div>
			<div class="flex items-center gap-3 mb-2">
				<h2 class="text-3xl font-bold text-foreground tracking-tight">
					<?php esc_html_e( 'Event Tracking', 'betterlytics' ); ?>
				</h2>
				<?php echo Betterlytics_Admin_Controller::get_help_link( 'integration/custom-events', 'is-blue scale-110 relative top-[1px]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<p class="text-muted-foreground font-medium">
				<?php echo $betterlytics_dashboard_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</p>
		</div>
	</div>

	<form action="options.php" method="post" id="betterlytics-settings-form">
		<?php settings_fields( 'betterlytics_events' ); ?>
		
		<div class="bg-card border border-border rounded-2xl shadow-sm flex flex-col min-h-[600px] mb-8">
			
			<!-- Tabs Component -->
			<?php
			Betterlytics_Admin_Controller::render_component(
				'ui/tabs',
				[
					'tabs'        => [
						'browser' => __( 'Browser Events', 'betterlytics' ),
						'server'  => __( 'Server Hooks', 'betterlytics' ),
					],
					'current_tab' => $betterlytics_current_subtab,
					'base_url'    => admin_url( 'options-general.php?page=betterlytics&tab=events' ),
					'param_name'  => 'subtab',
				]
			);
			?>

			<!-- Tab Content -->
			<div class="flex-1 p-10 relative">
				<!-- Browser Events Tab -->
				<div id="tab-browser" class="betterlytics-tab-content <?php echo 'browser' === $betterlytics_current_subtab ? 'active' : ''; ?>">
					<?php Betterlytics_Admin_Controller::render_component( 'views/events/browser-list', [ 'options' => $betterlytics_options ] ); ?>
				</div>

				<!-- Server Hooks Tab -->
				<div id="tab-server" class="betterlytics-tab-content <?php echo 'server' === $betterlytics_current_subtab ? 'active' : ''; ?>">
					<?php Betterlytics_Admin_Controller::render_component( 'views/events/server-list', [ 'options' => $betterlytics_options ] ); ?>
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
