<?php
/**
 * Template for the Course Syllabus Displayed on individual course pages
 *
 * @author      LifterLMS
 * @package     LifterLMS/Templates
 * @since       1.0.0
 * @version     4.4.0
 */

namespace Base;

use LLMS_Course;
use function llms_get_template;

defined( 'ABSPATH' ) || exit;
global $post;
$course   = new LLMS_Course( $post );
$sections = $course->get_sections();
?>

<div class="clear"></div>
<?php
$columns = webapp()->option( 'course_syllabus_columns' );
$columns_class = '';
$style = 'standard';
if ( '1' === $columns ) {
	$columns_class = 'grid-sm-col-1 grid-lg-col-1';
} elseif ( '2' === $columns ) {
	$columns_class = 'grid-sm-col-2 grid-lg-col-2';
} elseif ( '3' === $columns ) {
	$columns_class = 'grid-sm-col-2 grid-lg-col-3';
} elseif ( '4' === $columns ) {
	$columns_class = 'grid-ss-col-2 grid-sm-col-3 grid-lg-col-4';
}
if ( empty( $columns ) || '1' === $columns ) {
	$style = webapp()->option( 'course_syllabus_lesson_style' );
}
?>
<div class="llms-syllabus-wrapper <?php echo esc_attr( $columns_class ); ?> base-syllabus-style-<?php echo esc_attr( $style ); ?>">

	<?php if ( ! $sections ) : ?>

		<?php esc_html_e( 'This course does not have any sections.', 'avanam' ); ?>

	<?php else : ?>

		<?php foreach ( $sections as $section ) : ?>
			<div class="llms-course-wrap">
				<?php if ( apply_filters( 'llms_display_outline_section_titles', true ) ) : ?>
					<h3 class="llms-h3 llms-section-title"><?php echo esc_html( get_the_title( $section->get( 'id' ) ) ); ?></h3>
				<?php endif; ?>
				<?php $lesson_order = 0; ?>
				<?php $lessons = $section->get_lessons(); ?>
				<?php if ( $lessons ) : ?>

					<?php foreach ( $lessons as $lesson ) : ?>

						<?php
						llms_get_template(
							'course/lesson-preview.php',
							array(
								'lesson'        => $lesson,
								'total_lessons' => count( $lessons ),
								'order'         => ++$lesson_order,
							)
						);
						?>

					<?php endforeach; ?>

				<?php else : ?>

					<?php esc_html_e( 'This section does not have any lessons.', 'avanam' ); ?>

				<?php endif; ?>

			</div>

		<?php endforeach; ?>

	<?php endif; ?>

	<div class="clear"></div>

</div>
