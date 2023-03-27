<?php
/**
 * Template part for displaying a post's footer
 *
 * @package Base
 */

namespace Base;

use WP_Query;
use function add_action;
use function apply_filters;
use function Base\webapp;
use function get_template_part;

webapp()->print_styles( 'base-related-posts' );
webapp()->print_styles( 'bst-splide' );
wp_enqueue_script( 'base-slide-init' );

$args          = get_related_posts_args( $post->ID );
$cols          = get_related_posts_columns();
$columns_class = apply_filters( 'base_related_posts_columns_class', ( 2 === $cols['xxl'] ? 'grid-sm-col-2 grid-lg-col-2' : 'grid-sm-col-2 grid-lg-col-3' ) );

$bpc = new WP_Query( apply_filters( 'base_related_posts_carousel_args', $args ) );
if ( $bpc ) :
	$num = $bpc->post_count;
	if ( $num > 0 ) {
		?>
		<div class="entry-related alignfull entry-related-style-<?php echo esc_attr( webapp()->option( 'post_related_style' ) ); ?>">
			<div class="entry-related-inner content-container site-container">
				<div class="entry-related-inner-content alignwide">
					<?php echo wp_kses_post( apply_filters( 'base_single_post_similar_posts_title', '<h2 class="entry-related-title">' . esc_html__( 'Similar Posts', 'avanam' ) . '</h2>' ) ); ?>
					<div class="entry-related-carousel base-slide-init splide" data-columns-xxl="<?php echo esc_attr( $cols['xxl'] ); ?>" data-columns-xl="<?php echo esc_attr( $cols['xl'] ); ?>" data-columns-md="<?php echo esc_attr( $cols['md'] ); ?>" data-columns-sm="<?php echo esc_attr( $cols['sm'] ); ?>" data-columns-xs="<?php echo esc_attr( $cols['xs'] ); ?>" data-columns-ss="<?php echo esc_attr( $cols['ss'] ); ?>" data-slider-anim-speed="400" data-slider-scroll="1" data-slider-dots="true" data-slider-arrows="true" data-slider-hover-pause="false" data-slider-auto="<?php echo esc_attr( apply_filters( 'base_single_post_similar_posts_carousel_autoplay', false ) ? 'true' : 'false' ); ?>" data-slider-speed="7000" data-slider-gutter="40" data-slider-loop="<?php echo esc_attr( webapp()->option( 'post_related_carousel_loop' ) ? 'true' : 'false' ); ?>" data-slider-next-label="<?php echo esc_attr__( 'Next', 'avanam' ); ?>" data-slider-slide-label="<?php echo esc_attr__( 'Posts', 'avanam' ); ?>" data-slider-prev-label="<?php echo esc_attr__( 'Previous', 'avanam' ); ?>">
						<div class="splide__track">
							<div class="splide__list grid-cols <?php echo esc_attr( $columns_class ); ?>">
								<?php
								while ( $bpc->have_posts() ) :
									$bpc->the_post();
									echo '<div class="carousel-item splide__slide">';
									get_template_part( 'template-parts/content/entry', get_post_type() );
									echo '</div>';
								endwhile;
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- .entry-author -->
		<?php
	}
endif;
wp_reset_postdata();
