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
$betterlytics_has_woocommerce   = class_exists( 'WooCommerce' );
$betterlytics_setup_incomplete  = empty( $betterlytics_options['site_id'] ) || empty( $betterlytics_options['enabled'] );
$betterlytics_banner_dismissed  = Betterlytics_Admin::is_setup_banner_dismissed();
$betterlytics_show_setup_banner = $betterlytics_setup_incomplete && ! $betterlytics_banner_dismissed;

/**
 * Helper function to render a settings section with optional separator.
 *
 * @param array $section Section configuration.
 * @param bool  $show_separator Whether to show separator before section.
 */
function betterlytics_render_section( $section, $show_separator = false ) {
	if ( $show_separator ) {
		echo '<hr class="border-t border-border my-10">';
	}
	?>
	<div class="betterlytics-section mb-12 last:mb-0">
		<h3 class="text-xl font-bold mb-3 flex items-center gap-2 text-foreground tracking-tight">
			<?php echo esc_html( $section['title'] ); ?>
			<?php
			if ( ! empty( $section['help_path'] ) ) {
				echo Betterlytics_Admin_Controller::get_help_link( $section['help_path'], 'is-blue' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</h3>
		<?php if ( ! empty( $section['description'] ) ) : ?>
			<p class="mt-0 mb-8 text-sm text-foreground/70 leading-relaxed max-w-[850px] font-medium"><?php echo esc_html( $section['description'] ); ?></p>
		<?php endif; ?>
		
		<?php if ( ! empty( $section['custom_content'] ) ) : ?>
			<div class="betterlytics-custom-content">
				<?php echo $section['custom_content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Custom HTML content ?>
			</div>
		<?php endif; ?>
		
		<?php if ( ! empty( $section['fields'] ) ) : ?>
			<div class="bg-card border border-border rounded-xl shadow-[0_1px_3px_rgba(0,0,0,0.05)] overflow-hidden">
				<table class="form-table w-full m-0" role="presentation">
					<tbody class="divide-y divide-border">
						<?php foreach ( $section['fields'] as $field ) : ?>
							<tr class="hover:bg-muted/5 transition-colors">
								<th scope="row" class="w-[300px] py-6 px-10 text-left align-middle border-none">
									<span class="flex items-center gap-2 font-semibold text-foreground">
										<?php echo esc_html( $field['label'] ); ?>
										<?php
										if ( ! empty( $field['help_path'] ) ) {
											echo Betterlytics_Admin_Controller::get_help_link( $field['help_path'], 'is-blue' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										}
										?>
									</span>
								</th>
								<td class="py-6 px-10 align-middle border-none">
									<?php if ( ! empty( $field['type'] ) && 'select' === $field['type'] ) : ?>
										<select name="<?php echo esc_attr( $field['name'] ); ?>" class="bg-card border-border rounded-lg shadow-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all pr-10">
											<?php foreach ( $field['options'] as $option_value => $option_label ) : ?>
												<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $field['value'], $option_value ); ?>>
													<?php echo esc_html( $option_label ); ?>
												</option>
											<?php endforeach; ?>
										</select>
										<?php if ( ! empty( $field['description'] ) ) : ?>
											<p class="description mt-2.5 text-xs text-muted-foreground font-medium"><?php echo wp_kses_post( $field['description'] ); ?></p>
										<?php endif; ?>
									<?php else : ?>
										<label class="flex items-center gap-3 cursor-pointer group">
											<input type="checkbox" name="<?php echo esc_attr( $field['name'] ); ?>" value="1" <?php checked( ! empty( $field['checked'] ) ); ?> class="w-5 h-5 rounded border-border text-primary focus:ring-primary/30 transition-all">
											<span class="text-sm font-medium text-foreground/80 group-hover:text-foreground transition-colors"><?php echo wp_kses_post( $field['description'] ); ?></span>
										</label>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
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

/*
 * =============================================================================
 * SERVER HOOKS TAB - Section Definitions
 * =============================================================================
 */

// Custom Hooks Mapper section content.
ob_start();
?>
<div class="betterlytics-hooks-mapper-container">
	<!-- We remove the .fixed class from the table to avoid Tailwind conflicts with position: fixed -->
	<div class="bg-card border border-border rounded-xl shadow-[0_1px_3px_rgba(0,0,0,0.05)] overflow-hidden mb-6">
		<table class="wp-list-table widefat striped w-full m-0 border-collapse !static !relative-none" id="betterlytics-hooks-table">
			<thead class="bg-muted/30">
				<tr>
					<th scope="col" class="column-enabled py-4 px-6 border-b border-border text-left w-[100px] text-xs font-bold uppercase tracking-wider text-muted-foreground"><?php esc_html_e( 'Enabled', 'betterlytics' ); ?></th>
					<th scope="col" class="column-wp-hook py-4 px-6 border-b border-border text-left text-xs font-bold uppercase tracking-wider text-muted-foreground"><?php esc_html_e( 'WordPress Hook', 'betterlytics' ); ?></th>
					<th scope="col" class="column-event-name py-4 px-6 border-b border-border text-left text-xs font-bold uppercase tracking-wider text-muted-foreground"><?php esc_html_e( 'Event Name', 'betterlytics' ); ?></th>
					<th scope="col" class="column-actions py-4 px-6 border-b border-border text-right w-[120px] text-xs font-bold uppercase tracking-wider text-muted-foreground"><?php esc_html_e( 'Actions', 'betterlytics' ); ?></th>
				</tr>
			</thead>
			<tbody id="betterlytics-hooks-list" class="divide-y divide-border">
				<!-- Hooks will be rendered via JavaScript -->
			</tbody>
		</table>
	</div>

	<p class="mt-6">
		<button type="button" class="button button-secondary !flex items-center gap-2 !px-5 !py-1.5 !h-auto !text-sm !font-bold rounded-lg border-border hover:bg-muted transition-all" id="betterlytics-add-hook">
			<span class="dashicons dashicons-plus !text-xs !w-3.5 !h-3.5 !leading-none"></span>
			<?php esc_html_e( 'Add Hook', 'betterlytics' ); ?>
		</button>
	</p>
</div>
<?php
$betterlytics_hooks_mapper_content = ob_get_clean();

$betterlytics_server_sections = [
	[
		'title'          => __( 'Custom Hooks Mapper', 'betterlytics' ),
		'description'    => __( 'Map any WordPress action hook to a Betterlytics event.', 'betterlytics' ),
		'custom_content' => $betterlytics_hooks_mapper_content,
		'fields'         => [],
		'help_path'      => 'integration/custom-events',
	],
	[
		'title'       => __( 'WordPress User Management', 'betterlytics' ),
		'description'    => __( 'Track essential WordPress user lifecycle events.', 'betterlytics' ),
		'fields'      => [
			[
				'label'       => __( 'User Login', 'betterlytics' ),
				'name'        => 'betterlytics_options[track_wp_login]',
				'checked'     => ! empty( $betterlytics_options['track_wp_login'] ),
				'description' => __( 'Track when a user logs in (Event: user_login)', 'betterlytics' ),
			],
			[
				'label'       => __( 'User Logout', 'betterlytics' ),
				'name'        => 'betterlytics_options[track_wp_logout]',
				'checked'     => ! empty( $betterlytics_options['track_wp_logout'] ),
				'description' => __( 'Track when a user logs out (Event: user_logout)', 'betterlytics' ),
			],
			[
				'label'       => __( 'User Registration', 'betterlytics' ),
				'name'        => 'betterlytics_options[track_user_register]',
				'checked'     => ! empty( $betterlytics_options['track_user_register'] ),
				'description' => __( 'Track when a new user registers (Event: user_register)', 'betterlytics' ),
			],
		],
	],
];

// Add WooCommerce section if available.
if ( $betterlytics_has_woocommerce ) {
	$betterlytics_server_sections[] = [
		'title'       => __( 'WooCommerce Events', 'betterlytics' ),
		'description' => __( 'Automatically bridge WooCommerce actions to analytics events.', 'betterlytics' ),
		'fields'      => [
			[
				'label'       => __( 'Add to Cart', 'betterlytics' ),
				'name'        => 'betterlytics_options[woo_add_to_cart][enabled]',
				'checked'     => ! empty( $betterlytics_options['woo_add_to_cart']['enabled'] ),
				'description' => __( 'Track when products are added to the cart', 'betterlytics' ),
			],
			[
				'label'       => __( 'Remove from Cart', 'betterlytics' ),
				'name'        => 'betterlytics_options[woo_remove_from_cart][enabled]',
				'checked'     => ! empty( $betterlytics_options['woo_remove_from_cart']['enabled'] ),
				'description' => __( 'Track when products are removed from the cart', 'betterlytics' ),
			],
			[
				'label'       => __( 'Begin Checkout', 'betterlytics' ),
				'name'        => 'betterlytics_options[woo_checkout][enabled]',
				'checked'     => ! empty( $betterlytics_options['woo_checkout']['enabled'] ),
				'description' => __( 'Track when customers start the checkout process', 'betterlytics' ),
			],
			[
				'label'       => __( 'Purchase Complete', 'betterlytics' ),
				'name'        => 'betterlytics_options[woo_purchase][enabled]',
				'checked'     => ! empty( $betterlytics_options['woo_purchase']['enabled'] ),
				'description' => __( 'Track completed purchases', 'betterlytics' ),
			],
		],
	];
}
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

		<?php
		$current_subtab = isset( $_GET['subtab'] ) ? sanitize_text_field( wp_unslash( $_GET['subtab'] ) ) : 'browser';
		?>
		
		<div class="bg-card border border-border rounded-2xl shadow-sm flex flex-col min-h-[600px] mb-8">
			<!-- Multi-tab Card Header -->
			<div class="bg-muted/30 border-b border-border p-6 flex items-center justify-between">
				<nav class="betterlytics-subtabs flex gap-2 bg-muted/50 p-1 rounded-xl border border-border/50 no-scrollbar overflow-x-auto">
					<?php
					$subtabs = [
						'browser' => __( 'Browser Events', 'betterlytics' ),
						'server'  => __( 'Server Hooks', 'betterlytics' ),
					];
					foreach ( $subtabs as $subtab_key => $subtab_label ) :
						$is_active = $current_subtab === $subtab_key;
						?>
						<a href="?page=betterlytics&tab=events&subtab=<?php echo esc_attr( $subtab_key ); ?>" 
						   class="betterlytics-subtab px-6 py-2 text-sm font-bold rounded-lg transition-all duration-200 text-muted-foreground hover:text-foreground hover:bg-black/5 [.active]:text-primary [.active]:bg-card [.active]:shadow-sm [.active]:border [.active]:border-border/50 <?php echo $is_active ? 'active' : ''; ?>" 
						   data-tab="<?php echo esc_attr( $subtab_key ); ?>">
							<?php echo esc_html( $subtab_label ); ?>
						</a>
					<?php endforeach; ?>
				</nav>
				<div class="text-xs font-bold text-muted-foreground/40 uppercase tracking-widest hidden sm:block">
					<?php echo 'browser' === $current_subtab ? esc_html__( 'Client-side', 'betterlytics' ) : esc_html__( 'Server-side', 'betterlytics' ); ?>
				</div>
			</div>

			<!-- Multi-tab Card Body -->
			<div class="flex-1 p-10 relative">
				<!-- Browser Events Tab -->
				<div id="tab-browser" class="betterlytics-tab-content <?php echo 'browser' === $current_subtab ? 'active' : ''; ?>">
					<?php
					foreach ( $betterlytics_browser_sections as $betterlytics_index => $betterlytics_section ) {
						betterlytics_render_section( $betterlytics_section, $betterlytics_index > 0 );
					}
					?>
				</div>

				<!-- Server Hooks Tab -->
				<div id="tab-server" class="betterlytics-tab-content <?php echo 'server' === $current_subtab ? 'active' : ''; ?>">
					<?php
					foreach ( $betterlytics_server_sections as $betterlytics_index => $betterlytics_section ) {
						betterlytics_render_section( $betterlytics_section, $betterlytics_index > 0 );
					}
					?>
				</div>
			</div>
		</div>

		<!-- Global Sticky Footer -->
		<div class="betterlytics-sticky-footer">
			<?php submit_button( __( 'Save Settings', 'betterlytics' ), 'primary', 'submit', false, [ 'id' => 'betterlytics-save-settings', 'class' => 'button-primary !py-3.5 !px-16 !h-auto !text-base !font-bold rounded-xl shadow-lg transition-all hover:scale-[1.02] active:scale-[0.98]' ] ); ?>
		</div>
	</form>
</div>
