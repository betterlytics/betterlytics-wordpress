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
		echo '<hr class="border-t border-border my-8">';
	}
	?>
	<div class="betterlytics-section mb-12 last:mb-0">
		<h3 class="text-lg font-bold mb-3 flex items-center gap-1.5 text-foreground tracking-tight">
			<?php echo esc_html( $section['title'] ); ?>
			<?php
			if ( ! empty( $section['help_path'] ) ) {
				echo Betterlytics_Admin_Controller::get_help_link( $section['help_path'], 'is-blue' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</h3>
		<?php if ( ! empty( $section['description'] ) ) : ?>
			<p class="description mt-0 mb-6 text-sm text-foreground/80 leading-relaxed max-w-[800px]"><?php echo esc_html( $section['description'] ); ?></p>
		<?php endif; ?>
		
		<?php if ( ! empty( $section['custom_content'] ) ) : ?>
			<?php echo $section['custom_content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Custom HTML content ?>
		<?php endif; ?>
		
		<?php if ( ! empty( $section['fields'] ) ) : ?>
			<div class="betterlytics-table-container">
				<table class="form-table w-full m-0" role="presentation">
					<tbody>
						<?php foreach ( $section['fields'] as $field ) : ?>
							<tr>
								<th scope="row">
									<span class="flex items-center gap-1.5">
										<?php echo esc_html( $field['label'] ); ?>
										<?php
										if ( ! empty( $field['help_path'] ) ) {
											echo Betterlytics_Admin_Controller::get_help_link( $field['help_path'], 'is-blue' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										}
										?>
									</span>
								</th>
								<td>
									<?php if ( ! empty( $field['type'] ) && 'select' === $field['type'] ) : ?>
										<select name="<?php echo esc_attr( $field['name'] ); ?>">
											<?php foreach ( $field['options'] as $option_value => $option_label ) : ?>
												<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $field['value'], $option_value ); ?>>
													<?php echo esc_html( $option_label ); ?>
												</option>
											<?php endforeach; ?>
										</select>
										<?php if ( ! empty( $field['description'] ) ) : ?>
											<p class="description mt-2 text-xs text-muted-foreground"><?php echo wp_kses_post( $field['description'] ); ?></p>
										<?php endif; ?>
									<?php else : ?>
										<label>
											<input type="checkbox" name="<?php echo esc_attr( $field['name'] ); ?>" value="1" <?php checked( ! empty( $field['checked'] ) ); ?>>
											<?php echo wp_kses_post( $field['description'] ); ?>
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
 * Add new sections by appending to this array. Each section will automatically
 * have a separator rendered before it (except the first one).
 */
$betterlytics_browser_sections = [
	[
		'title'       => __( 'Page Events', 'betterlytics' ),
		'description' => '',
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
		'description' => '',
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
					'<code>betterlytics-event-name=YourEvent</code>'
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
 * Add new sections by appending to this array. Each section will automatically
 * have a separator rendered before it (except the first one).
 */

// Custom Hooks Mapper section content (special handling needed).
ob_start();
?>
<div class="betterlytics-hooks-mapper-container">
	<div class="bg-card border border-border rounded-md shadow-sm overflow-hidden mb-4">
		<table class="wp-list-table widefat fixed striped w-full m-0 border-collapse" id="betterlytics-hooks-table">
			<thead class="bg-muted/30">
				<tr>
					<th scope="col" class="column-enabled py-3 px-4 border-b border-border text-left w-[80px] text-xs font-bold"><?php esc_html_e( 'Enabled', 'betterlytics' ); ?></th>
					<th scope="col" class="column-wp-hook py-3 px-4 border-b border-border text-left text-xs font-bold"><?php esc_html_e( 'WordPress Hook', 'betterlytics' ); ?></th>
					<th scope="col" class="column-event-name py-3 px-4 border-b border-border text-left text-xs font-bold"><?php esc_html_e( 'Event Name', 'betterlytics' ); ?></th>
					<th scope="col" class="column-actions py-3 px-4 border-b border-border text-center w-[100px] text-xs font-bold"><?php esc_html_e( 'Actions', 'betterlytics' ); ?></th>
				</tr>
			</thead>
			<tbody id="betterlytics-hooks-list" class="divide-y divide-border">
				<!-- Hooks will be rendered via JavaScript -->
			</tbody>
		</table>
	</div>

	<p class="betterlytics-hooks-actions flex items-center mt-4">
		<button type="button" class="button button-secondary !flex items-center gap-1" id="betterlytics-add-hook">
			<span class="dashicons dashicons-plus !text-sm !w-4 !h-4 !leading-none"></span>
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
		'description' => __( 'Automatically track user login, logout, and registration events.', 'betterlytics' ),
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
		'description' => __( 'Automatically map WooCommerce actions to Betterlytics events.', 'betterlytics' ),
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

<div>

	<h2 class="text-[22px] font-bold mb-6 flex items-center gap-2 text-foreground tracking-tight">
		<?php esc_html_e( 'Event Tracking', 'betterlytics' ); ?>
		<?php echo Betterlytics_Admin_Controller::get_help_link( 'integration/custom-events', 'is-blue' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</h2>

	<p class="betterlytics-page-description mb-6 text-muted-foreground">
		<?php
		$betterlytics_dashboard_url = 'https://www.betterlytics.io/dashboards';
		if ( ! empty( $betterlytics_options['site_id'] ) ) {
			$betterlytics_dashboard_url .= '/' . esc_attr( $betterlytics_options['site_id'] );
		}
		printf(
			/* translators: %s: link to Betterlytics dashboard */
			esc_html__( 'Configure which events to track on your site. View your tracked events on your %s.', 'betterlytics' ),
			'<a href="' . esc_url( $betterlytics_dashboard_url ) . '" target="_blank" class="text-primary hover:underline">' . esc_html__( 'Betterlytics dashboard', 'betterlytics' ) . '</a>'
		);
		?>
	</p>

	<form action="options.php" method="post" id="betterlytics-settings-form">
		<?php settings_fields( 'betterlytics_events' ); ?>

		<nav class="betterlytics-subtabs no-scrollbar">
			<a href="?page=betterlytics&tab=events&subtab=browser" class="betterlytics-subtab active" data-tab="browser">
				<?php esc_html_e( 'Browser Events', 'betterlytics' ); ?>
			</a>
			<a href="?page=betterlytics&tab=events&subtab=server" class="betterlytics-subtab" data-tab="server">
				<?php esc_html_e( 'Server Hooks', 'betterlytics' ); ?>
			</a>
		</nav>

		<!-- Browser Events Tab -->
		<div id="tab-browser" class="betterlytics-tab-content active">
			<?php
			foreach ( $betterlytics_browser_sections as $betterlytics_index => $betterlytics_section ) {
				betterlytics_render_section( $betterlytics_section, $betterlytics_index > 0 );
			}
			?>
		</div>

		<!-- Server Hooks Tab -->
		<div id="tab-server" class="betterlytics-tab-content">
			<?php
			foreach ( $betterlytics_server_sections as $betterlytics_index => $betterlytics_section ) {
				betterlytics_render_section( $betterlytics_section, $betterlytics_index > 0 );
			}
			?>
		</div>

		<div class="betterlytics-sticky-footer">
			<?php submit_button( __( 'Save Settings', 'betterlytics' ), 'primary', 'submit', false, [ 'id' => 'betterlytics-save-settings', 'class' => 'button-primary !py-2.5 !px-10 !h-auto !text-base !font-bold transition-all hover:scale-[1.02] active:scale-[0.98]' ] ); ?>
		</div>
	</form>
</div>
