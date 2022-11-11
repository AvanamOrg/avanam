<?php
/**
 * Replies Loop - Single Reply
 *
 * @package bbPress
 * @subpackage Theme
 */

/* Edited
Edit: "remove bbp-meta."
move around content: "remove bbp-footer."
*/

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>


<div id="post-<?php bbp_reply_id(); ?>" <?php bbp_reply_class(); ?>>
	<div class="bbp-reply-author">

		<?php do_action( 'bbp_theme_before_reply_author_details' ); ?>

		<?php bbp_reply_author_avatar( bbp_get_reply_id(), 60 ); ?>

		<?php do_action( 'bbp_theme_after_reply_author_details' ); ?>

	</div><!-- .bbp-reply-author -->

	<div class="bbp-reply-content">

		<?php do_action( 'bbp_theme_before_reply_content' ); ?>
		<div class="bbp-head-area">

			<div class="reply-author-displayname"><?php bbp_reply_author_link( array( 'sep' => ' ', 'show_role' => false, 'type' => 'name' ) ); ?></div>

			<div class="bbp-meta">

				<?php printf( __( '%1$s at %2$s', 'avanam' ), get_the_date(), esc_attr( get_the_time() ) ); ?>

				<a href="<?php bbp_reply_url(); ?>" title="<?php bbp_reply_title(); ?>" class="bbp-reply-permalink">#<?php bbp_reply_id(); ?></a>

			</div><!-- .bbp-meta -->
		</div>

		<?php bbp_reply_content(); ?>
		<?php do_action( 'bbp_theme_before_reply_admin_links' ); ?>

			<?php bbp_reply_admin_links(); ?>

		<?php do_action( 'bbp_theme_after_reply_admin_links' ); ?>
		<?php do_action( 'bbp_theme_after_reply_content' ); ?>

	</div><!-- .bbp-reply-content -->
</div><!-- .reply -->
