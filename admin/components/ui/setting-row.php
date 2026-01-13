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
 * }
 */

$label       = $args['label'] ?? '';
$name        = $args['name'] ?? '';
$description = $args['description'] ?? '';
$help_path   = $args['help_path'] ?? '';
$type        = $args['type'] ?? 'checkbox';
$is_select   = 'select' === $type;
?>
<div class="betterlytics-checkmark-row group hover:bg-muted/5 transition-all duration-200">
	<div class="flex items-center justify-between py-6 !pl-10 !pr-10">
		<div class="flex flex-col gap-1 min-w-0 flex-1">
			<div class="flex items-center gap-2">
				<span class="text-base font-bold text-foreground truncate">
					<?php echo esc_html( $label ); ?>
				</span>
				<?php
				if ( ! empty( $help_path ) ) {
					echo Betterlytics_Admin_Controller::get_help_link( $help_path, 'is-blue' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
			<?php if ( ! empty( $description ) ) : ?>
				<span class="text-sm text-muted-foreground/80 font-medium truncate">
					<?php echo wp_kses_post( $description ); ?>
				</span>
			<?php endif; ?>
		</div>

		<div class="flex items-center gap-6 shrink-0 ml-8">
			<?php if ( $is_select ) : ?>
				<select name="<?php echo esc_attr( $name ); ?>" class="bg-card border-border rounded-xl shadow-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all pr-12 !py-2.5 !text-sm font-bold">
					<?php foreach ( $args['options'] as $option_value => $option_label ) : ?>
						<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $args['value'], $option_value ); ?>>
							<?php echo esc_html( $option_label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			<?php elseif ( in_array( $type, [ 'text', 'url' ], true ) ) : ?>
				<div class="w-64">
					<input type="<?php echo esc_attr( $type ); ?>" 
						name="<?php echo esc_attr( $name ); ?>" 
						value="<?php echo esc_attr( $args['value'] ?? '' ); ?>" 
						class="!w-full !p-2.5 !text-sm font-bold bg-card border-border rounded-xl shadow-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" 
						placeholder="<?php echo esc_attr( $args['placeholder'] ?? '' ); ?>">
				</div>
			<?php else : ?>
				<label class="relative flex items-center cursor-pointer">
					<input type="checkbox" name="<?php echo esc_attr( $name ); ?>" value="1" <?php checked( ! empty( $args['checked'] ) ); ?> class="w-6 h-6 rounded-lg border-2 border-border text-primary focus:ring-primary/30 transition-all cursor-pointer">
				</label>
			<?php endif; ?>
		</div>
	</div>
</div>
