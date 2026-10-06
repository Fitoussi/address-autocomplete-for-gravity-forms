/**
 * Display Search discovery dialogs without creating forms or loading Search.
 *
 * @since 1.0.0
 *
 * @file search-unavailable.js
 */

( function ( $ ) {
	'use strict';

	/**
	 * Open an external pricing page after a visitor chooses to follow the link.
	 *
	 * @since 1.0.0
	 *
	 * @param {string} url Public pricing URL.
	 * @returns {void}
	 */
	function openPricing( url ) {
		window.open( url, '_blank', 'noopener,noreferrer' );
	}

	/**
	 * Bind native, jQuery UI or browser-confirmation Search dialogs.
	 *
	 * @since 1.0.0
	 *
	 * @param {Object} config Localized dialog copy and selector configuration.
	 * @returns {void}
	 */
	function bindUnavailableModal( config ) {
		var selectors = config && config.selectors ? String( config.selectors ) : '';
		if ( ! selectors ) {
			return;
		}

		$( document ).on( 'click', selectors, function ( e ) {
			var title = String( config.title || '' );
			var message = String( config.message || '' );
			var pricingUrl = String( config.pricingUrl || '' );

			if ( window.gform && gform.instances ) {
				if ( typeof gform.instances.dialogConfirmAsync === 'function' ) {
					e.preventDefault();
					gform.instances.dialogConfirmAsync( title, message ).then( function ( confirmed ) {
						if ( confirmed ) {
							openPricing( pricingUrl );
						}
					} );
					return;
				}

				if ( typeof gform.instances.dialogConfirm === 'function' ) {
					e.preventDefault();
					gform.instances.dialogConfirm( title, message, function () {
						openPricing( pricingUrl );
					} );
					return;
				}

				if ( typeof gform.instances.dialogAlert === 'function' ) {
					e.preventDefault();
					gform.instances.dialogAlert( title, message + ' <a href="' + pricingUrl + '" target="_blank" rel="noopener noreferrer">' + String( config.viewPricingLabel || '' ) + '</a>' );
					return;
				}
			}

			if ( $.ui && typeof $.ui.dialog === 'function' ) {
				e.preventDefault();

				var dialogId = 'gfgeo-search-unavailable-dialog-content';
				var $existing = $( '#' + dialogId );
				if ( $existing.length ) {
					$existing.remove();
				}

				var $dialog = $( '<div/>' ).attr( 'id', dialogId ).html( message ).appendTo( 'body' );

				$dialog.dialog( {
					modal: true,
					title: title,
					width: 460,
					resizable: false,
					draggable: false,
					closeOnEscape: true,
					dialogClass: 'gfgeo-search-unavailable-dialog',
					buttons: [
						{
							text: String( config.cancelLabel || 'Cancel' ),
							click: function () {
								$( this ).dialog( 'close' );
							}
						},
						{
							text: String( config.viewPricingLabel || 'View pricing' ),
							click: function () {
								openPricing( pricingUrl );
								$( this ).dialog( 'close' );
							}
						}
					],
					close: function () {
						$( this ).dialog( 'destroy' ).remove();
					}
				} );
				return;
			}

			var confirmed = window.confirm( String( config.confirmText || '' ) );
			if ( ! confirmed ) {
				e.preventDefault();
			}
		} );
	}

	/**
	 * Add one non-functional Search discovery link beside the native Add New button.
	 *
	 * @since 1.0.0
	 *
	 * @param {Object} config Server-provided button visibility, URL and label.
	 * @returns {void}
	 */
	function injectFormsListButton( config ) {
		if ( ! config || ! config.enabled ) {
			return;
		}

		var $addNew = $( '.button.gform-add-new-form' ).first();
		if ( ! $addNew.length || $( '.gfgeo-add-new-search-form' ).length ) {
			return;
		}

		var $btn = $( '<a/>' ).attr( {
			'class': 'button page-title-action gfgeo-add-new-search-form gfgeo-create-search-form primary disabled',
			'href': String( config.url || '#' )
		} ).text( String( config.label || '' ) );

		$addNew.after( $btn );
	}

	$( function () {
		var data = window.gfgeoSearchUnavailable || {};
		bindUnavailableModal( data.modal || {} );
		injectFormsListButton( data.formsListButton || {} );
	} );
}( jQuery ) );
