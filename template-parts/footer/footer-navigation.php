<?php
/**
 * Template part for displaying the header Social Modual
 *
 * @package Base
 */

namespace Base;

$align        = ( webapp()->sub_option( 'footer_navigation_align', 'desktop' ) ? webapp()->sub_option( 'footer_navigation_align', 'desktop' ) : 'default' );
$tablet_align = ( webapp()->sub_option( 'footer_navigation_align', 'tablet' ) ? webapp()->sub_option( 'footer_navigation_align', 'tablet' ) : 'default' );
$mobile_align = ( webapp()->sub_option( 'footer_navigation_align', 'mobile' ) ? webapp()->sub_option( 'footer_navigation_align', 'mobile' ) : 'default' );

$valign        = ( webapp()->sub_option( 'footer_navigation_vertical_align', 'desktop' ) ? webapp()->sub_option( 'footer_navigation_vertical_align', 'desktop' ) : 'default' );
$tablet_valign = ( webapp()->sub_option( 'footer_navigation_vertical_align', 'tablet' ) ? webapp()->sub_option( 'footer_navigation_vertical_align', 'tablet' ) : 'default' );
$mobile_valign = ( webapp()->sub_option( 'footer_navigation_vertical_align', 'mobile' ) ? webapp()->sub_option( 'footer_navigation_vertical_align', 'mobile' ) : 'default' );

?>
<div class="footer-widget-area widget-area site-footer-focus-item footer-navigation-wrap content-align-<?php echo esc_attr( $align ); ?> content-tablet-align-<?php echo esc_attr( $tablet_align ); ?> content-mobile-align-<?php echo esc_attr( $mobile_align ); ?> content-valign-<?php echo esc_attr( $valign ); ?> content-tablet-valign-<?php echo esc_attr( $tablet_valign ); ?> content-mobile-valign-<?php echo esc_attr( $mobile_valign ); ?> footer-navigation-layout-stretch-<?php echo ( webapp()->option( 'footer_navigation_stretch' ) ? 'true' : 'false' ); ?>" data-section="base_customizer_footer_navigation">
	<div class="footer-widget-area-inner footer-navigation-inner">
		<?php
		/**
		 * Base Footer Navigation
		 *
		 * Hooked Base\footer_navigation
		 */
		do_action( 'base_footer_navigation' );
		?>
	</div>
</div><!-- data-section="footer_navigation" -->
