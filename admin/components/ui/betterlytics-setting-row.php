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

$betterlytics_label       = $args['label'] ?? '';
$betterlytics_name        = $args['name'] ?? '';
$betterlytics_description = $args['description'] ?? '';
$betterlytics_help_path   = $args['help_path'] ?? '';
$betterlytics_type        = $args['type'] ?? 'checkbox';
$betterlytics_is_select   = 'select' === $betterlytics_type;
?>
<div class="betterlytics-checkmark-row group hover:bg-muted/5 transition-all duration-200">
	<div class="flex items-center justify-between py-6 !pl-10 !pr-10">
		<div class="flex flex-col gap-1 min-w-0 flex-1">
			<div class="flex items-center gap-2">
				<span class="text-base font-bold text-foreground truncate">
					<?php echo esc_html( $betterlytics_label ); ?>
				</span>
				<?php
				if ( ! empty( $betterlytics_help_path ) ) {
					echo Betterlytics_Admin_Controller::get_help_link( $betterlytics_help_path, 'is-blue' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
			<?php if ( ! empty( $betterlytics_description ) ) : ?>
				<span class="text-sm text-muted-foreground/80 font-medium truncate">
					<?php echo wp_kses_post( $betterlytics_description ); ?>
				</span>
			<?php endif; ?>
		</div>

		<div class="flex items-center gap-6 shrink-0 ml-8">
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
					<input type="checkbox" name="<?php echo esc_attr( $betterlytics_name ); ?>" value="1" <?php checked( ! empty( $args['checked'] ) ); ?> class="w-6 h-6 rounded-lg border-2 border-border text-primary focus:ring-primary/30 transition-all cursor-pointer">
				</label>
			<?php endif; ?>
		</div>
	</div>
</div>
