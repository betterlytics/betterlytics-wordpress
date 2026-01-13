<?php
/**
 * UI Component: Card
 *
 * @package Betterlytics
 * @subpackage Betterlytics/admin/components/ui
 *
 * @var array $args {
 *     @type string $title       Card title.
 *     @type string $description Optional. Card description.
 *     @type string $help_path   Optional. Documentation path for help icon.
 *     @type string $children    Content to render inside the card (or inside the fields wrapper).
 * }
 */

$betterlytics_card_title       = $args['title'] ?? '';
$betterlytics_card_description = $args['description'] ?? '';
$betterlytics_card_help_path   = $args['help_path'] ?? '';
$betterlytics_card_children    = $args['children'] ?? '';
?>
<div class="betterlytics-event-card mb-12 last:mb-0">
	<div class="mb-8">
		<h1 class="text-2xl font-bold text-foreground tracking-tight mb-2 flex items-center gap-2">
			<?php echo esc_html( $betterlytics_card_title ); ?>
			<?php
			if ( ! empty( $betterlytics_card_help_path ) ) {
				echo Betterlytics_Admin_Controller::get_help_link( $betterlytics_card_help_path, 'is-blue' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</h1>
		<?php if ( ! empty( $betterlytics_card_description ) ) : ?>
			<h4 class="text-sm text-muted-foreground font-medium max-w-[850px] leading-relaxed">
				<?php echo esc_html( $betterlytics_card_description ); ?>
			</h4>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $betterlytics_card_children ) ) : ?>
		<div class="bg-card border border-border rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.05)] overflow-hidden">
			<div class="divide-y divide-border">
				<?php echo $betterlytics_card_children; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	<?php endif; ?>
</div>
