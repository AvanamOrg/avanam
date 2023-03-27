<?php
/**
 * Base\TutorLMS\Component class
 *
 * @package Base
 */

namespace Base\TutorLMS;

use Base\Base_CSS;
use Base\Component_Interface;
use Base_Blocks_Frontend;
use function Base\webapp;
use function add_action;
use function add_filter;
use function have_posts;
use function the_post;
use function is_search;
use function get_template_part;
use function get_post_type;

/**
 * Class for adding TutorLMS plugin support.
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
		return 'tutorlms';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', array( $this, 'tutorlms_styles' ), 60 );
		add_filter( 'base_theme_options_defaults', array( $this, 'add_option_defaults' ) );
		add_filter( 'base_dynamic_css', array( $this, 'dynamic_css' ), 20 );
		add_action( 'wp_head', array( $this, 'frontend_gfonts' ), 80 );
		add_filter( 'body_class', array( $this, 'filter_body_classes' ) );
		// Courses.
		// Outer Wrap.
		add_action( 'tutor_course/single/before/wrap', array( $this, 'output_course_content_wrapper' ) );
		add_action( 'tutor_course/single/after/wrap', array( $this, 'output_course_content_wrapper_end' ) );
		add_action( 'tutor_course/single/enrolled/before/wrap', array( $this, 'output_course_content_wrapper_enrolled' ) );
		add_action( 'tutor_course/single/enrolled/after/wrap', array( $this, 'output_course_content_wrapper_end' ) );
		// Inner.
		add_action( 'tutor_course/single/before/inner-wrap', array( $this, 'output_inner_content_wrapper' ) );
		add_action( 'tutor_course/single/after/inner-wrap', array( $this, 'output_inner_content_wrapper_end' ) );
		add_action( 'tutor_course/single/enrolled/before/inner-wrap', array( $this, 'output_inner_content_wrapper_enrolled' ) );
		add_action( 'tutor_course/single/enrolled/after/inner-wrap', array( $this, 'output_inner_content_wrapper_end' ), 80 );
		// Pages.
		add_action( 'tutor_dashboard/before/wrap', array( $this, 'output_content_wrapper' ) );
		add_action( 'tutor_dashboard/after/wrap', array( $this, 'output_content_wrapper_end' ) );
		add_action( 'tutor_student/before/wrap', array( $this, 'output_content_wrapper' ) );
		add_action( 'tutor_student/after/wrap', array( $this, 'output_content_wrapper_end' ) );
		add_action( 'tutor_course/archive/before/wrap', array( $this, 'output_content_wrapper' ) );
		add_action( 'tutor_course/archive/after/wrap', array( $this, 'output_content_wrapper_end' ) );
	}
	/**
	 * Outputs the above header tutor lead.
	 */
	public function header_lead_enrolled() {
		get_template_part( 'tutor/single/course/enrolled/above-lead-info' );
	}
	/**
	 * Outputs the tutor lead.
	 */
	public function header_lead() {
		if ( defined( 'TUTOR_VERSION' ) && version_compare( TUTOR_VERSION, '2.0.0' ) >= 0 ) {
			$is_enrolled = tutor_utils()->is_enrolled();
			( isset( $is_enrolled ) && $is_enrolled ) ? tutor_course_enrolled_lead_info() : tutor_course_lead_info();
			// remove normal Lead template.
			add_filter( 'should_tutor_load_template', array( $this, 'remove_lead_template' ), 10, 3 );
		} else {
			get_template_part( 'tutor/single/course/lead-info' );
		}
	}

	/**
	 * Removes the content.
	 *
	 * @param bool   $load if the template should load.
	 * @param string $template the name of the template.
	 * @param array  $variables for the template.
	 */
	public function remove_lead_template( $load, $template, $variables ) {
		if ( 'single.course.enrolled.lead-info' === $template || 'single.course.lead-info' === $template ) {
			$load = false;
		}
		return $load;
	}
	/**
	 * Adds theme output Inner Wrapper.
	 */
	public function output_inner_content_wrapper_enrolled() {
		echo '<div class="entry content-bg single-entry"><div class="entry-content-wrap">';
		if ( 'above' === webapp()->option( 'courses_title_layout' ) ) {
			// remove normal Lead template.
			add_filter( 'should_tutor_load_template', array( $this, 'remove_lead_template' ), 10, 3 );
			// Add content lead template.
			if ( defined( 'TUTOR_VERSION' ) && ! ( version_compare( TUTOR_VERSION, '2.0.0' ) >= 0 ) ) {
				get_template_part( 'tutor/single/course/enrolled/content-lead-info' );
			}
		}
	}
	/**
	 * Adds theme output Inner Wrapper.
	 */
	public function output_inner_content_wrapper() {
		echo '<div class="entry content-bg single-entry"><div class="entry-content-wrap">';
		if ( 'above' === webapp()->option( 'courses_title_layout' ) ) {
			// remove normal Lead template.
			add_filter( 'should_tutor_load_template', array( $this, 'remove_lead_template' ), 10, 3 );
			// Add content lead template.
			if ( defined( 'TUTOR_VERSION' ) && ! ( version_compare( TUTOR_VERSION, '2.0.0' ) >= 0 ) ) {
				get_template_part( 'tutor/single/course/content-lead-info' );
			}
		}
	}
	/**
	 * Adds theme output Inner Wrapper.
	 */
	public function output_inner_content_wrapper_end() {
		echo '</div></div>';
	}
	/**
	 * Adds theme output Wrapper enrolled.
	 */
	public function output_course_content_wrapper_enrolled() {
		webapp()->print_styles( 'base-content' );
		remove_action( 'base_entry_hero', 'Base\base_entry_header', 10, 2 );
		add_action( 'base_entry_hero', array( $this, 'header_lead_enrolled' ), 10, 2 );
		/**
		 * Hook for Hero Section
		 */
		do_action( 'base_hero_header' );
		echo '<div id="primary" class="content-area"><div class="content-container site-container"><main id="main" class="site-main" role="main">';
	}
	/**
	 * Adds theme output Wrapper.
	 */
	public function output_course_content_wrapper() {
		webapp()->print_styles( 'base-content' );
		remove_action( 'base_entry_hero', 'Base\base_entry_header', 10, 2 );
		add_action( 'base_entry_hero', array( $this, 'header_lead' ), 10, 2 );
		/**
		 * Hook for Hero Section
		 */
		do_action( 'base_hero_header' );
		echo '<div id="primary" class="content-area"><div class="content-container site-container"><main id="main" class="site-main" role="main">';
	}
	/**
	 * Adds theme end output Wrapper.
	 */
	public function output_course_content_wrapper_end() {
		echo '</main></div></div>';
	}
	/**
	 * Adds theme output Wrapper.
	 */
	public function output_content_wrapper() {
		webapp()->print_styles( 'base-content' );
		if ( is_archive() ) {
			/**
			 * Hook for Hero Section
			 */
			do_action( 'base_hero_header' );
		}
		echo '<div id="primary" class="content-area"><div class="content-container site-container"><main id="main" class="site-main" role="main">';
		if ( is_archive() && webapp()->show_in_content_title() ) {
			get_template_part( 'template-parts/content/archive_header' );
		}
	}
	/**
	 * Adds theme end output Wrapper.
	 */
	public function output_content_wrapper_end() {
		echo '</main>';
		if ( is_archive() ) {
			get_sidebar();
		}
		echo '</div></div>';
	}
	/**
	 * Add some css styles for tutorlms
	 */
	public function tutorlms_styles() {
		wp_enqueue_style( 'base-tutorlms', get_theme_file_uri( '/assets/css/tutorlms.min.css' ), array(), AVANAM_VERSION );
	}
	/**
	 * Add Defaults
	 *
	 * @access public
	 * @param array $defaults registered option defaults with base theme.
	 * @return array
	 */
	public function add_option_defaults( $defaults ) {
		// Tutor.
		$tutor_addons = array(
			'courses_title_layout'       => 'normal',
			'courses_title_inner_layout' => 'standard',
			'courses_title_align'        => 'left',
			'courses_enroll_overlay'     => true,
			'courses_title_height'       => array(
				'size' => array(
					'mobile'  => '',
					'tablet'  => '',
					'desktop' => 375,
				),
				'unit' => array(
					'mobile'  => 'px',
					'tablet'  => 'px',
					'desktop' => 'px',
				),
			),
			'courses_title_font' => array(
				'size' => array(
					'desktop' => '',
				),
				'lineHeight' => array(
					'desktop' => '',
				),
				'family'  => 'inherit',
				'google'  => false,
				'weight'  => '',
				'variant' => '',
				'color'   => '',
			),
			'courses_layout'           => 'right',
			'courses_content_style'    => 'boxed',
			'courses_vertical_padding' => 'show',
			'courses_archive_title'            => true,
			'courses_archive_title_layout'     => 'above',
			'courses_archive_layout'           => 'normal',
			'courses_archive_sidebar_id'       => 'sidebar-primary',
			'courses_archive_content_style'    => 'unboxed',
			'courses_archive_vertical_padding' => 'show',
			'courses_archive_title_inner_layout' => 'standard',
			'courses_archive_title_elements'        => array( 'breadcrumb', 'title' ),
			'courses_archive_title_element_title'   => array(
				'enabled' => true,
			),
			'courses_archive_title_element_breadcrumb' => array(
				'enabled' => false,
				'show_title' => true,
			),
			// 'courses_title_elements'      => array( 'breadcrumb', 'reviews', 'title', 'meta' ),
			// 'courses_title_element_title' => array(
			// 	'enabled' => true,
			// ),
			// 'courses_title_element_breadcrumb' => array(
			// 	'enabled' => false,
			// ),
			// 'courses_title_element_reviews' => array(
			// 	'enabled' => true,
			// ),
			// 'courses_title_element_meta' => array(
			// 	'id'                => 'meta',
			// 	'enabled'           => false,
			// 	'author'            => true,
			// 	'authorImage'       => true,
			// 	'authorEnableLabel' => true,
			// 	'authorLabel'       => '',
			// 	'courseLevel'       => true,
			// 	'courseShare'       => false,
			// ),
			// 'header_html2_typography' => array(
			// 	'size' => array(
			// 		'desktop' => '',
			// 	),
			// 	'lineHeight' => array(
			// 		'desktop' => '',
			// 	),
			// 	'family'  => 'inherit',
			// 	'google'  => false,
			// 	'weight'  => '',
			// 	'variant' => '',
			// 	'color'   => '',
			// ),
		);
		$defaults = array_merge(
			$defaults,
			$tutor_addons
		);
		return $defaults;
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
		$generated_css = $this->generate_tutor_css();
		if ( ! empty( $generated_css ) ) {
			$css .= "\n/* Base Tutor CSS */\n" . $generated_css;
		}
		return $css;
	}
	/**
	 * Generates the dynamic css based on page options.
	 *
	 * @return string
	 */
	public function generate_tutor_css() {
		$css                    = new Base_CSS();
		$media_query            = array();
		$media_query['mobile']  = apply_filters( 'base_mobile_media_query', '(max-width: 767px)' );
		$media_query['tablet']  = apply_filters( 'base_tablet_media_query', '(max-width: 1024px)' );
		$media_query['desktop'] = apply_filters( 'base_desktop_media_query', '(min-width: 1025px)' );
		// Above Course Title.
		$css->set_selector( '.courses-hero-section .entry-hero-container-inner' );
		$css->render_background( webapp()->sub_option( 'courses_title_background', 'desktop' ), $css );
		$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'courses_title_top_border', 'desktop' ) ) );
		$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'courses_title_bottom_border', 'desktop' ) ) );
		$css->set_selector( '.entry-hero.courses-hero-section .entry-header' );
		$css->add_property( 'min-height', $css->render_range( webapp()->option( 'courses_title_height' ), 'desktop' ) );
		$css->set_selector( '.courses-hero-section .hero-section-overlay' );
		$css->add_property( 'background', $css->render_color( webapp()->sub_option( 'courses_title_overlay_color', 'color' ) ) );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '.courses-hero-section .entry-hero-container-inner' );
		$css->render_background( webapp()->sub_option( 'courses_title_background', 'tablet' ), $css );
		$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'courses_title_top_border', 'tablet' ) ) );
		$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'courses_title_bottom_border', 'tablet' ) ) );
		$css->set_selector( '.entry-hero.courses-hero-section .entry-header' );
		$css->add_property( 'min-height', $css->render_range( webapp()->option( 'courses_title_height' ), 'tablet' ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '.courses-hero-section .entry-hero-container-inner' );
		$css->render_background( webapp()->sub_option( 'courses_title_background', 'mobile' ), $css );
		$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'courses_title_top_border', 'mobile' ) ) );
		$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'courses_title_bottom_border', 'mobile' ) ) );
		$css->set_selector( '.entry-hero.courses-hero-section .entry-header' );
		$css->add_property( 'min-height', $css->render_range( webapp()->option( 'courses_title_height' ), 'mobile' ) );
		$css->stop_media_query();
		// Course Title.
		$css->set_selector( '.tutor-single-course-lead-info h1.tutor-course-header-h1, .tutor-course-details-title .tutor-fs-4' );
		$css->render_font( webapp()->option( 'courses_title_font' ), $css, 'heading' );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '.tutor-single-course-lead-info h1.tutor-course-header-h1, .tutor-course-details-title .tutor-fs-4' );
		$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'courses_title_font' ), 'tablet' ) );
		$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'courses_title_font' ), 'tablet' ) );
		$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'courses_title_font' ), 'tablet' ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '.tutor-single-course-lead-info h1.tutor-course-header-h1, .tutor-course-details-title .tutor-fs-4' );
		$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'courses_title_font' ), 'mobile' ) );
		$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'courses_title_font' ), 'mobile' ) );
		$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'courses_title_font' ), 'mobile' ) );
		$css->stop_media_query();
		// Courses Backgrounds.
		$css->set_selector( 'body.post-type-archive-courses .site, body.post-type-archive-courses.content-style-unboxed .site' );
		$css->render_background( webapp()->sub_option( 'courses_archive_content_background', 'desktop' ), $css );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( 'body.post-type-archive-courses .site, body.post-type-archive-courses.content-style-unboxed .site' );
		$css->render_background( webapp()->sub_option( 'courses_archive_content_background', 'tablet' ), $css );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( 'body.post-type-archive-courses .site, body.post-type-archive-courses.content-style-unboxed .site' );
		$css->render_background( webapp()->sub_option( 'courses_archive_content_background', 'mobile' ), $css );
		$css->stop_media_query();
		// Events Hero Title Area.
		$css->set_selector( '.courses-archive-hero-section .entry-hero-container-inner' );
		$css->render_background( webapp()->sub_option( 'courses_archive_title_background', 'desktop' ), $css );
		$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'courses_archive_title_top_border', 'desktop' ) ) );
		$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'courses_archive_title_bottom_border', 'desktop' ) ) );
		$css->set_selector( '.entry-hero.courses-archive-hero-section .entry-header' );
		$css->add_property( 'min-height', $css->render_range( webapp()->option( 'courses_archive_title_height' ), 'desktop' ) );
		$css->set_selector( '.courses-archive-hero-section .hero-section-overlay' );
		$css->add_property( 'background', $css->render_color_or_gradient( webapp()->sub_option( 'courses_archive_title_overlay_color', 'color' ) ) );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '.courses-archive-hero-section .entry-hero-container-inner' );
		$css->render_background( webapp()->sub_option( 'courses_archive_title_background', 'tablet' ), $css );
		$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'courses_archive_title_top_border', 'tablet' ) ) );
		$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'courses_archive_title_bottom_border', 'tablet' ) ) );
		$css->set_selector( '.entry-hero.courses-archive-hero-section .entry-header' );
		$css->add_property( 'min-height', $css->render_range( webapp()->option( 'courses_archive_title_height' ), 'tablet' ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '.courses-archive-hero-section .entry-hero-container-inner' );
		$css->render_background( webapp()->sub_option( 'courses_archive_title_background', 'mobile' ), $css );
		$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'courses_archive_title_top_border', 'mobile' ) ) );
		$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'courses_archive_title_bottom_border', 'mobile' ) ) );
		$css->set_selector( '.entry-hero.courses-archive-hero-section .entry-header' );
		$css->add_property( 'min-height', $css->render_range( webapp()->option( 'courses_archive_title_height' ), 'mobile' ) );
		$css->stop_media_query();
		$css->set_selector( '.wp-site-blocks .courses-archive-title h1' );
		$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'courses_archive_title_color', 'color' ) ) );
		$css->set_selector( '.courses-archive-title .base-breadcrumbs' );
		$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'courses_archive_title_breadcrumb_color', 'color' ) ) );
		$css->set_selector( '.courses-archive-title .base-breadcrumbs a:hover' );
		$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'courses_archive_title_breadcrumb_color', 'hover' ) ) );

		self::$google_fonts = $css->fonts_output();
		return $css->css_output();
	}
	/**
	 * Adds custom classes to indicate whether a sidebar is present to the array of body classes.
	 *
	 * @param array $classes Classes for the body element.
	 * @return array Filtered body classes.
	 */
	public function filter_body_classes( array $classes ) : array {
		if ( is_singular( 'courses' ) ) {
			$classes[] = 'courses-sidebar-overlay-' . ( webapp()->option( 'courses_enroll_overlay' ) ? 'true' : 'false' );
		}
		return $classes;
	}
}
