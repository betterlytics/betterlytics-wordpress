<?php
/**
 * Plugin Name: Betterlytics Demo
 * Description: Demo pages and event triggers for testing the Betterlytics plugin.
 * Version: 1.0.0
 * Author: Betterlytics
 *
 * This is a Must-Use plugin that provides test pages for verifying
 * Betterlytics event tracking. It should NOT be included in production.
 *
 * @package Betterlytics_Demo
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Render the Betterlytics test page content.
 *
 * @param string $content The post content.
 * @return string Modified content for the test page.
 */
function betterlytics_demo_render_test_page( $content ) {
	if ( ! is_page( 'betterlytics-test' ) ) {
		return $content;
	}

	ob_start();
	?>
	<style>
		.betterlytics-demo {
			max-width: 800px;
			margin: 0 auto;
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
		}
		.betterlytics-demo h2 {
			border-bottom: 2px solid #0073aa;
			padding-bottom: 10px;
			margin-top: 30px;
		}
		.betterlytics-demo .test-section {
			background: #f9f9f9;
			border: 1px solid #ddd;
			border-radius: 4px;
			padding: 20px;
			margin: 20px 0;
		}
		.betterlytics-demo button {
			background: #0073aa;
			color: white;
			border: none;
			padding: 10px 20px;
			border-radius: 4px;
			cursor: pointer;
			font-size: 14px;
			margin: 5px 5px 5px 0;
		}
		.betterlytics-demo button:hover {
			background: #005a87;
		}
		.betterlytics-demo button.secondary {
			background: #666;
		}
		.betterlytics-demo button.secondary:hover {
			background: #444;
		}
		.betterlytics-demo .event-log {
			background: #1e1e1e;
			color: #0f0;
			font-family: monospace;
			padding: 15px;
			border-radius: 4px;
			max-height: 300px;
			overflow-y: auto;
			font-size: 12px;
			margin-top: 10px;
		}
		.betterlytics-demo .event-log .log-entry {
			margin: 5px 0;
			padding: 5px;
			border-bottom: 1px solid #333;
		}
		.betterlytics-demo .event-log .log-entry:last-child {
			border-bottom: none;
		}
		.betterlytics-demo .status-indicator {
			display: inline-block;
			width: 12px;
			height: 12px;
			border-radius: 50%;
			margin-right: 8px;
		}
		.betterlytics-demo .status-indicator.connected {
			background: #0f0;
		}
		.betterlytics-demo .status-indicator.disconnected {
			background: #f00;
		}
		.betterlytics-demo .quick-links {
			display: flex;
			flex-wrap: wrap;
			gap: 10px;
			margin: 20px 0;
		}
		.betterlytics-demo .quick-links a {
			display: inline-block;
			padding: 8px 16px;
			background: #f0f0f0;
			border-radius: 4px;
			text-decoration: none;
			color: #333;
		}
		.betterlytics-demo .quick-links a:hover {
			background: #e0e0e0;
		}
	</style>

	<div class="betterlytics-demo">
		<h2>Connection Status</h2>
		<div class="test-section">
			<p>
				<span class="status-indicator" id="betterlytics-status"></span>
				<strong>Betterlytics:</strong> <span id="betterlytics-status-text">Checking...</span>
			</p>
			<p><strong>Site ID:</strong> <code id="betterlytics-site-id">-</code></p>
			<p><strong>Server URL:</strong> <code id="betterlytics-server-url">-</code></p>
		</div>

		<h2>Quick Links</h2>
		<div class="quick-links">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=betterlytics' ) ); ?>">Betterlytics Settings</a>

			<a href="<?php echo esc_url( wp_login_url() ); ?>">Login Page</a>
			<a href="<?php echo esc_url( wp_registration_url() ); ?>">Registration</a>
		</div>

		<h2>Manual Event Testing</h2>
		<div class="test-section">
			<p>Click these buttons to manually fire Betterlytics events:</p>
			<button onclick="betterlyticsDemoFireEvent('test-button-click', {button: 'primary'})">
				Fire: test-button-click
			</button>
			<button class="secondary" onclick="betterlyticsDemoFireEvent('test-page-action', {action: 'demo'})">
				Fire: test-page-action
			</button>
			<button onclick="betterlyticsDemoFireEvent('test-conversion', {value: 99.99, currency: 'USD'})">
				Fire: test-conversion
			</button>

			<h4>Custom Event</h4>
			<p>
				<input type="text" id="custom-event-name" placeholder="Event name" value="custom-event" style="padding: 8px; width: 200px;">
				<button onclick="betterlyticsDemoFireCustomEvent()">Fire Custom Event</button>
			</p>
		</div>

		<h2>HTML Attribute Testing</h2>
		<div class="test-section">
			<p>Click these buttons to fire custom HTML attributed Betterlytics events:</p>
			<button data-betterlytics-event="YourEvent">
				Fire: YourEvent
			</button>
			<button class="secondary" data-betterlytics-event="test-page-action">
				Fire: test-page-action
			</button>
		</div>

		<h2>WordPress Hook Testing</h2>
			<?php if ( comments_open() ) : ?>
				<p style="margin-top: 15px;"><strong>Post a comment</strong> to trigger the <code>comment_post</code> hook:</p>
				<?php comment_form(); ?>
			<?php endif; ?>
		</div>

		<h2>Feature Testing</h2>
		<div class="test-section">
			<h3>404 Error Tracking</h3>
			<p>Click the link below to visit a non-existent page and trigger a 404 event:</p>
			<a href="<?php echo esc_url( home_url( '/this-page-does-not-exist-' . wp_generate_password( 8, false ) ) ); ?>" target="_blank">
				<button type="button">Trigger 404 Error (opens in new tab)</button>
			</a>

			<h3 style="margin-top: 20px;">Site Search Tracking</h3>
			<p>Enter a query below to trigger a search event:</p>
			<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input type="search" name="s" placeholder="Search for..." value="betterlytics test" style="padding: 8px; width: 200px;">
				<button type="submit">Search</button>
			</form>

			<h3 style="margin-top: 20px;">File Download Tracking</h3>
			<p>Click these links to trigger file download events:</p>
			<div class="quick-links" style="margin-top: 10px;">
				<a href="<?php echo esc_url( BETTERLYTICS_PLUGIN_URL . 'README.md' ); ?>" download>sample.md (not tracked)</a>
				<a href="https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf" target="_blank">sample.pdf (tracked)</a>
				<a href="https://github.com/google/betterlytics-wordpress/archive/refs/heads/main.zip" target="_blank">sample.zip (tracked)</a>
			</div>
			<p><small>Note: File download tracking is client-side and filters by specific extensions.</small></p>
		</div>



		<h2>Event Log</h2>
		<div class="test-section">
			<p>Events fired from this page (client-side only):</p>
			<button onclick="betterlyticsDemoClearLog()">Clear Log</button>
			<div class="event-log" id="event-log">
				<div class="log-entry">Waiting for events...</div>
			</div>
		</div>
	</div>

	<script>
	(function() {
		// Check Betterlytics status
		function checkStatus() {
			var statusEl = document.getElementById('betterlytics-status');
			var statusTextEl = document.getElementById('betterlytics-status-text');
			var siteIdEl = document.getElementById('betterlytics-site-id');
			var serverUrlEl = document.getElementById('betterlytics-server-url');

			if (typeof window.betterlytics !== 'undefined' && window.betterlytics.event) {
				statusEl.className = 'status-indicator connected';
				statusTextEl.textContent = 'Connected';

				// Try to get config from script tag
				var script = document.querySelector('script[data-site-id]');
				if (script) {
					siteIdEl.textContent = script.getAttribute('data-site-id') || 'Not set';
					serverUrlEl.textContent = script.getAttribute('data-server-url') || 'Not set';
				}
			} else {
				statusEl.className = 'status-indicator disconnected';
				statusTextEl.textContent = 'Not loaded (check plugin settings)';
			}
		}

		// Initialize after a short delay to allow analytics.js to load
		setTimeout(checkStatus, 1000);

		// Event logging
		var logEl = document.getElementById('event-log');
		var logEntries = [];

		window.betterlyticsDemoLog = function(eventName, properties) {
			var entry = {
				time: new Date().toLocaleTimeString(),
				event: eventName,
				properties: properties
			};
			logEntries.unshift(entry);

			var html = logEntries.map(function(e) {
				return '<div class="log-entry">' +
					'<strong>[' + e.time + ']</strong> ' + e.event +
					'<br><small>' + JSON.stringify(e.properties) + '</small>' +
					'</div>';
			}).join('');

			logEl.innerHTML = html;
		};

		window.betterlyticsDemoClearLog = function() {
			logEntries = [];
			logEl.innerHTML = '<div class="log-entry">Log cleared. Waiting for events...</div>';
		};

		// Fire event helper
		window.betterlyticsDemoFireEvent = function(eventName, properties) {
			if (typeof window.betterlytics !== 'undefined' && window.betterlytics.event) {
				window.betterlytics.event(eventName, properties);
				window.betterlyticsDemoLog(eventName, properties);
				console.log('Betterlytics event fired:', eventName, properties);
			} else {
				alert('Betterlytics is not loaded. Please check plugin settings.');
			}
		};

		window.betterlyticsDemoFireCustomEvent = function() {
			var eventName = document.getElementById('custom-event-name').value || 'custom-event';
			window.betterlyticsDemoFireEvent(eventName, { custom: true, timestamp: Date.now() });
		};
	})();
	</script>
	<?php

	return ob_get_clean();
}
add_filter( 'the_content', 'betterlytics_demo_render_test_page', 20 );

/**
 * Enable comments on the test page for testing comment_post hook.
 *
 * @param bool $open    Whether comments are open.
 * @param int  $post_id The post ID (unused, required by filter signature).
 * @return bool
 */
function betterlytics_demo_enable_comments( $open, $post_id ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	if ( is_page( 'betterlytics-test' ) ) {
		return true;
	}
	return $open;
}
add_filter( 'comments_open', 'betterlytics_demo_enable_comments', 10, 2 );

/**
 * Add a notice in the admin when the demo plugin is active.
 */
function betterlytics_demo_admin_notice() {
	$screen = get_current_screen();
	if ( $screen && 'toplevel_page_betterlytics' === $screen->id ) {
		?>
		<div class="notice notice-info">
			<p>
				<strong>Demo Mode Active:</strong>
				The Betterlytics demo plugin is loaded.
				<a href="<?php echo esc_url( home_url( '/betterlytics-test/' ) ); ?>">Visit the test page</a>
				to test event tracking.
			</p>
		</div>
		<?php
	}
}
add_action( 'admin_notices', 'betterlytics_demo_admin_notice' );

/**
 * Add test page link to admin bar for easy access.
 *
 * @param WP_Admin_Bar $admin_bar The admin bar instance.
 */
function betterlytics_demo_admin_bar_link( $admin_bar ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$admin_bar->add_node(
		[
			'id'    => 'betterlytics-demo',
			'title' => 'Betterlytics Test',
			'href'  => home_url( '/betterlytics-test/' ),
			'meta'  => [
				'title' => 'Go to Betterlytics test page',
			],
		]
	);
}
add_action( 'admin_bar_menu', 'betterlytics_demo_admin_bar_link', 100 );
