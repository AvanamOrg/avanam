<?php
/**
 * Template for a lesson preview element
 *
 * @author      LifterLMS
 * @package     LifterLMS/Templates
 * @since       1.0.0
 * @version     4.4.0
 */

namespace Base;

use LLMS_Course;
use function llms_page_restricted;
use function llms_get_restriction_message;
use function llms_get_excerpt;

defined( 'ABSPATH' ) || exit;

$restrictions = llms_page_restricted( $lesson->get( 'id' ), get_current_user_id() ); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedVariableFound
$data_msg     = $restrictions['is_restricted'] ? ' data-tooltip-msg="' . esc_html( strip_tags( llms_get_restriction_message( $restrictions ) ) ) . '"' : ''; // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedVariableFound
?>

<div class="llms-lesson-preview<?php echo esc_attr( $lesson->get_preview_classes() ); ?>">
	<a class="llms-lesson-link<?php echo $restrictions['is_restricted'] ? ' llms-lesson-link-locked' : ''; ?>" href="<?php echo ( ! $restrictions['is_restricted'] ) ? esc_url( get_permalink( $lesson->get( 'id' ) ) ) : '#llms-lesson-locked'; ?>"<?php echo $data_msg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

		<?php if ( 'course' === get_post_type( get_the_ID() ) ) : ?>

			<?php if ( apply_filters( 'llms_display_outline_thumbnails', true ) ) : // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound ?>
				<?php if ( has_post_thumbnail( $lesson->get( 'id' ) ) ) : ?>
					<div class="llms-lesson-thumbnail-wrap">
						<div class="llms-lesson-thumbnail post-thumbnail base-thumbnail-ratio-<?php echo esc_attr( webapp()->option( 'course_syllabus_thumbs_ratio', '2-3' ) ); ?>">
							<div class="post-thumbnail-inner">
								<?php echo wp_kses_post( get_the_post_thumbnail( $lesson->get( 'id' ) ) ); ?>
							</div>
						</div><!-- .llms-lesson-thumbnail -->
					</div>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>
		<div class="course-information">
			<?php if ( 'course' === get_post_type( get_the_ID() ) ) : ?>	
				<aside class="llms-extra">
					<?php // translators: %1$d is lession order %2$d is the total number of lessions. ?>
					<span class="llms-lesson-counter"><?php printf( _x( '%1$d of %2$d', 'lesson order within section', 'avanam' ), isset( $order ) ? $order : $lesson->get_order(), $total_lessons ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php echo $lesson->get_preview_icon_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</aside>

			<?php endif; ?>

			<section class="llms-main">
				<?php if ( 'lesson' === get_post_type( get_the_ID() ) ) : ?>
					<h6 class="llms-pre-text"><?php echo $pre_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h6>
				<?php endif; ?>
				<h5 class="llms-h5 llms-lesson-title"><?php echo get_the_title( $lesson->get( 'id' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h5>
				<?php if ( apply_filters( 'llms_show_preview_excerpt', true ) && llms_get_excerpt( $lesson->get( 'id' ) ) ) : // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound ?>
					<div class="llms-lesson-excerpt"><?php echo llms_get_excerpt( $lesson->get( 'id' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<?php endif; ?>
			</section>

			<div class="clear"></div>
		</div>

		<?php if ( $restrictions['is_restricted'] ) : ?>
		<?php endif; ?>

	</a>
</div>
