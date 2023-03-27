<?php
/**
 * Base\BBPress\Component class
 *
 * @package Base
 */

namespace Base\BBPress;

use Base\Base_CSS;
use Base\Component_Interface;
use Base_Blocks_Frontend;
use function Base\webapp;
use function bbp_is_single_user_profile;
use function is_bbpress;
use function bbp_is_search;
use function add_action;
use function add_filter;
use function have_posts;
use function the_post;
use function is_search;
use function get_template_part;
use function get_post_type;

/**
 * Class for adding bbpress plugin support.
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
		return 'bbpress';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', array( $this, 'bbpress_styles' ), 60 );
		add_action( 'bbpress_end_form_search', array( $this, 'add_search_icon' ) );
		add_filter( 'base_post_layout', array( $this, 'bbpress_user_layout' ), 99 );
		add_action( 'wp_head', array( $this, 'frontend_gfonts' ), 80 );
		add_filter( 'post_class', array( $this, 'post_class' ), 10, 3 );
		add_filter( 'base_dynamic_css', array( $this, 'dynamic_css' ), 20 );
		add_filter( 'bbp_breadcrumb_separator', array( $this, 'change_bbpress_separator' ), 10 );
		add_filter( 'bbp_get_reply_admin_links', array( $this, 'admin_reply_links' ), 10, 3 );
		add_filter( 'bbp_get_topic_admin_links', array( $this, 'admin_reply_links' ), 10, 3 );
		add_action( 'base_single_title_info_area', array( $this, 'bbpress_single_meta' ) );
		add_filter( 'base_theme_options_defaults', array( $this, 'bbpress_option_defaults' ) );
	}
	/**
	 * Filters admin links output.
	 *
	 * @param string $retval A string of the output.
	 * @param array  $r   An array of the output.
	 * @param array  $args The args.
	 */
	public function admin_reply_links( $retval, $r, $args ) {
		$retval = '<div class="bbpress-admin-settings-container">' . webapp()->get_icon( 'settings', __( 'Settings', 'avanam' ), false ) . $retval . '</div>';
		return $retval;
	}
	/**
	 * Filters the class for bbpress.
	 *
	 * @param array $classes An array of post class names.
	 * @param array $class   An array of additional class names added to the post.
	 * @param int    $post_id The post ID..
	 */
	public function post_class( $classes, $class, $post_ID ) {
		if ( is_admin() ) {
			return $classes;
		}
		if ( is_bbpress() && ( 'topic' === get_post_type() || bbp_is_search() ) ) {
			// $post = get_post( $post_id );
			// if ( 'topic' === $post->post_type ) {
				//print_r( $classes );
			$entry = array_search( 'entry', $classes );
			if ( is_numeric( $entry ) ) {
				unset( $classes[ $entry ] );
			}
			$bg = array_search( 'content-bg', $classes );
			if ( is_numeric( $bg ) ) {
				unset( $classes[ $bg ] );
			}
			$single = array_search( 'single-entry', $classes );
			if ( is_numeric( $single ) ) {
				unset( $classes[ $single ] );
			}
			//}
		}
		return $classes;
	}
	/**
	 * Add Search Icon.
	 */
	public function add_search_icon() {
		echo '<div class="base-search-icon-wrap">' . webapp()->get_icon( 'search', '', false ) . '</div>';
	}
	/**
	 * Change the breadcrumb separator.
	 *
	 * @param string $sep the separator.
	 */
	public function change_bbpress_separator( $sep ) {
		$sep = ' / ';

		return $sep;
	}
	/**
	 * Renders the layout for bbpress.
	 *
	 * @param array $layout the layout array.
	 */
	public function bbpress_user_layout( $layout ) {
		if ( is_bbpress() && bbp_is_single_user() ) {
			$layout = wp_parse_args(
				array(
					'feature'          => 'hide',
					'comments'         => 'hide',
					'navigation'       => 'hide',
					'transparent'      => 'disable',
				),
				$layout
			);
		}

		return $layout;
	}
	/**
	 * Add some css styles for learndash
	 */
	public function bbpress_styles() {
		if ( is_bbpress() ) {
			wp_enqueue_style( 'base-bbpress', get_theme_file_uri( '/assets/css/bbpress.min.css' ), array(), AVANAM_VERSION );
		}
	}
	/**
	 * Add meta below title.
	 */
	public function bbpress_single_meta() {
		if ( 'topic' === get_post_type() ) {
			echo '<div class="bbpress-topic-meta entry-meta">';
			echo '<span class="bbpress-back-to-forum-wrap"><a href="' . esc_url( bbp_get_forum_permalink( bbp_get_topic_forum_id() ) ) . '" class="bbpress-back-to-forum">' . webapp()->get_icon( 'arrow-left-alt', '', true ) . ' ' . __( 'Back to:', 'avanam' ) . ' ' . bbp_get_forum_title( bbp_get_topic_forum_id() ) . '</a></span>';
			echo '<span class="bbpress-meta-replies-wrap"><span class="bbpress-meta-replies">' . bbp_get_topic_reply_count() . ' ' . esc_html__( 'Replies', 'avanam' ) . '</span></span>';
			echo '<span class="bbpress-meta-subscribe-wrap">';
			bbp_topic_subscription_link( array( 'before' => '' ) );
			echo '</span>';
			echo '<span class="bbpress-meta-favorite-wrap">';
			bbp_topic_favorite_link( array( 'before' => '' ) );
			echo '</span>';
			do_action( 'base_bbpress_single_topic_meta' );
			echo '</div>';
		}
	}
	/**
	 * Adds bbpress options into the defaults array.
	 *
	 * @param array $defaults the bbpress options.
	 */
	public function bbpress_option_defaults( $defaults ) {
		$bbpress_add = array(
			'forum_layout'             => 'normal',
			'forum_content_style'      => 'boxed',
			'forum_vertical_padding'   => 'show',
			'forum_sidebar_id'         => 'sidebar-primary',
			'forum_background'         => '',
			'forum_content_background' => '',
			'forum_title'              => true,
			'forum_title_layout'       => 'normal',
			'forum_title_height'       => array(
				'size' => array(
					'mobile'  => '',
					'tablet'  => '',
					'desktop' => '',
				),
				'unit' => array(
					'mobile'  => 'px',
					'tablet'  => 'px',
					'desktop' => 'px',
				),
			),
			'forum_title_inner_layout' => 'standard',
			'forum_title_background'   => array(
				'desktop' => array(
					'color' => '',
				),
			),
			'forum_title_featured_image' => false,
			'forum_title_overlay_color'  => array(
				'color' => '',
			),
			'forum_title_top_border'    => array(),
			'forum_title_bottom_border' => array(),
			'forum_title_align'         => array(
				'mobile'  => '',
				'tablet'  => '',
				'desktop' => '',
			),
			'forum_title_font'   => array(
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
			'forum_title_breadcrumb_color' => array(
				'color' => '',
				'hover' => '',
			),
			'forum_title_breadcrumb_font'   => array(
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
			),
			'forum_title_search_width' => array(
				'size' => '300',
				'unit' => 'px',
			),
			'forum_title_search_border' => array(
				'width' => '',
				'unit'  => '',
				'style' => '',
				'color' => '',
			),
			'forum_title_search_color'       => array(
				'color'  => '',
				'hover'  => '',
			),
			'forum_title_search_background'       => array(
				'color'  => '',
				'hover'  => '',
			),
			'forum_title_search_typography'        => array(
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
			),
			'forum_title_search_margin' => array(
				'size'   => array( 1, 0, 1, 0 ),
				'unit'   => 'em',
				'locked' => false,
			),
			'forum_title_elements'           => array( 'title', 'breadcrumb', 'search', 'description' ),
			'forum_title_element_title' => array(
				'enabled' => true,
			),
			'forum_title_element_description' => array(
				'enabled' => false,
			),
			'forum_title_element_breadcrumb' => array(
				'enabled' => true,
				'show_title' => true,
			),
			'forum_title_element_search' => array(
				'enabled' => false,
			),
			// Forum Archive.
			'forum_archive_layout'             => 'normal',
			'forum_archive_content_style'      => 'boxed',
			'forum_archive_vertical_padding'   => 'show',
			'forum_archive_sidebar_id'         => 'sidebar-primary',
			'forum_archive_background'         => '',
			'forum_archive_content_background' => '',
			'forum_archive_title'              => true,
			'forum_archive_title_layout'       => 'above',
			'forum_archive_title_height'       => array(
				'size' => array(
					'mobile'  => '',
					'tablet'  => '',
					'desktop' => '',
				),
				'unit' => array(
					'mobile'  => 'px',
					'tablet'  => 'px',
					'desktop' => 'px',
				),
			),
			'forum_archive_title_inner_layout' => 'standard',
			'forum_archive_title_background'   => array(
				'desktop' => array(
					'color' => '',
				),
			),
			'forum_archive_title_featured_image' => false,
			'forum_archive_title_overlay_color'  => array(
				'color' => '',
			),
			'forum_archive_title_top_border'    => array(),
			'forum_archive_title_bottom_border' => array(),
			'forum_archive_title_align'         => array(
				'mobile'  => '',
				'tablet'  => '',
				'desktop' => '',
			),
			'forum_archive_title_font'   => array(
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
			'forum_archive_title_breadcrumb_color' => array(
				'color' => '',
				'hover' => '',
			),
			'forum_archive_title_breadcrumb_font'   => array(
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
			),
			'forum_archive_title_search_width' => array(
				'size' => '300',
				'unit' => 'px',
			),
			'forum_archive_title_search_border' => array(
				'width' => '',
				'unit'  => '',
				'style' => '',
				'color' => '',
			),
			'forum_archive_title_search_color'       => array(
				'color'  => '',
				'hover'  => '',
			),
			'forum_archive_title_search_background'       => array(
				'color'  => '',
				'hover'  => '',
			),
			'forum_archive_title_search_typography'        => array(
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
			),
			'forum_archive_title_search_margin' => array(
				'size'   => array( 1, 0, 1, 0 ),
				'unit'   => 'em',
				'locked' => false,
			),
			'forum_archive_title_elements'           => array( 'title', 'breadcrumb', 'search' ),
			'forum_archive_title_element_title' => array(
				'enabled' => true,
			),
			'forum_archive_title_element_breadcrumb' => array(
				'enabled' => false,
				'show_title' => true,
			),
			'forum_archive_title_element_search' => array(
				'enabled' => true,
			),
			// Topic.
			'topic_layout'             => 'normal',
			'topic_content_style'      => 'boxed',
			'topic_vertical_padding'   => 'show',
			'topic_sidebar_id'         => 'sidebar-primary',
			'topic_background'         => '',
			'topic_content_background' => '',
			'topic_title'              => true,
			'topic_title_layout'       => 'normal',
			'topic_title_height'       => array(
				'size' => array(
					'mobile'  => '',
					'tablet'  => '',
					'desktop' => '',
				),
				'unit' => array(
					'mobile'  => 'px',
					'tablet'  => 'px',
					'desktop' => 'px',
				),
			),
			'topic_title_inner_layout' => 'standard',
			'topic_title_background'   => array(
				'desktop' => array(
					'color' => '',
				),
			),
			'topic_title_overlay_color'  => array(
				'color' => '',
			),
			'topic_title_top_border'    => array(),
			'topic_title_bottom_border' => array(),
			'topic_title_align'         => array(
				'mobile'  => '',
				'tablet'  => '',
				'desktop' => '',
			),
			'topic_title_font'   => array(
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
			'topic_title_breadcrumb_color' => array(
				'color' => '',
				'hover' => '',
			),
			'topic_title_breadcrumb_font'   => array(
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
			),
			'topic_title_info_color' => array(
				'color' => '',
				'hover' => '',
			),
			'topic_title_info_font'   => array(
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
			),
			'topic_title_search_width' => array(
				'size' => '300',
				'unit' => 'px',
			),
			'topic_title_search_border' => array(
				'width' => '',
				'unit'  => '',
				'style' => '',
				'color' => '',
			),
			'topic_title_search_color'       => array(
				'color'  => '',
				'hover'  => '',
			),
			'topic_title_search_background'       => array(
				'color'  => '',
				'hover'  => '',
			),
			'topic_title_search_typography'        => array(
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
			),
			'topic_title_search_margin' => array(
				'size'   => array( 1, 0, 1, 0 ),
				'unit'   => 'em',
				'locked' => false,
			),
			'topic_title_elements'           => array( 'title', 'breadcrumb', 'search', 'info' ),
			'topic_title_element_title' => array(
				'enabled' => true,
			),
			'topic_title_element_info' => array(
				'enabled' => true,
			),
			'topic_title_element_breadcrumb' => array(
				'enabled' => false,
				'show_title' => true,
			),
			'topic_title_element_search' => array(
				'enabled' => false,
			),
		);
		$defaults = array_merge( $bbpress_add, $defaults );

		return $defaults;
	}
	/**
	 * Generates the dynamic css based on customizer options.
	 *
	 * @param string $css any custom css.
	 * @return string
	 */
	public function dynamic_css( $css ) {
		$generated_css = $this->generate_bbpress_css();
		if ( ! empty( $generated_css ) ) {
			$css .= "\n/* Base BBPress CSS */\n" . $generated_css;
		}
		return $css;
	}
	/**
	 * Generates the dynamic css based on page options.
	 *
	 * @return string
	 */
	public function generate_bbpress_css() {
		$css                    = new Base_CSS();
		$media_query            = array();
		$media_query['mobile']  = apply_filters( 'base_mobile_media_query', '(max-width: 767px)' );
		$media_query['tablet']  = apply_filters( 'base_tablet_media_query', '(max-width: 1024px)' );
		$media_query['desktop'] = apply_filters( 'base_desktop_media_query', '(min-width: 1025px)' );
		// Forum CSS.
		if ( is_bbpress() && is_singular( 'forum' ) ) {
			// Forum Backgrounds.
			$css->set_selector( 'body.single-forum' );
			$css->render_background( webapp()->sub_option( 'forum_background', 'desktop' ), $css );
			$css->set_selector( 'body.single-forum .content-bg, body.content-style-unboxed.single-forum .site' );
			$css->render_background( webapp()->sub_option( 'forum_content_background', 'desktop' ), $css );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( 'body.single-forum' );
			$css->render_background( webapp()->sub_option( 'forum_background', 'tablet' ), $css );
			$css->set_selector( 'body.single-forum .content-bg, body.content-style-unboxed.single-forum .site' );
			$css->render_background( webapp()->sub_option( 'forum_content_background', 'tablet' ), $css );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( 'body.single-forum' );
			$css->render_background( webapp()->sub_option( 'forum_background', 'mobile' ), $css );
			$css->set_selector( 'body.single-forum .content-bg, body.content-style-unboxed.single-forum .site' );
			$css->render_background( webapp()->sub_option( 'forum_content_background', 'mobile' ), $css );
			$css->stop_media_query();
			// Forum Title.
			$css->set_selector( '.forum-title h1' );
			$css->render_font( webapp()->option( 'forum_title_font' ), $css, 'heading' );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.forum-title h1' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'forum_title_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'forum_title_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'forum_title_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.forum-title h1' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'forum_title_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'forum_title_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'forum_title_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Forum Title Search.
			$css->set_selector( '.forum-title .bbp-search-form ::-webkit-input-placeholder' );
			$css->add_property( 'color', 'currentColor' );
			$css->add_property( 'opacity', '0.5' );
			$css->set_selector( '.forum-title .bbp-search-form ::placeholder' );
			$css->add_property( 'color', 'currentColor' );
			$css->add_property( 'opacity', '0.5' );
			$css->set_selector( '.forum-title .bbp-search-form' );
			$css->add_property( 'max-width', '100%' );
			$css->add_property( 'width', $css->render_size( webapp()->option( 'forum_title_search_width' ) ) );
			$css->set_selector( '.forum-title .bbp-search-form form' );
			$css->add_property( 'margin', $css->render_measure( webapp()->option( 'forum_title_search_margin' ) ) );
			$css->set_selector( '.forum-title .bbp-search-form input.search-field' );
			$css->render_font( webapp()->option( 'forum_title_search_typography' ), $css );
			$css->add_property( 'background', $css->render_color( webapp()->sub_option( 'forum_title_search_background', 'color' ) ) );
			$css->add_property( 'border', $css->render_border( webapp()->option( 'forum_title_search_border' ) ) );
			$css->add_property( 'border-color', $css->render_color( webapp()->sub_option( 'forum_title_search_border_color', 'color' ) ) );
			$css->set_selector( '.forum-title .bbp-search-form input.search-field, .forum-title .bbp-search-form .base-search-icon-wrap' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'forum_title_search_color', 'color' ) ) );
			$css->set_selector( '.forum-title .bbp-search-form input.search-field:focus' );
			$css->add_property( 'background', $css->render_color( webapp()->sub_option( 'forum_title_search_background', 'hover' ) ) );
			$css->add_property( 'border-color', $css->render_color( webapp()->sub_option( 'forum_title_search_border_color', 'hover' ) ) );
			$css->set_selector( '.forum-title .bbp-search-form input.search-field:focus, .forum-title .bbp-search-form input.search-submit:hover ~ .base-search-icon-wrap' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'forum_title_search_color', 'hover' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.forum-title .bbp-search-form input.search-field' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'forum_title_search_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'forum_title_search_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'forum_title_search_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.forum-title .bbp-search-form input.search-field' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'forum_title_search_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'forum_title_search_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'forum_title_search_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Forum Title Breadcrumbs.
			$css->set_selector( '.forum-title .base-breadcrumbs' );
			$css->render_font( webapp()->option( 'forum_title_breadcrumb_font' ), $css );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'forum_title_breadcrumb_color', 'color' ) ) );
			$css->set_selector( '.forum-title .base-breadcrumbs a:hover' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'forum_title_breadcrumb_color', 'hover' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.forum-title .base-breadcrumbs' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'forum_title_breadcrumb_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'forum_title_breadcrumb_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'forum_title_breadcrumb_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.forum-title .base-breadcrumbs' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'forum_title_breadcrumb_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'forum_title_breadcrumb_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'forum_title_breadcrumb_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Forum Title Description.
			$css->set_selector( '.forum-title .title-entry-description' );
			$css->render_font( webapp()->option( 'forum_title_description_font' ), $css );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'forum_title_description_color', 'color' ) ) );
			$css->set_selector( '.forum-title .title-entry-description a:hover' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'forum_title_description_color', 'hover' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.forum-title .title-entry-description' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'forum_title_description_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'forum_title_description_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'forum_title_description_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.forum-title .title-entry-description' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'forum_title_description_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'forum_title_description_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'forum_title_description_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Above Post Title.
			$css->set_selector( '.forum-hero-section .entry-hero-container-inner' );
			$css->render_background( webapp()->sub_option( 'forum_title_background', 'desktop' ), $css );
			$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'forum_title_top_border', 'desktop' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'forum_title_bottom_border', 'desktop' ) ) );
			$css->set_selector( '.entry-hero.forum-hero-section .entry-header' );
			$css->add_property( 'min-height', $css->render_range( webapp()->option( 'forum_title_height' ), 'desktop' ) );
			$css->set_selector( '.forum-hero-section .hero-section-overlay' );
			$css->add_property( 'background', $css->render_color_or_gradient( webapp()->sub_option( 'forum_title_overlay_color', 'color' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.forum-hero-section .entry-hero-container-inner' );
			$css->render_background( webapp()->sub_option( 'forum_title_background', 'tablet' ), $css );
			$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'forum_title_top_border', 'tablet' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'forum_title_bottom_border', 'tablet' ) ) );
			$css->set_selector( '.forum-hero-section .entry-header' );
			$css->add_property( 'min-height', $css->render_range( webapp()->option( 'forum_title_height' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.forum-hero-section .entry-hero-container-inner' );
			$css->render_background( webapp()->sub_option( 'forum_title_background', 'mobile' ), $css );
			$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'forum_title_top_border', 'mobile' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'forum_title_bottom_border', 'mobile' ) ) );
			$css->set_selector( '.entry-hero.forum-hero-section .entry-header' );
			$css->add_property( 'min-height', $css->render_range( webapp()->option( 'forum_title_height' ), 'mobile' ) );
			$css->stop_media_query();
		}
		if ( is_bbpress() && is_post_type_archive( 'forum' ) ) {
			// Forum Backgrounds.
			$css->set_selector( 'body.archive-forum' );
			$css->render_background( webapp()->sub_option( 'forum_archive_background', 'desktop' ), $css );
			$css->set_selector( 'body.archive-forum .content-bg, body.content-style-unboxed.archive-forum .site' );
			$css->render_background( webapp()->sub_option( 'forum_archive_content_background', 'desktop' ), $css );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( 'body.archive-forum' );
			$css->render_background( webapp()->sub_option( 'forum_archive_background', 'tablet' ), $css );
			$css->set_selector( 'body.archive-forum .content-bg, body.content-style-unboxed.archive-forum .site' );
			$css->render_background( webapp()->sub_option( 'forum_archive_content_background', 'tablet' ), $css );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( 'body.archive-forum' );
			$css->render_background( webapp()->sub_option( 'forum_archive_background', 'mobile' ), $css );
			$css->set_selector( 'body.archive-forum .content-bg, body.content-style-unboxed.archive-forum .site' );
			$css->render_background( webapp()->sub_option( 'forum_archive_content_background', 'mobile' ), $css );
			$css->stop_media_query();
			// Forum Title.
			$css->set_selector( '.forum-archive-title h1' );
			$css->render_font( webapp()->option( 'forum_archive_title_font' ), $css, 'heading' );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.forum-archive-title h1' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'forum_archive_title_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'forum_archive_title_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'forum_archive_title_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.forum-archive-title h1' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'forum_archive_title_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'forum_archive_title_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'forum_archive_title_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Forum Title Search.
			$css->set_selector( '.forum-archive-title .bbp-search-form ::-webkit-input-placeholder' );
			$css->add_property( 'color', 'currentColor' );
			$css->add_property( 'opacity', '0.5' );
			$css->set_selector( '.forum-archive-title .bbp-search-form ::placeholder' );
			$css->add_property( 'color', 'currentColor' );
			$css->add_property( 'opacity', '0.5' );
			$css->set_selector( '.forum-archive-title .bbp-search-form' );
			$css->add_property( 'max-width', '100%' );
			$css->add_property( 'width', $css->render_size( webapp()->option( 'forum_archive_title_search_width' ) ) );
			$css->set_selector( '.forum-archive-title .bbp-search-form form' );
			$css->add_property( 'margin', $css->render_measure( webapp()->option( 'forum_archive_title_search_margin' ) ) );
			$css->set_selector( '.forum-archive-title .bbp-search-form input.search-field' );
			$css->render_font( webapp()->option( 'forum_archive_title_search_typography' ), $css );
			$css->add_property( 'background', $css->render_color( webapp()->sub_option( 'forum_archive_title_search_background', 'color' ) ) );
			$css->add_property( 'border', $css->render_border( webapp()->option( 'forum_archive_title_search_border' ) ) );
			$css->add_property( 'border-color', $css->render_color( webapp()->sub_option( 'forum_archive_title_search_border_color', 'color' ) ) );
			$css->set_selector( '.forum-archive-title .bbp-search-form input.search-field, .forum-archive-title .bbp-search-form .base-search-icon-wrap' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'forum_archive_title_search_color', 'color' ) ) );
			$css->set_selector( '.forum-archive-title .bbp-search-form input.search-field:focus' );
			$css->add_property( 'background', $css->render_color( webapp()->sub_option( 'forum_archive_title_search_background', 'hover' ) ) );
			$css->add_property( 'border-color', $css->render_color( webapp()->sub_option( 'forum_archive_title_search_border_color', 'hover' ) ) );
			$css->set_selector( '.forum-archive-title .bbp-search-form input.search-field:focus, .forum-archive-title .bbp-search-form input.search-submit:hover ~ .base-search-icon-wrap' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'forum_archive_title_search_color', 'hover' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.forum-archive-title .bbp-search-form input.search-field' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'forum_archive_title_search_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'forum_archive_title_search_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'forum_archive_title_search_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.forum-archive-title .bbp-search-form input.search-field' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'forum_archive_title_search_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'forum_archive_title_search_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'forum_archive_title_search_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Forum Title Breadcrumbs.
			$css->set_selector( '.forum-archive-title .base-breadcrumbs' );
			$css->render_font( webapp()->option( 'forum_archive_title_breadcrumb_font' ), $css );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'forum_archive_title_breadcrumb_color', 'color' ) ) );
			$css->set_selector( '.forum-archive-title .base-breadcrumbs a:hover' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'forum_archive_title_breadcrumb_color', 'hover' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.forum-archive-title .base-breadcrumbs' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'forum_archive_title_breadcrumb_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'forum_archive_title_breadcrumb_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'forum_archive_title_breadcrumb_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.forum-archive-title .base-breadcrumbs' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'forum_archive_title_breadcrumb_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'forum_archive_title_breadcrumb_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'forum_archive_title_breadcrumb_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Forum Title Description.
			$css->set_selector( '.forum-archive-title .title-entry-description' );
			$css->render_font( webapp()->option( 'forum_archive_title_description_font' ), $css );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'forum_archive_title_description_color', 'color' ) ) );
			$css->set_selector( '.forum-archive-title .title-entry-description a:hover' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'forum_archive_title_description_color', 'hover' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.forum-archive-title .title-entry-description' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'forum_archive_title_description_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'forum_archive_title_description_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'forum_archive_title_description_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.forum-archive-title .title-entry-description' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'forum_archive_title_description_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'forum_archive_title_description_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'forum_archive_title_description_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Above Post Title.
			$css->set_selector( '.forum-archive-hero-section .entry-hero-container-inner' );
			$css->render_background( webapp()->sub_option( 'forum_archive_title_background', 'desktop' ), $css );
			$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'forum_archive_title_top_border', 'desktop' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'forum_archive_title_bottom_border', 'desktop' ) ) );
			$css->set_selector( '.entry-hero.forum-archive-hero-section .entry-header' );
			$css->add_property( 'min-height', $css->render_range( webapp()->option( 'forum_archive_title_height' ), 'desktop' ) );
			$css->set_selector( '.forum-archive-hero-section .hero-section-overlay' );
			$css->add_property( 'background', $css->render_color_or_gradient( webapp()->sub_option( 'forum_archive_title_overlay_color', 'color' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.forum-archive-hero-section .entry-hero-container-inner' );
			$css->render_background( webapp()->sub_option( 'forum_archive_title_background', 'tablet' ), $css );
			$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'forum_archive_title_top_border', 'tablet' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'forum_archive_title_bottom_border', 'tablet' ) ) );
			$css->set_selector( '.forum-archive-hero-section .entry-header' );
			$css->add_property( 'min-height', $css->render_range( webapp()->option( 'forum_archive_title_height' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.forum-archive-hero-section .entry-hero-container-inner' );
			$css->render_background( webapp()->sub_option( 'forum_archive_title_background', 'mobile' ), $css );
			$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'forum_archive_title_top_border', 'mobile' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'forum_archive_title_bottom_border', 'mobile' ) ) );
			$css->set_selector( '.entry-hero.forum-archive-hero-section .entry-header' );
			$css->add_property( 'min-height', $css->render_range( webapp()->option( 'forum_archive_title_height' ), 'mobile' ) );
			$css->stop_media_query();
		}
		// Topic.
		if ( is_bbpress() && is_singular( 'topic' ) ) {
			// Topic Backgrounds.
			$css->set_selector( 'body.single-topic' );
			$css->render_background( webapp()->sub_option( 'topic_background', 'desktop' ), $css );
			$css->set_selector( 'body.single-topic .content-bg, body.content-style-unboxed.single-topic .site' );
			$css->render_background( webapp()->sub_option( 'topic_content_background', 'desktop' ), $css );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( 'body.single-topic' );
			$css->render_background( webapp()->sub_option( 'topic_background', 'tablet' ), $css );
			$css->set_selector( 'body.single-topic .content-bg, body.content-style-unboxed.single-topic .site' );
			$css->render_background( webapp()->sub_option( 'topic_content_background', 'tablet' ), $css );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( 'body.single-topic' );
			$css->render_background( webapp()->sub_option( 'topic_background', 'mobile' ), $css );
			$css->set_selector( 'body.single-topic .content-bg, body.content-style-unboxed.single-topic .site' );
			$css->render_background( webapp()->sub_option( 'topic_content_background', 'mobile' ), $css );
			$css->stop_media_query();
			// Topic Title.
			$css->set_selector( '.topic-title h1' );
			$css->render_font( webapp()->option( 'topic_title_font' ), $css, 'heading' );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.topic-title h1' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'topic_title_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'topic_title_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'topic_title_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.topic-title h1' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'topic_title_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'topic_title_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'topic_title_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Topic Title Search.
			$css->set_selector( '.topic-title .bbp-search-form ::-webkit-input-placeholder' );
			$css->add_property( 'color', 'currentColor' );
			$css->add_property( 'opacity', '0.5' );
			$css->set_selector( '.topic-title .bbp-search-form ::placeholder' );
			$css->add_property( 'color', 'currentColor' );
			$css->add_property( 'opacity', '0.5' );
			$css->set_selector( '.topic-title .bbp-search-form' );
			$css->add_property( 'max-width', '100%' );
			$css->add_property( 'width', $css->render_size( webapp()->option( 'topic_title_search_width' ) ) );
			$css->set_selector( '.topic-title .bbp-search-form form' );
			$css->add_property( 'margin', $css->render_measure( webapp()->option( 'topic_title_search_margin' ) ) );
			$css->set_selector( '.topic-title .bbp-search-form input.search-field' );
			$css->render_font( webapp()->option( 'topic_title_search_typography' ), $css );
			$css->add_property( 'background', $css->render_color( webapp()->sub_option( 'topic_title_search_background', 'color' ) ) );
			$css->add_property( 'border', $css->render_border( webapp()->option( 'topic_title_search_border' ) ) );
			$css->add_property( 'border-color', $css->render_color( webapp()->sub_option( 'topic_title_search_border_color', 'color' ) ) );
			$css->set_selector( '.topic-title .bbp-search-form input.search-field, .topic-title .bbp-search-form .base-search-icon-wrap' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'topic_title_search_color', 'color' ) ) );
			$css->set_selector( '.topic-title .bbp-search-form input.search-field:focus' );
			$css->add_property( 'background', $css->render_color( webapp()->sub_option( 'topic_title_search_background', 'hover' ) ) );
			$css->add_property( 'border-color', $css->render_color( webapp()->sub_option( 'topic_title_search_border_color', 'hover' ) ) );
			$css->set_selector( '.topic-title .bbp-search-form input.search-field:focus, .topic-title .bbp-search-form input.search-submit:hover ~ .base-search-icon-wrap' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'topic_title_search_color', 'hover' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.topic-title .bbp-search-form input.search-field' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'topic_title_search_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'topic_title_search_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'topic_title_search_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.topic-title .bbp-search-form input.search-field' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'forum_title_search_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'forum_title_search_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'forum_title_search_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Topic Title Breadcrumbs.
			$css->set_selector( '.topic-title .base-breadcrumbs' );
			$css->render_font( webapp()->option( 'topic_title_breadcrumb_font' ), $css );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'topic_title_breadcrumb_color', 'color' ) ) );
			$css->set_selector( '.topic-title .base-breadcrumbs a:hover' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'topic_title_breadcrumb_color', 'hover' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.topic-title .base-breadcrumbs' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'topic_title_breadcrumb_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'topic_title_breadcrumb_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'topic_title_breadcrumb_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.topic-title .base-breadcrumbs' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'topic_title_breadcrumb_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'topic_title_breadcrumb_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'topic_title_breadcrumb_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Topic Title Info.
			$css->set_selector( '.topic-title .bbpress-topic-meta' );
			$css->render_font( webapp()->option( 'topic_title_info_font' ), $css );
			$css->set_selector( '.topic-title .bbpress-topic-meta, .topic-title .bbpress-topic-meta a' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'topic_title_info_color', 'color' ) ) );
			$css->set_selector( '.topic-title .bbpress-topic-meta a:hover' );
			$css->add_property( 'color', $css->render_color( webapp()->sub_option( 'topic_title_info_color', 'hover' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.topic-title .bbpress-topic-meta' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'topic_title_info_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'topic_title_info_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'topic_title_info_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.topic-title .bbpress-topic-meta' );
			$css->add_property( 'font-size', $css->render_font_size( webapp()->option( 'topic_title_info_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $css->render_font_height( webapp()->option( 'topic_title_info_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $css->render_font_spacing( webapp()->option( 'topic_title_info_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Above Post Title.
			$css->set_selector( '.topic-hero-section .entry-hero-container-inner' );
			$css->render_background( webapp()->sub_option( 'topic_title_background', 'desktop' ), $css );
			$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'topic_title_top_border', 'desktop' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'topic_title_bottom_border', 'desktop' ) ) );
			$css->set_selector( '.entry-hero.topic-hero-section .entry-header' );
			$css->add_property( 'min-height', $css->render_range( webapp()->option( 'topic_title_height' ), 'desktop' ) );
			$css->set_selector( '.topic-hero-section .hero-section-overlay' );
			$css->add_property( 'background', $css->render_color_or_gradient( webapp()->sub_option( 'topic_title_overlay_color', 'color' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.topic-hero-section .entry-hero-container-inner' );
			$css->render_background( webapp()->sub_option( 'topic_title_background', 'tablet' ), $css );
			$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'topic_title_top_border', 'tablet' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'topic_title_bottom_border', 'tablet' ) ) );
			$css->set_selector( '.topic-hero-section .entry-header' );
			$css->add_property( 'min-height', $css->render_range( webapp()->option( 'topic_title_height' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.topic-hero-section .entry-hero-container-inner' );
			$css->render_background( webapp()->sub_option( 'topic_title_background', 'mobile' ), $css );
			$css->add_property( 'border-top', $css->render_border( webapp()->sub_option( 'topic_title_top_border', 'mobile' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( webapp()->sub_option( 'topic_title_bottom_border', 'mobile' ) ) );
			$css->set_selector( '.entry-hero.topic-hero-section .entry-header' );
			$css->add_property( 'min-height', $css->render_range( webapp()->option( 'topic_title_height' ), 'mobile' ) );
			$css->stop_media_query();
		}

		self::$google_fonts = $css->fonts_output();
		return $css->css_output();
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
}
