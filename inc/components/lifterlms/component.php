<?php
/**
 * Base\LifterLMS\Component class
 *
 * @package Base
 */

namespace Base\LifterLMS;

use Base\Base_CSS;
use Base\Component_Interface;
use Base_Blocks_Frontend;
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
	 * Associative array of Google Fonts to load.
	 *
	 * Do not access this property directly, instead use the `get_google_fonts()` method.
	 *
	 * @var array
	 */
	protected static $google_fonts = array();

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
		add_filter( 'base_dynamic_css', array( $this, 'dynamic_css' ), 20 );
		add_action( 'wp_head', array( $this, 'frontend_gfonts' ), 80 );

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
	/**
	 * Enqueue Frontend Fonts
	 */
	public function frontend_gfonts() {
		if ( empty( self::$google_fonts ) ) {
			return;
		}
		if ( class_exists( 'Base_Blocks_Frontend' ) ) {
			$ktblocks_instance = Base_Blocks_Frontend::get_instance();
			foreach ( self::$google_fonts as $key => $font ) {
				if ( ! array_key_exists( $key, $ktblocks_instance::$gfonts ) ) {
					$add_font = array(
						'fontfamily'   => $font['fontfamily'],
						'fontvariants' => ( isset( $font['fontvariants'] ) && ! empty( $font['fontvariants'] ) && is_array( $font['fontvariants'] ) ? $font['fontvariants'] : array() ),
						'fontsubsets'  => ( isset( $font['fontsubsets'] ) && ! empty( $font['fontsubsets'] ) && is_array( $font['fontsubsets'] ) ? $font['fontsubsets'] : array() ),
					);
					$ktblocks_instance::$gfonts[ $key ] = $add_font;
				} else {
					foreach ( $font['fontvariants'] as $variant ) {
						if ( ! in_array( $variant, $ktblocks_instance::$gfonts[ $key ]['fontvariants'], true ) ) {
							array_push( $ktblocks_instance::$gfonts[ $key ]['fontvariants'], $variant );
						}
					}
				}
			}
		} else {
			add_filter( 'base_theme_google_fonts_array', array( $this, 'filter_in_fonts' ) );
		}
	}
	/**
	 * Filters in pro fronts for output with free.
	 *
	 * @param array $font_array any custom css.
	 * @return array
	 */
	public function filter_in_fonts( $font_array ) {
		// Enqueue Google Fonts.
		foreach ( self::$google_fonts as $key => $font ) {
			if ( ! array_key_exists( $key, $font_array ) ) {
				$add_font = array(
					'fontfamily'   => $font['fontfamily'],
					'fontvariants' => ( isset( $font['fontvariants'] ) && ! empty( $font['fontvariants'] ) && is_array( $font['fontvariants'] ) ? $font['fontvariants'] : array() ),
					'fontsubsets'  => ( isset( $font['fontsubsets'] ) && ! empty( $font['fontsubsets'] ) && is_array( $font['fontsubsets'] ) ? $font['fontsubsets'] : array() ),
				);
				$font_array[ $key ] = $add_font;
			} else {
				foreach ( $font['fontvariants'] as $variant ) {
					if ( ! in_array( $variant, $font_array[ $key ]['fontvariants'], true ) ) {
						array_push( $font_array[ $key ]['fontvariants'], $variant );
					}
				}
			}
		}
		return $font_array;
	}
	/**
	 * Generates the dynamic css based on customizer options.
	 *
	 * @param string $css any custom css.
	 * @return string
	 */
	public function dynamic_css( $css ) {
		$generated_css = $this->generate_lifter_css();
		if ( ! empty( $generated_css ) ) {
			$css .= "\n/* Base Lifter CSS */\n" . $generated_css;
		}
		return $css;
	}
	/**
	 * Generates the dynamic css based on page options.
	 *
	 * @return string
	 */
	public function generate_lifter_css() {
		$css                    = new Base_CSS();
		$media_query            = array();
		$media_query['mobile']  = apply_filters( 'base_mobile_media_query', '(max-width: 767px)' );
		$media_query['tablet']  = apply_filters( 'base_tablet_media_query', '(max-width: 1024px)' );
		$media_query['desktop'] = apply_filters( 'base_desktop_media_query', '(min-width: 1025px)' );
		// Lifter CSS.
		if ( class_exists( 'LifterLMS' ) ) {
			// Course Backgrounds.
			$css->set_selector( 'body.single-course' );
			$css->render_background( webapp()->sub_option( 'course_background', 'desktop' ), $css );
			$css->set_selector( 'body.single-course .content-bg, body.content-style-unboxed.single-course .site' );
			$css->render_background( webapp()->sub_option( 'course_content_background', 'desktop' ), $css );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( 'body.single-course' );
			$css->render_background( webapp()->sub_option( 'course_background', 'tablet' ), $css );
			$css->set_selector( 'body.single-course .content-bg, body.content-style-unboxed.single-course .site' );
			$css->render_background( webapp()->sub_option( 'course_content_background', 'tablet' ), $css );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( 'body.single-course' );
			$css->render_background( webapp()->sub_option( 'course_background', 'mobile' ), $css );
			$css->set_selector( 'body.single-course .content-bg, body.content-style-unboxed.single-course .site' );
			$css->render_background( webapp()->sub_option( 'course_content_background', 'mobile' ), $css );
			$css->stop_media_query();
			// Lesson Backgrounds.
			$css->set_selector( 'body.single-lesson' );
			$css->render_background( webapp()->sub_option( 'lesson_background', 'desktop' ), $css );
			$css->set_selector( 'body.single-lesson .content-bg, body.content-style-unboxed.single-lesson .site' );
			$css->render_background( webapp()->sub_option( 'lesson_content_background', 'desktop' ), $css );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( 'body.single-lesson' );
			$css->render_background( webapp()->sub_option( 'lesson_background', 'tablet' ), $css );
			$css->set_selector( 'body.single-lesson .content-bg, body.content-style-unboxed.single-lesson .site' );
			$css->render_background( webapp()->sub_option( 'lesson_content_background', 'tablet' ), $css );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( 'body.single-lesson' );
			$css->render_background( webapp()->sub_option( 'lesson_background', 'mobile' ), $css );
			$css->set_selector( 'body.single-lesson .content-bg, body.content-style-unboxed.single-lesson .site' );
			$css->render_background( webapp()->sub_option( 'lesson_content_background', 'mobile' ), $css );
			$css->stop_media_query();
			// Course Archive Backgrounds.
			$css->set_selector( 'body.archive.tax-course_cat, body.post-type-archive-course' );
			$css->render_background( webapp()->sub_option( 'course_archive_background', 'desktop' ), $css );
			$css->set_selector( 'body.archive.tax-course_cat .content-bg, body.content-style-unboxed.archive.tax-course_cat .site, body.post-type-archive-course .content-bg, body.content-style-unboxed.archive.post-type-archive-course .site' );
			$css->render_background( webapp()->sub_option( 'course_archive_content_background', 'desktop' ), $css );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( 'body.archive.tax-course_cat, body.post-type-archive-course' );
			$css->render_background( webapp()->sub_option( 'course_archive_background', 'tablet' ), $css );
			$css->set_selector( 'body.archive.tax-course_cat .content-bg, body.content-style-unboxed.archive.tax-course_cat .site, body.post-type-archive-course .content-bg, body.content-style-unboxed.archive.post-type-archive-course .site' );
			$css->render_background( webapp()->sub_option( 'course_archive_content_background', 'tablet' ), $css );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( 'body.archive.tax-course_cat, body.post-type-archive-course' );
			$css->render_background( webapp()->sub_option( 'course_archive_background', 'mobile' ), $css );
			$css->set_selector( 'body.archive.tax-course_cat .content-bg, body.content-style-unboxed.archive.tax-course_cat .site, body.post-type-archive-course .content-bg, body.content-style-unboxed.archive.post-type-archive-course .site' );
			$css->render_background( webapp()->sub_option( 'course_archive_content_background', 'mobile' ), $css );
			$css->stop_media_query();
			// Membership Archive Backgrounds.
			$css->set_selector( 'body.archive.tax-membership_cat, body.post-type-archive-llms_membership' );
			$css->render_background( webapp()->sub_option( 'llms_membership_archive_background', 'desktop' ), $css );
			$css->set_selector( 'body.archive.tax-membership_cat .content-bg, body.content-style-unboxed.archive.tax-membership_cat .site, body.post-type-archive-llms_membership .content-bg, body.content-style-unboxed.archive.post-type-archive-llms_membership .site' );
			$css->render_background( webapp()->sub_option( 'llms_membership_archive_content_background', 'desktop' ), $css );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( 'body.archive.tax-membership_cat, body.post-type-archive-llms_membership' );
			$css->render_background( webapp()->sub_option( 'llms_membership_archive_background', 'tablet' ), $css );
			$css->set_selector( 'body.archive.tax-membership_cat .content-bg, body.content-style-unboxed.archive.tax-membership_cat .site, body.post-type-archive-llms_membership .content-bg, body.content-style-unboxed.archive.post-type-archive-llms_membership .site' );
			$css->render_background( webapp()->sub_option( 'llms_membership_archive_content_background', 'tablet' ), $css );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( 'body.archive.tax-membership_cat, body.post-type-archive-llms_membership' );
			$css->render_background( webapp()->sub_option( 'llms_membership_archive_background', 'mobile' ), $css );
			$css->set_selector( 'body.archive.tax-membership_cat .content-bg, body.content-style-unboxed.archive.tax-membership_cat .site, body.post-type-archive-llms_membership .content-bg, body.content-style-unboxed.archive.post-type-archive-llms_membership .site' );
			$css->render_background( webapp()->sub_option( 'llms_membership_archive_content_background', 'mobile' ), $css );
			$css->stop_media_query();
			// Course Title.
			$css->set_selector( '.wp-site-blocks .course-title h1' );
			$css->render_font( webapp()->option( 'course_title_font' ), $css, 'heading' );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.wp-site-blocks .course-title h1' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'course_title_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'course_title_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'course_title_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.wp-site-blocks .course-title h1' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'course_title_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'course_title_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'course_title_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Course Title Breadcrumbs.
			$css->set_selector( '.course-title .base-breadcrumbs' );
			$css->render_font( webapp()->option( 'course_title_breadcrumb_font' ), $css );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'course_title_breadcrumb_color', 'color' ) ) );
			$css->set_selector( '.course-title .base-breadcrumbs a:hover' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'course_title_breadcrumb_color', 'hover' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.course-title .base-breadcrumbs' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'course_title_breadcrumb_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'course_title_breadcrumb_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'course_title_breadcrumb_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.course-title .base-breadcrumbs' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'course_title_breadcrumb_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'course_title_breadcrumb_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'course_title_breadcrumb_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Above Course Title.
			$css->set_selector( '.course-hero-section .entry-hero-container-inner' );
			$css->render_background( webapp()->sub_option( 'course_title_background', 'desktop' ), $css );
			$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'course_title_top_border', 'desktop' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'course_title_bottom_border', 'desktop' ) ) );
			$css->set_selector( '.entry-hero.course-hero-section .entry-header' );
			$css->add_property( 'min-height', $css->render_range( webapp()->option( 'course_title_height' ), 'desktop' ) );
			$css->set_selector( '.course-hero-section .hero-section-overlay' );
			$css->add_property( 'background', $css->render_color_or_gradient( webapp()->sub_option( 'course_title_overlay_color', 'color' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.course-hero-section .entry-hero-container-inner' );
			$css->render_background( webapp()->sub_option( 'course_title_background', 'tablet' ), $css );
			$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'course_title_top_border', 'tablet' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'course_title_bottom_border', 'tablet' ) ) );
			$css->set_selector( '.entry-hero.course-hero-section .entry-header' );
			$css->add_property( 'min-height', $css->render_range( webapp()->option( 'course_title_height' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.course-hero-section .entry-hero-container-inner' );
			$css->render_background( webapp()->sub_option( 'course_title_background', 'mobile' ), $css );
			$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'course_title_top_border', 'mobile' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'course_title_bottom_border', 'mobile' ) ) );
			$css->set_selector( '.entry-hero.course-hero-section .entry-header' );
			$css->add_property( 'min-height', $css->render_range( webapp()->option( 'course_title_height' ), 'mobile' ) );
			$css->stop_media_query();
			// Lesson Title.
			$css->set_selector( '.wp-site-blocks .lesson-title h1' );
			$css->render_font( webapp()->option( 'lesson_title_font' ), $css, 'heading' );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.wp-site-blocks .lesson-title h1' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'lesson_title_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'lesson_title_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'lesson_title_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.wp-site-blocks .lesson-title h1' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'lesson_title_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'lesson_title_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'lesson_title_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Lesson Title Breadcrumbs.
			$css->set_selector( '.lesson-title .base-breadcrumbs' );
			$css->render_font( webapp()->option( 'lesson_title_breadcrumb_font' ), $css );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'lesson_title_breadcrumb_color', 'color' ) ) );
			$css->set_selector( '.lesson-title .base-breadcrumbs a:hover' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'lesson_title_breadcrumb_color', 'hover' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.lesson-title .base-breadcrumbs' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'lesson_title_breadcrumb_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'lesson_title_breadcrumb_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'lesson_title_breadcrumb_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.lesson-title .base-breadcrumbs' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'lesson_title_breadcrumb_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'lesson_title_breadcrumb_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'lesson_title_breadcrumb_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Above Lesson Title.
			$css->set_selector( '.lesson-hero-section .entry-hero-container-inner' );
			$css->render_background( webapp()->sub_option( 'lesson_title_background', 'desktop' ), $css );
			$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'lesson_title_top_border', 'desktop' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'lesson_title_bottom_border', 'desktop' ) ) );
			$css->set_selector( '.entry-hero.lesson-hero-section .entry-header' );
			$css->add_property( 'min-height', $css->render_range( webapp()->option( 'lesson_title_height' ), 'desktop' ) );
			$css->set_selector( '.lesson-hero-section .hero-section-overlay' );
			$css->add_property( 'background', $css->render_color_or_gradient( webapp()->sub_option( 'lesson_title_overlay_color', 'color' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.lesson-hero-section .entry-hero-container-inner' );
			$css->render_background( webapp()->sub_option( 'lesson_title_background', 'tablet' ), $css );
			$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'lesson_title_top_border', 'tablet' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'lesson_title_bottom_border', 'tablet' ) ) );
			$css->set_selector( '.entry-hero.lesson-hero-section .entry-header' );
			$css->add_property( 'min-height', $css->render_range( webapp()->option( 'lesson_title_height' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.lesson-hero-section .entry-hero-container-inner' );
			$css->render_background( webapp()->sub_option( 'lesson_title_background', 'mobile' ), $css );
			$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'lesson_title_top_border', 'mobile' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'lesson_title_bottom_border', 'mobile' ) ) );
			$css->set_selector( '.entry-hero.lesson-hero-section .entry-header' );
			$css->add_property( 'min-height', $css->render_range( webapp()->option( 'lesson_title_height' ), 'mobile' ) );
			$css->stop_media_query();
			// Course Archive Title.
			$css->set_selector( '.course-archive-hero-section .entry-hero-container-inner' );
			$css->render_background( webapp()->sub_option( 'course_archive_title_background', 'desktop' ), $css );
			$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'course_archive_title_top_border', 'desktop' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'course_archive_title_bottom_border', 'desktop' ) ) );
			$css->set_selector( '.entry-hero.course-archive-hero-section .entry-header' );
			$css->add_property( 'min-height', $css->render_range( webapp()->option( 'course_archive_title_height' ), 'desktop' ) );
			$css->set_selector( '.course-archive-hero-section .hero-section-overlay' );
			$css->add_property( 'background', $css->render_color_or_gradient( webapp()->sub_option( 'course_archive_title_overlay_color', 'color' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.course-archive-hero-section .entry-hero-container-inner' );
			$css->render_background( webapp()->sub_option( 'course_archive_title_background', 'tablet' ), $css );
			$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'course_archive_title_top_border', 'tablet' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'course_archive_title_bottom_border', 'tablet' ) ) );
			$css->set_selector( '.entry-hero.course-archive-hero-section .entry-header' );
			$css->add_property( 'min-height', $css->render_range( webapp()->option( 'course_archive_title_height' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.course-archive-hero-section .entry-hero-container-inner' );
			$css->render_background( webapp()->sub_option( 'course_archive_title_background', 'mobile' ), $css );
			$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'course_archive_title_top_border', 'mobile' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'course_archive_title_bottom_border', 'mobile' ) ) );
			$css->set_selector( '.entry-hero.course-archive-hero-section .entry-header' );
			$css->add_property( 'min-height', $css->render_range( webapp()->option( 'course_archive_title_height' ), 'mobile' ) );
			$css->stop_media_query();
			$css->set_selector( '.wp-site-blocks .course-archive-title h1' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'course_archive_title_color', 'color' ) ) );
			$css->set_selector( '.course-archive-title .base-breadcrumbs' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'course_archive_title_breadcrumb_color', 'color' ) ) );
			$css->set_selector( '.course-archive-title .base-breadcrumbs a:hover' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'course_archive_title_breadcrumb_color', 'hover' ) ) );
			$css->set_selector( '.course-archive-title .archive-description' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'course_archive_title_description_color', 'color' ) ) );
			$css->set_selector( '.course-archive-title .archive-description a:hover' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'course_archive_title_description_color', 'hover' ) ) );
			// Membership Archive Title.
			$css->set_selector( '.llms_membership-archive-hero-section .entry-hero-container-inner' );
			$css->render_background( webapp()->sub_option( 'llms_membership_archive_title_background', 'desktop' ), $css );
			$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'llms_membership_archive_title_top_border', 'desktop' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'llms_membership_archive_title_bottom_border', 'desktop' ) ) );
			$css->set_selector( '.entry-hero.llms_membership-archive-hero-section .entry-header' );
			$css->add_property( 'min-height', $css->render_range( webapp()->option( 'llms_membership_archive_title_height' ), 'desktop' ) );
			$css->set_selector( '.llms_membership-archive-hero-section .hero-section-overlay' );
			$css->add_property( 'background', $css->render_color_or_gradient( webapp()->sub_option( 'llms_membership_archive_title_overlay_color', 'color' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.llms_membership-archive-hero-section .entry-hero-container-inner' );
			$css->render_background( webapp()->sub_option( 'llms_membership_archive_title_background', 'tablet' ), $css );
			$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'llms_membership_archive_title_top_border', 'tablet' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'llms_membership_archive_title_bottom_border', 'tablet' ) ) );
			$css->set_selector( '.entry-hero.llms_membership-archive-hero-section .entry-header' );
			$css->add_property( 'min-height', $css->render_range( webapp()->option( 'llms_membership_archive_title_height' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.llms_membership-archive-hero-section .entry-hero-container-inner' );
			$css->render_background( webapp()->sub_option( 'llms_membership_archive_title_background', 'mobile' ), $css );
			$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'llms_membership_archive_title_top_border', 'mobile' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'llms_membership_archive_title_bottom_border', 'mobile' ) ) );
			$css->set_selector( '.entry-hero.llms_membership-archive-hero-section .entry-header' );
			$css->add_property( 'min-height', $css->render_range( webapp()->option( 'llms_membership_archive_title_height' ), 'mobile' ) );
			$css->stop_media_query();
			$css->set_selector( '.wp-site-blocks .llms_membership-archive-title h1' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'llms_membership_archive_title_color', 'color' ) ) );
			$css->set_selector( '.llms_membership-archive-title .base-breadcrumbs' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'llms_membership_archive_title_breadcrumb_color', 'color' ) ) );
			$css->set_selector( '.llms_membership-archive-title .base-breadcrumbs a:hover' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'llms_membership_archive_title_breadcrumb_color', 'hover' ) ) );
			$css->set_selector( '.llms_membership-archive-title .archive-description' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'llms_membership_archive_title_description_color', 'color' ) ) );
			$css->set_selector( '.llms_membership-archive-title .archive-description a:hover' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'llms_membership_archive_title_description_color', 'hover' ) ) );
		}
		self::$google_fonts = $css->fonts_output();
		return $css->css_output();
	}
}
