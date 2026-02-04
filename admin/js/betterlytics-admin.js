/**
 * Betterlytics Admin Scripts
 *
 * @package Betterlytics
 */

( function() {
	'use strict';

	/**
	 * Set target="_blank" on external dashboard link in admin menu.
	 * Runs on all admin pages.
	 */
	function initDashboardLink() {
		function setTarget() {
			var link = document.querySelector( '#adminmenu a[href*="betterlytics.io"]' );
			if ( link ) {
				link.target = '_blank';
				link.rel = 'noopener';
			}
		}

		if ( document.readyState === 'loading' ) {
			document.addEventListener( 'DOMContentLoaded', setTarget );
		} else {
			setTarget();
		}
	}

	/**
	 * Setup banner dismiss functionality.
	 * Only runs when banner exists and config is provided.
	 */
	function initBannerDismiss() {
		var dismissBtn = document.getElementById( 'betterlytics-dismiss-banner' );
		if ( ! dismissBtn || typeof betterlyticsAdmin === 'undefined' ) {
			return;
		}

		dismissBtn.addEventListener( 'click', function() {
			var banner = document.getElementById( 'betterlytics-setup-banner' );
			if ( banner ) {
				banner.style.display = 'none';
			}

			var xhr = new XMLHttpRequest();
			xhr.open( 'POST', betterlyticsAdmin.ajaxUrl );
			xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
			xhr.send( 'action=betterlytics_dismiss_setup_banner&nonce=' + betterlyticsAdmin.nonce );
		} );
	}

	// Initialize.
	initDashboardLink();
	initBannerDismiss();

} )();
