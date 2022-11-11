<?php
/**
 * Template part for displaying the page header of the currently displayed page
 *
 * @package Base
 */

namespace Base;

$slug = ( is_search() && ! is_post_type_archive( 'product' ) ? 'search' : get_post_type() );
if ( empty( $slug ) ) {
	$queried_object = get_queried_object();
	if ( property_exists( $queried_object, 'taxonomy' ) ) {
		$current_tax = get_taxonomy( $queried_object->taxonomy );
		if ( property_exists( $current_tax, 'object_type' ) ) {
			$post_types = $current_tax->object_type;
			$slug = $post_types[0];
		}
	}
}
?>
<header class="<?php echo esc_attr( implode( ' ', get_archive_title_classes() ) ); ?>">
	<?php
	do_action( 'base_archive_before_entry_header' );
	/**
	 * Base Entry Header
	 *
	 * Hooked base_entry_header 10
	 */
	do_action( 'base_entry_archive_header', $slug . '_archive', 'normal' );

	do_action( 'base_archive_after_entry_header' );
	?>
</header><!-- .entry-header -->
