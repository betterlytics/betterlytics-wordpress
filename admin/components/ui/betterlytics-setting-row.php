<?php
/**
 * UI Component: Setting Row
 *
 * @package Betterlytics
 * @subpackage Betterlytics/admin/components/ui
 *
 * @var array $args {
 *     @type string $label       Setting label.
 *     @type string $name        Input name attribute.
 *     @type string $description Optional. Helper text.
 *     @type string $help_path   Optional. Documentation link.
 *     @type string $type        'checkbox' | 'select'. Default 'checkbox'.
 *     @type bool   $checked     For checkboxes.
 *     @type string $value       For selects.
 *     @type array  $options     For selects ([value => label]).
 *     @type bool   $indent      Optional. Indent as sub-option. Default false.
 * }
 */

$betterlytics_label       = $args['label'] ?? '';
$betterlytics_name        = $args['name'] ?? '';
$betterlytics_description = $args['description'] ?? '';
$betterlytics_help_path   = $args['help_path'] ?? '';
$betterlytics_type        = $args['type'] ?? 'checkbox';
$betterlytics_is_select   = 'select' === $betterlytics_type;
$betterlytics_indent      = ! empty( $args['indent'] );

// Styling for indented sub-options: compact, subordinate appearance.
if ( $betterlytics_indent ) {
	$betterlytics_row_classes   = '-mt-2';
	$betterlytics_inner_classes = 'py-3 !pl-16 !pr-10';
	$betterlytics_label_classes = 'text-sm font-medium text-muted-foreground';
	$betterlytics_desc_classes  = 'text-xs text-muted-foreground/60 font-normal';
	$betterlytics_check_classes = 'w-4 h-4 rounded border border-border text-primary focus:ring-primary/30 transition-all cursor-pointer';
} else {
	$betterlytics_row_classes   = '';
	$betterlytics_inner_classes = 'py-6 !pl-10 !pr-10';
	$betterlytics_label_classes = 'text-base font-bold text-foreground';
	$betterlytics_desc_classes  = 'text-sm text-muted-foreground/80 font-medium';
	$betterlytics_check_classes = 'w-6 h-6 rounded-lg border-2 border-border text-primary focus:ring-primary/30 transition-all cursor-pointer';
}
?>
<div class="betterlytics-checkmark-row group <?php echo esc_attr( $betterlytics_indent ? '' : 'hover:bg-muted/5' ); ?> transition-all duration-200 <?php echo esc_attr( $betterlytics_row_classes ); ?>">
	<div class="flex items-center justify-between <?php echo esc_attr( $betterlytics_inner_classes ); ?>">
		<div class="flex flex-col <?php echo esc_attr( $betterlytics_indent ? 'gap-0.5' : 'gap-1' ); ?> min-w-0 flex-1">
			<div class="flex items-center gap-2">
				<span class="<?php echo esc_attr( $betterlytics_label_classes ); ?> truncate">
					<?php echo esc_html( $betterlytics_label ); ?>
				</span>
				<?php
				if ( ! empty( $betterlytics_help_path ) ) {
					$betterlytics_help_classes = $betterlytics_indent ? 'is-blue scale-90' : 'is-blue';
					echo Betterlytics_Admin_Controller::get_help_link( $betterlytics_help_path, $betterlytics_help_classes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
			<?php if ( ! empty( $betterlytics_description ) ) : ?>
				<span class="<?php echo esc_attr( $betterlytics_desc_classes ); ?> truncate">
					<?php echo wp_kses_post( $betterlytics_description ); ?>
				</span>
			<?php endif; ?>
		</div>

		<div class="flex items-center <?php echo $betterlytics_indent ? 'gap-3 ml-4' : 'gap-6 ml-8'; ?> shrink-0">
			<?php if ( $betterlytics_is_select ) : ?>
				<select name="<?php echo esc_attr( $betterlytics_name ); ?>" class="bg-card border-border rounded-xl shadow-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all pr-12 !py-2.5 !text-sm font-bold">
					<?php foreach ( $args['options'] as $betterlytics_option_value => $betterlytics_option_label ) : ?>
						<option value="<?php echo esc_attr( $betterlytics_option_value ); ?>" <?php selected( $args['value'], $betterlytics_option_value ); ?>>
							<?php echo esc_html( $betterlytics_option_label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			<?php elseif ( in_array( $betterlytics_type, [ 'text', 'url' ], true ) ) : ?>
				<div class="w-64">
					<input type="<?php echo esc_attr( $betterlytics_type ); ?>"
						name="<?php echo esc_attr( $betterlytics_name ); ?>"
						value="<?php echo esc_attr( $args['value'] ?? '' ); ?>"
						class="!w-full !p-2.5 !text-sm font-bold bg-card border-border rounded-xl shadow-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
						placeholder="<?php echo esc_attr( $args['placeholder'] ?? '' ); ?>">
				</div>
			<?php else : ?>
				<label class="relative flex items-center cursor-pointer">
					<input type="checkbox" name="<?php echo esc_attr( $betterlytics_name ); ?>" value="1" <?php checked( ! empty( $args['checked'] ) ); ?> class="<?php echo esc_attr( $betterlytics_check_classes ); ?>">
				</label>
			<?php endif; ?>
		</div>
	</div>
</div>
