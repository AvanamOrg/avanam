<?php
/**
 * NOTE: This template is from the TutorLMS plugin is is overridden in Theme for better theme support of TutorLMS.
 * Template for displaying above lead info
 *
 * @package TutorLMS/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post, $authordata;
$profile_url = tutor_utils()->profile_url( $authordata->ID );
?>

<div class="tutor-single-course-segment tutor-single-course-lead-info">

	<?php
	$disable = get_tutor_option( 'disable_course_review' );
	if ( ! $disable ) {
		?>
		<div class="tutor-leadinfo-top-meta">
			<span class="tutor-single-course-rating">
			<?php
			$course_rating = tutor_utils()->get_course_rating();
			tutor_utils()->star_rating_generator( $course_rating->rating_avg );
			?>
				<span class="tutor-single-rating-count">
					<?php
					echo esc_html( $course_rating->rating_avg );
					echo '<i>(' . esc_html( $course_rating->rating_count ) . ')</i>';
					?>
				</span>
			</span>
		</div>
	<?php } ?>

	<h1 class="tutor-course-header-h1"><?php the_title(); ?></h1>

	<?php do_action( 'tutor_course/single/title/after' ); ?>
	<?php do_action( 'tutor_course/single/lead_meta/before' ); ?>

	<div class="tutor-single-course-meta tutor-meta-top">
		<?php
			$disable_course_author = get_tutor_option( 'disable_course_author' );
			$disable_course_level  = get_tutor_option( 'disable_course_level' );
			$disable_course_share  = get_tutor_option( 'disable_course_share' );
		?>
		<ul>
			<?php if ( ! $disable_course_author ) { ?>
				<li class="tutor-single-course-author-meta">
					<div class="tutor-single-course-avatar">
						<a href="<?php echo esc_url( $profile_url ); ?>"> <?php echo wp_kses_post( tutor_utils()->get_tutor_avatar( $post->post_author ) ); ?></a>
					</div>
					<div class="tutor-single-course-author-name">
						<span><?php esc_html_e( 'by', 'avanam' ); ?></span>
						<a href="<?php echo esc_url( tutor_utils()->profile_url( $authordata->ID ) ); ?>"><?php echo wp_kses_post( get_the_author() ); ?></a>
					</div>
				</li>
			<?php } ?>

			<?php if ( ! $disable_course_level ) { ?>
				<li class="tutor-course-level">
					<span><?php esc_html_e( 'Course level:', 'avanam' ); ?></span>
					<?php echo wp_kses_post( get_tutor_course_level() ); ?>
				</li>
			<?php } ?>

			<?php if ( ! $disable_course_share ) { ?>
				<li class="tutor-social-share">
					<?php tutor_social_share(); ?>
				</li>
			<?php } ?>
		</ul>
	</div>
</div>
