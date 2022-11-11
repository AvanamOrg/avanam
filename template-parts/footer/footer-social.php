<?php
/**
 * Template part for displaying the Footer Social Module
 *
 * @package Base
 */

namespace Base;

$align        = ( webapp()->sub_option( 'footer_social_align', 'desktop' ) ? webapp()->sub_option( 'footer_social_align', 'desktop' ) : 'default' );
$tablet_align = ( webapp()->sub_option( 'footer_social_align', 'tablet' ) ? webapp()->sub_option( 'footer_social_align', 'tablet' ) : 'default' );
$mobile_align = ( webapp()->sub_option( 'footer_social_align', 'mobile' ) ? webapp()->sub_option( 'footer_social_align', 'mobile' ) : 'default' );

$valign        = ( webapp()->sub_option( 'footer_social_vertical_align', 'desktop' ) ? webapp()->sub_option( 'footer_social_vertical_align', 'desktop' ) : 'default' );
$tablet_valign = ( webapp()->sub_option( 'footer_social_vertical_align', 'tablet' ) ? webapp()->sub_option( 'footer_social_vertical_align', 'tablet' ) : 'default' );
$mobile_valign = ( webapp()->sub_option( 'footer_social_vertical_align', 'mobile' ) ? webapp()->sub_option( 'footer_social_vertical_align', 'mobile' ) : 'default' );
if ( ! wp_style_is( 'base-header', 'enqueued' ) ) {
	wp_enqueue_style( 'base-header' );
}
?>
<div class="footer-widget-area widget-area site-footer-focus-item footer-social content-align-<?php echo esc_attr( $align ); ?> content-tablet-align-<?php echo esc_attr( $tablet_align ); ?> content-mobile-align-<?php echo esc_attr( $mobile_align ); ?> content-valign-<?php echo esc_attr( $valign ); ?> content-tablet-valign-<?php echo esc_attr( $tablet_valign ); ?> content-mobile-valign-<?php echo esc_attr( $mobile_valign ); ?>" data-section="base_customizer_footer_social">
	<div class="footer-widget-area-inner footer-social-inner">
		<?php
		/**
		 * Base Footer Social
		 *
		 * Hooked Base\footer_social
		 */
		do_action( 'base_footer_social' );
		?>
	</div>
</div><!-- data-section="footer_social" -->
