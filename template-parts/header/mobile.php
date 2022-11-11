<?php
/**
 * Template part for displaying the Mobile Header
 *
 * @package Base
 */

namespace Base;

?>

<div id="mobile-header" class="site-mobile-header-wrap">
	<div class="site-header-inner-wrap<?php echo esc_attr( 'top_main_bottom' === webapp()->option( 'mobile_header_sticky' ) ? ' base-sticky-header' : '' ); ?>"<?php
	if ( 'top_main_bottom' === webapp()->option( 'mobile_header_sticky' ) ) {
		echo ' data-shrink="' . ( webapp()->option( 'mobile_header_sticky_shrink' ) ? 'true' : 'false' ) . '"';
		echo ' data-reveal-scroll-up="' . ( webapp()->option( 'mobile_header_reveal_scroll_up' ) ? 'true' : 'false' ) . '"';
		if ( webapp()->option( 'mobile_header_sticky_shrink' ) ) {
			echo ' data-shrink-height="' . esc_attr( webapp()->sub_option( 'mobile_header_sticky_main_shrink', 'size' ) ) . '"';
		}
	}
	?>>
		<div class="site-header-upper-wrap">
			<div class="site-header-upper-inner-wrap<?php echo esc_attr( 'top_main' === webapp()->option( 'mobile_header_sticky' ) ? ' base-sticky-header' : '' ); ?>"<?php
			if ( 'top_main' === webapp()->option( 'mobile_header_sticky' ) ) {
				echo ' data-shrink="' . ( webapp()->option( 'mobile_header_sticky_shrink' ) ? 'true' : 'false' ) . '"';
				echo ' data-reveal-scroll-up="' . ( webapp()->option( 'mobile_header_reveal_scroll_up' ) ? 'true' : 'false' ) . '"';
				if ( webapp()->option( 'mobile_header_sticky_shrink' ) ) {
					echo ' data-shrink-height="' . esc_attr( webapp()->sub_option( 'mobile_header_sticky_main_shrink', 'size' ) ) . '"';
				}
			}
			?>>
			<?php
			/**
			 * Base Top Header
			 *
			 * Hooked base_mobile_top_header 10
			 */
			do_action( 'base_mobile_top_header' );
			/**
			 * Base Main Header
			 *
			 * Hooked base_mobile_main_header 10
			 */
			do_action( 'base_mobile_main_header' );
			?>
			</div>
		</div>
		<?php
		/**
		 * Base Mobile Bottom Header
		 *
		 * Hooked base_mobile_bottom_header 10
		 */
		do_action( 'base_mobile_bottom_header' );
		?>
	</div>
</div>
