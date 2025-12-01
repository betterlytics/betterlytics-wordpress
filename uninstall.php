<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package Betterlytics
 * @since   1.0.0
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete plugin options.
delete_option( 'betterlytics_options' );

// For multisite, delete options from all sites.
if ( is_multisite() ) {
	$sites = get_sites();
	foreach ( $sites as $site ) {
		switch_to_blog( $site->blog_id );
		delete_option( 'betterlytics_options' );
		restore_current_blog();
	}
}
