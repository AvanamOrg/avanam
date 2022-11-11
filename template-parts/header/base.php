<?php
/**
 * Template part for displaying the header
 *
 * @package Base
 */

namespace Base;

webapp()->print_styles( 'base-header' );
?>
<header id="masthead" class="site-header" role="banner" <?php webapp()->print_microdata( 'header' ); ?>>
	<div id="main-header" class="site-header-wrap">
		<div class="site-header-inner-wrap<?php echo esc_attr( 'top_main_bottom' === webapp()->option( 'header_sticky' ) ? ' base-sticky-header' : '' ); ?>"<?php
		if ( 'top_main_bottom' === webapp()->option( 'header_sticky' ) ) {
			echo ' data-reveal-scroll-up="' . ( webapp()->option( 'header_reveal_scroll_up' ) ? 'true' : 'false' ) . '"';
			echo ' data-shrink="' . ( webapp()->option( 'header_sticky_shrink' ) ? 'true' : 'false' ) . '"';
			if ( webapp()->option( 'header_sticky_shrink' ) ) {
				echo ' data-shrink-height="' . esc_attr( webapp()->sub_option( 'header_sticky_main_shrink', 'size' ) ) . '"';
			}
		}
		?>>
			<div class="site-header-upper-wrap">
				<div class="site-header-upper-inner-wrap<?php echo esc_attr( 'top_main' === webapp()->option( 'header_sticky' ) ? ' base-sticky-header' : '' ); ?>"<?php
				if ( 'top_main' === webapp()->option( 'header_sticky' ) ) {
					echo ' data-reveal-scroll-up="' . ( webapp()->option( 'header_reveal_scroll_up' ) ? 'true' : 'false' ) . '"';
					echo ' data-shrink="' . ( webapp()->option( 'header_sticky_shrink' ) ? 'true' : 'false' ) . '"';
					if ( webapp()->option( 'header_sticky_shrink' ) ) {
						echo ' data-shrink-height="' . esc_attr( webapp()->sub_option( 'header_sticky_main_shrink', 'size' ) ) . '"';
					}
				}
				?>>
					<?php
					/**
					 * Base Top Header
					 *
					 * Hooked Base\top_header
					 */
					do_action( 'base_top_header' );
					/**
					 * Base Main Header
					 *
					 * Hooked Base\main_header
					 */
					do_action( 'base_main_header' );
					?>
				</div>
			</div>
			<?php
			/**
			 * Base Bottom Header
			 *
			 * Hooked Base\bottom_header
			 */
			do_action( 'base_bottom_header' );
			?>
		</div>
	</div>
	<?php
	/**
	 * Base Mobile Header
	 *
	 * Hooked Base\mobile_header
	 */
	do_action( 'base_mobile_header' );
	?>
</header><!-- #masthead -->
