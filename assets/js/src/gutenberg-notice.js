/**
 * Ajax install the Theme Plugin
 *
 */
(function($, window, document, undefined){
	"use strict";
	$(function(){
		$( '#base-notice-gutenberg-plugin .notice-dismiss' ).on( 'click', function( event ) {
			base_dismissGutenbergNotice();
		} );
		function base_dismissGutenbergNotice(){
			var data = new FormData();
			data.append( 'action', 'base_dismiss_gutenberg_notice' );
			data.append( 'security', baseGutenbergDeactivate.ajax_nonce );
			$.ajax({
				url : baseGutenbergDeactivate.ajax_url,
				method:  'POST',
				data: data,
				contentType: false,
				processData: false,
			});
		}
	});
})(jQuery, window, document);