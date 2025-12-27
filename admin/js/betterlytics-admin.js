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
			this.initTabs();
			this.bindEvents();

			// Load hooks from localized data.
			if ( typeof betterlyticsAdmin !== 'undefined' && betterlyticsAdmin.hooks ) {
				this.hooks = betterlyticsAdmin.hooks;
			}

			this.renderHooks();
		},

		initTabs: function() {
			var hash = window.location.hash.replace( '#', '' );
			if ( hash && $( '#tab-' + hash ).length ) {
				this.activateTab( hash );
			}
		},

		bindEvents: function() {
			$( '.betterlytics-tabs .nav-tab' ).on( 'click', this.switchTab.bind( this ) );
			$( '#betterlytics-add-hook' ).on( 'click', this.addHook.bind( this ) );
			$( '#betterlytics-save-hooks' ).on( 'click', this.saveHooks.bind( this ) );
			
			var $list = $( '#betterlytics-hooks-list' );
			$list.on( 'click', '.betterlytics-delete-hook', this.deleteHook.bind( this ) );
			$list.on( 'click', '.betterlytics-toggle-metadata', this.toggleMetadata.bind( this ) );
			$list.on( 'click', '.betterlytics-add-metadata', this.addMetadata.bind( this ) );
			$list.on( 'click', '.betterlytics-delete-metadata', this.deleteMetadata.bind( this ) );
		},

		switchTab: function( e ) {
			e.preventDefault();
			var tab = $( e.currentTarget ).data( 'tab' );
			this.activateTab( tab );
			window.history.replaceState( null, null, '#' + tab );
		},

		activateTab: function( tab ) {
			$( '.betterlytics-tabs .nav-tab' ).removeClass( 'nav-tab-active' );
			$( '.betterlytics-tabs .nav-tab[data-tab="' + tab + '"]' ).addClass( 'nav-tab-active' );
			$( '.betterlytics-tab-content' ).removeClass( 'active' );
			$( '#tab-' + tab ).addClass( 'active' );
		},

		renderHooks: function() {
			var $list = $( '#betterlytics-hooks-list' );
			$list.empty();

			if ( this.hooks.length === 0 ) {
				$list.append(
					'<tr class="empty-row">' +
					'<td colspan="4">' + this.escapeHtml( 'No custom hooks configured. Click "Add Hook" to create one.' ) + '</td>' +
					'</tr>'
				);
				return;
			}

			this.hooks.forEach( function( hook, index ) {
				$list.append( this.renderHookRow( hook, index ) );
			}.bind( this ) );
		},

		renderHookRow: function( hook, index ) {
			var checked = hook.enabled !== false ? 'checked' : '';
			
			var metadataHtml = '';
			if ( hook.metadata && hook.metadata.length ) {
				hook.metadata.forEach( function( meta ) {
					metadataHtml += this.renderMetadataItem( meta );
				}.bind( this ) );
			}

			return '<tr data-index="' + index + '">' +
				'<td class="column-enabled">' +
				'<input type="checkbox" class="hook-enabled" ' + checked + '>' +
				'</td>' +
				'<td class="column-wp-hook">' +
				'<input type="text" class="regular-text wp-hook-input" value="' + this.escapeHtml( hook.wp_hook || '' ) + '" placeholder="wp_hook_name">' +
				'</td>' +
				'<td class="column-event-name">' +
				'<input type="text" class="regular-text event-name-input" value="' + this.escapeHtml( hook.event_name || '' ) + '" placeholder="Event Name">' +
				'</td>' +
				'<td class="column-actions">' +
				'<button type="button" class="button button-small betterlytics-toggle-metadata" title="Configure Metadata"><span class="dashicons dashicons-admin-generic"></span></button> ' +
				'<button type="button" class="button button-small betterlytics-delete-hook" title="Delete"><span class="dashicons dashicons-trash"></span></button>' +
				'</td>' +
				'</tr>' +
				'<tr class="betterlytics-metadata-row" data-index="' + index + '">' +
				'<td colspan="4">' +
				'<div class="betterlytics-metadata-wrapper">' +
				'<h4>Metadata</h4>' +
				'<p class="description">Map event properties to hook arguments. Use <code>{0}</code> for the first argument, <code>{0->ID}</code> for a property, or <code>{0[key]}</code> for an array key.</p>' +
				'<table class="betterlytics-metadata-table">' +
				'<thead><tr><th style="width: 45%;">Property Key</th><th style="width: 45%;">Value Pattern</th><th style="width: 10%;"></th></tr></thead>' +
				'<tbody>' + metadataHtml + '</tbody>' +
				'</table>' +
				'<button type="button" class="button betterlytics-add-metadata">Add Metadata</button>' +
				'</div>' +
				'</td>' +
				'</tr>';
		},
		
		renderMetadataItem: function( meta ) {
			return '<tr>' +
				'<td><input type="text" class="betterlytics-metadata-key" value="' + this.escapeHtml( meta.key || '' ) + '" placeholder="e.g. order_id"></td>' +
				'<td><input type="text" class="betterlytics-metadata-value" value="' + this.escapeHtml( meta.value || '' ) + '" placeholder="e.g. {0}"></td>' +
				'<td><button type="button" class="betterlytics-delete-metadata"><span class="dashicons dashicons-no"></span></button></td>' +
				'</tr>';
		},
		
		toggleMetadata: function( e ) {
			e.preventDefault();
			var $btn = $( e.currentTarget );
			var $row = $btn.closest( 'tr' );
			var $metaRow = $row.next( '.betterlytics-metadata-row' );
			
			$metaRow.toggleClass( 'active' );
		},
		
		addMetadata: function( e ) {
			e.preventDefault();
			var $tbody = $( e.currentTarget ).prev( 'table' ).find( 'tbody' );
			$tbody.append( this.renderMetadataItem( {} ) );
		},
		
		deleteMetadata: function( e ) {
			e.preventDefault();
			if ( confirm( 'Remove this metadata field?' ) ) {
				$( e.currentTarget ).closest( 'tr' ).remove();
			}
		},

		addHook: function() {
			this.hooks.push( {
				wp_hook: '',
				event_name: '',
				enabled: true,
				metadata: []
			} );

			this.renderHooks();

			// Focus the new row.
			$( '#betterlytics-hooks-list tr[data-index="' + (this.hooks.length - 1) + '"] .wp-hook-input' ).focus();
		},

		deleteHook: function( e ) {
			e.preventDefault();

			if ( typeof betterlyticsAdmin !== 'undefined' && ! window.confirm( betterlyticsAdmin.strings.confirmDelete ) ) {
				return;
			}

			var $row = $( e.currentTarget ).closest( 'tr' );
			var index = $row.data( 'index' );

			this.hooks.splice( index, 1 );
			this.renderHooks();
		},

		collectHooksFromUI: function() {
			var hooks = [];

			$( '#betterlytics-hooks-list tr[data-index]' ).each( function() {
				var $row = $( this );
				var index = $row.data( 'index' );
				var $metaRow = $row.next( '.betterlytics-metadata-row' );
				
				var wpHook = $row.find( '.wp-hook-input' ).val().trim();
				var eventName = $row.find( '.event-name-input' ).val().trim();

				// Only include hooks with both fields filled.
				if ( wpHook && eventName ) {
					var metadata = [];
					$metaRow.find( 'tbody tr' ).each( function() {
						var key = $( this ).find( '.betterlytics-metadata-key' ).val().trim();
						var value = $( this ).find( '.betterlytics-metadata-value' ).val().trim();
						if ( key && value ) {
							metadata.push( { key: key, value: value } );
						}
					} );
				
					hooks.push( {
						wp_hook: wpHook,
						event_name: eventName,
						enabled: $row.find( '.hook-enabled' ).is( ':checked' ),
						metadata: metadata
					} );
				}
			} );

			return hooks;
		},

		saveHooks: function() {
			var $status = $( '#betterlytics-hooks-status' );
			var $button = $( '#betterlytics-save-hooks' );
			var hooks = this.collectHooksFromUI();

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
						$status.text( '' ).removeClass( 'success error' );
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
