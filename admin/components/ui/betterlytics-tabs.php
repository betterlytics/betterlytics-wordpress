<?php
/**
 * UI Component: Tabs
 *
 * @package Betterlytics
 * @subpackage Betterlytics/admin/components/ui
 *
 * @var array $args {
 *     @type array  $tabs        Associative array of slug => label.
 *     @type string $current_tab Key of the currently active tab.
 *     @type string $base_url    Base URL for tab links.
 *     @type string $param_name  URL parameter name for the tab (default 'subtab').
 * }
 */

$betterlytics_tabs        = $args['tabs'] ?? [];
$betterlytics_current_tab = $args['current_tab'] ?? '';
$betterlytics_base_url    = $args['base_url'] ?? '';
$betterlytics_param_name  = $args['param_name'] ?? 'subtab';
?>
<div class="bg-muted/30 border-b border-border p-6 flex items-center justify-between">
	<nav class="betterlytics-subtabs flex gap-2 bg-muted/50 p-1 rounded-xl border border-border/50 no-scrollbar overflow-x-auto">
		<?php
		foreach ( $betterlytics_tabs as $betterlytics_slug => $betterlytics_label ) :
			$betterlytics_is_active = $betterlytics_current_tab === $betterlytics_slug;
			$betterlytics_url       = add_query_arg( $betterlytics_param_name, $betterlytics_slug, $betterlytics_base_url );
			?>
			<a href="<?php echo esc_url( $betterlytics_url ); ?>" 
				class="betterlytics-subtab px-6 py-2 text-sm font-bold rounded-lg transition-all duration-200 text-muted-foreground hover:text-foreground hover:bg-black/5 [.active]:text-primary [.active]:bg-card [.active]:shadow-sm [.active]:border [.active]:border-border/50 <?php echo $betterlytics_is_active ? 'active' : ''; ?>" 
				data-tab="<?php echo esc_attr( $betterlytics_slug ); ?>">
				<?php echo esc_html( $betterlytics_label ); ?>
			</a>
		<?php endforeach; ?>
	</nav>
	<div class="text-xs font-bold text-muted-foreground/40 uppercase tracking-widest hidden sm:block">
		<?php echo 'browser' === $betterlytics_current_tab ? esc_html__( 'Client-side', 'betterlytics' ) : esc_html__( 'Server-side', 'betterlytics' ); ?>
	</div>
</div>
