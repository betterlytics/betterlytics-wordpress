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

		$tab                            = $this->get_current_tab();
		$betterlytics_options           = Betterlytics_Options::get_options();
		$betterlytics_setup_incomplete  = empty( $betterlytics_options['site_id'] ) || empty( $betterlytics_options['enabled'] );
		$betterlytics_banner_dismissed  = Betterlytics_Admin::is_setup_banner_dismissed();
		$betterlytics_show_setup_banner = $betterlytics_setup_incomplete && ! $betterlytics_banner_dismissed;

		if ( $betterlytics_show_setup_banner ) : ?>
			<div class="betterlytics-setup-banner" id="betterlytics-setup-banner">
				<span class="dashicons dashicons-info"></span>
				<p>
					<?php
					printf(
						/* translators: %s: link to setup guide */
						esc_html__( 'Setup is not complete. Follow the %s to start tracking.', 'betterlytics' ),
						'<a href="' . esc_url( admin_url( 'options-general.php?page=betterlytics&tab=home' ) ) . '">' . esc_html__( 'setup guide', 'betterlytics' ) . '</a>'
					);
					?>
				</p>
				<a href="<?php echo esc_url( admin_url( 'options-general.php?page=betterlytics&tab=home' ) ); ?>" class="button">
					<?php esc_html_e( 'View Setup', 'betterlytics' ); ?>
				</a>
				<button type="button" class="dismiss" id="betterlytics-dismiss-banner" title="<?php esc_attr_e( 'Dismiss', 'betterlytics' ); ?>">
					<span class="dashicons dashicons-no-alt"></span>
				</button>
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

		<div class="wrap betterlytics-admin-wrap">
			<div class="betterlytics-header">
				<div class="betterlytics-header-title-container">
					<img src="<?php echo esc_url( BETTERLYTICS_PLUGIN_URL . 'public/logo/betterlytics-logo-dark-simple.svg' ); ?>" class="betterlytics-header-logo" alt="Betterlytics">
					<h1 class="betterlytics-header-title"><?php esc_html_e( 'Betterlytics', 'betterlytics' ); ?></h1>
				</div>
				<p class="betterlytics-header-description">
					<?php esc_html_e( 'Privacy-first analytics for WordPress. Track your visitors without compromising their privacy.', 'betterlytics' ); ?>
				</p>
			</div>

			<hr class="wp-header-end">

			<?php $this->render_tabs( $tab ); ?>

			<div class="betterlytics-admin-content">
				<?php
				$file = BETTERLYTICS_PLUGIN_DIR . "admin/partials/betterlytics-{$tab}-display.php";
				if ( file_exists( $file ) ) {
					include $file;
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the tabs navigation.
	 *
	 * @since 1.0.0
	 * @param string $current_tab The key of the current tab.
	 */
	private function render_tabs( $current_tab ) {
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( self::TABS as $key => $tab ) {
			$active_class = ( $current_tab === $key ) ? 'nav-tab-active' : '';
			$url          = add_query_arg(
				[
					'page' => self::MENU_SLUG,
					'tab'  => $key,
				],
				admin_url( 'options-general.php' )
			);

			printf(
				'<a href="%s" class="nav-tab %s">%s</a>',
				esc_url( $url ),
				esc_attr( $active_class ),
				esc_html( $tab['title'] )
			);
		}
		echo '</h2>';
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
}
