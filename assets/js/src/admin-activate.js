/**
 * Ajax install the Theme Plugin
 *
 */
(function($, window, document, undefined){
	"use strict";
	$(function(){
		$( '#base-notice-starter-templates .notice-dismiss' ).on( 'click', function( event ) {
			base_dismissNotice();
		} );
		function base_dismissNotice(){
			var data = new FormData();
			data.append( 'action', 'base_dismiss_notice' );
			data.append( 'security', baseStarterInstall.ajax_nonce );
			$.ajax({
				url : baseStarterInstall.ajax_url,
				method:  'POST',
				data: data,
				contentType: false,
				processData: false,
			});
		}
		$( '#base-notice-starter-templates .base-install-starter-btn' ).on( 'click', function( event ) {
			var $button = $( event.target );
			event.preventDefault();
			/**
			 * Keep button from running twice
			 */
			if ( $button.hasClass( 'updating-message' ) || $button.hasClass( 'button-disabled' ) ) {
				return;
			}

			var data = new FormData();
			data.append( 'action', 'base_install_starter' );
			data.append( 'security', baseStarterInstall.ajax_nonce );
			data.append( 'status', baseStarterInstall.status );

			/**
			 * Install a plugin
			 *
			 * @return void
			 */
			function installPlugin(){
				$.ajax({
					method: 'POST',
					url: baseStarterInstall.ajax_url,
					data: data,
					contentType: false,
					processData: false,
					beforeSend: function () {
						buttonStatusInProgress( $button.data('installing-label') );
					},
					success: function( response ) {
						if ( response.success ) {
							buttonStatusInstalled( $button.data('installed-label') );
							buttonStatusDisabled( $button.data('activated-label') );
							base_dismissNotice();
							location.replace( $button.data('redirect-url') );
						} else {
							console.log( response );
							buttonStatusDisabled( response.data );
						}
					},
					error: function (xhr, ajaxOptions, thrownError) {
						console.log( xhr.responseText );
						// Installation failed
						buttonStatusDisabled( 'Error' );
					}
				});
			}

			/**
			 * Activate a plugin
			 *
			 * @return void
			 */
			function activatePlugin(){

				$.ajax({
					url: baseStarterInstall.ajax_url,
					method:  'POST',
					data: data,
					contentType: false,
					processData: false,
					beforeSend: function () {
						buttonStatusInProgress( $button.data('activating-label') );
					},
					success: function( response ) {
						if ( response.success ) {
							buttonStatusDisabled( $button.data('activated-label') );
							base_dismissNotice();
							location.replace( $button.data('redirect-url') );
						} else {
							console.log( response );
							buttonStatusDisabled( response.data );
						}
					},
					error: function (xhr, ajaxOptions, thrownError) {
						 // Activation failed
						console.log( xhr.responseText );
						buttonStatusDisabled( 'Error' );
					}
				});
			}

			/**
			 * Change button status to in-progress
			 *
			 * @return void
			 */
			function buttonStatusInProgress( message ){
				$button.addClass('updating-message').removeClass('button-disabled bst-not-installed installed').text( message );
			}

			/**
			 * Change button status to disabled
			 *
			 * @return void
			 */
			function buttonStatusDisabled( message ){
				$button.removeClass('updating-message bst-not-installed installed')
				.addClass('button-disabled')
				.text( message );
			}

			/**
			 * Change button status to installed
			 *
			 * @return void
			 */
			function buttonStatusInstalled( message ){
				$button.removeClass('updating-message bst-not-installed')
					.addClass('installed')
					.text( message );
			}


			if ( baseStarterInstall.status === 'install' ){
				installPlugin();
			} else if( baseStarterInstall.status === 'activate' ){
				activatePlugin();
			}
		});
	});
})(jQuery, window, document);