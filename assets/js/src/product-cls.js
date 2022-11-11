/**
 * File product-cls.js.
 * Sets a fixed height while loading the product image slider to fix an issue with CLS. This should ultimately be fixed by wooocommerce/flexslider
 */

 jQuery( function( $ ) {
	$( '.woocommerce-product-gallery.gallery-has-thumbnails' ).each( function() {
		var gallery_wrap = $( this );
		gallery_wrap.height( gallery_wrap.height() );
		gallery_wrap.on( 'wc-product-gallery-after-init', function (event) {
			setTimeout( function() {
				gallery_wrap.height( '' );
			}, 500 );
		} );
	} );
} );

