<?php
/**
 * Class for the Customizer
 *
 * @package Base
 */

namespace Base;

use Base_Control_Builder;
use Base_Control_Builder_Tabs;
use Customizer_Sanitize;
use WP_Customize_Control;
use LearnDash_Settings_Section;
use Base\WebFont_Loader;
use function Base\webapp;
use function add_action;
use function get_template_part;
use function add_filter;
use function wp_enqueue_style;
use function get_template_directory;
use function wp_style_add_data;
use function get_theme_file_uri;
use function get_theme_file_path;
use function wp_styles;
use function esc_attr;
use function esc_url;
use function wp_style_is;
use function _doing_it_wrong;
use function wp_print_styles;
use function post_password_required;
use function get_option;
use function comments_open;
use function get_comments_number;
use function apply_filters;
use function add_query_arg;
use function wp_add_inline_style;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for Customizer
 *
 * @category class
 */
class Theme_Customizer {
	/**
	 * Instance Control.
	 *
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * Holds theme settings
	 *
	 * @var the theme settings array.
	 */
	public static $settings = array();

	/**
	 * Panels.
	 *
	 * @var null
	 */
	private static $panels = null;
	/**
	 * Sections.
	 *
	 * @var null
	 */
	private static $sections = null;

	/**
	 * Sections added.
	 *
	 * @var array
	 */
	private static $sections_added = array();

	/**
	 * Panels added.
	 *
	 * @var array
	 */
	private static $panels_added = array();

	/**
	 * Capability for customizer access.
	 *
	 * @var null
	 */
	private static $capability = null;

	/**
	 * Context for customizer controls.
	 *
	 * @var null
	 */
	private static $contexts = array();

	/**
	 * Live Preview Control Data for customizer controls.
	 *
	 * @var null
	 */
	private static $live_control = array();

	/**
	 * Choices Data for customizer controls.
	 *
	 * @var null
	 */
	private static $choices = array();

	/**
	 * Holds theme settings array sections.
	 *
	 * @var the theme settings sections.
	 */
	public static $settings_sections = array(
		'general-layout',
		'general-colors',
		'general-button',
		'general-typography',
		'general-social',
		'general-scroll-to-top',
		'general-performance',
		'general-image',
		'general-breadcrumb',
		'general-sidebar',
		'general-comments',
		'header-builder',
		'header-logo',
		'header-navigation',
		'header-secondary-navigation',
		'header-dropdown',
		'transparent-header',
		'header-trigger',
		'header-sticky',
		'header-mobile-nav',
		'header-mobile-button',
		'header-mobile-html',
		'header-main',
		'header-top',
		'header-bottom',
		'header-popup',
		'header-html',
		'header-button',
		'header-social',
		'header-mobile-social',
		'header-search',
		'page-layout',
		'footer-builder',
		'footer-middle',
		'footer-top',
		'footer-bottom',
		'footer-widget1',
		'footer-widget2',
		'footer-widget3',
		'footer-widget4',
		'footer-widget5',
		'footer-widget6',
		'footer-html',
		'footer-social',
		'footer-navigation',
		'post-layout',
		'post-archive-layout',
		'custom-layout',
		'search-layout',
		'404-layout',
	);
	/**
	 * Instance Control.
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	/**
	 * Constructor function.
	 */
	public function __construct() {

		add_action( 'customize_register', array( $this, 'create_settings_array' ), 1 );

		add_action( 'customize_register', array( $this, 'register_controls' ) );

		add_action( 'customize_register', array( $this, 'register_settings' ) );

		add_action( 'customize_register', array( $this, 'add_controls' ) );

		add_action( 'customize_register', array( $this, 'override_defaults' ), 20 );

		add_action( 'customize_register', array( $this, 'add_woocommerce_choices' ), 20 );

		add_action( 'customize_section_active', array( $this, 'active_footer_widgets' ), 20, 2 );

		add_filter( 'customize_controls_enqueue_scripts', array( $this, 'convert_beaver_custom_fonts' ), 5 );

		add_filter( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_customizer_scripts' ) );

		add_action( 'customize_preview_init', array( $this, 'action_enqueue_customize_preview_scripts' ) );

		add_filter( 'customizer_widgets_section_args', array( $this, 'customizer_custom_widget_areas' ), 10, 3 );

		add_action( 'wp_ajax_base_flush_fonts_folder', array( $this, 'ajax_delete_fonts_folder' ) );

	}
	/**
	 * Add a filter that matches beaver builder custom font filter.
	 */
	public function convert_beaver_custom_fonts() {
		$beaver_fonts = apply_filters( 'base_theme_add_custom_fonts', array() );
		if ( ! empty( $beaver_fonts ) && is_array( $beaver_fonts ) ) {
			add_filter(
				'base_theme_custom_fonts',
				function( $custom_fonts ) use( $beaver_fonts ) {
					foreach ( $beaver_fonts as $font_name => $args ) {
						$weights_arg = array();
						if ( is_array( $args ) && isset( $args['weights'] ) && is_array( $args['weights'] ) ) {
							$weights_arg = $args['weights'];
						}
						$font_slug = ( is_array( $args ) && isset( $args['fallback'] ) && ! empty( $args['fallback'] ) ? '"' . $font_name . '", ' . $args['fallback'] : $font_name );
						$custom_fonts[ $font_slug  ] = array(
							'v' => $weights_arg,
						);
					}
					return $custom_fonts;
				},
				10
			);
		}
	}
	/**
	 * Reset font folder
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_delete_fonts_folder() {
		// Check request.
		if ( ! check_ajax_referer( 'base-local-fonts-flush', 'nonce', false ) ) {
			wp_send_json_error( 'invalid_nonce' );
		}
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( 'invalid_permissions' );
		}
		if ( class_exists( '\Base\WebFont_Loader' ) ) {
			$font_loader = new \Base\WebFont_Loader( '' );
			$removed = $font_loader->delete_fonts_folder();
			if ( ! $removed ) {
				wp_send_json_error( 'failed_to_flush' );
			}
			wp_send_json_success();
		}
		wp_send_json_error( 'no_font_loader' );
	}
	/**
	 * Add Woocommerce Cart option to header.
	 */
	public function add_woocommerce_choices() {
		if ( class_exists( 'woocommerce' ) ) {
			$options         = self::$choices['header_desktop_items'];
			$options['cart'] = array(
				'name'    => esc_html__( 'Cart', 'avanam' ),
				'section' => 'base_customizer_cart',
			);
			self::$choices['header_desktop_items'] = $options;
			// Mobile Cart.
			$options         = self::$choices['header_mobile_items'];
			$options['mobile-cart'] = array(
				'name'    => esc_html__( 'Cart', 'avanam' ),
				'section' => 'base_customizer_mobile_cart',
			);
			self::$choices['header_mobile_items'] = $options;
		}
	}
	/**
	 * Filter footer widget areas.
	 *
	 * @param array  $section_args the widget sections args.
	 * @param string $section_id the widget sections id.
	 * @param string $sidebar_id the widget area id.
	 */
	public function customizer_custom_widget_areas( $section_args, $section_id, $sidebar_id ) {
		if ( 'footer1' === $sidebar_id || 'footer2' === $sidebar_id || 'footer3' === $sidebar_id || 'footer4' === $sidebar_id || 'footer5' === $sidebar_id || 'footer6' === $sidebar_id ) {
			$section_args['panel'] = 'base_customizer_footer';
		}
		return $section_args;
	}
	/**
	 * Add settings
	 *
	 * @access public
	 * @param object $wp_customize the customizer object.
	 * @return void
	 */
	public function create_settings_array( $wp_customize ) {
		// Load Sanitize Class.
		require_once get_template_directory() . '/inc/customizer/class-customizer-sanitize.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		// Load Settings files.
		foreach ( self::$settings_sections as $key ) {
			require_once get_template_directory() . '/inc/customizer/options/' . $key . '-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		}
		if ( class_exists( 'woocommerce' ) ) {
			require_once get_template_directory() . '/inc/customizer/options/header-cart-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			require_once get_template_directory() . '/inc/customizer/options/header-mobile-cart-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			require_once get_template_directory() . '/inc/customizer/options/product-layout-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			require_once get_template_directory() . '/inc/customizer/options/product-archive-layout-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			require_once get_template_directory() . '/inc/customizer/options/my-account-layout-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			require_once get_template_directory() . '/inc/customizer/options/woo-store-notice-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		}
		if ( class_exists( 'SFWD_LMS' ) ) {
			require_once get_template_directory() . '/inc/customizer/options/learndash-course-layout-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			require_once get_template_directory() . '/inc/customizer/options/learndash-groups-layout-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			require_once get_template_directory() . '/inc/customizer/options/learndash-essays-layout-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			require_once get_template_directory() . '/inc/customizer/options/learndash-course-archive-layout-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			if ( class_exists( 'LearnDash_Settings_Section' ) ) {
				$in_focus_mode = \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Theme_LD30', 'focus_mode_enabled' );
				if ( ! $in_focus_mode ) {
					require_once get_template_directory() . '/inc/customizer/options/learndash-lesson-layout-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
					require_once get_template_directory() . '/inc/customizer/options/learndash-topic-layout-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
					require_once get_template_directory() . '/inc/customizer/options/learndash-quiz-layout-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
				}
			}
			if ( defined( 'LEARNDASH_COURSE_GRID_VERSION' ) && version_compare( LEARNDASH_COURSE_GRID_VERSION, '2.0.0', '<' ) ) {
				require_once get_template_directory() . '/inc/customizer/options/learndash-grid-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			}
		}
		if ( class_exists( 'LifterLMS' ) ) {
			require_once get_template_directory() . '/inc/customizer/options/lifter-course-layout-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			require_once get_template_directory() . '/inc/customizer/options/lifter-course-archive-layout-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			require_once get_template_directory() . '/inc/customizer/options/lifter-lesson-layout-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			require_once get_template_directory() . '/inc/customizer/options/lifter-quiz-layout-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			require_once get_template_directory() . '/inc/customizer/options/lifter-member-layout-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			require_once get_template_directory() . '/inc/customizer/options/lifter-member-archive-layout-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			require_once get_template_directory() . '/inc/customizer/options/lifter-dashboard-layout-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		}
		if ( class_exists( 'TUTOR\Tutor' ) ) {
			require_once get_template_directory() . '/inc/customizer/options/tutor-course-layout-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			require_once get_template_directory() . '/inc/customizer/options/tutor-course-archive-layout-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		}
		if ( class_exists( 'BBPress' ) ) {
			require_once get_template_directory() . '/inc/customizer/options/forum-archive-layout-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			require_once get_template_directory() . '/inc/customizer/options/forum-layout-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			require_once get_template_directory() . '/inc/customizer/options/topic-layout-options.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		}
		if ( defined( 'TRIBE_EVENTS_FILE' ) ) {
			require_once get_template_directory() . '/inc/customizer/options/tec-event-layout-options.php'; // phpcs:ignore WPThemeReview.
			require_once get_template_directory() . '/inc/customizer/options/tec-event-archive-layout-options.php'; // phpcs:ignore WPThemeReview.
		}
	}
	/**
	 * Overide default settings
	 *
	 * @access public
	 * @param object $wp_customize the customizer object.
	 * @return void
	 */
	public function override_defaults( $wp_customize ) {
		/**
		 * Override Settings
		 */
		$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

		/**
		 * Move Site Identity
		 */
		$wp_customize->get_control( 'site_icon' )->section = 'base_customizer_site_identity';

		/**
		 * Override Controls Priority
		 */
		$wp_customize->get_control( 'custom_logo' )->priority     = 4;
		$wp_customize->get_control( 'blogname' )->priority        = 7;
		$wp_customize->get_control( 'blogdescription' )->priority = 10;

		/**
		 * Override Post Message
		 */
		$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
		$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

		if ( isset( $wp_customize->selective_refresh ) ) {
			$wp_customize->selective_refresh->add_partial(
				'blogname',
				array(
					'selector'        => '.site-title',
					'render_callback' => function() {
						bloginfo( 'name' );
					},
				)
			);
			$wp_customize->selective_refresh->add_partial(
				'blogdescription',
				array(
					'selector'        => '.site-description',
					'render_callback' => function() {
						bloginfo( 'description' );
					},
				)
			);
			$wp_customize->selective_refresh->add_partial(
				'custom_logo',
				array(
					'selector'            => '.site-branding',
					'container_inclusive' => true,
					'render_callback'     => 'Base\site_branding',
				)
			);
		}

		if ( ! empty( $opt_name_key = apply_filters( 'base_settings_key_custom_mapping', '', 'custom_logo' ) ) ) {
			$wp_customize->remove_control( 'custom_logo' );
			$id = $opt_name_key . '[custom_logo]';
			$wp_customize->add_setting(
				$id,
				array(
					'type'              => 'option',
					'transport'         => 'postMessage',
					'capability'        => self::get_capability(),
					'default'           => isset( $setting['default'] ) && $control_class !== 'Base_Control_Background' ? $setting['default'] : '',
					'sanitize_callback' => array( 'Base\Customizer_Sanitize', 'base_sanitize_option' ),
				)
			);
			$control_config = array(
				'settings'        => $id,
				'section'      => 'title_tagline',
				'capability'      => self::get_capability(),
				'priority'        => 4,
				'label'        => esc_html__( 'Logo', 'avanam' ),
				'mime_type'    => 'image',
				'active_callback' => '__return_true',
			);
			$wp_customize->add_control( new \WP_Customize_Media_Control( $wp_customize, $id, $control_config ) );
			$wp_customize->selective_refresh->add_partial(
				$id,
				array(
					'selector'            => '.site-branding',
					'container_inclusive' => true,
					'render_callback'     => 'Base\site_branding',
				)
			);
			$general_tab = array(
				array(
					'setting' => '__current_tab',
					'value'   => 'general',
				),
			);
			self::$contexts[$id] = $general_tab;
		}

		/**
		 * Add Tab Conditionals
		 */
		$general_tab = array(
			array(
				'setting' => '__current_tab',
				'value'   => 'general',
			),
		);
		self::$contexts['custom_logo']     = $general_tab;
		self::$contexts['blogname']        = $general_tab;
		self::$contexts['blogdescription'] = $general_tab;

		$palette_live = array(
			array(
				'type'     => 'palette',
				'selector' => ':root',
				'property' => 'global-palette',
				'pattern'  => '$',
				'key'      => 'palette',
			),
		);

		self::$live_control['base_global_palette'] = $palette_live;

	}
	/**
	 * Add Controls
	 *
	 * @access public
	 * @param object $wp_customize the customizer object.
	 * @return void
	 */
	public function register_controls( $wp_customize ) {
		$path       = get_template_directory() . '/inc/customizer/custom-controls/';
		$react_path = get_template_directory() . '/inc/customizer/react/';
		// Register the custom control type.
		require_once $path . 'class-base-control-blank.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-color.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-range.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-switch.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-radio-icon.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-multi-radio-icon.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-builder.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-available.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-color-palette.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-background.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-border.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-borders.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-typography.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-title.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-focus-button.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-color-link.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-text.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-measure.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-editor.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-sorter.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-social.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-contact.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-check-icon.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-select.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-row.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-tab.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once $react_path . 'class-base-control-shadow.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
	}

	/**
	 * Adds Settings to settings array.
	 *
	 * @param array $new_settings an array of settings.
	 */
	public static function add_settings( $new_settings ) {
		self::$settings = array_merge( $new_settings, self::$settings );
	}

	/**
	 * Get header customizer panels
	 *
	 * @access public
	 * @return array
	 */
	public static function get_panels() {
		// Define panels.
		if ( is_null( self::$panels ) ) {
			$panels = array(
				'design' => array(
					'title'    => __( 'Colors & Fonts', 'avanam' ),
					'priority' => 18,
				),
				'header' => array(
					'title'    => __( 'Header', 'avanam' ),
					'priority' => 20,
				),
				'footer' => array(
					'title'    => __( 'Footer', 'avanam' ),
					'priority' => 22,
				),
				'general' => array(
					'title'    => __( 'General', 'avanam' ),
					'priority' => 23,
				),
				'all_posts' => array(
					'title'    => __( 'Posts/Pages Layout', 'avanam' ),
					'priority' => 24,
				),
			);
			if ( class_exists( 'SFWD_LMS' ) ) {
				$extra_learn['learndash'] = array(
					'title'    => __( 'LearnDash', 'avanam' ),
					'priority' => 25,
				);
				$panels = array_merge(
					$panels,
					$extra_learn
				);
			}
			if ( class_exists( 'LifterLMS' ) ) {
				$extra_lifter['lifterlms'] = array(
					'title'    => __( 'LifterLMS', 'avanam' ),
					'priority' => 25,
				);
				$panels = array_merge(
					$panels,
					$extra_lifter
				);
			}
			if ( class_exists( 'BBPress' ) ) {
				$extra_bbpress['bbpress'] = array(
					'title'    => __( 'bbPress', 'avanam' ),
					'priority' => 25,
				);
				$panels = array_merge(
					$panels,
					$extra_bbpress
				);
			}
			if ( class_exists( 'TUTOR\Tutor' ) ) {
				$extra_tutor['tutorlms'] = array(
					'title'    => __( 'TutorLMS', 'avanam' ),
					'priority' => 25,
				);
				$panels = array_merge(
					$panels,
					$extra_tutor
				);
			}
			self::$panels = $panels;
		}

		// Return panels.
		return self::$panels;
	}

	/**
	 * Get Customizer sections
	 *
	 * @access public
	 * @return array
	 */
	public static function get_sections() {
		// Define sections.
		if ( is_null( self::$sections ) ) {
			$sections = array(
				'site_identity' => array(
					'title'    => __( 'Site Identity', 'avanam' ),
					'priority' => 25,
				),
				'general_layout' => array(
					'title'    => __( 'Layout', 'avanam' ),
					'panel'    => 'general',
					'priority' => 7,
				),
				'sidebar' => array(
					'title'    => __( 'Sidebar', 'avanam' ),
					'panel'    => 'general',
					'priority' => 8,
				),
				'sidebar_design' => array(
					'title'    => __( 'Sidebar Design', 'avanam' ),
					'panel'    => 'general',
					'priority' => 8,
				),
				'general_colors' => array(
					'title'    => __( 'Colors', 'avanam' ),
					'panel'    => 'design',
					'priority' => 9,
				),
				'general_buttons' => array(
					'title'    => __( 'Buttons', 'avanam' ),
					'panel'    => 'design',
					'priority' => 10,
				),
				'general_typography' => array(
					'title'    => __( 'Typography', 'avanam' ),
					'panel'    => 'design',
					'priority' => 11,
				),
				'general_image' => array(
					'title'    => __( 'Images', 'avanam' ),
					'panel'    => 'general',
					'priority' => 11,
				),
				'scroll_up' => array(
					'title'    => __( 'Scroll To Top', 'avanam' ),
					'panel'    => 'general',
					'priority' => 12,
				),
				'scroll_up_design' => array(
					'title'    => __( 'Scroll To Top', 'avanam' ),
					'panel'    => 'general',
					'priority' => 12,
				),
				'general_social' => array(
					'title'    => __( 'Social Links', 'avanam' ),
					'panel'    => 'general',
					'priority' => 20,
				),
				'general_comments' => array(
					'title'    => __( 'Comments', 'avanam' ),
					'panel'    => 'general',
					'priority' => 20,
				),
				'general_performance' => array(
					'title'    => __( 'Performance', 'avanam' ),
					'panel'    => 'general',
					'priority' => 21,
				),
				'breadcrumbs' => array(
					'title'    => __( 'Breadcrumbs', 'avanam' ),
					'panel'    => 'general',
					'priority' => 20,
				),
				'general_404' => array(
					'title'    => __( '404 Page Layout', 'avanam' ),
					'panel'    => 'general',
					'priority' => 20,
				),
				'general_404_design' => array(
					'title'    => __( '404 Page Layout', 'avanam' ),
					'panel'    => 'general',
					'priority' => 20,
				),
				'header_layout' => array(
					'title'    => __( 'Header Layout', 'avanam' ),
					'panel'    => 'header',
					'priority' => 8,
				),
				'header_builder' => array(
					'title'    => __( 'Header Builder', 'avanam' ),
					'panel'    => 'header',
					'priority' => 9,
				),
				'header_top' => array(
					'title'    => __( 'Top Row', 'avanam' ),
					'panel'    => 'header',
					'priority' => 10,
				),
				'header_main' => array(
					'title'    => __( 'Main Row', 'avanam' ),
					'panel'    => 'header',
					'priority' => 10,
				),
				'header_bottom' => array(
					'title'    => __( 'Bottom Row', 'avanam' ),
					'panel'    => 'header',
					'priority' => 10,
				),
				'header_popup' => array(
					'title'    => __( 'Header Off Canvas', 'avanam' ),
					'panel'    => 'header',
					'priority' => 10,
				),
				'header_popup_design' => array(
					'title'    => __( 'Header Off Canvas', 'avanam' ),
					'panel'    => 'header',
					'priority' => 10,
				),
				'title_tagline' => array(
					'title'    => __( 'Site Identity', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'primary_navigation' => array(
					'title'    => __( 'Primary Navigation', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'secondary_navigation' => array(
					'title'    => __( 'Secondary Navigation', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'dropdown_navigation' => array(
					'title'    => __( 'Dropdown Navigation', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'dropdown_navigation_design' => array(
					'title'    => __( 'Dropdown Navigation', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'mobile_trigger' => array(
					'title'    => __( 'Mobile Trigger', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'mobile_trigger_design' => array(
					'title'    => __( 'Mobile Trigger', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'mobile_navigation' => array(
					'title'    => __( 'Mobile Navigation', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'mobile_button' => array(
					'title'    => __( 'Header Mobile Button', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'mobile_button_design' => array(
					'title'    => __( 'Header Mobile Button', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'mobile_html' => array(
					'title'    => __( 'Header Mobile HTML', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'header_html' => array(
					'title'    => __( 'HTML Editor', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'header_html_design' => array(
					'title'    => __( 'HTML Editor', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'cart' => array(
					'title'    => __( 'Header Cart', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'cart_design' => array(
					'title'    => __( 'Header Cart Design', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'mobile_cart' => array(
					'title'    => __( 'Mobile Header Cart', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'mobile_cart_design' => array(
					'title'    => __( 'Mobile Header Cart', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'header_button' => array(
					'title'    => __( 'Header Button', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'header_button_design' => array(
					'title'    => __( 'Header Button', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'header_social' => array(
					'title'    => __( 'Header Social', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'header_social_design' => array(
					'title'    => __( 'Header Social', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'mobile_social' => array(
					'title'    => __( 'Header Social', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'mobile_social_design' => array(
					'title'    => __( 'Header Social', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'header_search' => array(
					'title'    => __( 'Header Search', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'transparent_header' => array(
					'title'    => __( 'Transparent Header', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'transparent_header_design' => array(
					'title'    => __( 'Transparent Header', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'header_sticky' => array(
					'title'    => __( 'Sticky Header', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'header_sticky_design' => array(
					'title'    => __( 'Sticky Header', 'avanam' ),
					'panel'    => 'header',
					'priority' => 20,
				),
				'footer_layout' => array(
					'title'    => __( 'Footer Layout', 'avanam' ),
					'panel'    => 'footer',
					'priority' => 8,
				),
				'footer_builder' => array(
					'title'    => __( 'Footer Builder', 'avanam' ),
					'panel'    => 'footer',
					'priority' => 9,
				),
				'footer_top' => array(
					'title'    => __( 'Footer Top', 'avanam' ),
					'panel'    => 'footer',
					'priority' => 10,
				),
				'footer_top_design' => array(
					'title'    => __( 'Footer Top', 'avanam' ),
					'panel'    => 'footer',
					'priority' => 10,
				),
				'footer_middle' => array(
					'title'    => __( 'Footer Middle', 'avanam' ),
					'panel'    => 'footer',
					'priority' => 10,
				),
				'footer_middle_design' => array(
					'title'    => __( 'Footer Middle', 'avanam' ),
					'panel'    => 'footer',
					'priority' => 10,
				),
				'footer_bottom' => array(
					'title'    => __( 'Footer Bottom', 'avanam' ),
					'panel'    => 'footer',
					'priority' => 10,
				),
				'footer_bottom_design' => array(
					'title'    => __( 'Footer Bottom', 'avanam' ),
					'panel'    => 'footer',
					'priority' => 10,
				),
				'footer_html' => array(
					'title'    => __( 'Footer Copyright HTML', 'avanam' ),
					'panel'    => 'footer',
					'priority' => 10,
				),
				'footer_social' => array(
					'title'    => __( 'Footer Social', 'avanam' ),
					'panel'    => 'footer',
					'priority' => 10,
				),
				'footer_social_design' => array(
					'title'    => __( 'Footer Social', 'avanam' ),
					'panel'    => 'footer',
					'priority' => 10,
				),
				'footer_navigation' => array(
					'title'    => __( 'Footer Navigation', 'avanam' ),
					'panel'    => 'footer',
					'priority' => 10,
				),
				'page_layout' => array(
					'title'    => __( 'Page Layout', 'avanam' ),
					'panel'    => 'all_posts',
					'priority' => 3,
				),
				'search' => array(
					'title'    => __( 'Search Results', 'avanam' ),
					'priority' => 24,
				),
				'search_design' => array(
					'title'    => __( 'Search Results', 'avanam' ),
					'priority' => 24,
				),
				'post_layout' => array(
					'title'    => __( 'Single Post Layout', 'avanam' ),
					'panel'    => 'all_posts',
					'priority' => 7,
				),
				'post_layout_design' => array(
					'title'    => __( 'Single Post Layout', 'avanam' ),
					'panel'    => 'all_posts',
					'priority' => 7,
				),
				'post_archive' => array(
					'title'    => __( 'Archive Layout', 'avanam' ),
					'panel'    => 'all_posts',
					'priority' => 8,
				),
				'post_archive_design' => array(
					'title'    => __( 'Archive Layout', 'avanam' ),
					'panel'    => 'all_posts',
					'priority' => 8,
				),
				// 'custom_post' => array(
				// 	'title'    => __( 'Custom Post Types', 'avanam' ),
				// 	'panel'    => 'all_posts',
				// 	'priority' => 24,
				// ),
			);
			if ( ( defined( 'GUTENBERG_VERSION' ) && version_compare( GUTENBERG_VERSION, '10.6.2', '>' ) ) || version_compare( substr( get_bloginfo( 'version' ), 0, 3 ), '5.8', '>=' ) ) {
				$blocks_extra['sidebar-widgets-footer1'] = array(
					'title'    => __( 'Widget Footer 1 Settings', 'avanam' ),
					'panel'    => 'footer',
					'priority' => 10,
				);
				$blocks_extra['sidebar-widgets-footer2'] = array(
					'title'    => __( 'Widget Footer 2 Settings', 'avanam' ),
					'panel'    => 'footer',
					'priority' => 10,
				);
				$blocks_extra['sidebar-widgets-footer3'] = array(
					'title'    => __( 'Widget Footer 3 Settings', 'avanam' ),
					'panel'    => 'footer',
					'priority' => 10,
				);
				$blocks_extra['sidebar-widgets-footer4'] = array(
					'title'    => __( 'Widget Footer 4 Settings', 'avanam' ),
					'panel'    => 'footer',
					'priority' => 10,
				);
				$blocks_extra['sidebar-widgets-footer5'] = array(
					'title'    => __( 'Widget Footer 5 Settings', 'avanam' ),
					'panel'    => 'footer',
					'priority' => 10,
				);
				$blocks_extra['sidebar-widgets-footer6'] = array(
					'title'    => __( 'Widget Footer 6 Settings', 'avanam' ),
					'panel'    => 'footer',
					'priority' => 10,
				);
				$blocks_extra['sidebar-widgets-header1'] = array(
					'title'    => __( 'Widget Header Settings', 'avanam' ),
					'panel'    => 'header',
					'priority' => 10,
				);
				$blocks_extra['sidebar-widgets-header2'] = array(
					'title'    => __( 'Widget Area Settings', 'avanam' ),
					'panel'    => 'header',
					'priority' => 10,
				);
				$blocks_extra['sidebar-widgets-product-filter'] = array(
					'title'    => __( 'Catalog Off Canvas Sidebar Settings', 'avanam' ),
					'panel'    => 'woocommerce',
					'priority' => 18,
				);
				$sections = array_merge(
					$sections,
					$blocks_extra
				);
			}
			if ( class_exists( 'woocommerce' ) ) {
				$extra_woo['woocommerce_product_catalog'] = array(
					'title'    => __( 'Product Catalog', 'avanam' ),
					'panel'    => 'woocommerce',
					'priority' => 10,
				);
				$extra_woo['woocommerce_product_catalog_design'] = array(
					'title'    => __( 'Product Catalog', 'avanam' ),
					'panel'    => 'woocommerce',
					'priority' => 10,
				);
				$extra_woo['product_layout'] = array(
					'title'    => __( 'Single Product Layout', 'avanam' ),
					'panel'    => 'woocommerce',
					'priority' => 10,
				);
				$extra_woo['product_layout_design'] = array(
					'title'    => __( 'Single Product Layout', 'avanam' ),
					'panel'    => 'woocommerce',
					'priority' => 10,
				);
				$extra_woo['account_layout'] = array(
					'title'    => __( 'My Account Layout', 'avanam' ),
					'panel'    => 'woocommerce',
					'priority' => 24,
				);
				$extra_woo['woocommerce_store_notice_design'] = array(
					'title'    => __( 'Store Notice', 'avanam' ),
					'panel'    => 'woocommerce',
					'priority' => 10,
				);
				$sections = array_merge(
					$sections,
					$extra_woo
				);
			}
			if ( class_exists( 'SFWD_LMS' ) ) {
				$extra_learn = array();
				$extra_learn['sfwd_courses_layout'] = array(
					'title'    => __( 'Course Layout', 'avanam' ),
					'panel'    => 'learndash',
					'priority' => 10,
				);
				$extra_learn['sfwd_courses_layout_design'] = array(
					'title'    => __( 'Course Layout', 'avanam' ),
					'panel'    => 'learndash',
					'priority' => 10,
				);
				$extra_learn['sfwd_courses_archive_layout'] = array(
					'title'    => __( 'Course Archive Layout', 'avanam' ),
					'panel'    => 'learndash',
					'priority' => 11,
				);
				$extra_learn['sfwd_courses_archive_layout_design'] = array(
					'title'    => __( 'Course Archive Layout', 'avanam' ),
					'panel'    => 'learndash',
					'priority' => 11,
				);
				$extra_learn['sfwd_lesson_layout'] = array(
					'title'    => __( 'Lesson Layout', 'avanam' ),
					'panel'    => 'learndash',
					'priority' => 12,
				);
				$extra_learn['sfwd_lesson_layout_design'] = array(
					'title'    => __( 'Lesson Layout', 'avanam' ),
					'panel'    => 'learndash',
					'priority' => 12,
				);
				$extra_learn['sfwd_quiz_layout'] = array(
					'title'    => __( 'Quiz Layout', 'avanam' ),
					'panel'    => 'learndash',
					'priority' => 12,
				);
				$extra_learn['sfwd_quiz_layout_design'] = array(
					'title'    => __( 'Quiz Layout', 'avanam' ),
					'panel'    => 'learndash',
					'priority' => 12,
				);
				$extra_learn['sfwd_topic_layout'] = array(
					'title'    => __( 'Topic Layout', 'avanam' ),
					'panel'    => 'learndash',
					'priority' => 12,
				);
				$extra_learn['sfwd_topic_layout_design'] = array(
					'title'    => __( 'Topic Layout', 'avanam' ),
					'panel'    => 'learndash',
					'priority' => 12,
				);
				$extra_learn['sfwd_groups_layout'] = array(
					'title'    => __( 'Groups Layout', 'avanam' ),
					'panel'    => 'learndash',
					'priority' => 11,
				);
				$extra_learn['sfwd_groups_layout_design'] = array(
					'title'    => __( 'Groups Layout', 'avanam' ),
					'panel'    => 'learndash',
					'priority' => 11,
				);
				$extra_learn['sfwd_essays_layout'] = array(
					'title'    => __( 'Essay Layout', 'avanam' ),
					'panel'    => 'learndash',
					'priority' => 11,
				);
				$extra_learn['sfwd_essays_layout_design'] = array(
					'title'    => __( 'Essay Layout', 'avanam' ),
					'panel'    => 'learndash',
					'priority' => 11,
				);
				$extra_learn['sfwd_design'] = array(
					'title'    => __( 'Learn Dash Design', 'avanam' ),
					'panel'    => 'learndash',
					'priority' => 12,
				);
				$extra_learn['sfwd_grid_layout'] = array(
					'title'    => __( 'Course Grid Design', 'avanam' ),
					'panel'    => 'learndash',
					'priority' => 12,
				);
				$extra_learn['sfwd_grid_layout_design'] = array(
					'title'    => __( 'Course Grid Design', 'avanam' ),
					'panel'    => 'learndash',
					'priority' => 12,
				);
				$sections = array_merge(
					$sections,
					$extra_learn
				);
			}
			if ( class_exists( 'BBPress' ) ) {
				$extra_bbpress = array();
				$extra_bbpress['topic_layout'] = array(
					'title'    => __( 'Topic Layout', 'avanam' ),
					'panel'    => 'bbpress',
					'priority' => 10,
				);
				$extra_bbpress['topic_layout_design'] = array(
					'title'    => __( 'Topic Layout', 'avanam' ),
					'panel'    => 'bbpress',
					'priority' => 10,
					'type'     => 'design-hidden',
				);
				$extra_bbpress['forum_layout'] = array(
					'title'    => __( 'Forum Layout', 'avanam' ),
					'panel'    => 'bbpress',
					'priority' => 10,
				);
				$extra_bbpress['forum_layout_design'] = array(
					'title'    => __( 'Forum Layout', 'avanam' ),
					'panel'    => 'bbpress',
					'priority' => 10,
					'type'     => 'design-hidden',
				);
				$extra_bbpress['forum_archive'] = array(
					'title'    => __( 'Forum Archive', 'avanam' ),
					'panel'    => 'bbpress',
					'priority' => 10,
				);
				$extra_bbpress['forum_archive_design'] = array(
					'title'    => __( 'Forum Archive', 'avanam' ),
					'panel'    => 'bbpress',
					'priority' => 10,
					'type'     => 'design-hidden',
				);
				$sections = array_merge(
					$sections,
					$extra_bbpress
				);
			}
			if ( class_exists( 'LifterLMS' ) ) {
				$extra_lifter['course_layout'] = array(
					'title'    => __( 'Course Layout', 'avanam' ),
					'panel'    => 'lifterlms',
					'priority' => 10,
				);
				$extra_lifter['course_layout_design'] = array(
					'title'    => __( 'Course Layout', 'avanam' ),
					'panel'    => 'lifterlms',
					'priority' => 10,
				);
				$extra_lifter['course_archive'] = array(
					'title'    => __( 'Course Archive', 'avanam' ),
					'panel'    => 'lifterlms',
					'priority' => 11,
				);
				$extra_lifter['course_archive_design'] = array(
					'title'    => __( 'Course Archive', 'avanam' ),
					'panel'    => 'lifterlms',
					'priority' => 11,
				);
				$extra_lifter['lesson_layout'] = array(
					'title'    => __( 'Lesson Layout', 'avanam' ),
					'panel'    => 'lifterlms',
					'priority' => 12,
				);
				$extra_lifter['lesson_layout_design'] = array(
					'title'    => __( 'Lesson Layout', 'avanam' ),
					'panel'    => 'lifterlms',
					'priority' => 12,
				);
				$extra_lifter['llms_quiz_layout'] = array(
					'title'    => __( 'Quiz Layout', 'avanam' ),
					'panel'    => 'lifterlms',
					'priority' => 13,
				);
				$extra_lifter['llms_membership_layout'] = array(
					'title'    => __( 'Membership Layout', 'avanam' ),
					'panel'    => 'lifterlms',
					'priority' => 14,
				);
				$extra_lifter['llms_membership_archive'] = array(
					'title'    => __( 'Membership Archive', 'avanam' ),
					'panel'    => 'lifterlms',
					'priority' => 15,
				);
				$extra_lifter['llms_membership_archive_design'] = array(
					'title'    => __( 'Membership Archive', 'avanam' ),
					'panel'    => 'lifterlms',
					'priority' => 15,
				);
				$extra_lifter['llms_dashboard_layout'] = array(
					'title'    => __( 'User Dashboard', 'avanam' ),
					'panel'    => 'lifterlms',
					'priority' => 16,
				);
				$sections = array_merge(
					$sections,
					$extra_lifter
				);
			}
			if ( class_exists( 'TUTOR\Tutor' ) ) {
				$extra_tutor['courses_layout'] = array(
					'title'    => __( 'Course Layout', 'avanam' ),
					'panel'    => 'tutorlms',
					'priority' => 10,
				);
				$extra_tutor['courses_layout_design'] = array(
					'title'    => __( 'Course Layout', 'avanam' ),
					'panel'    => 'tutorlms',
					'priority' => 10,
				);
				$extra_tutor['courses_archive_layout'] = array(
					'title'    => __( 'Course Archive Layout', 'avanam' ),
					'panel'    => 'tutorlms',
					'priority' => 11,
				);
				$extra_tutor['courses_archive_layout_design'] = array(
					'title'    => __( 'Course Archive Layout', 'avanam' ),
					'panel'    => 'tutorlms',
					'priority' => 11,
				);
				$sections = array_merge(
					$sections,
					$extra_tutor
				);
			}
			if ( defined( 'TRIBE_EVENTS_FILE' ) ) {
				$extra_events['tribe_events_layout'] = array(
					'title'    => __( 'Single Event Layout', 'avanam' ),
					'panel'    => 'tribe_customizer',
					'priority' => 80,
				);
				$extra_events['tribe_events_layout_design'] = array(
					'title'    => __( 'Single Event Layout', 'avanam' ),
					'panel'    => 'tribe_customizer',
					'priority' => 80,
				);
				$extra_events['tribe_events_archive'] = array(
					'title'    => __( 'Events Layout', 'avanam' ),
					'panel'    => 'tribe_customizer',
					'priority' => 85,
				);
				$sections = array_merge(
					$sections,
					$extra_events
				);
			}
			$post_types        = webapp()->get_post_types_objects();
			$extras_post_types = array();
			$add_extras        = false;
			foreach ( $post_types as $post_type_item ) {
				$post_type_name  = $post_type_item->name;
				$post_type_label = $post_type_item->label;
				$ignore_type     = webapp()->get_post_types_to_ignore();
				$panel_name = 'all_posts';
				$panel_priority = 10;
				if ( ! in_array( $post_type_name, $ignore_type, true ) ) {
					if ( $post_type_name === 'ld-exam' ) {
						$panel_name = 'learndash';
						$panel_priority = 40;
					}
					$add_extras = true;
					$extras_post_types[ $post_type_name . '_layout' ] = array(
						'title'    => $post_type_label . ' ' . __( 'Layout', 'avanam' ),
						'panel'    => $panel_name,
						'priority' => $panel_priority,
					);
					$extras_post_types[ $post_type_name . '_layout_design' ] = array(
						'title'    => $post_type_label . ' ' . __( 'Layout', 'avanam' ),
						'panel'    => $panel_name,
						'priority' => $panel_priority,
						'type'     => 'design-hidden',
					);
					if ( $post_type_name !== 'ld-exam' ) {
						$extras_post_types[ $post_type_name . '_archive' ] = array(
							'title'    => $post_type_label . ' ' . __( 'Archive', 'avanam' ),
							'panel'    => $panel_name,
							'priority' => $panel_priority,
						);
						$extras_post_types[ $post_type_name . '_archive_design' ] = array(
							'title'    => $post_type_label . ' ' . __( 'Archive', 'avanam' ),
							'panel'    => $panel_name,
							'priority' => $panel_priority,
							'type'     => 'design-hidden',
						);
					}
				}
			}
			if ( $add_extras ) {
				$extras_post_types['custom_posts_placeholder'] = array(
					'title'    => __( 'Custom Post Types', 'avanam' ),
					'panel'    => 'all_posts',
					'priority' => 9,
				);
				$final_sections = array_merge(
					$sections,
					$extras_post_types
				);

			} else {
				$final_sections = $sections;
			}
			self::$sections = apply_filters( 'base_theme_customizer_sections', $final_sections );
		}
		// Return sections.
		return self::$sections;
	}

	/**
	 * Get Customizer Capability
	 *
	 * @access public
	 * @return string
	 */
	public static function get_capability() {
		// Define sections.
		if ( is_null( self::$capability ) ) {
			self::$capability = apply_filters( 'base_theme_customizer_capability', 'manage_options' );
		}
		// Return capability.
		return self::$capability;
	}

	/**
	 * Add settings
	 *
	 * @access public
	 * @param object $wp_customize the customizer object.
	 * @return void
	 */
	public function register_settings( $wp_customize ) {
		// Iterate over settings.
		foreach ( self::$settings as $setting_key => $setting ) {
			if ( isset( $setting['settings'] ) && false === $setting['settings'] ) {
				continue;
			}
			// Define Control Class.
			if ( isset( $setting['control_type'] ) && ! empty( $setting['control_type'] ) ) {
				if ( substr( $setting['control_type'], 0, 5 ) === 'base_' ) {
					$control_class = 'Base_Control_' . ucfirst( str_replace( 'base_', '', str_replace( '_control', '', $setting['control_type'] ) ) );
				} else {
					$control_class = 'WP_Customize_' . ucfirst( $setting['control_type'] ) . '_Control';
				}
			} else {
				$control_class = 'WP_Customize_Control';
			}
			// Define Sanitize Function.
			if ( isset( $setting['sanitize'] ) && ! empty( $setting['sanitize'] ) ) {
				if ( substr( $setting['sanitize'], 0, 5 ) === 'base_' ) {
					$sanitize_function = array( 'Base\Customizer_Sanitize', $setting['sanitize'] );
				} else {
					$sanitize_function = $setting['sanitize'];
				}
			} else {
				$sanitize_function = array( 'Base\Customizer_Sanitize', 'base_sanitize_option' );
				$sanitize_function = '';
			}
			if ( 'option' === webapp()->get_option_type() ) {
				$setting_key = webapp()->get_option_name() . '[' . $setting_key . ']';
			}
			if ( ! empty( $opt_name_key = apply_filters( 'base_settings_key_custom_mapping', '', $setting_key ) ) ) {
				$id = $opt_name_key . '[' . $setting_key . ']';
				if ( isset( $setting['settings'] ) && is_array( $setting['settings'] ) ) {
					foreach ( $setting['settings'] as $setting_key => $id ) {
						$setting_id = $opt_name_key . '[' . $id . ']';
						$wp_customize->add_setting(
							$setting_id,
							array(
								'type'              => 'option',
								'default'           => webapp()->default( $id ),
								'transport'         => isset( $setting['transport'] ) ? $setting['transport'] : 'postMessage',
								'capability'        => self::get_capability(),
								'sanitize_callback' => $sanitize_function,
							)
						);
					}
				} else {
					$wp_customize->add_setting(
						$id,
						array(
							'type'              => 'option',
							'transport'         => isset( $setting['transport'] ) ? $setting['transport'] : 'postMessage',
							'capability'        => self::get_capability(),
							'default'           => isset( $setting['default'] ) && $control_class !== 'Base_Control_Background' ? $setting['default'] : '',
							'sanitize_callback' => $sanitize_function,
						)
					);
				}
			} else {
				if ( isset( $setting['settings'] ) && is_array( $setting['settings'] ) ) {
					foreach ( $setting['settings'] as $setting_key => $id ) {
						if ( 'option' === webapp()->get_option_type() ) {
							$id = self::get_option_name() . '[' . $id . ']';
						}
						$wp_customize->add_setting(
							$id,
							array(
								'type'              => webapp()->get_option_type(),
								'default'           => webapp()->default( $id ),
								'transport'         => isset( $setting['transport'] ) ? $setting['transport'] : 'postMessage',
								'capability'        => self::get_capability(),
								'sanitize_callback' => $sanitize_function,
							)
						);
					}
				} else {
					$wp_customize->add_setting(
						$setting_key,
						array(
							'type'              => webapp()->get_option_type(),
							'transport'         => isset( $setting['transport'] ) ? $setting['transport'] : 'postMessage',
							'capability'        => self::get_capability(),
							'default'           => isset( $setting['default'] ) && $control_class !== 'Base_Control_Background' ? $setting['default'] : '',
							'sanitize_callback' => $sanitize_function,
						)
					);
				}
			}
		}
	}
	/**
	 * Maybe add section
	 *
	 * @access public
	 * @param object $wp_customize the object.
	 * @param array  $child the child item.
	 * @return void
	 */
	public static function maybe_add_section( $wp_customize, $child ) {
		// Get sections.
		$sections = self::get_sections();

		// Check if section is set and exists.
		if ( ! empty( $child['section'] ) && isset( $sections[ $child['section'] ] ) ) {

			// Reference current section key.
			$section_key = $child['section'];

			// Check if section was not added yet.
			if ( ! in_array( $section_key, self::$sections_added, true ) ) {

				// Reference current section.
				$section = $sections[ $section_key ];

				// Section config.
				$section_config = array(
					'title'    => $section['title'],
					'priority' => ( isset( $section['priority'] ) ? $section['priority'] : 10 ),
					'type'     => ( isset( $section['type'] ) ? $section['type'] : 'default' ),
				);

				// Description.
				if ( ! empty( $section['description'] ) ) {
					$section_config['description'] = $section['description'];
				}

				// Maybe add panel.
				self::maybe_add_panel( $wp_customize, $section );

				// Maybe add section to panel.
				if ( ! empty( $section['panel'] ) ) {
					if ( 'woocommerce' === $section['panel'] ) {
						$section_config['panel'] = $section['panel'];
					} elseif ( 'tribe_customizer' === $section['panel'] ) {
						$section_config['panel'] = $section['panel'];
					} else {
						$section_config['panel'] = 'base_customizer_' . $section['panel'];
					}
				}
				$section_id = 'title_tagline' === $section_key || 'static_front_page' === $section_key || 'sidebar-widgets-product-filter' === $section_key || 'sidebar-widgets-header1' === $section_key || 'sidebar-widgets-header2' === $section_key || 'sidebar-widgets-footer1' === $section_key || 'sidebar-widgets-footer2' === $section_key || 'sidebar-widgets-footer3' === $section_key || 'sidebar-widgets-footer4' === $section_key || 'sidebar-widgets-footer5' === $section_key || 'sidebar-widgets-footer6' === $section_key || 'woocommerce_product_catalog_design' === $section_key || 'woocommerce_product_catalog' === $section_key || 'woocommerce_store_notice' === $section_key || 'woocommerce_store_notice_design' === $section_key ? $section_key : 'base_customizer_' . $section_key;
				// Undo widgets if using gutenberg plugin or 5.8 beta.
				if ( ( defined( 'GUTENBERG_VERSION' ) && version_compare( GUTENBERG_VERSION, '10.6.2', '>' ) ) || version_compare( substr( get_bloginfo( 'version' ), 0, 3 ), '5.8', '>=' ) ) {
					if ( 'sidebar-widgets-footer1' === $section_key || 'sidebar-widgets-footer2' === $section_key || 'sidebar-widgets-footer3' === $section_key || 'sidebar-widgets-footer4' === $section_key || 'sidebar-widgets-footer5' === $section_key || 'sidebar-widgets-footer6' === $section_key || 'sidebar-widgets-header1' === $section_key || 'sidebar-widgets-header2' === $section_key || 'sidebar-widgets-product-filter' === $section_key ) {
						$section_id = 'base_customizer_' . $section_key;
					}
				}
				// Register section.
				$wp_customize->add_section( $section_id, $section_config );

				// Track which sections were added.
				self::$sections_added[] = $section_key;
			}
		}
	}
	/**
	 * Maybe add panel
	 *
	 * @access public
	 * @param object $wp_customize the object.
	 * @param array  $child the child item.
	 * @return void
	 */
	public static function maybe_add_panel( $wp_customize, $child ) {
		// Get panels.
		$panels = self::get_panels();
		// Check if panel is set and exists.
		if ( ! empty( $child['panel'] ) && isset( $panels[ $child['panel'] ] ) ) {

			// Reference current panel key.
			$panel_key = $child['panel'];

			// Check if panel was not added yet.
			if ( ! in_array( $panel_key, self::$panels_added, true ) ) {

				// Reference current panel.
				$panel = $panels[ $panel_key ];

				// Panel config.
				$panel_config = array(
					'title'      => $panel['title'],
					'priority'   => ( isset( $panel['priority'] ) ? $panel['priority'] : 10 ),
					'capability' => self::get_capability(),
				);

				// Panel description.
				if ( ! empty( $panel['description'] ) ) {
					$panel_config['description'] = $panel['description'];
				}

				// Register panel.
				$wp_customize->add_panel( 'base_customizer_' . $panel_key, $panel_config );

				// Track which panels were added.
				self::$panels_added[] = $panel_key;
			}
		}
	}
	/**
	 * Add controls, sections and panels
	 *
	 * @access public
	 * @param object $wp_customize the customizer object.
	 * @return void
	 */
	public function add_controls( $wp_customize ) {
		// Iterate over settings.
		foreach ( self::$settings as $setting_key => $setting ) {

			// Maybe add section.
			self::maybe_add_section( $wp_customize, $setting );

			// Maybe add panel.
			self::maybe_add_panel( $wp_customize, $setting );

			// Maybe add context.
			if ( isset( $setting['context'] ) && ! empty( $setting['context'] ) ) {
				self::maybe_add_context( $setting_key, $setting );
			}

			// Maybe add live changes.
			if ( isset( $setting['live_method'] ) && ! empty( $setting['live_method'] ) ) {
				if ( isset( $setting['settings'] ) && is_array( $setting['settings'] ) ) {
					foreach ( $setting['settings'] as $setting_item => $id ) {
						self::maybe_add_live( $id, $setting );
					}
				} else {
					self::maybe_add_live( $setting_key, $setting );
				}
			}

			// Maybe add partial.
			if ( isset( $setting['partial'] ) && ! empty( $setting['partial'] ) ) {
				self::maybe_add_partial( $wp_customize, $setting_key, $setting );
			}

			// Maybe add choices.
			if ( isset( $setting['choices'] ) && ! empty( $setting['choices'] ) ) {
				self::maybe_add_choices( $setting_key, $setting );
			}

			// Get control class name (none, color, upload, image).
			if ( isset( $setting['control_type'] ) && ! empty( $setting['control_type'] ) ) {
				if ( substr( $setting['control_type'], 0, 5 ) === 'base_' ) {
					$control_class = 'Base_Control_' . ucfirst( str_replace( 'base_', '', str_replace( '_control', '', $setting['control_type'] ) ) );
				} else {
					$control_class = 'WP_Customize_' . ucfirst( $setting['control_type'] ) . '_Control';
				}
			} else {
				$control_class = 'WP_Customize_Control';
			}
			if ( isset( $setting['settings'] ) && false === $setting['settings'] ) {
				$control_config = array(
					'settings' => array(),
					'priority' => isset( $setting['priority'] ) ? $setting['priority'] : 10,
				);
			} else {
				// Control configuration.
				$control_config = array(
					'settings'        => $setting_key,
					'capability'      => self::get_capability(),
					'priority'        => isset( $setting['priority'] ) ? $setting['priority'] : 10,
					'active_callback' => ( isset( $setting['active_callback'] ) ? array( $this, 'active_callback' ) : '__return_true' ),
				);
			}
			// Title.
			if ( ! empty( $setting['label'] ) ) {
				$control_config['label'] = $setting['label'];
			}
			// Description.
			if ( ! empty( $setting['description'] ) ) {
				$control_config['description'] = $setting['description'];
			}
			// Add control to section.
			if ( ! empty( $setting['section'] ) ) {
				$control_config['section'] = ( 'title_tagline' === $setting['section'] || 'static_front_page' === $setting['section'] || 'sidebar-widgets-product-filter' === $setting['section'] || 'sidebar-widgets-header1' === $setting['section'] || 'sidebar-widgets-header2' === $setting['section'] || 'sidebar-widgets-footer1' === $setting['section'] || 'sidebar-widgets-footer2' === $setting['section'] || 'sidebar-widgets-footer3' === $setting['section'] || 'sidebar-widgets-footer4' === $setting['section'] || 'sidebar-widgets-footer5' === $setting['section'] || 'sidebar-widgets-footer6' === $setting['section'] || 'woocommerce_product_catalog' === $setting['section'] || 'woocommerce_product_catalog_design' === $setting['section'] || 'woocommerce_store_notice' === $setting['section'] || 'woocommerce_store_notice_design' === $setting['section'] ? $setting['section'] : 'base_customizer_' . $setting['section'] );
				// Undo widgets if using gutenberg plugin or 5.8 beta.
				if ( ( defined( 'GUTENBERG_VERSION' ) && version_compare( GUTENBERG_VERSION, '10.6.2', '>' ) ) || version_compare( substr( get_bloginfo( 'version' ), 0, 3 ), '5.8', '>=' ) ) {
					if ( 'sidebar-widgets-footer1' === $setting['section'] || 'sidebar-widgets-footer2' === $setting['section'] || 'sidebar-widgets-footer3' === $setting['section'] || 'sidebar-widgets-footer4' === $setting['section'] || 'sidebar-widgets-footer5' === $setting['section'] || 'sidebar-widgets-footer6' === $setting['section'] || 'sidebar-widgets-header1' === $setting['section'] || 'sidebar-widgets-header2' === $setting['section'] || 'sidebar-widgets-product-filter' === $setting['section'] ) {
						$control_config['section'] = 'base_customizer_' . $setting['section'];
					}
				}
			}
			// Add control to panel.
			if ( ! empty( $setting['panel'] ) ) {
				if ( 'woocommerce' === $control_config['panel'] ) {
					$control_config['panel'] = $setting['panel'];
				} elseif ( 'tribe_customizer' === $control_config['panel'] ) {
					$control_config['panel'] = $setting['panel'];
				} else {
					$control_config['panel'] = 'base_customizer_' . $setting['panel'];
				}
			}
			// Add custom field type.
			if ( ! empty( $setting['type'] ) ) {
				$control_config['type'] = $setting['type'];
			}
			// Add select field options.
			if ( ! empty( $setting['choices'] ) ) {
				$control_config['choices'] = $setting['choices'];
			}
			// Defaults.
			if ( ! empty( $setting['default'] ) ) {
				$control_config['default'] = $setting['default'];
			}
			// Input attributes.
			if ( ! empty( $setting['input_attrs'] ) ) {
				$control_config['input_attrs'] = $setting['input_attrs'];
			}
			// Input attributes.
			if ( ! empty( $setting['mime_type'] ) ) {
				$control_config['mime_type'] = $setting['mime_type'];
			}
			// Builder.
			if ( ! empty( $setting['labels'] ) ) {
				$control_config['labels'] = $setting['labels'];
			}
			if ( isset( $setting['settings'] ) && ! empty( $setting['settings'] ) && is_array( $setting['settings'] ) ) {
				$settings_array = array();
				foreach ( $setting['settings'] as $s_key => $s_id ) {
					$settings_array[ $s_key ] = $s_id;
				}
				$control_config['settings'] = $settings_array;
			}
			if ( ! empty( $opt_name_key = apply_filters( 'base_settings_key_custom_mapping', '', $setting_key ) ) ) {
				$setting_key = $opt_name_key . '[' . $setting_key . ']';
				if ( isset( $setting['settings'] ) && ! empty( $setting['settings'] ) && is_array( $setting['settings'] ) ) {
					$settings_array = array();
					foreach ( $setting['settings'] as $s_key => $s_id ) {
						$settings_array[ $s_key ] = $opt_name_key . '[' . $s_id . ']';
					}
					$control_config['settings'] = $settings_array;
				} else {
					if ( isset( $setting['settings'] ) && false === $setting['settings'] ) {
						$control_config['settings'] = array();
					} else {
						$control_config['settings'] = $setting_key;
					}
				}
			}
			// Add control.
			$wp_customize->add_control( new $control_class( $wp_customize, $setting_key, $control_config ) );
		}
	}
	/**
	 * Call back to hide or show field.
	 *
	 * @access public
	 * @param object $object the customizer object.
	 */
	public static function active_callback( $object ) {
		if ( ! isset( $object->setting->id ) && 'base_builder_control' !== $object->type ) {
			return true;
		}
		if ( 'base_builder_control' === $object->type ) {
			$id = str_replace( webapp()->get_option_name() . '_', '', $object->id );
		} else {
			$opt_name = explode( '[', $object->setting->id );
			$opt_name = $opt_name[0];
			$id       = str_replace( $opt_name . '[', '', str_replace( ']', '', $object->setting->id ) );
		}
		$settings = self::$settings;
		if ( ! isset( $settings[ $id ] ) ) {
			return true;
		}
		$show = true;
		if ( ! isset( $settings[ $id ]['active_callback'][0] ) && 3 === count( $settings[ $id ]['active_callback'] ) ) {
			$field_id    = $settings[ $id ]['active_callback']['id'];
			$compare     = $settings[ $id ]['active_callback']['compare'];
			$value       = $settings[ $id ]['active_callback']['value'];
			$field_value = webapp()->option( $field_id );
			$show        = self::active_callback_compare( $compare, $value, $field_value );
		} elseif ( is_array( $settings[ $id ]['active_callback'][0] ) ) {
			foreach ( $settings[ $id ]['active_callback'] as $required ) {
				$field_id    = $required['id'];
				$compare     = $required['compare'];
				$value       = $required['value'];
				$field_value = webapp()->option( $field_id );
				$show        = self::active_callback_compare( $compare, $value, $field_value );
				if ( ! $show ) {
					return false;
				}
			}
		}
		return $show;
	}
	/**
	 * Call back to compare.
	 *
	 * @access public
	 * @param string $compare the compare item.
	 * @param string $value the first item.
	 * @param string $field_value the second item.
	 */
	public static function active_callback_compare( $compare, $value, $field_value ) {
		$show = true;
		switch ( $compare ) {
			case '==':
			case '=':
			case 'equals':
			case 'equal':
				if ( is_array( $value ) ) {
					foreach ( $value as $sub_value ) {
						if ( $field_value === $sub_value ) {
							$show = true;
							break;
						} else {
							$show = false;
						}
					}
				} else {
					$show = ( $field_value === $value ) ? true : false;
				}
				break;

			case '!=':
			case 'not equal':
				$show = ( $field_value !== $value ) ? true : false;
				break;
		}
		return $show;
	}
	/**
	 * Get Builder Choices.
	 *
	 * @access public
	 */
	public static function get_builder_choices() {
		return apply_filters( 'base_theme_customizer_control_choices', self::$choices );
	}
	/**
	 * Enqueue Customizer scripts
	 *
	 * @access public
	 * @return void
	 */
	public function enqueue_customizer_scripts() {
		// Make it possible to prevent gutenberg from loading in the customizer for plugins that are using the customizer for other purposes.
		if ( isset( $_GET['wpum_email_customizer'] ) || apply_filters( 'prevent_base_customizer_scripts', false ) ) {
			return;
		}
		$palette_presets = '{"basic":[{"color":"#2B6CB0"},{"color":"#215387"},{"color":"#222222"},{"color":"#3B3B3B"},{"color":"#515151"},{"color":"#626262"},{"color":"#E1E1E1"},{"color":"#F7F7F7"},{"color":"#ffffff"}],"palette":[{"color":"#255FDD"},{"color":"#00F2FF"},{"color":"#1A202C"},{"color":"#2D3748"},{"color":"#4A5568"},{"color":"#718096"},{"color":"#EDF2F7"},{"color":"#F7FAFC"},{"color":"#ffffff"}],"first-palette":[{"color":"#3296ff"},{"color":"#003174"},{"color":"#ffffff"},{"color":"#f7fafc"},{"color":"#edf2f7"},{"color":"#cbd2d9"},{"color":"#1A202C"},{"color":"#252c39"},{"color":"#2D3748"}],"orange":[{"color":"#e47b02"},{"color":"#ed8f0c"},{"color":"#1f2933"},{"color":"#3e4c59"},{"color":"#52606d"},{"color":"#7b8794"},{"color":"#f3f4f7"},{"color":"#f9f9fb"},{"color":"#ffffff"}],"third-palette":[{"color":"#E21E51"},{"color":"#4d40ff"},{"color":"#040037"},{"color":"#032075"},{"color":"#514d7c"},{"color":"#666699"},{"color":"#deddeb"},{"color":"#efeff5"},{"color":"#f8f9fa"}],"forth-palette":[{"color":"#E21E51"},{"color":"#4d40ff"},{"color":"#f8f9fa"},{"color":"#efeff5"},{"color":"#deddeb"},{"color":"#c3c2d6"},{"color":"#040037"},{"color":"#221e5b"},{"color":"#514D7C"}],"fifth-palette":[{"color":"#049f82"},{"color":"#008f72"},{"color":"#222222"},{"color":"#353535"},{"color":"#454545"},{"color":"#676767"},{"color":"#eeeeee"},{"color":"#f7f7f7"},{"color":"#ffffff"}],"sixth-palette":[{"color":"#dd6b20"},{"color":"#cf3033"},{"color":"#27241d"},{"color":"#423d33"},{"color":"#504a40"},{"color":"#625d52"},{"color":"#e8e6e1"},{"color":"#faf9f7"},{"color":"#ffffff"}]}';
		$fonts = array(
			array(
				'name' => 'Montserrat & Source Sans Pro',
				'hfont' => 'Montserrat',
				'bfont' => 'Source Sans Pro',
				'hv'    => array( '100', '100italic', '200', '200italic', '300', '300italic', 'regular', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic' ),
				'img'  => get_template_directory_uri() . '/assets/images/fonts/montserrat.jpg',
			),
			array(
				'name' => 'Libre Franklin & Libre Baskerville',
				'hfont' => 'Libre Franklin',
				'bfont' => 'Libre Baskerville',
				'hv'    => array( '100', '100italic', '200', '200italic', '300', '300italic', 'regular', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic' ),
				'img'  => get_template_directory_uri() . '/assets/images/fonts/libre.jpg',
			),
			array(
				'name' => 'Proza Libre & Open Sans',
				'hfont' => 'Proza Libre',
				'bfont' => 'Open Sans',
				'hv'    => array( 'regular', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic' ),
				'img'  => get_template_directory_uri() . '/assets/images/fonts/proza.jpg',
			),
			array(
				'name' => 'Work Sans & Work Sans',
				'hfont' => 'Work Sans',
				'bfont' => 'Work Sans',
				'hv'    => array( '100', '100italic', '200', '200italic', '300', '300italic', 'regular', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic' ),
				'img'  => get_template_directory_uri() . '/assets/images/fonts/worksans.jpg',
			),
			array(
				'name' => 'Josefin Sans & Lato',
				'hfont' => 'Josefin Sans',
				'bfont' => 'Lato',
				'hv'    => array( '100', '100italic', '200', '200italic', '300', '300italic', 'regular', 'italic', '500', '500italic', '600', '600italic', '700', '700italic' ),
				'img'  => get_template_directory_uri() . '/assets/images/fonts/josefin.jpg',
			),
			array(
				'name' => 'Oswald & Open Sans',
				'hfont' => 'Oswald',
				'bfont' => 'Open Sans',
				'hv'    => array( '200', '300', 'regular', '500', '600', '700' ),
				'img'  => get_template_directory_uri() . '/assets/images/fonts/oswald.jpg',
			),
			array(
				'name' => 'Nunito & Roboto',
				'hfont' => 'Nunito',
				'bfont' => 'Roboto',
				'hv'    => array( '200', '200italic', '300', '300italic', 'regular', 'italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic' ),
				'img'  => get_template_directory_uri() . '/assets/images/fonts/nunito.jpg',
			),
			array(
				'name' => 'Rubik & Karla',
				'hfont' => 'Rubik',
				'bfont' => 'Karla',
				'hv'    => array( '300', '300italic', 'regular', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic' ),
				'img'  => get_template_directory_uri() . '/assets/images/fonts/rubik.jpg',
			),
			array(
				'name' => 'Lora & Merriweather',
				'hfont' => 'Lora',
				'bfont' => 'Merriweather',
				'hv'    => array( 'regular', 'italic', '500', '500italic', '600', '600italic', '700', '700italic' ),
				'img'  => get_template_directory_uri() . '/assets/images/fonts/lora.jpg',
			),
			array(
				'name' => 'Playfair Display & Raleway',
				'hfont' => 'Playfair Display',
				'bfont' => 'Raleway',
				'hv'    => array( 'regular', 'italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic' ),
				'img'  => get_template_directory_uri() . '/assets/images/fonts/playfair.jpg',
			),
			array(
				'name' => 'Antic Didone & Raleway',
				'hfont' => 'Antic Didone',
				'bfont' => 'Raleway',
				'hv'    =>array( 'regular' ),
				'img'  => get_template_directory_uri() . '/assets/images/fonts/antic.jpg',
			),
			array(
				'name' => 'Gilda Display & Raleway',
				'hfont' => 'Gilda Display',
				'bfont' => 'Raleway',
				'hv'    => array( 'regular' ),
				'img'  => get_template_directory_uri() . '/assets/images/fonts/gilda.jpg',
			),
		);
		$path     = get_template_directory_uri() . '/inc/customizer/';
		$dir_path = get_template_directory() . '/inc/customizer/';
		wp_enqueue_style( 'base-customizer-styles', $path . 'css/base-customizer.css', false, AVANAM_VERSION );
		if ( is_rtl() ) {
			wp_enqueue_style( 'base-customizer-rtl', $path . 'css/rtl-base-customizer.css', false, AVANAM_VERSION );
		}
		// Enqueue Customizer script.
		$google_font_variants = include $dir_path . 'google-font-variants.php';
		$editor_dependencies = array(
			'jquery',
			'customize-controls',
			'wp-i18n',
			'wp-components',
			'wp-edit-post',
			'wp-element',
			'lodash',
			'react',
			'react-dom',
			'wp-compose',
			'wp-polyfill',
			'wp-primitives',
		);
		// Overide Core Text scripts because of naming issues.
		wp_enqueue_script( 'customizer-text-widgets', get_template_directory_uri() . '/assets/js/text-widgets.min.js', array( 'jquery', 'backbone', 'editor', 'wp-util', 'wp-a11y', 'text-widgets' ), AVANAM_VERSION, false );
		wp_enqueue_script( 'base-customizer-controls', get_template_directory_uri() . '/assets/js/admin/customizer.js', $editor_dependencies, AVANAM_VERSION, true );
		wp_enqueue_style( 'base-customizer-controls', $path . 'react/build/controls.css', array( 'wp-components' ), AVANAM_VERSION );
		wp_localize_script(
			'base-customizer-controls',
			'baseCustomizerControlsData',
			array(
				'contexts'           => self::get_control_contexts(),
				'choices'            => self::get_builder_choices(),
				'palette'            => webapp()->get_palette_for_customizer(),
				'gfontvars'          => $google_font_variants,
				'cfontvars'          => apply_filters( 'base_theme_custom_fonts', array() ),
				'palettePresets'     => apply_filters( 'base_theme_palette_presets', $palette_presets ),
				'flushFonts'         => wp_create_nonce( 'base-local-fonts-flush' ),
				'fontPairs'          => apply_filters( 'base_font_pairs_array', $fonts ),
				'source'             => apply_filters( 'base_settings_extra_source', false ),
				'blockWidgets'       => ( ( defined( 'GUTENBERG_VERSION' ) && version_compare( GUTENBERG_VERSION, '10.6.2', '>' ) ) || version_compare( substr( get_bloginfo( 'version' ), 0, 3 ), '5.8', '>=' ) ? true : false ),
			)
		);
		$css = ':root {
			--global-palette1: ' . webapp()->palette_option( 'palette1' ) . ';
			--global-palette2: ' . webapp()->palette_option( 'palette2' ) . ';
			--global-palette3: ' . webapp()->palette_option( 'palette3' ) . ';
			--global-palette4: ' . webapp()->palette_option( 'palette4' ) . ';
			--global-palette5: ' . webapp()->palette_option( 'palette5' ) . ';
			--global-palette6: ' . webapp()->palette_option( 'palette6' ) . ';
			--global-palette7: ' . webapp()->palette_option( 'palette7' ) . ';
			--global-palette8: ' . webapp()->palette_option( 'palette8' ) . ';
			--global-palette9: ' . webapp()->palette_option( 'palette9' ) . ';
			--global-palette-highlight: ' . $this->render_color( webapp()->sub_option( 'link_color', 'highlight' ) ) . ';
			--global-palette-highlight-alt: ' . $this->render_color( webapp()->sub_option( 'link_color', 'highlight-alt' ) ) . ';
			--global-palette-highlight-alt2: ' . $this->render_color( webapp()->sub_option( 'link_color', 'highlight-alt2' ) ) . ';
			--global-palette-btn: ' . $this->render_color( webapp()->sub_option( 'buttons_color', 'color' ) ) . ';
			--global-palette-btn-hover: ' . $this->render_color( webapp()->sub_option( 'buttons_color', 'hover' ) ) . ';
			--global-palette-btn-bg: ' . $this->render_color( webapp()->sub_option( 'buttons_background', 'color' ) ) . ';
			--global-palette-btn-bg-hover: ' . $this->render_color( webapp()->sub_option( 'buttons_background', 'hover' ) ) . ';
			--global-base-font: ' . webapp()->sub_option( 'base_font', 'family' ) . ';
			--global-heading-font: ' . ( 'inherit' !== webapp()->sub_option( 'heading_font', 'family' ) ? webapp()->sub_option( 'heading_font', 'family' ) : 'var(--global-base-font)' ) . ';
		}';
		wp_add_inline_style( 'base-customizer-controls', wp_strip_all_tags( $css ) );
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'base-customizer-controls', 'avanam' );
		}
	}
	/**
	 * Generates the color output.
	 *
	 * @param string $color any color attribute.
	 * @return string
	 */
	public function render_color( $color ) {
		if ( empty( $color ) ) {
			return false;
		}
		if ( ! is_array( $color ) && strpos( $color, 'palette' ) !== false ) {
			$color = 'var(--global-' . $color . ')';
		}
		return $color;
	}

	/**
	 * Enqueues JavaScript to make Customizer preview reload changes asynchronously.
	 */
	public function action_enqueue_customize_preview_scripts() {
		$path = get_template_directory_uri() . '/inc/customizer/';
		wp_enqueue_style( 'base-customizer-preview', $path . 'css/base-customizer-preview.css', false, AVANAM_VERSION );
		wp_enqueue_script( 'base-webfont-js', 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js', array( 'jquery' ), '1.6.26', true );
		wp_enqueue_script( 'base-customizer-preview', $path . 'js/base-customizer-preview.min.js', array( 'customize-preview' ), AVANAM_VERSION, true );
		wp_localize_script(
			'base-customizer-preview',
			'baseCustomizerPreviewData',
			array(
				'liveControl' => self::get_live_control_data(),
			)
		);
	}

	/**
	 * Get Customizer Control Contexts
	 *
	 * @access public
	 * @return array
	 */
	public static function get_control_contexts() {
		// Return contexts.
		return apply_filters( 'base_theme_customizer_control_contexts', self::$contexts );
	}

	/**
	 * Get Customizer Live Control Data
	 *
	 * @access public
	 * @return array
	 */
	public static function get_live_control_data() {
		// Return contexts.
		return apply_filters( 'base_theme_customizer_live_control_data', self::$live_control );
	}

	/**
	 * Maybe add Context
	 *
	 * @access public
	 * @param string $setting_key the item setting key.
	 * @param array  $setting the item setting.
	 * @return void
	 */
	public static function maybe_add_context( $setting_key, $setting ) {
		if ( isset( $setting['context'] ) && ! empty( $setting['context'] ) ) {
			// Add to contexts array.
			if ( ! empty( $opt_name_key = apply_filters( 'base_settings_key_custom_mapping', '', $setting_key ) ) ) {
				$setting_key = $opt_name_key . '[' . $setting_key . ']';
			}
			self::$contexts[ $setting_key ] = $setting['context'];
		}
	}

	/**
	 * Maybe add Live Control Data
	 *
	 * @access public
	 * @param string $setting_key the item setting key.
	 * @param array  $setting the item setting.
	 * @return void
	 */
	public static function maybe_add_live( $setting_key, $setting ) {
		if ( isset( $setting['live_method'] ) && ! empty( $setting['live_method'] ) ) {
			// Add to live_method array.
			$setting_id = $setting_key;
			if ( ! empty( $opt_name_key = apply_filters( 'base_settings_key_custom_mapping', '', $setting_key ) ) ) {
				$setting_id = $opt_name_key . '[' . $setting_key . ']';
			}
			if ( isset( $setting['settings'] ) && is_array( $setting['settings'] ) ) {
				self::$live_control[ $setting_id ] = $setting['live_method'][ $setting_key ];
			} else {
				self::$live_control[ $setting_id ] = $setting['live_method'];
			}
		}
	}

	/**
	 * Maybe add Partial Control Data
	 *
	 * @access public
	 * @param object $wp_customize the object.
	 * @param string $setting_key the item setting key.
	 * @param array  $setting the item setting.
	 * @return void
	 */
	public static function maybe_add_partial( $wp_customize, $setting_key, $setting ) {
		if ( isset( $setting['partial'] ) && ! empty( $setting['partial'] ) ) {
			if ( isset( $wp_customize->selective_refresh ) ) {
				if ( ! empty( $opt_name_key = apply_filters( 'base_settings_key_custom_mapping', '', $setting_key ) ) ) {
					$setting_key = $opt_name_key . '[' . $setting_key . ']';
				}
				$wp_customize->selective_refresh->add_partial(
					$setting_key,
					$setting['partial']
				);
			}
		}
	}

	/**
	 * Maybe add Choices
	 *
	 * @access public
	 * @param string $setting_key the item setting key.
	 * @param array  $setting the item setting.
	 * @return void
	 */
	public static function maybe_add_choices( $setting_key, $setting ) {
		if ( isset( $setting['choices'] ) && ! empty( $setting['choices'] ) ) {
			// Add to choices array.
			self::$choices[ $setting_key ] = $setting['choices'];
		}
	}
	/**
	 * Show footer widgets as active
	 *
	 * @param bool   $active wether the panel is active.
	 * @param object $section the section object.
	 */
	public function active_footer_widgets( $active, $section ) {
		if ( strpos( $section->id, 'widgets-footer' ) ) {
			$active = true;
		}

		return $active;
	}

}
Theme_Customizer::get_instance();
