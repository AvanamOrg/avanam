<?php
/**
 * Base\LifterLMS\Component class
 *
 * @package Base
 */

namespace Base\LifterLMS;

use Base\Component_Interface;
use function Base\webapp;
use function add_action;
use function add_filter;
use function add_theme_support;
use function have_posts;
use function the_post;
use function is_search;
use function get_template_part;
use function get_post_type;

/**
 * Class for adding LifterLMS plugin support.
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'lifterlms';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'after_setup_theme', array( $this, 'action_add_lifterlms_support' ) );
		add_filter( 'llms_get_theme_default_sidebar', array( $this, 'llms_sidebar_function' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'lifterlms_styles' ), 60 );
		// Remove Content Wrappers.
		remove_action( 'lifterlms_before_main_content', 'lifterlms_output_content_wrapper' );
		remove_action( 'lifterlms_after_main_content', 'lifterlms_output_content_wrapper_end' );
		// Remove Title.
		add_filter( 'lifterlms_show_page_title', '__return_false' );
		// Remove Sidebar.
		remove_action( 'lifterlms_sidebar', 'lifterlms_get_sidebar', 10 );
		// Add Content wrappers.
		add_action( 'lifterlms_before_main_content', array( $this, 'output_content_wrapper' ) );
		add_action( 'lifterlms_after_main_content', array( $this, 'output_main_wrapper_end' ), 8 );
		add_action( 'lifterlms_after_main_content', 'lifterlms_get_sidebar', 9 );
		add_action( 'lifterlms_after_main_content', array( $this, 'output_content_wrapper_end' ), 10 );

		add_filter( 'post_class', array( $this, 'set_lifter_entry_class' ), 10, 3 );
		add_filter( 'llms_get_loop_list_classes', array( $this, 'set_lifter_grid_class' ) );
		// Change Lifter Columns.
		add_filter( 'lifterlms_loop_columns', array( $this, 'set_lifter_columns' ) );

		// Remove normal archive Description.
		remove_action( 'lifterlms_archive_description', 'lifterlms_archive_description' );

		add_filter( 'llms_display_outline_thumbnails', array( $this, 'lifter_syllabus_thumbnails' ) );
		// Add div with class for Navigation Position.
		add_action( 'lifterlms_before_student_dashboard', array( $this, 'dashboard_wrapper_open' ), 5 );
		// Close added div with class for Navigation Position.
		add_action( 'lifterlms_after_student_dashboard', array( $this, 'dashboard_wrapper_close' ), 20 );
		// Could use to move the nav out of the header area, absolute position seems to work just as well though.
		// remove_action( 'lifterlms_student_dashboard_header', 'lifterlms_template_student_dashboard_navigation' );
		// add_action( 'lifterlms_before_student_dashboard_content', 'lifterlms_template_student_dashboard_navigation', 5 );
	}
	/**
	 * Adds opening div with class for Navigation Position.
	 */
	public function dashboard_wrapper_open() {
		echo '<div class="base-llms-dash-wrap base-llms-dash-nav-' . esc_attr( webapp()->option( 'llms_dashboard_navigation_layout' ) ) . '">';
	}
	/**
	 * Adds closing div with class for Navigation Position.
	 */
	public function dashboard_wrapper_close() {
		echo '</div>';
	}
	/**
	 * Adds thumbnail control for syllabus thumbnails
	 *
	 * @param boolean $show the whether to show the thumbnail.
	 */
	public function lifter_syllabus_thumbnails( $show ) {
		if ( webapp()->option( 'course_syllabus_thumbs' ) ) {
			$show = true;
		} else {
			$show = false;
		}
		return $show;
	}
	/**
	 * Changes the columns for lifter archives.
	 *
	 * @param array $columns the columns.
	 */
	public function set_lifter_columns( $columns ) {
		$dash_id = llms_get_page_id( 'myaccount' );
		if ( get_the_ID() === $dash_id ) {
			$columns = absint( webapp()->option( 'llms_dashboard_archive_columns' ) );
		} elseif ( is_archive() ) {
			if ( is_post_type_archive( 'course' ) || is_tax( 'course_cat' ) || is_tax( 'course_tag' ) || is_tax( 'course_track' ) ) {
				$columns = absint( webapp()->option( 'course_archive_columns' ) );
			} elseif ( is_post_type_archive( 'llms_membership' ) || is_tax( 'membership_cat' ) || is_tax( 'membership_tag' ) ) {
				$columns = absint( webapp()->option( 'llms_membership_archive_columns' ) );
			}
		}
		return $columns;
	}
	/**
	 * Adds grid class to archive items.
	 *
	 * @param array $classes the classes.
	 */
	public function set_lifter_grid_class( $classes ) {
		$classes[] = 'grid-cols';
		if ( in_array( 'cols-4', $classes, true ) ) {
			$classes[] = 'grid-sm-col-3';
			$classes[] = 'grid-lg-col-4';
			$classes   = array_diff( $classes, array( 'cols-4' ) );
		} elseif ( in_array( 'cols-2', $classes, true ) ) {
			$classes[] = 'grid-sm-col-2';
			$classes[] = 'grid-lg-col-2';
			$classes   = array_diff( $classes, array( 'cols-2' ) );
		} else {
			$classes[] = 'grid-sm-col-2';
			$classes[] = 'grid-lg-col-3';
			$classes   = array_diff( $classes, array( 'cols-3' ) );
		}
		return $classes;
	}
	/**
	 * Adds entry class to loop items.
	 *
	 * @param array  $classes the classes.
	 * @param string $class the class.
	 * @param int    $post_id the post id.
	 */
	public function set_lifter_entry_class( $classes, $class, $post_id ) {
		if ( in_array( 'llms-loop-item', $classes, true ) ) {
			$classes[] = 'entry';
			$classes[] = 'content-bg';
		}
		return $classes;
	}
	/**
	 * Adds theme output Wrapper.
	 */
	public function output_content_wrapper() {
		webapp()->print_styles( 'base-content' );
		/**
		 * Hook for Hero Section
		 */
		do_action( 'base_hero_header' );
		echo '<div id="primary" class="content-area"><div class="content-container site-container">';
		$this->output_main_wrapper();
		if ( is_archive() && webapp()->show_in_content_title() ) {
			get_template_part( 'template-parts/content/archive_header' );
		}
	}

	/**
	 * Adds theme main output Wrapper.
	 */
	public function output_main_wrapper() {
		echo '<main id="main" class="site-main" role="main">';
	}

	/**
	 * Adds theme main end output Wrapper.
	 */
	public function output_main_wrapper_end() {
		echo '</main>';
	}

	/**
	 * Adds theme end output Wrapper.
	 */
	public function output_content_wrapper_end() {
		echo '</div></div>';
	}
	/**
	 * Add some css styles for lifterLMS
	 */
	public function lifterlms_styles() {
		wp_enqueue_style( 'base-lifterlms', get_theme_file_uri( '/assets/css/lifterlms.min.css' ), array(), AVANAM_VERSION );
	}

	/**
	 * Adds theme support for the Lifter plugin.
	 *
	 * See: https://lifterlms.com/docs/lifterlms-sidebar-support
	 */
	public function action_add_lifterlms_support() {
		add_theme_support( 'lifterlms-sidebars' );
	}
	/**
	 * Display LifterLMS Course and Lesson sidebars
	 * on courses and lessons in place of the sidebar returned by
	 * this function
	 * @param string $id default sidebar id (an empty string).
	 * @return string
	 */
	public function llms_sidebar_function( $id ) {

		$sidebar_id = 'primary-sidebar';

		return $sidebar_id;

	}
}
