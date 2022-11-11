<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Base
 */

namespace Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
webapp()->print_styles( 'base-comments' );
?>
<div id="comments" class="comments-area<?php echo ( webapp()->option( 'post_footer_area_boxed' ) ? ' content-bg entry-content-wrap entry' : '' ); ?>">
	<?php
	do_action( 'base_before_comments' );
	/**
	 * Base Comments hook.
	 *
	 * @hooked Base\comments_list - 10
	 * @hooked Base\comments_form - 15/5
	 */
	do_action( 'base_comments' );

	do_action( 'base_after_comments' );

	?>
</div><!-- #comments -->
