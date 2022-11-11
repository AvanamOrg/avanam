<?php
/**
 * Template part for displaying the footer info
 *
 * @package Base
 */

namespace Base;

$align = ( webapp()->sub_option( 'footer_widget6_align', 'desktop' ) ? webapp()->sub_option( 'footer_widget6_align', 'desktop' ) : 'default' );
$tablet_align = ( webapp()->sub_option( 'footer_widget6_align', 'tablet' ) ? webapp()->sub_option( 'footer_widget6_align', 'tablet' ) : 'default' );
$mobile_align = ( webapp()->sub_option( 'footer_widget6_align', 'mobile' ) ? webapp()->sub_option( 'footer_widget6_align', 'mobile' ) : 'default' );

$valign = ( webapp()->sub_option( 'footer_widget6_vertical_align', 'desktop' ) ? webapp()->sub_option( 'footer_widget6_vertical_align', 'desktop' ) : 'default' );
$tablet_valign = ( webapp()->sub_option( 'footer_widget6_vertical_align', 'tablet' ) ? webapp()->sub_option( 'footer_widget6_vertical_align', 'tablet' ) : 'default' );
$mobile_valign = ( webapp()->sub_option( 'footer_widget6_vertical_align', 'mobile' ) ? webapp()->sub_option( 'footer_widget6_vertical_align', 'mobile' ) : 'default' );

?>
<div class="footer-widget-area widget-area site-footer-focus-item footer-widget6 content-align-<?php echo esc_attr( $align ); ?> content-tablet-align-<?php echo esc_attr( $tablet_align ); ?> content-mobile-align-<?php echo esc_attr( $mobile_align ); ?> content-valign-<?php echo esc_attr( $valign ); ?> content-tablet-valign-<?php echo esc_attr( $tablet_valign ); ?> content-mobile-valign-<?php echo esc_attr( $mobile_valign ); ?>" data-section="sidebar-widgets-footer6">
	<div class="footer-widget-area-inner site-info-inner">
		<?php
		dynamic_sidebar( 'footer6' );
		?>
	</div>
</div><!-- .footer-widget6 -->
