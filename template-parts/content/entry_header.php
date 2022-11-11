<?php
/**
 * Template part for displaying a post's header
 *
 * @package Base
 */

namespace Base;

$classes   = array();
$classes[] = 'entry-header';
if ( is_singular( get_post_type() ) ) {
	$classes[] = get_post_type() . '-title';
	$classes[] = 'title-align-' . ( webapp()->sub_option( get_post_type() . '_title_align', 'desktop' ) ? webapp()->sub_option( get_post_type() . '_title_align', 'desktop' ) : 'inherit' );
	$classes[] = 'title-tablet-align-' . ( webapp()->sub_option( get_post_type() . '_title_align', 'tablet' ) ? webapp()->sub_option( get_post_type() . '_title_align', 'tablet' ) : 'inherit' );
	$classes[] = 'title-mobile-align-' . ( webapp()->sub_option( get_post_type() . '_title_align', 'mobile' ) ? webapp()->sub_option( get_post_type() . '_title_align', 'mobile' ) : 'inherit' );
}
?>
<header class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<?php
	do_action( 'base_single_before_entry_header' );
	/**
	 * Base Entry Header
	 *
	 * Hooked base_entry_header 10
	 */
	do_action( 'base_entry_header', get_post_type(), 'normal' );
	
	do_action( 'base_single_after_entry_header' );
	?>
</header><!-- .entry-header -->
