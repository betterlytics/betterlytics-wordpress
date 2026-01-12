<?php
/**
 * Setup Guide partial for the plugin.
 *
 * @package    Betterlytics
 * @subpackage Betterlytics/admin/partials
 * @since      1.0.2
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$betterlytics_options     = Betterlytics_Options::get_options();
$betterlytics_is_enabled  = ! empty( $betterlytics_options['enabled'] );
$betterlytics_has_site_id = ! empty( $betterlytics_options['site_id'] );

/**
 * Define the steps data structure for reusability.
 */
$steps = array(
	array(
		'id'          => 'site_id',
		'title'       => __( 'Get your Site ID', 'betterlytics' ),
		'description' => sprintf(
			/* translators: %s: link to Betterlytics dashboard */
			__( 'Sign up or log in to your %s to get your unique Site ID.', 'betterlytics' ),
			'<a href="https://www.betterlytics.io/dashboards" target="_blank" class="text-primary underline font-bold">' . __( 'Betterlytics dashboard', 'betterlytics' ) . '</a>'
		),
		'completed'   => $betterlytics_has_site_id,
		'cta_text'    => __( 'Enter Site ID', 'betterlytics' ),
		'cta_url'     => admin_url( 'options-general.php?page=betterlytics&tab=settings' ),
	),
	array(
		'id'          => 'enable_tracking',
		'title'       => __( 'Enable tracking', 'betterlytics' ),
		'description' => __( 'Turn on tracking to start collecting analytics data.', 'betterlytics' ),
		'completed'   => $betterlytics_is_enabled,
		'cta_text'    => __( 'Enable Tracking', 'betterlytics' ),
		'cta_url'     => admin_url( 'options-general.php?page=betterlytics&tab=settings' ),
		'dependency'  => $betterlytics_has_site_id,
	),
	array(
		'id'          => 'configure_events',
		'title'       => __( 'Configure events (optional)', 'betterlytics' ),
		'description' => __( 'Set up event tracking for downloads, outbound links, WooCommerce, and more.', 'betterlytics' ),
		'completed'   => false, // Optional step
		'cta_text'    => __( 'Configure Events', 'betterlytics' ),
		'cta_url'     => admin_url( 'options-general.php?page=betterlytics&tab=events' ),
		'dependency'  => $betterlytics_has_site_id && $betterlytics_is_enabled,
	),
);

// Determine which step is currently "active"
$active_step_id = null;
foreach ( $steps as $step ) {
	$dependency_met = ! isset( $step['dependency'] ) || $step['dependency'];
	if ( ! $step['completed'] && $dependency_met ) {
		$active_step_id = $step['id'];
		break;
	}
}
?>

<div class="betterlytics-setup-steps bg-card border border-border rounded-xl shadow-sm overflow-hidden">
	<?php foreach ( $steps as $index => $step ) : ?>
		<?php
		$is_completed = $step['completed'];
		$is_active    = $step['id'] === $active_step_id;
		
		// Scale: py-6 px-10 (Smaller than previous 10)
		$step_classes = 'flex items-center gap-6 py-4 px-8 border-border border-b last:border-b-0 transition-all duration-200';
		if ( $is_completed ) $step_classes .= ' opacity-80';
		if ( $is_active )    $step_classes .= ' bg-primary/5 border-l-4 border-l-primary !pl-[30px]'; 
		if ( ! $is_completed && ! $is_active ) $step_classes .= ' opacity-60';

		// Strikethrough both lines
		$text_classes = 'm-0 transition-all duration-200';
		if ( $is_completed ) $text_classes .= ' line-through opacity-70';
		?>
		
		<div class="<?php echo esc_attr( $step_classes ); ?>">
			<!-- Large Numbers (w-12 h-12 = 48px, 1.5x of 32px standard) -->
			<div class="shrink-0">
				<div class="w-10 h-10 rounded-full border-2 flex items-center justify-center font-bold text-lg transition-all duration-200 <?php echo $is_completed ? 'bg-primary border-primary text-white' : 'border-primary text-primary bg-transparent'; ?>">
					<?php if ( $is_completed ) : ?>
						<span class="dashicons dashicons-yes !text-2xl !w-6 !h-6 !leading-none"></span>
					<?php else : ?>
						<?php echo esc_html( $index + 1 ); ?>
					<?php endif; ?>
				</div>
			</div>
			
			<!-- Content centered vertically -->
			<div class="flex-1">
				<h3 class="<?php echo esc_attr( $text_classes ); ?> text-lg font-bold text-foreground">
					<?php echo esc_html( $step['title'] ); ?>
				</h3>
				<p class="<?php echo esc_attr( $text_classes ); ?> text-sm font-medium text-muted-foreground mt-0.5">
					<?php echo wp_kses_post( $step['description'] ); ?>
				</p>
			</div>

			<?php if ( ! $is_completed ) : ?>
				<?php 
				$dependency_met = ! isset( $step['dependency'] ) || $step['dependency'];
				if ( $is_active || $dependency_met ) : 
				?>
					<div class="shrink-0 ml-4">
						<a href="<?php echo esc_url( $step['cta_url'] ); ?>" class="button <?php echo $is_active ? 'button-primary' : ''; ?> !m-0">
							<?php echo esc_html( $step['cta_text'] ); ?>
						</a>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
