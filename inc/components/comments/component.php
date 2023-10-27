<?php
/**
 * Base\Comments\Component class
 *
 * @package Base
 */

namespace Base\Comments;

use Base\Component_Interface;
use Base\Templating_Component_Interface;
use function Base\webapp;
use function add_action;
use function apply_filters;
use function is_singular;
use function comments_open;
use function get_option;
use function wp_enqueue_script;
use function the_ID;
use function esc_attr;
use function wp_list_comments;
use function the_comments_navigation;
use function add_filter;
use function remove_filter;

/**
 * Class for managing comments UI.
 *
 * Exposes template tags:
 * * `webapp()->the_comments( array $args = [] )`
 *
 * @link https://wordpress.org/plugins/amp/
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'comments';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', array( $this, 'action_enqueue_comment_reply_script' ) );
		add_filter( 'comment_form_default_fields', array( $this, 'filter_default_fields_markup' ) );
		add_filter( 'comment_form_defaults', array( $this, 'filter_default_markup' ) );
	}

	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `webapp()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() : array {
		return array(
			'the_comments' => array( $this, 'the_comments' ),
		);
	}

	/**
	 * Enqueues the WordPress core 'comment-reply' script as necessary.
	 */
	public function action_enqueue_comment_reply_script() {

		// If the AMP plugin is active, return early.
		if ( webapp()->is_amp() ) {
			return;
		}

		// Enqueue comment script on singular post/page views only.
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

	/**
	 * Displays the list of comments for the current post.
	 *
	 * Internally this method calls `wp_list_comments()`. However, in addition to that it will render the wrapping
	 * element for the list, so that must not be added manually. The method will also take care of generating the
	 * necessary markup if amp-live-list should be used for comments.
	 *
	 * @param array $args Optional. Array of arguments. See `wp_list_comments()` documentation for a list of supported
	 *                    arguments.
	 */
	public function the_comments( array $args = array() ) {
		$args = array_merge(
			$args,
			array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 60,
			)
		);

		$amp_live_list = webapp()->using_amp_live_list_comments();

		if ( $amp_live_list ) {
			$comment_order     = get_option( 'comment_order' );
			$comments_per_page = get_option( 'page_comments' ) ? (int) get_option( 'comments_per_page' ) : 10000;
			$poll_inverval     = MINUTE_IN_SECONDS * 1000;

			?>
			<amp-live-list
				id="amp-live-comments-list-<?php the_ID(); ?>"
				<?php echo ( 'asc' === $comment_order ) ? ' sort="ascending" ' : ''; ?>
				data-poll-interval="<?php echo esc_attr( $poll_inverval ); ?>"
				data-max-items-per-page="<?php echo esc_attr( $comments_per_page ); ?>"
			>
			<?php

			add_filter( 'navigation_markup_template', array( $this, 'filter_add_amp_live_list_pagination_attribute' ) );
		}

		?>
		<ol class="comment-list"<?php echo $amp_live_list ? ' items' : ''; ?>>
			<?php wp_list_comments( $args ); ?>
		</ol><!-- .comment-list -->
		<?php

		the_comments_navigation();

		if ( $amp_live_list ) {
			remove_filter( 'navigation_markup_template', array( $this, 'filter_add_amp_live_list_pagination_attribute' ) );

			?>
				<div update>
					<button class="button" on="tap:amp-live-comments-list-<?php the_ID(); ?>.update"><?php esc_html_e( 'New comment(s)', 'avanam' ); ?></button>
				</div>
			</amp-live-list>
			<?php
		}
	}

	/**
	 * Adds a div wrapper around the author, email and url fields.
	 *
	 * @param array $fields the contact form fields.
	 * @return array Filtered markup.
	 */
	public function filter_default_fields_markup( $fields ) {
		$commenter = wp_get_current_commenter();
		$req       = get_option( 'require_name_email' );
		$aria_req  = ( $req ? " aria-required='true' required='required'" : '' );
		$label_req = ( $req ? ' <span class="required">*</span>' : '' );
		$show_web  = webapp()->option( 'comment_form_remove_web' );
		$fields['author'] = '<div class="comment-input-wrap ' . ( $show_web ? 'no-url-field' : 'has-url-field' ) . '"><p class="comment-form-author"><input aria-label="' . esc_attr__( 'Name', 'avanam' ) . '" id="author" name="author" type="text" placeholder="' . esc_attr__( 'John Doe', 'avanam' ) . '" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" maxlength="245"' . $aria_req . ' /><label class="float-label" for="author">' . esc_html__( 'Name', 'avanam' ) . $label_req . '</label></p>';

		$fields['email'] = '<p class="comment-form-email"><input aria-label="' . esc_attr__( 'Email', 'avanam' ) . '" id="email" name="email" type="email" placeholder="' . esc_attr__( 'john@example.com', 'avanam' ) . '" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" maxlength="100" aria-describedby="email-notes"' . $aria_req . ' /><label class="float-label" for="email">' . esc_html__( 'Email', 'avanam' ) . $label_req . '</label></p>';
		if ( $show_web ) {
			$fields['url'] = '</div>';
		} else {
			$fields['url'] = '<p class="comment-form-url"><input aria-label="' . esc_attr__( 'Website', 'avanam' ) . '" id="url" name="url" type="url" placeholder="' . esc_attr__( 'https://www.example.com', 'avanam' ) . '" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" maxlength="200" /><label class="float-label" for="url">' . esc_html__( 'Website', 'avanam' ) . '</label></p></div>';
		}

		$new_fields = array(
			'author' => $fields['author'],
			'email' => $fields['email'],
			'url' => $fields['url'],
			'cookies' => $fields['cookies'],
		);
		
		return apply_filters( 'base_comment_fields', $new_fields );
	}

	/**
	 * Adds a div wrapper around the author, email and url fields.
	 *
	 * @param array $args the contact form args.
	 * @return array Filtered markup.
	 */
	public function filter_default_markup( $args ) {
		$commenter = wp_get_current_commenter();

		$args['comment_field'] = '<p class="comment-form-comment comment-form-float-label"><textarea id="comment" name="comment" placeholder="' . esc_attr__( 'Leave a comment...', 'avanam' ) . '" cols="45" rows="8" maxlength="65525" aria-required="true" required="required"></textarea><label class="float-label" for="comment">' . esc_html__( 'Comment', 'avanam' ) . ' <span class="required">*</span></label></p>';

		return apply_filters( 'base_comment_args', $args );
	}

	/**
	 * Adds a pagination reference point attribute for amp-live-list when theme supports AMP.
	 *
	 * This is used by the navigation_markup_template filter in the comments template.
	 *
	 * @link https://www.ampproject.org/docs/reference/components/amp-live-list#pagination
	 *
	 * @param string $markup Navigation markup.
	 * @return string Filtered markup.
	 */
	public function filter_add_amp_live_list_pagination_attribute( string $markup ) : string {
		return preg_replace( '/(\s*<[a-z0-9_-]+)/i', '$1 pagination ', $markup, 1 );
	}
}
