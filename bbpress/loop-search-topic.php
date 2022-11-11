<?php
/**
 * Search Loop - Single Topic
 *
 * @package bbPress
 * @subpackage Theme
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<div class="bbp-topic-header bbp-search-item-header">
<div class="bbp-topic-sub-title">
		<?php if ( function_exists( 'bbp_is_forum_group_forum' ) && bbp_is_forum_group_forum( bbp_get_topic_forum_id() ) ) : ?>

			<?php esc_html_e( 'In group forum:', 'avanam' ); ?>

		<?php else : ?>

			<?php esc_html_e( 'In forum:', 'avanam' ); ?>

		<?php endif; ?>
		<a href="<?php bbp_forum_permalink( bbp_get_topic_forum_id() ); ?>"><?php bbp_forum_title( bbp_get_topic_forum_id() ); ?></a>
	</div><!-- .bbp-topic-sub-title -->
	<div class="bbp-topic-title">
		<h3><?php esc_html_e( 'Topic:', 'avanam' ); ?>
		<a href="<?php bbp_topic_permalink(); ?>"><?php bbp_topic_title(); ?></a></h3>
	</div><!-- .bbp-topic-title -->

</div><!-- .bbp-topic-header -->

<div id="post-<?php bbp_topic_id(); ?>" <?php bbp_topic_class(); ?>>
	<div class="bbp-topic-author">

		<?php do_action( 'bbp_theme_before_topic_author_details' ); ?>

		<?php bbp_topic_author_avatar( bbp_get_topic_id(), 60 ); ?>

		<?php do_action( 'bbp_theme_after_topic_author_details' ); ?>

	</div><!-- .bbp-topic-author -->

	<div class="bbp-topic-content bbp-reply-content">

		<div class="bbp-head-area">

			<div class="reply-author-displayname"><?php bbp_topic_author_link( array( 'sep' => ' ', 'show_role' => false, 'type' => 'name' ) ); ?></div>

			<div class="bbp-meta">
				<span class="bbp-reply-post-date"><?php bbp_topic_post_date( bbp_get_topic_id() ); ?></span>

				<a href="<?php bbp_topic_permalink(); ?>" title="<?php bbp_topic_title(); ?>" class="bbp-reply-permalink">#<?php bbp_topic_id(); ?></a>

			</div><!-- .bbp-meta -->
		</div>

		<?php do_action( 'bbp_theme_before_topic_content' ); ?>

		<?php bbp_topic_content(); ?>

		<?php do_action( 'bbp_theme_after_topic_content' ); ?>

	</div><!-- .bbp-topic-content -->
</div><!-- #post-<?php bbp_topic_id(); ?> -->
