<?php
/**
 * View: Browser Events List
 *
 * @package Betterlytics
 * @subpackage Betterlytics/admin/components/views/events
 *
 * @var array $args {
 *     @type array $options Plugin options.
 * }
 */

$betterlytics_options = $args['options'];

/*
 * =============================================================================
 * BROWSER EVENTS TAB - Section Definitions
 * =============================================================================
 */
$betterlytics_sections = [
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

// Loop through sections and render cards.
foreach ( $betterlytics_sections as $betterlytics_index => $betterlytics_section ) {
	// Capture the fields content.
	ob_start();
	if ( ! empty( $betterlytics_section['fields'] ) ) {
		foreach ( $betterlytics_section['fields'] as $betterlytics_field ) {
			Betterlytics_Admin_Controller::render_component( 'ui/setting-row', $betterlytics_field );
		}
	}
	$betterlytics_fields_html = ob_get_clean();

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
			'children'    => $betterlytics_fields_html,
		]
	);
}
