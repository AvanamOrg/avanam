<?php
/**
 * Template part for displaying comments list.
 *
 * @package Base
 */

namespace Base;

?>
<?php
if ( have_comments() ) {
	do_action( 'base_before_comments_list' );

	$title_output = '<h2 class="comments-title">';
	$comment_count = (int) get_comments_number();
	if ( 1 === $comment_count ) {
		$title_output .= esc_html__( 'One Comment', 'avanam' );
	} else {
		$title_output .= sprintf(
			/* translators: 1: comment count number */
			esc_html( _nx( '%1$s Comment', '%1$s Comments', $comment_count, 'comments title', 'avanam' ) ),
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			number_format_i18n( $comment_count )
		);
	}
	$title_output .= '</h2><!-- .comments-title -->';
	echo wp_kses_post( apply_filters( 'base_single_post_comments_title', $title_output ) );

	the_comments_navigation();

	webapp()->the_comments();

	if ( ! comments_open() ) {
		?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'avanam' ); ?></p>
		<?php
	}
	do_action( 'base_after_comments_list' );
}
