<?php
/**
 * Home/Setup page view for the plugin.
 *
 * @package    Betterlytics
 * @subpackage Betterlytics/admin/partials
 * @since      1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$betterlytics_options        = Betterlytics_Options::get_options();
$betterlytics_is_enabled     = ! empty( $betterlytics_options['enabled'] );
$betterlytics_has_site_id    = ! empty( $betterlytics_options['site_id'] );
$betterlytics_setup_complete = Betterlytics_Options::is_setup_complete();
?>
	<?php require BETTERLYTICS_PLUGIN_DIR . 'admin/partials/betterlytics-setup-guide.php'; ?>

	<div class="betterlytics-resources mt-8">
		<h2 class="text-lg font-semibold mb-4 pb-2 border-b border-border"><?php esc_html_e( 'Resources', 'betterlytics' ); ?></h2>
		<ul class="bg-card border border-border rounded-md divide-y divide-border list-none p-0 m-0">
			<li>
				<a href="https://betterlytics.io/docs/integration/wordpress" target="_blank" class="flex items-center gap-2 px-4 py-3 no-underline text-foreground hover:bg-muted transition-colors">
					<span class="dashicons dashicons-book text-muted-foreground"></span>
					<?php esc_html_e( 'Documentation', 'betterlytics' ); ?>
					<span class="dashicons dashicons-external text-muted-foreground/50 !w-4 !h-4 ml-auto"></span>
				</a>
			</li>
			<li>
				<a href="https://betterlytics.io/dashboards" target="_blank" class="flex items-center gap-2 px-4 py-3 no-underline text-foreground hover:bg-muted transition-colors">
					<span class="dashicons dashicons-chart-bar text-muted-foreground"></span>
					<?php esc_html_e( 'Betterlytics Dashboard', 'betterlytics' ); ?>
					<span class="dashicons dashicons-external text-muted-foreground/50 !w-4 !h-4 ml-auto"></span>
				</a>
			</li>
			<li>
				<a href="https://github.com/betterlytics/betterlytics-wordpress" target="_blank" class="flex items-center gap-2 px-4 py-3 no-underline text-foreground hover:bg-muted transition-colors">
					<span class="dashicons dashicons-editor-code text-muted-foreground"></span>
					<?php esc_html_e( 'GitHub Repository', 'betterlytics' ); ?>
					<span class="dashicons dashicons-external text-muted-foreground/50 !w-4 !h-4 ml-auto"></span>
				</a>
			</li>
			<li>
				<a href="https://betterlytics.io" target="_blank" class="flex items-center gap-2 px-4 py-3 no-underline text-foreground hover:bg-muted transition-colors">
					<span class="dashicons dashicons-admin-home text-muted-foreground"></span>
					<?php esc_html_e( 'Betterlytics Website', 'betterlytics' ); ?>
					<span class="dashicons dashicons-external text-muted-foreground/50 !w-4 !h-4 ml-auto"></span>
				</a>
			</li>
		</ul>
	</div>

