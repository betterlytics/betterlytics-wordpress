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
					<th scope="col" class="column-actions py-4 px-6 border-b border-border text-left w-[140px] text-xs font-bold uppercase tracking-wider text-muted-foreground"><?php esc_html_e( 'Actions', 'betterlytics' ); ?></th>
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
				<span class="dashicons dashicons-plus !text-lg !leading-none !flex !items-center !justify-center !w-5 !h-5 !m-0"></span>
				<?php esc_html_e( 'Add Hook', 'betterlytics' ); ?>
			</button>
		</div>
	</div>
</div>
<?php
$betterlytics_hooks_mapper_content = ob_get_clean();

$betterlytics_sections = [
	[
		'title'       => __( 'Custom Hooks Mapper', 'betterlytics' ),
		'description' => __( 'Map any WordPress action hook to a Betterlytics event.', 'betterlytics' ),
		'children'    => $betterlytics_hooks_mapper_content,
		'fields'      => [],
		'help_path'   => 'integration/custom-events',
		'is_custom'   => true, // Flag to indicate custom content rendering.
	],
];

// Add WooCommerce section if available.
if ( $betterlytics_has_woocommerce ) {
	$betterlytics_sections[] = [
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

// Loop through sections and render cards.
foreach ( $betterlytics_sections as $betterlytics_index => $betterlytics_section ) {
	// Capture the fields content or custom content.
	$betterlytics_content = '';
	if ( ! empty( $betterlytics_section['is_custom'] ) ) {
		$betterlytics_content = '<div class="betterlytics-custom-content">' . $betterlytics_section['children'] . '</div>';
	} else {
		ob_start();
		if ( ! empty( $betterlytics_section['fields'] ) ) {
			foreach ( $betterlytics_section['fields'] as $betterlytics_field ) {
				Betterlytics_Admin_Controller::render_component( 'ui/setting-row', $betterlytics_field );
			}
		}
		$betterlytics_content = ob_get_clean();
	}

	// Render separator if not first item.
	if ( $betterlytics_index > 0 ) {
		echo '<hr class="border-t border-border my-12">';
	}

	Betterlytics_Admin_Controller::render_component(
		'ui/card',
		[
			'title'       => $betterlytics_section['title'],
			'description' => $betterlytics_section['description'],
			'help_path'   => $betterlytics_section['help_path'] ?? '',
			'children'    => $betterlytics_content,
		]
	);
}
