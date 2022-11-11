<?php
/**
 * Template part for displaying a row of the mobile header
 *
 * @package Base
 */

namespace Base;

$row           = get_query_var( 'mobile_row' );
$tablet_layout = ( webapp()->sub_option( 'header_' . $row . '_layout', 'tablet' ) ? webapp()->sub_option( 'header_' . $row . '_layout', 'tablet' ) : 'default' );
$mobile_layout = ( webapp()->sub_option( 'header_' . $row . '_layout', 'mobile' ) ? webapp()->sub_option( 'header_' . $row . '_layout', 'mobile' ) : 'default' );
?>
<div class="site-<?php echo esc_attr( $row ); ?>-header-wrap site-header-focus-item site-header-row-layout-<?php echo esc_attr( webapp()->sub_option( 'header_' . $row . '_layout', 'desktop' ) ); ?> site-header-row-tablet-layout-<?php echo esc_attr( $tablet_layout ); ?> site-header-row-mobile-layout-<?php echo esc_attr( $mobile_layout ); ?> <?php echo esc_attr( $row === webapp()->option( 'mobile_header_sticky' ) ? ' base-sticky-header' : '' ); ?>"<?php
	if ( $row === 'main' && 'main' === webapp()->option( 'mobile_header_sticky' ) ) {
		echo ' data-shrink="' . ( webapp()->option( 'mobile_header_sticky_shrink' ) ? 'true' : 'false' ) . '"';
		echo ' data-reveal-scroll-up="' . ( webapp()->option( 'mobile_header_reveal_scroll_up' ) ? 'true' : 'false' ) . '"';
		if ( webapp()->option( 'mobile_header_sticky_shrink' ) ) {
			echo ' data-shrink-height="' . esc_attr( webapp()->sub_option( 'mobile_header_sticky_main_shrink', 'size' ) ) . '"';
		}
	}
	?>>
	<div class="site-header-row-container-inner">
		<div class="site-container">
			<div class="site-<?php echo esc_attr( $row ); ?>-header-inner-wrap site-header-row <?php echo ( webapp()->has_mobile_side_columns( $row ) ? 'site-header-row-has-sides' : 'site-header-row-only-center-column' ); ?> <?php echo ( webapp()->has_mobile_center_column( $row ) ? 'site-header-row-center-column' : 'site-header-row-no-center' ); ?>">
				<?php if ( webapp()->has_mobile_side_columns( $row ) ) { ?>
					<div class="site-header-<?php echo esc_attr( $row ); ?>-section-left site-header-section site-header-section-left">
						<?php
						/**
						 * Base Render Header Column
						 *
						 * Hooked Base\mobile_header_column
						 */
						do_action( 'base_render_mobile_header_column', $row, 'left' );

						if ( webapp()->has_mobile_center_column( $row ) ) {
							/**
							 * Base Render Header Column
							 *
							 * Hooked Base\mobile_header_column
							 */
							do_action( 'base_render_mobile_header_column', $row, 'left_center' );
						}
						?>
					</div>
				<?php } ?>
				<?php if ( webapp()->has_mobile_center_column( $row ) ) { ?>
					<div class="site-header-<?php echo esc_attr( $row ); ?>-section-center site-header-section site-header-section-center">
						<?php
						/**
						 * Base Render Header Column
						 *
						 * Hooked Base\mobile_header_column
						 */
						do_action( 'base_render_mobile_header_column', $row, 'center' );
						?>
					</div>
				<?php } ?>
				<?php if ( webapp()->has_mobile_side_columns( $row ) ) { ?>
					<div class="site-header-<?php echo esc_attr( $row ); ?>-section-right site-header-section site-header-section-right">
						<?php
						if ( webapp()->has_mobile_center_column( $row ) ) {
							/**
							 * Base Render Header Column
							 *
							 * Hooked Base\mobile_header_column
							 */
							do_action( 'base_render_mobile_header_column', $row, 'right_center' );
						}
						/**
							 * Base Render Header Column
							 *
							 * Hooked Base\mobile_header_column
							 */
							do_action( 'base_render_mobile_header_column', $row, 'right' );
						?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
