/**
 * Betterlytics Admin JavaScript
 *
 * @package Betterlytics
 * @since   1.0.0
 */

( function( $ ) {
	'use strict';

	var BetterlyticsAdmin = {
		hooks: [],

		init: function() {
			this.hooks = betterlyticsAdmin.hooks || [];
			this.renderHooks();
			this.bindEvents();
		},

		bindEvents: function() {
			$( '#betterlytics-add-hook' ).on( 'click', this.addHook.bind( this ) );
			$( '#betterlytics-save-hooks' ).on( 'click', this.saveHooks.bind( this ) );
			$( '#betterlytics-hooks-list' ).on( 'click', '.betterlytics-delete-hook', this.deleteHook.bind( this ) );
		},

		renderHooks: function() {
			var $list = $( '#betterlytics-hooks-list' );
			$list.empty();

			if ( this.hooks.length === 0 ) {
				$list.append(
					'<tr class="empty-row">' +
					'<td colspan="4">No hooks configured. Click "Add New Hook" to get started.</td>' +
					'</tr>'
				);
				return;
			}

			this.hooks.forEach( function( hook, index ) {
				$list.append( this.renderHookRow( hook, index ) );
			}.bind( this ) );
		},

		renderHookRow: function( hook, index ) {
			var checked = hook.enabled ? 'checked' : '';
			return '<tr data-index="' + index + '">' +
				'<td class="column-enabled">' +
				'<input type="checkbox" class="hook-enabled" ' + checked + '>' +
				'</td>' +
				'<td class="column-wp-hook">' +
				'<input type="text" class="hook-wp-hook" value="' + this.escapeHtml( hook.wp_hook ) + '" placeholder="e.g., wp_login">' +
				'</td>' +
				'<td class="column-event-name">' +
				'<input type="text" class="hook-event-name" value="' + this.escapeHtml( hook.event_name ) + '" placeholder="e.g., user-login">' +
				'</td>' +
				'<td class="column-actions">' +
				'<button type="button" class="button-link betterlytics-delete-hook" title="Delete">' +
				'<span class="dashicons dashicons-trash"></span>' +
				'</button>' +
				'</td>' +
				'</tr>';
		},

		addHook: function() {
			this.hooks.push( {
				wp_hook: '',
				event_name: '',
				enabled: true
			} );
			this.renderHooks();

			// Focus the new row's first input.
			$( '#betterlytics-hooks-list tr:last-child .hook-wp-hook' ).focus();
		},

		deleteHook: function( e ) {
			e.preventDefault();

			if ( ! window.confirm( betterlyticsAdmin.strings.confirmDelete ) ) {
				return;
			}

			var index = $( e.currentTarget ).closest( 'tr' ).data( 'index' );
			this.hooks.splice( index, 1 );
			this.renderHooks();
		},

		collectHooksFromUI: function() {
			var hooks = [];
			$( '#betterlytics-hooks-list tr:not(.empty-row)' ).each( function() {
				var $row = $( this );
				hooks.push( {
					enabled: $row.find( '.hook-enabled' ).is( ':checked' ),
					wp_hook: $row.find( '.hook-wp-hook' ).val().trim(),
					event_name: $row.find( '.hook-event-name' ).val().trim()
				} );
			} );
			return hooks;
		},

		saveHooks: function() {
			var $status = $( '#betterlytics-hooks-status' );
			var $button = $( '#betterlytics-save-hooks' );
			var hooks = this.collectHooksFromUI();

			// Filter out empty hooks.
			hooks = hooks.filter( function( hook ) {
				return hook.wp_hook && hook.event_name;
			} );

			$button.prop( 'disabled', true );
			$status.text( 'Saving...' ).removeClass( 'success error' );

			$.ajax( {
				url: betterlyticsAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'betterlytics_save_hooks',
					nonce: betterlyticsAdmin.nonce,
					hooks: JSON.stringify( hooks )
				},
				success: function( response ) {
					if ( response.success ) {
						this.hooks = hooks;
						this.renderHooks();
						$status.text( betterlyticsAdmin.strings.saved ).addClass( 'success' );
					} else {
						$status.text( response.data.message || betterlyticsAdmin.strings.error ).addClass( 'error' );
					}
				}.bind( this ),
				error: function() {
					$status.text( betterlyticsAdmin.strings.error ).addClass( 'error' );
				},
				complete: function() {
					$button.prop( 'disabled', false );
					setTimeout( function() {
						$status.text( '' );
					}, 3000 );
				}
			} );
		},

		escapeHtml: function( str ) {
			if ( ! str ) {
				return '';
			}
			var div = document.createElement( 'div' );
			div.textContent = str;
			return div.innerHTML;
		}
	};

	$( document ).ready( function() {
		BetterlyticsAdmin.init();
	} );
}( jQuery ) );
