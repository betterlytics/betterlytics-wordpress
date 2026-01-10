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
		echo '<hr class="betterlytics-section-separator">';
	}
	?>
	<div class="betterlytics-section">
		<h2><?php echo esc_html( $section['title'] ); ?></h2>
		<?php if ( ! empty( $section['description'] ) ) : ?>
			<p class="description"><?php echo esc_html( $section['description'] ); ?></p>
		<?php endif; ?>
		
		<?php if ( ! empty( $section['custom_content'] ) ) : ?>
			<?php echo $section['custom_content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Custom HTML content ?>
		<?php endif; ?>
		
		<?php if ( ! empty( $section['fields'] ) ) : ?>
			<table class="form-table" role="presentation">
				<tbody>
					<?php foreach ( $section['fields'] as $field ) : ?>
						<tr>
							<th scope="row"><?php echo esc_html( $field['label'] ); ?></th>
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
										<p class="description"><?php echo wp_kses_post( $field['description'] ); ?></p>
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
<table class="wp-list-table widefat fixed striped" id="betterlytics-hooks-table">
	<thead>
		<tr>
			<th scope="col" class="column-enabled"><?php esc_html_e( 'Enabled', 'betterlytics' ); ?></th>
			<th scope="col" class="column-wp-hook"><?php esc_html_e( 'WordPress Hook', 'betterlytics' ); ?></th>
			<th scope="col" class="column-event-name"><?php esc_html_e( 'Event Name', 'betterlytics' ); ?></th>
			<th scope="col" class="column-actions"><?php esc_html_e( 'Actions', 'betterlytics' ); ?></th>
		</tr>
	</thead>
	<tbody id="betterlytics-hooks-list">
		<!-- Hooks will be rendered via JavaScript -->
	</tbody>
</table>

<p class="betterlytics-hooks-actions">
	<button type="button" class="button button-secondary" id="betterlytics-add-hook">
		<?php esc_html_e( 'Add Hook', 'betterlytics' ); ?>
	</button>
</p>
<?php
$betterlytics_hooks_mapper_content = ob_get_clean();

$betterlytics_server_sections = [
	[
		'title'          => __( 'Custom Hooks Mapper', 'betterlytics' ),
		'description'    => __( 'Map any WordPress action hook to a Betterlytics event.', 'betterlytics' ),
		'custom_content' => $betterlytics_hooks_mapper_content,
		'fields'         => [],
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

<?php if ( $betterlytics_show_setup_banner ) : ?>
<div class="betterlytics-setup-banner" id="betterlytics-setup-banner">
	<span class="dashicons dashicons-info"></span>
	<p>
		<?php
		printf(
			/* translators: %s: link to setup guide */
			esc_html__( 'Setup is not complete. Follow the %s to start tracking.', 'betterlytics' ),
			'<a href="' . esc_url( admin_url( 'admin.php?page=betterlytics' ) ) . '">' . esc_html__( 'setup guide', 'betterlytics' ) . '</a>'
		);
		?>
	</p>
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=betterlytics' ) ); ?>" class="button">
		<?php esc_html_e( 'View Setup', 'betterlytics' ); ?>
	</a>
	<button type="button" class="dismiss" id="betterlytics-dismiss-banner" title="<?php esc_attr_e( 'Dismiss', 'betterlytics' ); ?>">
		<span class="dashicons dashicons-no-alt"></span>
	</button>
</div>
<script>
document.getElementById('betterlytics-dismiss-banner').addEventListener('click', function() {
	var banner = document.getElementById('betterlytics-setup-banner');
	banner.style.display = 'none';
	var xhr = new XMLHttpRequest();
	xhr.open('POST', '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>');
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhr.send('action=betterlytics_dismiss_setup_banner&nonce=<?php echo esc_js( wp_create_nonce( 'betterlytics_admin' ) ); ?>');
});
</script>
<?php endif; ?>

<div class="wrap betterlytics-settings">
	<h1><?php esc_html_e( 'Events', 'betterlytics' ); ?></h1>
	<?php settings_errors(); ?>

	<p class="betterlytics-page-description">
		<?php
		$betterlytics_dashboard_url = 'https://www.betterlytics.io/dashboard';
		if ( ! empty( $betterlytics_options['site_id'] ) ) {
			$betterlytics_dashboard_url .= '/' . esc_attr( $betterlytics_options['site_id'] );
		}
		printf(
			/* translators: %s: link to Betterlytics dashboard */
			esc_html__( 'Configure which events to track on your site. View your tracked events on your %s.', 'betterlytics' ),
			'<a href="' . esc_url( $betterlytics_dashboard_url ) . '" target="_blank">' . esc_html__( 'Betterlytics dashboard', 'betterlytics' ) . '</a>'
		);
		?>
	</p>

	<form action="options.php" method="post" id="betterlytics-settings-form">
		<?php settings_fields( 'betterlytics_events' ); ?>

		<nav class="nav-tab-wrapper betterlytics-tabs">
			<a href="#browser" class="nav-tab nav-tab-active" data-tab="browser">
				<?php esc_html_e( 'Browser Events', 'betterlytics' ); ?>
			</a>
			<a href="#server" class="nav-tab" data-tab="server">
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
			<div class="betterlytics-sticky-footer-content">
				<?php submit_button( __( 'Save Settings', 'betterlytics' ), 'primary', 'submit', false, [ 'id' => 'betterlytics-save-settings' ] ); ?>
				<span id="betterlytics-hooks-status"></span>
			</div>
		</div>
	</form>
</div>
