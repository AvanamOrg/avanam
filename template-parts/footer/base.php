<?php
/**
 * Template part for displaying the footer info
 *
 * @package Base
 */

namespace Base;

if ( webapp()->has_content() ) {
	webapp()->print_styles( 'base-content' );
}
webapp()->print_styles( 'base-footer' );

?>
<footer id="colophon" class="site-footer" role="contentinfo">
	<div class="site-footer-wrap">
		<?php
		/**
		 * Base Top footer
		 *
		 * Hooked Base\top_footer
		 */
		do_action( 'base_top_footer' );
		/**
		 * Base Middle footer
		 *
		 * Hooked Base\middle_footer
		 */
		do_action( 'base_middle_footer' );
		/**
		 * Base Bottom footer
		 *
		 * Hooked Base\bottom_footer
		 */
		do_action( 'base_bottom_footer' );
		?>
	</div>
</footer><!-- #colophon -->

