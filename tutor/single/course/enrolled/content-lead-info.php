<?php
/**
 * NOTE: This template is from the TutorLMS plugin is is overridden in Theme for better theme support of TutorLMS.
 * Template for displaying in content lead info
 *
 * @package TutorLMS/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post, $authordata;
$profile_url = tutor_utils()->profile_url( $authordata->ID );
?>
<div class="tutor-single-course-segment tutor-single-course-lead-info tutor-single-course-content-lead-info">

	<div class="tutor-single-course-meta tutor-lead-meta">
		<ul>
			<?php
			$course_categories = get_tutor_course_categories();
			if ( is_array( $course_categories ) && count( $course_categories ) ) {
				?>
				<li>
					<span><?php esc_html_e( 'Categories', 'avanam' ); ?></span>
					<?php
					foreach ( $course_categories as $course_category ) {
						$category_name = $course_category->name;
						$category_link = get_term_link( $course_category->term_id );
						echo '<a href="' . esc_url( $category_link ) . '">' . esc_html( $category_name ) . '</a>';
					}
					?>
				</li>
			<?php } ?>

			<?php
			$disable_course_duration = get_tutor_option( 'disable_course_duration' );
			$disable_total_enrolled  = get_tutor_option( 'disable_course_total_enrolled' );
			$disable_update_date     = get_tutor_option( 'disable_course_update_date' );
			$course_duration         = get_tutor_course_duration_context();

			if ( ! empty( $course_duration ) && ! $disable_course_duration ) {
				?>
				<li>
					<span><?php esc_html_e( 'Duration', 'avanam' ); ?></span>
					<?php echo esc_html( $course_duration ); ?>
				</li>
				<?php
			}

			if ( ! $disable_total_enrolled ) {
				?>
				<li>
					<span><?php esc_html_e( 'Total Enrolled', 'avanam' ); ?></span>
					<?php echo (int) tutor_utils()->count_enrolled_users_by_course(); ?>
				</li>
				<?php
			}

			if ( ! $disable_update_date ) {
				?>
				<li>
					<span><?php esc_html_e( 'Last Update', 'avanam' ); ?></span>
					<?php echo esc_html( get_the_modified_date() ); ?>
				</li>
			<?php } ?>
		</ul>
	</div>

	<div class="tutor-course-enrolled-info">
		<?php $count_completed_lesson = tutor_course_completing_progress_bar(); ?>
	</div>

	<?php do_action( 'tutor_course/single/lead_meta/after' ); ?>
	<?php do_action( 'tutor_course/single/excerpt/before' ); ?>

	<?php
	$excerpt       = tutor_get_the_excerpt();
	$disable_about = get_tutor_option( 'disable_course_about' );
	if ( ! empty( $excerpt ) && ! $disable_about ) {
		?>
		<div class="tutor-course-summery">
			<h4 class="tutor-segment-title"><?php esc_html_e( 'About Course', 'avanam' ); ?></h4>
			<?php echo wp_kses_post( $excerpt ); ?>
		</div>
		<?php
	}
	?>
	<?php do_action( 'tutor_course/single/excerpt/after' ); ?>
</div>
