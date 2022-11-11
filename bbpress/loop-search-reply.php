<?php
/**
 * Search Loop - Single Reply
 *
 * @package bbPress
 * @subpackage Theme
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<div class="bbp-reply-header bbp-search-item-header">
	<div class="bbp-reply-sub-title">
		<?php esc_html_e( 'In forum:', 'avanam' ); ?>
		<a href="<?php bbp_forum_permalink( bbp_get_topic_forum_id( bbp_get_reply_topic_id() ) ); ?>"><?php bbp_forum_title( bbp_get_topic_forum_id( bbp_get_reply_topic_id() ) ); ?></a>
	</div><!-- .bbp-reply-title -->
	<div class="bbp-reply-title">
		<h3><?php esc_html_e( 'In reply to:', 'avanam' ); ?>
		<a class="bbp-topic-permalink" href="<?php bbp_topic_permalink( bbp_get_reply_topic_id() ); ?>"><?php bbp_topic_title( bbp_get_reply_topic_id() ); ?></a></h3>
	</div><!-- .bbp-reply-title -->
</div><!-- .bbp-reply-header -->

<div id="post-<?php bbp_reply_id(); ?>" <?php bbp_reply_class(); ?>>
	
	<div class="bbp-reply-author">

	<?php do_action( 'bbp_theme_before_reply_author_details' ); ?>

	<?php bbp_reply_author_avatar( bbp_get_reply_id(), 60 ); ?>

	<?php do_action( 'bbp_theme_after_reply_author_details' ); ?>

	</div><!-- .bbp-reply-author -->

	<div class="bbp-reply-content">
		<div class="bbp-head-area">

		<div class="reply-author-displayname"><?php bbp_reply_author_link( array( 'sep' => ' ', 'show_role' => false, 'type' => 'name' ) ); ?></div>

		<div class="bbp-meta">
			<span class="bbp-reply-post-date"><?php bbp_reply_post_date(); ?></span>

			<a href="<?php bbp_reply_url(); ?>" title="<?php bbp_reply_title(); ?>" class="bbp-reply-permalink">#<?php bbp_reply_id(); ?></a>

		</div><!-- .bbp-meta -->
		</div>

		<?php do_action( 'bbp_theme_before_reply_content' ); ?>

		<?php bbp_reply_content(); ?>

		<?php do_action( 'bbp_theme_after_reply_content' ); ?>

	</div><!-- .bbp-reply-content -->
</div><!-- #post-<?php bbp_reply_id(); ?> -->

