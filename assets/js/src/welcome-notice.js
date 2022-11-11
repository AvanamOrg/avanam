/**
 * Ajax Welcome the Theme Plugin
 *
 */
(function($, window, document, undefined){
	"use strict";
	$(function(){
		$( '#base-notice-welcome-plugin .notice-dismiss' ).on( 'click', function( event ) {
			base_dismissWelcomeNotice();
		} );
		function base_dismissWelcomeNotice(){
			var data = new FormData();
			data.append( 'action', 'base_dismiss_welcome_notice' );
			data.append( 'security', baseWelcomeDeactivate.ajax_nonce );
			$.ajax({
				url : baseWelcomeDeactivate.ajax_url,
				method:  'POST',
				data: data,
				contentType: false,
				processData: false,
			});
		}
	});
})(jQuery, window, document);