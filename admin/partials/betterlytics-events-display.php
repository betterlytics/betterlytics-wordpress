<?php
/**
 * Events page view for the plugin.
 *
 * @package    Betterlytics
 * @subpackage Betterlytics/admin/partials
 * @since      1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$betterlytics_options           = Betterlytics_Options::get_options();
$betterlytics_setup_incomplete  = empty( $betterlytics_options['site_id'] ) || empty( $betterlytics_options['enabled'] );
$betterlytics_banner_dismissed  = Betterlytics_Admin::is_setup_banner_dismissed();
$betterlytics_show_setup_banner = $betterlytics_setup_incomplete && ! $betterlytics_banner_dismissed;

/**
 * Renders an unified Event Card section.
 *
 * @param array $section Section configuration.
 * @param bool  $show_separator Whether to show separator before section.
 */
function betterlytics_render_event_card( $section, $show_separator = false ) {
	if ( $show_separator ) {
		echo '<hr class="border-t border-border my-12">';
	}
	?>
	<div class="betterlytics-event-card mb-12 last:mb-0">
		<div class="mb-8">
			<h1 class="text-2xl font-bold text-foreground tracking-tight mb-2 flex items-center gap-2">
				<?php echo esc_html( $section['title'] ); ?>
				<?php
				if ( ! empty( $section['help_path'] ) ) {
					echo Betterlytics_Admin_Controller::get_help_link( $section['help_path'], 'is-blue' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</h1>
			<?php if ( ! empty( $section['description'] ) ) : ?>
				<h4 class="text-sm text-muted-foreground font-medium max-w-[850px] leading-relaxed">
					<?php echo esc_html( $section['description'] ); ?>
				</h4>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $section['custom_content'] ) ) : ?>
			<div class="betterlytics-custom-content">
				<?php echo $section['custom_content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Custom HTML content ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $section['fields'] ) ) : ?>
			<div class="bg-card border border-border rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.05)] overflow-hidden">
				<div class="divide-y divide-border">
					<?php foreach ( $section['fields'] as $field ) : ?>
						<?php betterlytics_render_checkmark_row( $field ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Renders a unified Checkmark Card row.
 *
 * @param array $field Field configuration.
 */
function betterlytics_render_checkmark_row( $field ) {
	$is_select = ! empty( $field['type'] ) && 'select' === $field['type'];
	?>
	<div class="betterlytics-checkmark-row group hover:bg-muted/5 transition-all duration-200">
		<div class="flex items-center justify-between py-6 !pl-10 !pr-10">
			<div class="flex flex-col gap-1 min-w-0 flex-1">
				<div class="flex items-center gap-2">
					<span class="text-base font-bold text-foreground truncate">
						<?php echo esc_html( $field['label'] ); ?>
					</span>
					<?php
					if ( ! empty( $field['help_path'] ) ) {
						echo Betterlytics_Admin_Controller::get_help_link( $field['help_path'], 'is-blue' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
				<?php if ( ! empty( $field['description'] ) ) : ?>
					<span class="text-sm text-muted-foreground/80 font-medium truncate">
						<?php echo wp_kses_post( $field['description'] ); ?>
					</span>
				<?php endif; ?>
			</div>

			<div class="flex items-center gap-6 shrink-0 ml-8">
				<?php if ( $is_select ) : ?>
					<select name="<?php echo esc_attr( $field['name'] ); ?>" class="bg-card border-border rounded-xl shadow-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all pr-12 !py-2.5 !text-sm font-bold">
						<?php foreach ( $field['options'] as $option_value => $option_label ) : ?>
							<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $field['value'], $option_value ); ?>>
								<?php echo esc_html( $option_label ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				<?php else : ?>
					<label class="relative flex items-center cursor-pointer">
						<input type="checkbox" name="<?php echo esc_attr( $field['name'] ); ?>" value="1" <?php checked( ! empty( $field['checked'] ) ); ?> class="w-6 h-6 rounded-lg border-2 border-border text-primary focus:ring-primary/30 transition-all cursor-pointer">
					</label>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
}

/*
 * =============================================================================
 * BROWSER EVENTS TAB - Section Definitions
 * =============================================================================
 */
$betterlytics_browser_sections = [
	[
		'title'       => __( 'Page Events', 'betterlytics' ),
		'description' => __( 'Configure tracking for standard page interactions and performance metrics.', 'betterlytics' ),
		'fields'      => [
			[
				'label'       => __( '404 Error Pages', 'betterlytics' ),
				'name'        => 'betterlytics_options[track_404][enabled]',
				'checked'     => ! empty( $betterlytics_options['track_404']['enabled'] ),
				'description' => __( 'Track when visitors land on pages that don\'t exist', 'betterlytics' ),
			],
			[
				'label'       => __( 'Site Search', 'betterlytics' ),
				'name'        => 'betterlytics_options[track_search][enabled]',
				'checked'     => ! empty( $betterlytics_options['track_search']['enabled'] ),
				'description' => __( 'Track search queries on your site', 'betterlytics' ),
			],
			[
				'label'       => __( 'Core Web Vitals', 'betterlytics' ),
				'name'        => 'betterlytics_options[track_web_vitals]',
				'checked'     => ! empty( $betterlytics_options['track_web_vitals'] ),
				'description' => __( 'Track Core Web Vitals performance metrics', 'betterlytics' ),
				'help_path'   => 'integration/web-vitals',
			],
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
			],
		],
	],
	[
		'title'       => __( 'Click Events', 'betterlytics' ),
		'description' => __( 'Track specific element interactions across your site.', 'betterlytics' ),
		'fields'      => [
			[
				'label'       => __( 'File Downloads', 'betterlytics' ),
				'name'        => 'betterlytics_options[track_downloads][enabled]',
				'checked'     => ! empty( $betterlytics_options['track_downloads']['enabled'] ),
				'description' => __( 'Track downloads of files (.pdf, .zip, .doc, etc.)', 'betterlytics' ),
			],
			[
				'label'       => __( 'CSS Class Events', 'betterlytics' ),
				'name'        => 'betterlytics_options[track_css_events][enabled]',
				'checked'     => ! empty( $betterlytics_options['track_css_events']['enabled'] ),
				'description' => sprintf(
					/* translators: %s: CSS class example */
					__( 'Track clicks on elements with %s class', 'betterlytics' ),
					'<code class="bg-muted px-1.5 py-0.5 rounded text-primary">betterlytics-event-name=YourEvent</code>'
				),
				'help_path'   => 'integration/custom-events',
			],
		],
	],
];
?>



<div class="animate-in fade-in duration-500 pb-24">

	<div class="flex items-center justify-between mb-8">
		<div>
			<h2 class="text-3xl font-bold text-foreground tracking-tight mb-2">
				<?php esc_html_e( 'Event Tracking', 'betterlytics' ); ?>
			</h2>
			<p class="text-muted-foreground font-medium">
				<?php
				$betterlytics_dashboard_url = 'https://www.betterlytics.io/dashboards';
				if ( ! empty( $betterlytics_options['site_id'] ) ) {
					$betterlytics_dashboard_url .= '/' . esc_attr( $betterlytics_options['site_id'] );
				}
				printf(
					/* translators: %s: link to Betterlytics dashboard */
					esc_html__( 'Configure which events to track on your site. View your tracked events on your %s.', 'betterlytics' ),
					'<a href="' . esc_url( $betterlytics_dashboard_url ) . '" target="_blank" class="text-primary font-bold hover:underline underline-offset-4 decoration-2">' . esc_html__( 'Betterlytics dashboard', 'betterlytics' ) . '</a>'
				);
				?>
			</p>
		</div>
		<div class="shrink-0">
			<?php echo Betterlytics_Admin_Controller::get_help_link( 'integration/custom-events', 'is-blue scale-125' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>

	<form action="options.php" method="post" id="betterlytics-settings-form">
		<?php settings_fields( 'betterlytics_events' ); ?>


		
		<div class="bg-card border border-border rounded-2xl shadow-sm flex flex-col min-h-[600px] mb-8">
			<!-- Multi-tab Card Header -->
			<div class="bg-muted/30 border-b border-border p-6 flex items-center justify-between">
				<div class="flex items-center gap-2">
					<h3 class="text-lg font-bold text-foreground tracking-tight">
						<?php esc_html_e( 'Browser Events', 'betterlytics' ); ?>
					</h3>
					<span class="px-2.5 py-0.5 rounded-full bg-primary/10 text-primary text-xs font-bold border border-primary/20">
						<?php esc_html_e( 'Client-side', 'betterlytics' ); ?>
					</span>
				</div>
			</div>

			<!-- Card Body -->
			<div class="flex-1 p-10 relative">
				<!-- Browser Events Tab -->
				<div id="tab-browser" class="betterlytics-tab-content active">
					<?php
					foreach ( $betterlytics_browser_sections as $betterlytics_index => $betterlytics_section ) {
						betterlytics_render_event_card( $betterlytics_section, $betterlytics_index > 0 );
					}
					?>
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
