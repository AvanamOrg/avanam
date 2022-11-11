<?php
/**
 * Template part for displaying a post's content
 *
 * @package Base
 */

namespace Base;

?>

<div class="<?php echo esc_attr( apply_filters( 'base_entry_content_class', 'entry-content single-content' ) ); ?>">
	<?php
	do_action( 'base_single_before_entry_content' );

	the_content(
		sprintf(
			wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers */
				__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'avanam' ),
				array(
					'span' => array(
						'class' => array(),
					),
				)
			),
			get_the_title()
		)
	);

	wp_link_pages(
		array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'avanam' ),
			'after'  => '</div>',
		)
	);
	do_action( 'base_single_after_entry_content' );
	?>
</div><!-- .entry-content -->
