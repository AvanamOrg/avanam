<?php
/**
 * Template part for displaying the a row of the footer
 *
 * @package Base
 */

namespace Base;

$row              = get_query_var( 'row' );
$tablet_contain   = ( webapp()->sub_option( 'footer_' . $row . '_contain', 'tablet' ) ? webapp()->sub_option( 'footer_' . $row . '_contain', 'tablet' ) : 'default' );
$mobile_contain   = ( webapp()->sub_option( 'footer_' . $row . '_contain', 'mobile' ) ? webapp()->sub_option( 'footer_' . $row . '_contain', 'mobile' ) : 'default' );
$tablet_layout    = ( webapp()->sub_option( 'footer_' . $row . '_layout', 'tablet' ) ? webapp()->sub_option( 'footer_' . $row . '_layout', 'tablet' ) : 'default' );
$link_style       = webapp()->option( 'footer_' . $row . '_link_style' );
$columns          = absint( webapp()->option( 'footer_' . $row . '_columns' ) );
$tablet_direction = ( webapp()->sub_option( 'footer_' . $row . '_direction', 'tablet' ) ? webapp()->sub_option( 'footer_' . $row . '_direction', 'tablet' ) : 'default' );
$mobile_direction = ( webapp()->sub_option( 'footer_' . $row . '_direction', 'mobile' ) ? webapp()->sub_option( 'footer_' . $row . '_direction', 'mobile' ) : 'default' );
$i                = 0;
?>
<div class="site-<?php echo esc_attr( $row ); ?>-footer-wrap site-footer-row-container site-footer-focus-item site-footer-row-layout-<?php echo esc_attr( webapp()->sub_option( 'footer_' . $row . '_contain', 'desktop' ) ); ?> site-footer-row-tablet-layout-<?php echo esc_attr( $tablet_contain ); ?> site-footer-row-mobile-layout-<?php echo esc_attr( $mobile_contain ); ?>" data-section="base_customizer_footer_<?php echo esc_attr( $row ); ?>">
	<div class="site-footer-row-container-inner">
		<?php if ( is_customize_preview() ) { ?>
			<div class="customize-partial-edit-shortcut base-custom-partial-edit-shortcut">
				<button aria-label="<?php esc_attr_e( 'Click to edit this element.', 'avanam' ); ?>" title="<?php esc_attr_e( 'Click to edit this element.', 'avanam' ); ?>" class="customize-partial-edit-shortcut-button item-customizer-focus"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13.89 3.39l2.71 2.72c.46.46.42 1.24.03 1.64l-8.01 8.02-5.56 1.16 1.16-5.58s7.6-7.63 7.99-8.03c.39-.39 1.22-.39 1.68.07zm-2.73 2.79l-5.59 5.61 1.11 1.11 5.54-5.65zm-2.97 8.23l5.58-5.6-1.07-1.08-5.59 5.6z"></path></svg></button>
			</div>
		<?php } ?>
		<div class="site-container">
			<div class="site-<?php echo esc_attr( $row ); ?>-footer-inner-wrap site-footer-row site-footer-row-columns-<?php echo esc_attr( webapp()->option( 'footer_' . $row . '_columns' ) ); ?> site-footer-row-column-layout-<?php echo esc_attr( webapp()->sub_option( 'footer_' . $row . '_layout', 'desktop' ) ); ?> site-footer-row-tablet-column-layout-<?php echo esc_attr( $tablet_layout ); ?> site-footer-row-mobile-column-layout-<?php echo esc_attr( webapp()->sub_option( 'footer_' . $row . '_layout', 'mobile' ) ); ?> ft-ro-dir-<?php echo esc_attr( webapp()->sub_option( 'footer_' . $row . '_direction', 'desktop' ) ); ?> ft-ro-collapse-<?php echo esc_attr( webapp()->option( 'footer_' . $row . '_collapse' ) ); ?> ft-ro-t-dir-<?php echo esc_attr( $tablet_direction ); ?> ft-ro-m-dir-<?php echo esc_attr( $mobile_direction ); ?> ft-ro-lstyle-<?php echo esc_attr( $link_style ); ?>">
				<?php
				while ( $i++ < $columns ) {
					?>
					<div class="site-footer-<?php echo esc_attr( $row ); ?>-section-<?php echo esc_attr( $i ); ?> site-footer-section footer-section-inner-items-<?php echo esc_attr( webapp()->footer_column_item_count( $row, $i ) ); ?>">
						<?php
						/**
						 * Base Render Footer Column
						 *
						 * Hooked Base\footer_column
						 */
						do_action( 'base_render_footer_column', $row, $i );
						?>
					</div>
					<?php
				}
				?>
			</div>
		</div>
	</div>
</div>
