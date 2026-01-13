<?php
/**
 * View: Server Hooks List
 *
 * @package Betterlytics
 * @subpackage Betterlytics/admin/components/views/events
 * 
 * @var array $args {
 *     @type array $options Plugin options.
 * }
 */

$betterlytics_options         = $args['options'];
$betterlytics_has_woocommerce = class_exists( 'WooCommerce' );

/*
 * =============================================================================
 * SERVER HOOKS TAB - Section Definitions
 * =============================================================================
 */

// Custom Hooks Mapper section content.
ob_start();
?>
<div class="betterlytics-hooks-mapper-container">
	<div class="bg-card border border-border rounded-xl shadow-[0_1px_3px_rgba(0,0,0,0.05)] overflow-hidden">
		<table class="wp-list-table widefat striped w-full m-0 border-collapse !static !relative-none" id="betterlytics-hooks-table">
			<thead class="bg-muted/30">
				<tr>
					<th scope="col" class="column-enabled py-4 px-6 border-b border-border text-center w-auto text-xs font-bold uppercase tracking-wider text-muted-foreground"><?php esc_html_e( 'Enabled', 'betterlytics' ); ?></th>
					<th scope="col" class="column-wp-hook py-4 px-6 border-b border-border text-left text-xs font-bold uppercase tracking-wider text-muted-foreground"><?php esc_html_e( 'WordPress Hook', 'betterlytics' ); ?></th>
					<th scope="col" class="column-event-name py-4 px-6 border-b border-border text-left text-xs font-bold uppercase tracking-wider text-muted-foreground"><?php esc_html_e( 'Event Name', 'betterlytics' ); ?></th>
					<th scope="col" class="column-actions py-4 px-6 border-b border-border text-right w-[120px] text-xs font-bold uppercase tracking-wider text-muted-foreground"><?php esc_html_e( 'Actions', 'betterlytics' ); ?></th>
				</tr>
			</thead>
			<tbody id="betterlytics-hooks-list" class="divide-y divide-border">
				<!-- Hooks will be rendered via JavaScript -->
			</tbody>
		</table>
        
        <!-- Add Hook Button Area -->
        <div class="bg-card p-4 flex items-center justify-between border-t border-border">
            <a href="https://betterlytics.io/docs/integration/custom-events" target="_blank" class="text-xs text-muted-foreground hover:text-primary font-bold flex items-center gap-1.5 transition-colors no-underline">
                <span class="dashicons dashicons-book !w-auto !h-auto !text-sm leading-none"></span>
                <?php esc_html_e( 'Learn how to map custom hooks', 'betterlytics' ); ?>
                <span class="dashicons dashicons-external !w-auto !h-auto !text-[10px] leading-none opacity-70"></span>
            </a>

            <button type="button" class="button button-secondary !flex items-center gap-2 !px-5 !py-1.5 !h-auto !text-sm !font-bold rounded-lg border-border hover:bg-muted transition-all" id="betterlytics-add-hook">
                <span class="dashicons dashicons-plus !text-xs !w-3.5 !h-3.5 !leading-none"></span>
                <?php esc_html_e( 'Add Hook', 'betterlytics' ); ?>
            </button>
        </div>
	</div>
</div>
<?php
$betterlytics_hooks_mapper_content = ob_get_clean();

$sections = [
	[
		'title'       => __( 'Custom Hooks Mapper', 'betterlytics' ),
		'description' => __( 'Map any WordPress action hook to a Betterlytics event.', 'betterlytics' ),
		'children'    => $betterlytics_hooks_mapper_content,
		'fields'      => [],
		'help_path'   => 'integration/custom-events',
		'is_custom'   => true, // Flag to indicate custom content rendering
	],
	[
		'title'       => __( 'WordPress User Management', 'betterlytics' ),
		'description' => __( 'Track essential WordPress user lifecycle events.', 'betterlytics' ),
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
	$sections[] = [
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

// Loop through sections and render cards
foreach ( $sections as $index => $section ) {
	// Capture the fields content or custom content
	$content = '';
	if ( ! empty( $section['is_custom'] ) ) {
		$content = '<div class="betterlytics-custom-content">' . $section['children'] . '</div>';
	} else {
		ob_start();
		if ( ! empty( $section['fields'] ) ) {
			foreach ( $section['fields'] as $field ) {
				Betterlytics_Admin_Controller::render_component( 'ui/setting-row', $field );
			}
		}
		$content = ob_get_clean();
	}

    // Render separator if not first item
    if ( $index > 0 ) {
		echo '<hr class="border-t border-border my-12">';
	}

	Betterlytics_Admin_Controller::render_component( 'ui/card', [
		'title'       => $section['title'],
		'description' => $section['description'],
		'help_path'   => $section['help_path'] ?? '',
		'children'    => $content,
	] );
}
