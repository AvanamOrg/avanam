<?php
/**
 * Template part for displaying a post's Hero header
 *
 * @package Base
 */

namespace Base;

$slug = ( is_search() && ! is_post_type_archive( 'product' ) ? 'search' : get_post_type() );
if ( empty( $slug ) ) {
	$queried_object = get_queried_object();
	if ( is_object( $queried_object ) && property_exists( $queried_object, 'taxonomy' ) ) {
		$current_tax = get_taxonomy( $queried_object->taxonomy );
		if ( property_exists( $current_tax, 'object_type' ) ) {
			$post_types = $current_tax->object_type;
			$slug = $post_types[0];
		}
	}
}
?>
<section class="<?php echo esc_attr( implode( ' ', get_archive_hero_classes() ) ); ?>">
	<div class="entry-hero-container-inner">
		<div class="hero-section-overlay"></div>
		<div class="hero-container site-container">
			<header class="<?php echo esc_attr( implode( ' ', get_archive_title_classes() ) ); ?>">
				<?php
				/**
				 * Base Entry Hero
				 *
				 * Hooked base_entry_archive_header 10
				 */
				do_action( 'base_entry_archive_hero', $slug . '_archive', 'above' );
				?>
			</header><!-- .entry-header -->
		</div>
	</div>
</section><!-- .entry-hero -->
