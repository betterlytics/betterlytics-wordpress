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
		builtinHooks: {},

		init: function() {
			this.initTabs();
			// Load data from localized script
			if ( typeof betterlyticsAdmin !== 'undefined' ) {
				if ( betterlyticsAdmin.hooks ) {
					this.hooks = betterlyticsAdmin.hooks;
				}
				if ( betterlyticsAdmin.builtinHooks ) {
					this.builtinHooks = betterlyticsAdmin.builtinHooks;
				}
			}

			this.syncBuiltinsToList();
			this.bindEvents();
			this.renderHooks();
		},

		initTabs: function() {
			var urlParams = new URLSearchParams( window.location.search );
			var subtab = urlParams.get( 'subtab' );
			var validTabs = ['browser', 'server'];

			if ( subtab && validTabs.indexOf( subtab ) !== -1 ) {
				this.activateTab( subtab );
			} else {
				this.activateTab( 'browser' );
			}
		},

		bindEvents: function() {
			$( '.betterlytics-subtabs .betterlytics-subtab' ).on( 'click', this.switchTab.bind( this ) );
			$( '#betterlytics-add-hook' ).on( 'click', this.addHook.bind( this ) );
			
			var $list = $( '#betterlytics-hooks-list' );
			$list.on( 'click', '.betterlytics-delete-hook', this.deleteHook.bind( this ) );
			$list.on( 'click', '.betterlytics-toggle-metadata', this.toggleMetadata.bind( this ) );
			$list.on( 'click', '.betterlytics-add-metadata', this.addMetadata.bind( this ) );
			$list.on( 'click', '.betterlytics-delete-metadata', this.deleteMetadata.bind( this ) );

			// Sync inputs to model
			$list.on( 'change input', 'input', this.updateModel.bind( this ) );

			// Listen for changes on built-in setting toggles
			$( 'input[name^="betterlytics_options[woo_"]' ).on( 'change', this.handleBuiltinToggle.bind( this ) );
		},

		syncBuiltinsToList: function() {
			// Iterate over all built-in hooks/options.
			// If the option checkbox is checked, ensure it exists in this.hooks.
			var self = this;
			$.each( this.builtinHooks, function( optionKey, config ) {
				var wpHook = config[0];
				var eventName = config[1];
				// Selector updated for new array format: betterlytics_options[key][enabled]
				var $checkbox = $( 'input[name="betterlytics_options[' + optionKey + '][enabled]"]' );

				if ( $checkbox.is( ':checked' ) ) {
					// Check if already in hooks list
					var exists = self.hooks.some( function( h ) {
						return h.wp_hook === wpHook;
					} );

					if ( ! exists ) {
						self.hooks.push( {
							wp_hook: wpHook,
							event_name: eventName,
							enabled: true,
							metadata: []
						} );
					}
				}
			} );
		},

		handleBuiltinToggle: function( e ) {
			var $checkbox = $( e.currentTarget );
			var optionName = $checkbox.attr( 'name' ); // betterlytics_options[woo_add_to_cart][enabled]
			var match = optionName.match( /\[(woo_[a-z_]+)\]/ );
			
			if ( ! match ) return;

			var optionKey = match[1];
			var config = this.builtinHooks[ optionKey ];
			if ( ! config ) return;

			var wpHook = config[0];
			var eventName = config[1];

			if ( $checkbox.is( ':checked' ) ) {
				// Add to list if not exists
				var exists = this.hooks.some( function( h ) {
					return h.wp_hook === wpHook;
				} );

				if ( ! exists ) {
					this.hooks.push( {
						wp_hook: wpHook,
						event_name: eventName,
						enabled: true,
						metadata: []
					} );
					this.renderHooks();
				}
			} else {
				// Remove from list
				this.hooks = this.hooks.filter( function( h ) {
					return h.wp_hook !== wpHook;
				} );
				this.renderHooks();
			}
		},

		switchTab: function( e ) {
			e.preventDefault();
			var tab = $( e.currentTarget ).data( 'tab' );
			this.activateTab( tab );

			// Update URL with subtab query param
			var url = new URL( window.location.href );
			url.searchParams.set( 'subtab', tab );
			window.history.replaceState( null, null, url.toString() );

			// Update the referer field so WordPress redirects back with the correct subtab
			var $referer = $( 'input[name="_wp_http_referer"]' );
			if ( $referer.length ) {
				var refererUrl = new URL( $referer.val(), window.location.origin );
				refererUrl.searchParams.set( 'subtab', tab );
				$referer.val( refererUrl.pathname + refererUrl.search );
			}
		},

		activateTab: function( tab ) {
			$( '.betterlytics-subtabs .betterlytics-subtab' ).removeClass( 'active' );
			$( '.betterlytics-subtabs .betterlytics-subtab[data-tab="' + tab + '"]' ).addClass( 'active' );
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
				hook.metadata.forEach( function( meta, metaIndex ) {
					metadataHtml += this.renderMetadataItem( meta, index, metaIndex );
				}.bind( this ) );
			}

			// Name attributes are critical for native form submission
			var namePrefix = 'betterlytics_options[hooks][' + index + ']';

			return '<tr data-index="' + index + '">' +
				'<td class="column-enabled text-center flex justify-center">' +
				'<input type="checkbox" name="' + namePrefix + '[enabled]" class="hook-enabled" value="true" ' + checked + '>' +
				'</td>' +
				'<td class="column-wp-hook">' +
				'<input type="text" name="' + namePrefix + '[wp_hook]" class="regular-text wp-hook-input" value="' + this.escapeHtml( hook.wp_hook || '' ) + '" placeholder="wp_hook_name">' +
				'</td>' +
				'<td class="column-event-name">' +
				'<input type="text" name="' + namePrefix + '[event_name]" class="regular-text event-name-input" value="' + this.escapeHtml( hook.event_name || '' ) + '" placeholder="Event Name">' +
				'</td>' +
				'<td class="column-actions">' +
				'<div class="flex items-center justify-start gap-2 whitespace-nowrap">' +
				'<button type="button" class="button button-small betterlytics-toggle-metadata !flex !items-center !justify-center !w-8 !h-8 !p-0" title="Configure Metadata"><span class="dashicons dashicons-admin-generic !leading-none !m-0"></span></button> ' +
				'<button type="button" class="button button-small betterlytics-delete-hook !flex !items-center !justify-center !w-8 !h-8 !p-0" title="Delete"><span class="dashicons dashicons-trash !leading-none !m-0"></span></button>' +
				'</div>' +
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
		
		renderMetadataItem: function( meta, hookIndex, metaIndex ) {
			var namePrefix = 'betterlytics_options[hooks][' + hookIndex + '][metadata][' + metaIndex + ']';
			return '<tr>' +
				'<td><input type="text" name="' + namePrefix + '[key]" class="betterlytics-metadata-key" value="' + this.escapeHtml( meta.key || '' ) + '" placeholder="e.g. order_id"></td>' +
				'<td><input type="text" name="' + namePrefix + '[value]" class="betterlytics-metadata-value" value="' + this.escapeHtml( meta.value || '' ) + '" placeholder="e.g. {0}"></td>' +
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
			var $btn = $( e.currentTarget );
			var hookIndex = $btn.closest( '.betterlytics-metadata-row' ).data( 'index' );
			
			if ( ! this.hooks[ hookIndex ].metadata ) {
				this.hooks[ hookIndex ].metadata = [];
			}
			
			this.hooks[ hookIndex ].metadata.push( {} );
			
			// Re-render only affects this hook row ideally, but renderHooks does all.
			// To keep it simple and robust (indices), we re-render all.
			// Focus management might be tricky, but for clicking 'Add' button it's okay.
			this.renderHooks();

			// Expand the metadata row again
			var $newRow = $( '#betterlytics-hooks-list' ).find( 'tr[data-index="' + hookIndex + '"]' );
			$newRow.next( '.betterlytics-metadata-row' ).addClass( 'active' );
		},
		
		deleteMetadata: function( e ) {
			e.preventDefault();
			if ( ! confirm( 'Remove this metadata field?' ) ) {
				return;
			}
			
			var $btn = $( e.currentTarget );
			var $tr = $btn.closest( 'tr' );
			var metaIndex = $tr.index();
			var hookIndex = $tr.closest( '.betterlytics-metadata-row' ).data( 'index' );
			
			if ( this.hooks[ hookIndex ] && this.hooks[ hookIndex ].metadata ) {
				this.hooks[ hookIndex ].metadata.splice( metaIndex, 1 );
				this.renderHooks();
				// Expand meta again
				var $newRow = $( '#betterlytics-hooks-list' ).find( 'tr[data-index="' + hookIndex + '"]' );
				$newRow.next( '.betterlytics-metadata-row' ).addClass( 'active' );
			}
		},

		updateModel: function( e ) {
			var $input = $( e.target );
			var $tr = $input.closest( 'tr' );
			
			// Check if it's metadata
			if ( $input.hasClass( 'betterlytics-metadata-key' ) || $input.hasClass( 'betterlytics-metadata-value' ) ) {
				var metaIndex = $tr.index();
				var hookIndex = $tr.closest( '.betterlytics-metadata-row' ).data( 'index' );
				var field = $input.hasClass( 'betterlytics-metadata-key' ) ? 'key' : 'value';
				
				if ( this.hooks[ hookIndex ] && this.hooks[ hookIndex ].metadata[ metaIndex ] ) {
					this.hooks[ hookIndex ].metadata[ metaIndex ][ field ] = $input.val();
				}
			} else {
				// Main hook property
				var index = $tr.data( 'index' );
				if ( typeof index !== 'undefined' && this.hooks[ index ] ) {
					if ( $input.hasClass( 'wp-hook-input' ) ) {
						this.hooks[ index ].wp_hook = $input.val();
					} else if ( $input.hasClass( 'event-name-input' ) ) {
						this.hooks[ index ].event_name = $input.val();
					} else if ( $input.hasClass( 'hook-enabled' ) ) {
						this.hooks[ index ].enabled = $input.is( ':checked' );
					}
				}
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

			// Confirmation removed as per user request (saving is explicit)


			var $row = $( e.currentTarget ).closest( 'tr' );
			var index = $row.data( 'index' );
			var hook = this.hooks[ index ];

			// Sync back to settings toggle: if this hook matches a built-in, uncheck it.
			var self = this;
			$.each( this.builtinHooks, function( optionKey, config ) {
				if ( config[0] === hook.wp_hook ) {
					// Selector updated for new array format: betterlytics_options[key][enabled]
					$( 'input[name="betterlytics_options[' + optionKey + '][enabled]"]' ).prop( 'checked', false );
				}
			} );

			this.hooks.splice( index, 1 );
			this.renderHooks();
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
