<?php
/**
 * Template part for displaying a post of post type 'attachment'
 *
 * @package Base
 */

namespace Base;

if ( ! is_singular( 'attachment' ) ) {
?>

<article <?php post_class( 'entry content-bg loop-entry' ); ?>>
	<?php
		$defaults = array(
			'enabled'   => true,
			'ratio'     => '2-3',
			'size'      => 'medium_large',
			'imageLink' => true,
		);
		$slug            = ( is_search() ? 'search' : get_post_type() );
		$feature_element = webapp()->option( $slug . '_archive_element_feature', $defaults );
		if ( isset( $feature_element ) && is_array( $feature_element ) && true === $feature_element['enabled'] ) {
			$feature_element = wp_parse_args( $feature_element, $defaults );
			$ratio = ( isset( $feature_element['ratio'] ) && ! empty( $feature_element['ratio'] ) ? $feature_element['ratio'] : '2-3' );
			$size  = ( isset( $feature_element['size'] ) && ! empty( $feature_element['size'] ) ? $feature_element['size'] : 'medium_large' );
			if ( isset( $feature_element['imageLink'] ) && ! $feature_element['imageLink'] ) {
				?>
				<div class="post-thumbnail base-thumbnail-ratio-<?php echo esc_attr( $ratio ); ?>">
					<div class="post-thumbnail-inner">
						<?php
						echo wp_get_attachment_image(
							get_the_ID(),
							$size,
							false,
							array(
								'alt' => the_title_attribute(
									array(
										'echo' => false,
									)
								),
							)
						);
						?>
					</div>
				</div><!-- .post-thumbnail -->
				<?php
			} else {
				?>
				<a class="post-thumbnail base-thumbnail-ratio-<?php echo esc_attr( $ratio ); ?>" href="<?php the_permalink(); ?>">
					<div class="post-thumbnail-inner">
						<?php
						echo wp_get_attachment_image(
							get_the_ID(),
							$size,
							false,
							array(
								'alt' => the_title_attribute(
									array(
										'echo' => false,
									)
								),
							)
						);
						?>
					</div>
				</a><!-- .post-thumbnail -->
				<?php
			}
		}		
	?>
	<div class="entry-content-wrap">
		<?php
		/**
		 * Hook for entry content.
		 *
		 * @hooked Base\loop_entry_header - 10
		 * @hooked Base\loop_entry_summary - 20
		 * @hooked Base\loop_entry_footer - 30
		 */
		do_action( 'base_loop_entry_content' );
		?>
	</div>
</article>

<?php
}
if ( is_singular( 'attachment' ) ) {
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
	<?php get_template_part( 'template-parts/content/entry_header', get_post_type() ); ?>

	<?php get_template_part( 'template-parts/content/entry_content', get_post_type() ); ?>

	<?php get_template_part( 'template-parts/content/entry_footer', get_post_type() ); ?>
</article><!-- #post-<?php the_ID(); ?> -->

<?php
}
if ( is_singular( get_post_type() ) ) {
	// Show attachment navigation only when the attachment has a parent.
	if ( ! empty( $post->post_parent ) ) {

		// TODO: There should be a WordPress core function for this, similar to `the_post_navigation()`.
		$attachment_navigation = '';

		ob_start();
		previous_image_link( false );
		$prev_link = ob_get_clean();
		if ( ! empty( $prev_link ) ) {
			$attachment_navigation .= '<div class="nav-previous">';
			$attachment_navigation .= '<div class="post-navigation-sub"><span>' . esc_html__( 'Previous:', 'avanam' ) . '</span></div>';
			$attachment_navigation .= $prev_link;
			$attachment_navigation .= '</div>';
		}

		ob_start();
		next_image_link( false );
		$next_link = ob_get_clean();
		if ( ! empty( $next_link ) ) {
			$attachment_navigation .= '<div class="nav-next">';
			$attachment_navigation .= '<div class="post-navigation-sub"><span>' . esc_html__( 'Next:', 'avanam' ) . '</span></div>';
			$attachment_navigation .= $next_link;
			$attachment_navigation .= '</div>';
		}

		if ( ! empty( $attachment_navigation ) ) {
			echo _navigation_markup( $attachment_navigation, $class = 'post-navigation', __( 'Post navigation', 'avanam' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	// Show comments only when the post type supports it and when comments are open or at least one comment exists.
	if ( post_type_supports( get_post_type(), 'comments' ) && ( comments_open() || get_comments_number() ) ) {
		comments_template();
	}
}
