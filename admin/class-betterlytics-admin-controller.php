<?php
/**
 * Admin page controller for Betterlytics.
 *
 * Handles menu registration and page rendering.
 *
 * @package    Betterlytics
 * @subpackage Betterlytics/admin
 * @since      1.0.0
 */

/**
 * Admin page controller.
 *
 * @since 1.0.0
 */
class Betterlytics_Admin_Controller {

	/**
	 * Menu slug.
	 *
	 * @var string
	 */
	const MENU_SLUG = 'betterlytics';

	/**
	 * Admin pages configuration.
	 *
	 * @var array
	 */
	const TABS = [
		'home'     => [
			'title' => 'Home',
			'slug'  => 'home',
		],
		'events'   => [
			'title' => 'Events',
			'slug'  => 'events',
		],
		'settings' => [
			'title' => 'Settings',
			'slug'  => 'settings',
		],
	];

	/**
	 * Register admin menu.
	 *
	 * @since 1.0.0
	 */
	public function register_menu() {
		add_options_page(
			__( 'Betterlytics', 'betterlytics' ),
			__( 'Betterlytics', 'betterlytics' ),
			'manage_options',
			self::MENU_SLUG,
			[ $this, 'render' ]
		);
	}

	/**
	 * Render the current admin page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$tab                           = $this->get_current_tab();
		$betterlytics_options          = Betterlytics_Options::get_options();
		$betterlytics_setup_incomplete = empty( $betterlytics_options['site_id'] ) || empty( $betterlytics_options['enabled'] );

		$betterlytics_show_setup_banner = $betterlytics_setup_incomplete;
		?>
		<div id="betterlytics-admin-root" class="betterlytics-admin-root">
			<?php if ( $betterlytics_show_setup_banner ) : ?>
				<div class="betterlytics-setup-banner sticky top-0 z-[1000] bg-primary text-white p-3.5 px-3 flex items-center justify-center box-border shadow-md relative" id="betterlytics-setup-banner">
					<!-- Horizontally Centered Message -->
					<div class="flex items-center gap-3">
						<span class="dashicons dashicons-info !text-xl !w-5 !h-5 !flex !items-center !justify-center"></span>
						<p class="m-0 text-sm font-medium">
							<?php
							printf(
								/* translators: %s: link to setup guide */
								esc_html__( 'Setup is not complete. Follow the %s to start tracking.', 'betterlytics' ),
								'<a href="' . esc_url( admin_url( 'options-general.php?page=betterlytics&tab=home' ) ) . '" class="!text-white underline font-bold hover:no-underline">' . esc_html__( 'setup guide', 'betterlytics' ) . '</a>'
							);
							?>
						</p>
					</div>

					<!-- Edge-Aligned Buttons -->
					<div class="absolute right-3 flex items-center gap-3">
						<a href="<?php echo esc_url( admin_url( 'options-general.php?page=betterlytics&tab=home' ) ); ?>" class="button bg-white text-primary border-none font-bold hover:bg-white/90 !px-4 !h-8 !leading-8">
							<?php esc_html_e( 'View Setup', 'betterlytics' ); ?>
						</a>
						<button type="button" class="dismiss bg-transparent border-none text-white cursor-pointer p-1 opacity-80 leading-none hover:opacity-100" id="betterlytics-dismiss-banner" title="<?php esc_attr_e( 'Dismiss', 'betterlytics' ); ?>">
							<span class="dashicons dashicons-no-alt !text-xl !w-5 !h-5"></span>
						</button>
					</div>
				</div>
				<script>
				document.getElementById('betterlytics-dismiss-banner').addEventListener('click', function() {
					var banner = document.getElementById('betterlytics-setup-banner');
					banner.style.display = 'none';
					var xhr = new XMLHttpRequest();
					xhr.open('POST', '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>');
					xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
					xhr.send('action=betterlytics_dismiss_setup_banner&nonce=<?php echo esc_js( wp_create_nonce( 'betterlytics_admin' ) ); ?>');
				});
				</script>
			<?php endif; ?>

			<div class="wrap betterlytics-admin-wrap max-w-[1200px] mx-auto px-10 relative">
				<div class="betterlytics-immersive-container bg-card rounded-lg shadow-sm border border-border overflow-hidden">
					<?php $this->render_immersive_header( $tab ); ?>

					<div class="betterlytics-admin-content p-12">
					<?php
					// Try to load from new views structure first.
					$betterlytics_view_file = BETTERLYTICS_PLUGIN_DIR . "admin/components/views/{$tab}/betterlytics-page.php";

					if ( file_exists( $betterlytics_view_file ) ) {
						// Pass common data to the view.
						$betterlytics_args = [
							'options' => $betterlytics_options,
							'tab'     => $tab,
						];
						self::render_component_file( $betterlytics_view_file, $betterlytics_args );
					} else {
						// Fallback to old partials (legacy support during migration).
						$betterlytics_file = BETTERLYTICS_PLUGIN_DIR . "admin/partials/betterlytics-{$tab}-display.php";
						if ( file_exists( $betterlytics_file ) ) {
							include $betterlytics_file;
						}
					}
					?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the immersive header with logo, tabs, and external links.
	 *
	 * @since 1.0.0
	 * @param string $current_tab The key of the current tab.
	 */
	private function render_immersive_header( $current_tab ) {
		$home_url = add_query_arg(
			[
				'page' => self::MENU_SLUG,
				'tab'  => 'home',
			],
			admin_url( 'options-general.php' )
		);
		?>
		<div class="betterlytics-immersive-header flex justify-between items-center px-10 py-5 border-b border-border bg-card">
			<a href="<?php echo esc_url( $home_url ); ?>" class="betterlytics-brand flex items-center gap-2.5 no-underline text-foreground hover:text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded-md">
				<img src="<?php echo esc_url( BETTERLYTICS_PLUGIN_URL . 'public/logo/betterlytics-logo-dark-simple.svg' ); ?>" class="betterlytics-brand-logo w-7 h-7 block" alt="">
				<span class="betterlytics-brand-name text-lg font-semibold leading-none"><?php esc_html_e( 'Betterlytics', 'betterlytics' ); ?></span>
			</a>

			<nav class="betterlytics-header-nav flex items-center gap-2">
				<div class="betterlytics-nav-tabs flex gap-1 mr-4 pr-4 border-r border-border">
					<?php
					// Only show Settings and Events tabs (Home is accessible via logo).
					$tabs = [
						'settings' => __( 'Settings', 'betterlytics' ),
						'events'   => __( 'Events', 'betterlytics' ),
					];

					foreach ( $tabs as $key => $label ) {
						$active_classes = ( $current_tab === $key ) ? 'text-foreground bg-muted' : 'text-muted-foreground hover:text-foreground hover:bg-muted';
						$url            = add_query_arg(
							[
								'page' => self::MENU_SLUG,
								'tab'  => $key,
							],
							admin_url( 'options-general.php' )
						);

						printf(
							'<a href="%s" class="betterlytics-nav-tab inline-block px-3 py-2 text-sm font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 %s">%s</a>',
							esc_url( $url ),
							esc_attr( $active_classes ),
							esc_html( $label )
						);
					}
					?>
				</div>

				<div class="betterlytics-nav-links flex gap-4">
					<a href="https://www.betterlytics.io/docs/installation/wordpress" target="_blank" rel="noopener" class="betterlytics-nav-link betterlytics-link-external text-sm text-muted-foreground hover:text-primary hover:underline transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded-sm">
						<?php esc_html_e( 'Documentation', 'betterlytics' ); ?>
					</a>
					<a href="https://www.betterlytics.io/contact" target="_blank" rel="noopener" class="betterlytics-nav-link betterlytics-link-external text-sm text-muted-foreground hover:text-primary hover:underline transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded-sm">
						<?php esc_html_e( 'Contact Support', 'betterlytics' ); ?>
					</a>
				</div>
			</nav>
		</div>
		<?php
	}

	/**
	 * Get the current tab from the request.
	 *
	 * @since  1.0.0
	 * @return string Tab key (home, events, settings).
	 */
	private function get_current_tab() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Just reading page parameter.
		$tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'home';

		if ( array_key_exists( $tab, self::TABS ) ) {
			return $tab;
		}

		return 'home';
	}

	/**
	 * Get a page slug by name.
	 *
	 * @since  1.0.0
	 * @param  string $page Page name.
	 * @return string Page slug.
	 */
	public static function get_slug( $page ) {
		return self::TABS[ $page ]['slug'] ?? 'home';
	}

	/**
	 * Render a help link icon.
	 *
	 * @since 1.0.0
	 * @param string $path    Optional path or URL.
	 * @param string $variant Optional style variant (e.g. 'is-blue').
	 * @return string Help link HTML.
	 */
	public static function get_help_link( $path = '', $variant = '' ) {
		$url   = 'https://betterlytics.io/docs/' . ltrim( $path, '/' );
		$class = 'betterlytics-help-icon text-primary' . ( $variant ? ' ' . $variant : '' );

		return sprintf(
			'<a href="%s" target="_blank" rel="noopener" class="%s" title="%s"><span class="dashicons dashicons-editor-help"></span></a>',
			esc_url( $url ),
			esc_attr( $class ),
			esc_attr__( 'View Documentation', 'betterlytics' )
		);
	}

	/**
	 * Render a component with arguments.
	 *
	 * @since 1.0.0
	 * @param string $name Component name (e.g. 'ui/card' or 'views/events/list').
	 * @param array  $args Arguments to pass to the component.
	 */
	public static function render_component( $name, $args = [] ) {
		$betterlytics_dir  = dirname( $name );
		$betterlytics_file = basename( $name );
		// Enforce prefix: ui/card -> ui/betterlytics-card.php.
		$betterlytics_path = BETTERLYTICS_PLUGIN_DIR . "admin/components/{$betterlytics_dir}/betterlytics-{$betterlytics_file}.php";

		if ( file_exists( $betterlytics_path ) ) {
			self::render_component_file( $betterlytics_path, $args );
		}
	}

	/**
	 * Internal helper to require the file with args exposed.
	 *
	 * @since 1.0.0
	 * @param string $file Absolute path to file.
	 * @param array  $args Arguments to extract.
	 */
	private static function render_component_file( $file, $args ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- Args are used in the included file.
		// Include file, logic within file can access $args directly.
		include $file;
	}
}
