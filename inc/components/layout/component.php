<?php
/**
 * Base\Layout\Component class
 *
 * @package Base
 */

namespace Base\Layout;

use Base\Component_Interface;
use Base\Templating_Component_Interface;
use function Base\webapp;
use function add_action;
use function add_filter;
use function register_sidebar;
use function is_active_sidebar;
use function dynamic_sidebar;

/**
 * Class for managing page/post layouts.
 *
 * Exposes template tags:
 * * `webapp()->get_layout()`
 * * `webapp()->get_feature()`
 * * `webapp()->show_feature()`
 * * `webapp()->show_feature_above()`
 * * `webapp()->show_feature_below()`
 * * `webapp()->get_feature_position()`
 * * `webapp()->show_comments()`
 * * `webapp()->show_hero_title()`
 * * `webapp()->show_in_content_title()`
 * * `webapp()->get_boxed()`
 * * `webapp()->has_sidebar()`
 * * `webapp()->sidebar_id()`
 * * `webapp()->sidebar_id_class()`
 * * `webapp()->sidebar_side()`
 * * `webapp()->display_sidebar()`
 * * `webapp()->has_header()`
 * * `webapp()->has_footer()`
 * * `webapp()->has_content()`
 *
 * @link https://developer.wordpress.org/themes/functionality/layout/
 */
class Component implements Component_Interface, Templating_Component_Interface {

	const PRIMARY_SIDEBAR_SLUG   = 'sidebar-primary';
	const SECONDARY_SIDEBAR_SLUG = 'sidebar-secondary';
	/**
	 * Holds the string for width layout.
	 *
	 * @var values of the theme settings.
	 */
	public static $layout = null;

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'layout';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_filter( 'body_class', array( $this, 'filter_body_classes' ) );
		add_action( 'widgets_init', array( $this, 'action_register_sidebars' ) );
		add_filter( 'register_sidebar_defaults', array( $this, 'change_sidebar_default_args' ) );
	}

	/**
	 * Registers the sidebars.
	 */
	public function action_register_sidebars() {
		$widgets = array(
			'sidebar-primary'   => __( 'Sidebar 1', 'avanam' ),
			'sidebar-secondary' => __( 'Sidebar 2', 'avanam' ),
			'footer1'           => __( 'Footer 1', 'avanam' ),
			'footer2'           => __( 'Footer 2', 'avanam' ),
			'footer3'           => __( 'Footer 3', 'avanam' ),
			'footer4'           => __( 'Footer 4', 'avanam' ),
			'footer5'           => __( 'Footer 5', 'avanam' ),
			'footer6'           => __( 'Footer 6', 'avanam' ),
		);

		foreach ( $widgets as $id => $name ) {
			register_sidebar(
				apply_filters(
					'base_widget_area_args',
					array(
						'name'          => $name,
						'id'            => $id,
						'description'   => esc_html__( 'Add widgets here.', 'avanam' ),
						'before_widget' => '<section id="%1$s" class="widget %2$s">',
						'after_widget'  => '</section>',
						'before_title'  => '<h2 class="widget-title">',
						'after_title'   => '</h2>',
					)
				)
			);
		}
	}

	/**
	 * Registers the sidebars.
	 *
	 * @param array $defaults the default args.
	 */
	public function change_sidebar_default_args( $defaults ) {
		$args = apply_filters(
			'base_widget_area_args',
			array(
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
		$args = wp_parse_args( $args, $defaults );
		return $args;
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
			'get_layout'                => array( $this, 'get_layout' ),
			'get_boxed'                 => array( $this, 'get_boxed' ),
			'get_title_layout'          => array( $this, 'get_title_layout' ),
			'show_hero_title'           => array( $this, 'show_hero_title' ),
			'show_in_content_title'     => array( $this, 'show_in_content_title' ),
			'get_feature'               => array( $this, 'get_feature' ),
			'show_feature'              => array( $this, 'show_feature' ),
			'show_feature_above'        => array( $this, 'show_feature_above' ),
			'show_feature_below'        => array( $this, 'show_feature_below' ),
			'get_feature_position'      => array( $this, 'get_feature_position' ),
			'show_comments'             => array( $this, 'show_comments' ),
			'show_post_navigation'      => array( $this, 'show_post_navigation' ),
			'has_sidebar'               => array( $this, 'has_sidebar' ),
			'sidebar_id'                => array( $this, 'sidebar_id' ),
			'sidebar_side'              => array( $this, 'sidebar_side' ),
			'sidebar_id_class'          => array( $this, 'sidebar_id_class' ),
			'display_sidebar'           => array( $this, 'display_sidebar' ),
			'desk_transparent_header'   => array( $this, 'desk_transparent_header' ),
			'mobile_transparent_header' => array( $this, 'mobile_transparent_header' ),
			'has_header'                => array( $this, 'has_header' ),
			'has_footer'                => array( $this, 'has_footer' ),
			'has_content'               => array( $this, 'has_content' ),
		);
	}

	/**
	 * Displays the sidebar.
	 */
	public function display_sidebar() {
		ob_start();
		dynamic_sidebar( self::sidebar_id() );
		echo apply_filters( 'base_dynamic_sidebar_content', ob_get_clean() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	/**
	 * Checks if page should show in hero title.
	 *
	 * @return bool true or false.
	 */
	public static function show_hero_title() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return ( 'above' === self::$layout['title'] ? true : false );
	}
	/**
	 * Checks if page should show in content title.
	 *
	 * @return bool true or false.
	 */
	public static function show_in_content_title() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return ( 'normal' === self::$layout['title'] ? true : false );
	}
	/**
	 * Checks if header is here or incontent
	 *
	 * @return string normal or above
	 */
	public static function has_content() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return ( 'disable' === self::$layout['content'] ? false : true );
	}
	/**
	 * Checks if header is here or incontent
	 *
	 * @return string normal or above
	 */
	public static function has_header() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return ( 'disable' === self::$layout['header'] ? false : true );
	}
	/**
	 * Checks if footer is here or incontent
	 *
	 * @return string normal or above
	 */
	public static function has_footer() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return ( 'disable' === self::$layout['footer'] ? false : true );
	}
	/**
	 * Checks if page title is here or incontent
	 *
	 * @return string normal or above
	 */
	public static function get_title_layout() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return self::$layout['title'];
	}
	/**
	 * Checks if page should have desktop transparent header.
	 *
	 * @return bool true or false.
	 */
	public static function desk_transparent_header() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return ( 'enable' === self::$layout['transparent'] && webapp()->sub_option( 'transparent_header_device', 'desktop' ) ? true : false );
	}
	/**
	 * Checks if page should have desktop transparent header.
	 *
	 * @return bool true or false.
	 */
	public static function get_desk_transparent_class() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return ( 'enable' === self::$layout['transparent'] && webapp()->sub_option( 'transparent_header_device', 'desktop' ) ? 'transparent-header' : 'non-transparent-header' );
	}
	/**
	 * Checks if page should have mobile transparent header.
	 *
	 * @return bool true or false.
	 */
	public static function get_mobile_transparent_class() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return ( 'enable' === self::$layout['transparent'] && webapp()->sub_option( 'transparent_header_device', 'mobile' ) ? 'mobile-transparent-header' : 'mobile-non-transparent-header' );
	}
	/**
	 * Checks if page should have mobile transparent header.
	 *
	 * @return bool true or false.
	 */
	public static function mobile_transparent_header() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return ( 'enable' === self::$layout['transparent'] && webapp()->sub_option( 'transparent_header_device', 'mobile' ) ? true : false );
	}
	/**
	 * Checks if page should show comments.
	 *
	 * @return bool true or false.
	 */
	public static function show_post_navigation() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return ( 'show' === self::$layout['navigation'] ? true : false );
	}
	/**
	 * Checks if page should show comments.
	 *
	 * @return bool true or false.
	 */
	public static function show_comments() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return ( post_type_supports( get_post_type(), 'comments' ) && ( comments_open() || get_comments_number() ) && 'show' === self::$layout['comments'] ? true : false );
	}

	/**
	 * Checks if page has a sidebar.
	 *
	 * @return boolean True will display the sidebar, False will not
	 */
	public static function has_sidebar() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return ( 'enable' === self::$layout['sidebar'] ? true : false );
	}
	/**
	 * Checks if page is hiding the footer
	 *
	 * @return boolean True will hide the footer, False will not
	 */
	public static function no_footer() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return ( 'disable' === self::$layout['footer'] ? true : false );
	}

	/**
	 * Checks if page is hiding the header
	 *
	 * @return boolean True will hide the header, False will not
	 */
	public static function no_header() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return ( 'disable' === self::$layout['header'] ? true : false );
	}

	/**
	 * Checks which sidebar to show if showing.
	 *
	 * @return string the sidebar ID to call.
	 */
	public static function sidebar_id() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return self::$layout['sidebar_id'];
	}

	/**
	 * Checks which sidebar to show if showing.
	 *
	 * @return string the sidebar ID to call.
	 */
	public static function sidebar_id_class() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return 'sidebar-slug-' . self::$layout['sidebar_id'];
	}

	/**
	 * Checks if page has a sidebar.
	 *
	 * @return string left will display the sidebar on the left, right will display sidebar on the right.
	 */
	public static function sidebar_side() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return self::$layout['side'];
	}
	/**
	 * Checks if page has a featured image.
	 *
	 * @return bool true or false.
	 */
	public static function show_feature() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return ( 'show' === self::$layout['feature'] ? true : false );
	}
	/**
	 * Checks if page has a featured image.
	 *
	 * @return bool true or false.
	 */
	public static function show_feature_above() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return ( self::show_feature() && ( 'above' === self::$layout['feature_position'] || 'behind' === self::$layout['feature_position'] ) ? true : false );
	}
	/**
	 * Checks if page has a featured image.
	 *
	 * @return bool true or false.
	 */
	public static function show_feature_below() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return ( self::show_feature() && ( 'below' === self::$layout['feature_position'] ) ? true : false );
	}
	/**
	 * Get the feature position.
	 *
	 * @return bool true or false.
	 */
	public static function get_feature_position() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return self::$layout['feature_position'];
	}
	/**
	 * Checks if page has a featured image.
	 *
	 * @return string hide or show.
	 */
	public static function get_feature() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return self::$layout['feature'];
	}
	/**
	 * Checks if page is using boxed layout.
	 *
	 * @return string left will display the sidebar on the left, right will display sidebar on the right.
	 */
	public static function get_boxed() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return self::$layout['boxed'];
	}

	/**
	 * Checks if page has veritcal padding.
	 *
	 * @return string left will display the sidebar on the left, right will display sidebar on the right.
	 */
	public static function get_vertical_padding() {
		if ( is_null( self::$layout ) ) {
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return self::$layout['vpadding'];
	}

	/**
	 * Checks if page has a sidebar.
	 *
	 * @return string left will display the sidebar on the left, right will display sidebar on the right.
	 */
	public static function get_layout() {
		if ( is_null( self::$layout ) ) {
			self::check_conditionals();
			self::$layout = apply_filters( 'base_post_layout', self::check_conditionals() );
		}
		return self::$layout['layout'];
	}

	/**
	 * Checks conditionals to see what layout options are used to create this page.
	 *
	 * @return array of layout options.
	 */
	public static function check_conditionals() {
		$boxed       = 'boxed';
		$layout      = 'normal';
		$feature     = 'hide';
		$f_position  = 'above';
		$comments    = 'hide';
		$navigation  = 'hide';
		$title       = 'normal';
		$sidebar     = 'disable';
		$sidebar_id  = static::PRIMARY_SIDEBAR_SLUG;
		$side        = 'right';
		$vpadding    = 'show';
		$header      = 'enable';
		$footer      = 'enable';
		$content     = 'enable';
		$transparent = ( webapp()->option( 'transparent_header_enable' ) ? 'enable' : 'disable' );
		if ( ( is_singular() || is_front_page() ) && ! is_home() ) {
			if ( is_front_page() ) {
				$post_id = get_option( 'page_on_front' );
				$post_type  = 'page';
				$trans_type = 'page';
			} else {
				$post_id    = get_the_ID();
				$post_type  = get_post_type();
				$trans_type = $post_type;
			}
			$postlayout     = get_post_meta( $post_id, '_bst_post_layout', true );
			$postsidebar    = get_post_meta( $post_id, '_bst_post_sidebar_id', true );
			$postboxed      = get_post_meta( $post_id, '_bst_post_content_style', true );
			$postfeature    = get_post_meta( $post_id, '_bst_post_feature', true );
			$postf_position = get_post_meta( $post_id, '_bst_post_feature_position', true );
			$posttitle      = get_post_meta( $post_id, '_bst_post_title', true );
			$posttrans      = get_post_meta( $post_id, '_bst_post_transparent', true );
			$postvpadding   = get_post_meta( $post_id, '_bst_post_vertical_padding', true );
			$postheader     = get_post_meta( $post_id, '_bst_post_header', true );
			$postfooter     = get_post_meta( $post_id, '_bst_post_footer', true );
			// header.
			if ( isset( $postheader ) && true == $postheader ) {
				$header = 'disable';
			}
			// Footer.
			if ( isset( $postfooter ) && true == $postfooter ) {
				$footer = 'disable';
			}
			// Sidebar ID.
			if ( isset( $postsidebar ) && ! empty( $postsidebar ) && 'defualt' !== $postsidebar && 'default' !== $postsidebar ) {
				$sidebar_id = $postsidebar;
			} else {
				$sidebar_id = webapp()->option( $post_type . '_sidebar_id', $sidebar_id );
			}
			// Transparent.
			if ( isset( $posttrans ) && ( 'enable' === $posttrans || 'disable' === $posttrans ) ) {
				$transparent = $posttrans;
			} else {
				$option_trans = webapp()->option( 'transparent_header_' . $trans_type, null );
				if ( true === $option_trans ) {
					$transparent = 'disable';
				} else if ( is_null( $option_trans ) ) {
					$transparent = 'disable';
				}
			}
			// Title.
			if ( isset( $posttitle ) && ( 'above' === $posttitle || 'normal' === $posttitle || 'hide' === $posttitle ) ) {
				$title = $posttitle;
			} elseif ( isset( $posttitle ) && 'show' === $posttitle ) {
				$option_title_layout = webapp()->option( $post_type . '_title_layout' );
				if ( 'above' === $option_title_layout || 'normal' === $option_title_layout ) {
					$title = $option_title_layout;
				}
			} else {
				$option_title = webapp()->option( $post_type . '_title' );
				if ( false === $option_title ) {
					$title = 'hide';
				} else {
					$option_title_layout = webapp()->option( $post_type . '_title_layout' );
					if ( 'above' === $option_title_layout || 'normal' === $option_title_layout ) {
						$title = $option_title_layout;
					}
				}
			}
			// Post Vertical Padding.
			if ( isset( $postvpadding ) && ( 'show' === $postvpadding || 'hide' === $postvpadding || 'top' === $postvpadding || 'bottom' === $postvpadding ) ) {
				$vpadding = $postvpadding;
			} else {
				$option_vpadding = webapp()->option( $post_type . '_vertical_padding' );
				if ( 'show' === $option_vpadding || 'hide' === $option_vpadding || 'top' === $option_vpadding || 'bottom' === $option_vpadding ) {
					$vpadding = $option_vpadding;
				}
			}
			// Post Navigation.
			if ( 'post' === $post_type ) {
				$option_navigation = webapp()->option( $post_type . '_navigation' );
				if ( $option_navigation ) {
					$navigation = 'show';
				}
			}
			// Post Comments.
			$option_comments = webapp()->option( $post_type . '_comments' );
			if ( $option_comments ) {
				$comments = 'show';
			}
			if ( 'product' === $post_type ) {
				$comments = 'show';
			}
			// Post Boxed.
			if ( isset( $postboxed ) && ( 'unboxed' === $postboxed || 'boxed' === $postboxed ) ) {
				$boxed = $postboxed;
			} else {
				$option_boxed = webapp()->option( $post_type . '_content_style' );
				if ( 'unboxed' === $option_boxed || 'boxed' === $option_boxed ) {
					$boxed = $option_boxed;
				}
			}
			// Post Feature.
			if ( isset( $postfeature ) && ( 'show' === $postfeature || 'hide' === $postfeature ) ) {
				$feature = $postfeature;
			} else {
				$option_feature = webapp()->option( $post_type . '_feature' );
				if ( $option_feature ) {
					$feature = 'show';
				}
			}
			// Post Feature position.
			if ( isset( $postf_position ) && ( 'above' === $postf_position || 'behind' === $postf_position || 'below' === $postf_position ) ) {
				$f_position = $postf_position;
			} else {
				$option_f_position = webapp()->option( $post_type . '_feature_position' );
				if ( 'above' === $option_f_position || 'behind' === $option_f_position || 'below' === $option_f_position ) {
					$f_position = $option_f_position;
				}
			}
			// Post Layout.
			if ( isset( $postlayout ) && ( 'narrow' === $postlayout || 'fullwidth' === $postlayout ) ) {
				$layout = $postlayout;
			} elseif ( ( isset( $postlayout ) && 'default' === $postlayout ) || empty( $postlayout ) ) {
				$option_layout = webapp()->option( $post_type . '_layout' );
				if ( 'narrow' === $option_layout || 'fullwidth' === $option_layout ) {
					$layout = $option_layout;
				}
			}
			// Post Sidebar.
			if ( isset( $postlayout ) && ( 'left' === $postlayout || 'right' === $postlayout ) ) {
				$side    = $postlayout;
				$sidebar = 'enable';
				$layout  = $postlayout;
			} elseif ( ( isset( $postlayout ) && 'default' === $postlayout ) || empty( $postlayout ) ) {
				$option_layout = webapp()->option( $post_type . '_layout' );
				if ( 'left' === $option_layout || 'right' === $option_layout ) {
					$side    = $option_layout;
					$sidebar = 'enable';
				}
			}
		} elseif ( is_archive() || is_search() || is_home() || is_404() ) {
			if ( is_home() && is_front_page() ) {
				$archive_type = 'post_archive';
				$trans_type   = 'archive';
			} elseif ( is_home() && ! is_front_page() ) {
				if ( get_query_var( 'tribe_events_front_page' ) ) {
					$archive_type = 'tribe_events_archive';
					$trans_type   = 'archive';
				} else {
					$post_id         = get_option( 'page_for_posts' );
					$archivelayout   = get_post_meta( $post_id, '_bst_post_layout', true );
					$archiveboxed    = get_post_meta( $post_id, '_bst_post_content_style', true );
					$archivesidebar  = get_post_meta( $post_id, '_bst_post_sidebar_id', true );
					$archivefeature  = get_post_meta( $post_id, '_bst_post_feature', true );
					$archivetitle    = get_post_meta( $post_id, '_bst_post_title', true );
					$archivetrans    = get_post_meta( $post_id, '_bst_post_transparent', true );
					$archivevpadding = get_post_meta( $post_id, '_bst_post_vertical_padding', true );
					$postf_position  = get_post_meta( $post_id, '_bst_post_feature_position', true );
					$archive_type    = 'post_archive';
					$trans_type      = 'archive';
				}
			} elseif ( class_exists( 'woocommerce' ) && is_shop() && ! is_search() ) {
				$post_id         = wc_get_page_id( 'shop' );
				$archivelayout   = get_post_meta( $post_id, '_bst_post_layout', true );
				$archiveboxed    = get_post_meta( $post_id, '_bst_post_content_style', true );
				$archivesidebar  = get_post_meta( $post_id, '_bst_post_sidebar_id', true );
				$archivefeature  = get_post_meta( $post_id, '_bst_post_feature', true );
				$archivetitle    = get_post_meta( $post_id, '_bst_post_title', true );
				$archivetrans    = get_post_meta( $post_id, '_bst_post_transparent', true );
				$archivevpadding = get_post_meta( $post_id, '_bst_post_vertical_padding', true );
				$postf_position  = get_post_meta( $post_id, '_bst_post_feature_position', true );
				$archive_type    = 'product_archive';
				$trans_type      = 'archive';
			} elseif ( class_exists( 'woocommerce' ) && ( is_product_category() || is_product_tag() || is_tax( 'product_brands' ) || ( is_shop() && is_search() ) ) ) {
				$archive_type = 'product_archive';
				$trans_type   = 'archive';
			} elseif ( function_exists( 'geodir_is_page' ) && ( geodir_is_page( 'post_type' ) || geodir_is_page( 'archive' ) || geodir_is_page( 'search' ) ) ) {
				$post_type       = geodir_get_current_posttype();
				$post_id         = (int) \GeoDir_Compatibility::gd_page_id();
				$archivelayout   = get_post_meta( $post_id, '_bst_post_layout', true );
				$archiveboxed    = get_post_meta( $post_id, '_bst_post_content_style', true );
				$archivesidebar  = get_post_meta( $post_id, '_bst_post_sidebar_id', true );
				$archivefeature  = get_post_meta( $post_id, '_bst_post_feature', true );
				$archivetitle    = get_post_meta( $post_id, '_bst_post_title', true );
				$archivetrans    = get_post_meta( $post_id, '_bst_post_transparent', true );
				$archivevpadding = get_post_meta( $post_id, '_bst_post_vertical_padding', true );
				$postf_position  = get_post_meta( $post_id, '_bst_post_feature_position', true );
				$archive_type    = $post_type . '_archive';
				$trans_type      = 'archive';
			} elseif ( is_post_type_archive( 'llms_membership' ) && function_exists( 'llms_get_page_id' ) ) {
				$post_id         = llms_get_page_id( 'memberships' );
				$archivelayout   = get_post_meta( $post_id, '_bst_post_layout', true );
				$archiveboxed    = get_post_meta( $post_id, '_bst_post_content_style', true );
				$archivesidebar  = get_post_meta( $post_id, '_bst_post_sidebar_id', true );
				$archivefeature  = get_post_meta( $post_id, '_bst_post_feature', true );
				$archivetitle    = get_post_meta( $post_id, '_bst_post_title', true );
				$archivetrans    = get_post_meta( $post_id, '_bst_post_transparent', true );
				$archivevpadding = get_post_meta( $post_id, '_bst_post_vertical_padding', true );
				$postf_position  = get_post_meta( $post_id, '_bst_post_feature_position', true );
				$archive_type    = 'llms_membership_archive';
				$trans_type      = 'archive';
			} elseif ( is_tax( 'membership_cat' ) || is_tax( 'membership_tag' ) ) {
				$archive_type = 'llms_membership_archive';
				$trans_type   = 'archive';
			} elseif ( is_post_type_archive( 'course' ) && function_exists( 'llms_get_page_id' ) ) {
				$post_id         = llms_get_page_id( 'courses' );
				$archivelayout   = get_post_meta( $post_id, '_bst_post_layout', true );
				$archiveboxed    = get_post_meta( $post_id, '_bst_post_content_style', true );
				$archivesidebar  = get_post_meta( $post_id, '_bst_post_sidebar_id', true );
				$archivefeature  = get_post_meta( $post_id, '_bst_post_feature', true );
				$archivetitle    = get_post_meta( $post_id, '_bst_post_title', true );
				$archivetrans    = get_post_meta( $post_id, '_bst_post_transparent', true );
				$archivevpadding = get_post_meta( $post_id, '_bst_post_vertical_padding', true );
				$postf_position  = get_post_meta( $post_id, '_bst_post_feature_position', true );
				$archive_type    = 'course_archive';
				$trans_type      = 'archive';
			} elseif ( is_tax( 'course_cat' ) || is_tax( 'course_tag' ) || is_tax( 'course_track' ) ) {
				$archive_type = 'course_archive';
				$trans_type   = 'archive';
			} elseif ( is_post_type_archive( 'tribe_events' ) ) {
				$archive_type = 'tribe_events_archive';
				$trans_type = 'archive';
			} elseif ( is_tax( 'portfolio-type' ) || is_tax( 'portfolio-tag' ) ) {
				$archive_type = 'portfolio_archive';
				$trans_type   = 'archive';
			} elseif ( is_tax( 'staff-group' ) ) {
				$archive_type = 'staff_archive';
				$trans_type   = 'archive';
			} elseif ( is_tax( 'testimonial-group' ) ) {
				$archive_type = 'testimonial_archive';
				$trans_type   = 'archive';
			} elseif ( ( is_tax( 'ht_kb_category' ) || is_tax( 'ht_kb_tag' ) || is_post_type_archive( 'ht_kb' ) || ( is_search() && array_key_exists( 'ht-kb-search', $_REQUEST ) ) ) ) {
				$archive_type = 'ht_kb_archive';
				$trans_type   = 'archive';
			} elseif ( is_search() ) {
				$archive_type = 'search_archive';
				$trans_type = 'archive';
			} elseif ( is_404() ) {
				$archive_type = '404';
				$trans_type = 'archive';
			} elseif ( is_category() || is_tag() ) {
				$archive_type = 'post_archive';
				$trans_type = 'archive';
			} elseif ( is_tax( 'knowledgebase_cat' ) ) {
				$archive_type = 'knowledgebase_archive';
				$trans_type = 'archive';
			} else {
				$post_type  = get_post_type();
				$archive_type = $post_type . '_archive';
				$trans_type = 'archive';
			}
			// Sidebar ID.
			if ( isset( $archivesidebar ) && ! empty( $archivesidebar ) && 'default' !== $archivesidebar && 'defualt' !== $archivesidebar ) {
				$sidebar_id = $archivesidebar;
			} else {
				$sidebar_id = webapp()->option( $archive_type . '_sidebar_id', $sidebar_id );
			}
			// Archive Transparent.
			if ( isset( $archivetrans ) && ( 'enable' === $archivetrans || 'disable' === $archivetrans ) ) {
				$transparent = $archivetrans;
			} else {
				$option_trans = webapp()->option( 'transparent_header_' . $trans_type );
				if ( true === $option_trans ) {
					$transparent = 'disable';
				}
			}
			// Archive Title.
			if ( isset( $archivetitle ) && ( 'above' === $archivetitle || 'normal' === $archivetitle || 'hide' === $archivetitle ) ) {
				$title = $archivetitle;
			} else {
				$option_title = webapp()->option( $archive_type . '_title' );
				if ( false === $option_title ) {
					$title = 'hide';
				} else {
					$option_title_layout = webapp()->option( $archive_type . '_title_layout' );
					if ( empty( $option_title_layout ) ) {
						$option_title_layout = webapp()->option( 'post_archive_title_layout' );
					}
					if ( 'above' === $option_title_layout || 'normal' === $option_title_layout ) {
						$title = $option_title_layout;
					}
				}
			}
			if ( is_home() && is_front_page() ) {
				if ( ! webapp()->option( 'post_archive_home_title' ) ) {
					$title = 'hide';
				}
			}
			if ( is_404() ) {
				$title = 'normal';
				$transparent = 'disable';
			}
			// Archive Feature.
			if ( isset( $archivefeature ) && ( 'show' === $archivefeature || 'hide' === $archivefeature ) ) {
				$feature = $archivefeature;
			}
			// Post Feature position.
			if ( isset( $postf_position ) && ( 'above' === $postf_position || 'behind' === $postf_position || 'below' === $postf_position ) ) {
				$f_position = $postf_position;
			}
			// Archive Boxed.
			if ( isset( $archiveboxed ) && ( 'unboxed' === $archiveboxed || 'boxed' === $archiveboxed ) ) {
				$boxed = $archiveboxed;
			} else {
				$option_boxed = webapp()->option( $archive_type . '_content_style' );
				if ( empty( $option_boxed ) ) {
					$option_boxed = webapp()->option( 'post_archive_content_style' );
				}
				if ( 'unboxed' === $option_boxed || 'boxed' === $option_boxed ) {
					$boxed = $option_boxed;
				}
			}
			// Archive Vertical Padding.
			if ( isset( $archivevpadding ) && ( 'show' === $archivevpadding || 'hide' === $archivevpadding || 'top' === $archivevpadding || 'bottom' === $archivevpadding ) ) {
				$vpadding = $archivevpadding;
			} else {
				$option_vpadding = webapp()->option( $archive_type . '_vertical_padding' );
				if ( $option_vpadding && ( 'show' === $option_vpadding || 'hide' === $option_vpadding || 'top' === $option_vpadding || 'bottom' === $option_vpadding ) ) {
					$vpadding = $option_vpadding;
				}
			}
			// Archive Layout.
			if ( isset( $archivelayout ) && ( 'narrow' === $archivelayout || 'fullwidth' === $archivelayout ) ) {
				$layout = $archivelayout;
			} elseif ( ( isset( $archivelayout ) && 'default' === $archivelayout ) || empty( $archivelayout ) ) {
				$option_layout = webapp()->option( $archive_type . '_layout' );
				if ( empty( $option_layout ) ) {
					$option_layout = webapp()->option( 'post_archive_layout' );
				}
				if ( 'narrow' === $option_layout || 'fullwidth' === $option_layout ) {
					$layout = $option_layout;
				}
			}
			// Archive Sidebar.
			if ( isset( $archivelayout ) && ( 'left' === $archivelayout || 'right' === $archivelayout ) ) {
				$side    = $archivelayout;
				$sidebar = 'enable';
			} elseif ( ( isset( $archivelayout ) && 'default' === $archivelayout ) || empty( $archivelayout ) ) {
				$option_layout = webapp()->option( $archive_type . '_layout' );
				if ( empty( $option_layout ) ) {
					$option_layout = webapp()->option( 'post_archive_layout' );
				}
				if ( 'left' === $option_layout || 'right' === $option_layout ) {
					$side    = $option_layout;
					$sidebar = 'enable';
				}
			}
		}
		$return_array = array(
			'layout'           => $layout,
			'boxed'            => $boxed,
			'feature'          => $feature,
			'feature_position' => $f_position,
			'comments'         => $comments,
			'navigation'       => $navigation,
			'title'            => $title,
			'transparent'      => $transparent,
			'side'             => $side,
			'sidebar'          => $sidebar,
			'vpadding'         => $vpadding,
			'sidebar_id'       => $sidebar_id,
			'footer'           => $footer,
			'header'           => $header,
			'content'          => $content,
		);
		return $return_array;
	}
	/**
	 * Adds custom classes to indicate whether a sidebar is present to the array of body classes.
	 *
	 * @param array $classes Classes for the body element.
	 * @return array Filtered body classes.
	 */
	public function filter_body_classes( array $classes ) : array {
		if ( self::no_header() ) {
			$classes[] = 'no-header';
		}
		if ( self::no_footer() ) {
			$classes[] = 'no-footer';
		}
		if ( self::has_sidebar() ) {
			$classes[] = 'has-sidebar';
			if ( 'left' === self::sidebar_side() ) {
				$classes[] = 'has-left-sidebar';
			}
			if ( webapp()->option( 'sidebar_sticky' ) ) {
				if ( webapp()->option( 'sidebar_sticky_last_widget' ) ) {
					$classes[] = 'has-sticky-sidebar-widget';
				} else {
					$classes[] = 'has-sticky-sidebar';
				}
			}
		}
		$classes[] = 'content-title-style-' . esc_attr( self::get_title_layout() );
		$classes[] = 'content-width-' . esc_attr( self::get_layout() );
		$classes[] = 'content-style-' . esc_attr( self::get_boxed() );
		$classes[] = 'content-vertical-padding-' . esc_attr( self::get_vertical_padding() );
		$classes[] = esc_attr( self::get_desk_transparent_class() );
		$classes[] = esc_attr( self::get_mobile_transparent_class() );

		return $classes;
	}
}
