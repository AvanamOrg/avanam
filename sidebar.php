<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Base
 */

namespace Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! webapp()->has_sidebar() ) {
	return;
}
webapp()->print_styles( 'base-sidebar' );

?>
<aside id="secondary" role="complementary" class="primary-sidebar widget-area <?php echo esc_attr( webapp()->sidebar_id_class() ); ?> sidebar-link-style-<?php echo esc_attr( webapp()->option( 'sidebar_link_style' ) ); ?>">
	<div class="sidebar-inner-wrap">
		<?php
		/**
		 * Hook for before sidebar.
		 */
		do_action( 'base_before_sidebar' );

		webapp()->display_sidebar();
		/**
		 * Hook for after sidebar.
		 */
		do_action( 'base_after_sidebar' );
		?>
	</div>
</aside><!-- #secondary -->
