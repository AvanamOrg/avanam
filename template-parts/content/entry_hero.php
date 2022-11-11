<?php
/**
 * Template part for displaying a post's Hero header
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
<section class="entry-hero <?php echo esc_attr( get_post_type() ) . '-hero-section'; ?> <?php echo esc_attr( 'entry-hero-layout-' . webapp()->option( get_post_type() . '_title_inner_layout' ) ); ?>">
	<div class="entry-hero-container-inner">
		<div class="hero-section-overlay"></div>
		<div class="hero-container site-container">
			<header class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
				<?php
				/**
				 * Base Entry Hero
				 *
				 * Hooked base_entry_header 10
				 */
				do_action( 'base_entry_hero', get_post_type(), 'above' );
				?>
			</header><!-- .entry-header -->
		</div>
	</div>
</section><!-- .entry-hero -->
