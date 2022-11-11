<?php
/**
 * Class for the Meta Fields
 *
 * @package Base
 */

namespace Base;

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
 * Class for Meta
 *
 * @category class
 */
class Theme_Meta {
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
		add_action( 'init', array( $this, 'plugin_register' ), 20 );
		add_action( 'init', array( $this, 'register_meta' ), 20 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'script_enqueue' ) );
		add_filter( 'register_post_type_args', array( $this, 'add_needed_custom_fields_support' ), 20, 2 );
		if ( is_admin() ) {
			add_action( 'load-post.php', array( $this, 'init_metabox' ) );
			add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
		}
	}
	/**
	 * Meta box initialization.
	 */
	public function init_metabox() {
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_product_metabox' ) );
		add_action( 'save_post', array( $this, 'save_metabox' ), 10, 2 );
	}

	/**
	 * Adds the meta box.
	 */
	public function add_metabox() {
		$all_post_types    = webapp()->get_post_types_objects();
		$extras_post_types = array( 'post', 'page' );
		$ignore_type       = webapp()->get_post_types_to_ignore();
		foreach ( $all_post_types as $post_type_item ) {
			$post_type_name  = $post_type_item->name;
			if ( ! in_array( $post_type_name, $ignore_type, true ) ) {
				$extras_post_types[] = $post_type_name;
			}
		}
		add_meta_box(
			'_bst_classic_meta_control',
			__( 'Post Settings', 'avanam' ),
			array( $this, 'render_metabox' ),
			apply_filters( 'base_classic_meta_box_post_types', $extras_post_types ),
			'side',
			'low',
			array(
				'__back_compat_meta_box' => true,
			)
		);
	}
	/**
	 * Adds the product meta box.
	 */
	public function add_product_metabox() {
		add_meta_box(
			'_bst_classic_meta_control',
			__( 'Post Settings', 'avanam' ),
			array( $this, 'render_product_metabox' ),
			array( 'product' ),
			'side',
			'low',
			array(
				'__back_compat_meta_box' => true,
			)
		);
	}
	/**
	 * Handles saving the meta box.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @return null
	 */
	public function save_metabox( $post_id, $post ) {
		// Add nonce for security and authentication.
		$nonce_action = 'base_theme_classic_meta_nonce_action';

		// Check if nonce is set.
		if ( ! isset( $_POST['base_theme_classic_meta_nonce'] ) ) {
			return;
		}

		// Check if nonce is valid.
		if ( ! wp_verify_nonce( $_POST['base_theme_classic_meta_nonce'], $nonce_action ) ) {
			return;
		}

		// Check if user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check if not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Check if not a revision.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( isset( $_POST['_bst_post_transparent'] ) ) {
			$trans_control_value = sanitize_key( wp_unslash( $_POST['_bst_post_transparent'] ) );
			update_post_meta( $post_id, '_bst_post_transparent', $trans_control_value );
		}
		if ( isset( $_POST['_bst_post_title'] ) ) {
			$title_control_value = sanitize_key( wp_unslash( $_POST['_bst_post_title'] ) );
			update_post_meta( $post_id, '_bst_post_title', $title_control_value );
		}
		if ( isset( $_POST['_bst_post_layout'] ) ) {
			$layout_control_value = sanitize_key( wp_unslash( $_POST['_bst_post_layout'] ) );
			update_post_meta( $post_id, '_bst_post_layout', $layout_control_value );
		}
		if ( isset( $_POST['_bst_post_content_style'] ) ) {
			$content_control_value = sanitize_key( wp_unslash( $_POST['_bst_post_content_style'] ) );
			update_post_meta( $post_id, '_bst_post_content_style', $content_control_value );
		}
		if ( isset( $_POST['_bst_post_vertical_padding'] ) ) {
			$padding_control_value = sanitize_key( wp_unslash( $_POST['_bst_post_vertical_padding'] ) );
			update_post_meta( $post_id, '_bst_post_vertical_padding', $padding_control_value );
		}
	}
	/**
	 * Renders the meta box.
	 *
	 * @param object $post the post object.
	 */
	public function render_product_metabox( $post ) {
		// Add nonce for security and authentication.
		wp_nonce_field( 'base_theme_classic_meta_nonce_action', 'base_theme_classic_meta_nonce' );
		$output = '<div class="base_classic_meta_boxes">';
		$output .= '<div class="base_classic_meta_boxes" style="padding: 10px 0 0;">';
		$output .= '<div style="padding-bottom:10px;">';
		$output .= '<label for="_bst_post_transparent" style="font-weight: 600;">' . esc_html__( 'Transparent Header', 'avanam' ) . '</label>';
		$output .= '</div>';
		$output .= '<div>';
		$trans_option_values = array(
			'default' => __( 'Default', 'avanam' ),
			'enable'  => __( 'Enable', 'avanam' ),
			'disable' => __( 'Disable', 'avanam' ),
		);
		$trans_select_value = get_post_meta( $post->ID, '_bst_post_transparent', true );
		$output .= '<select style="width:100%; box-sizing: border-box;" name="_bst_post_transparent">';
		foreach ( $trans_option_values as $key => $value ) {
			if ( $key == $trans_select_value ) {
				$output .= '<option value="' . esc_attr( $key ) . '" selected>' . esc_attr( $value ) . '</option>';
			} else {
				$output .= '<option value="' . esc_attr( $key ) . '">' . esc_attr( $value ) . '</option>';
			}
		}
		$output .= '</select>';
		$output .= '</div>';
		$output .= '<div class="clearfixit" style="padding: 5px 0; clear:both;"></div>';
		$output .= '</div>';

		$output .= '<div class="base_classic_meta_boxes" style="padding: 10px 0 0;">';
		$output .= '<div style="padding-bottom:10px;">';
		$output .= '<label for="_bst_post_layout" style="font-weight: 600;">' . esc_html__( 'Layout', 'avanam' ) . '</label>';
		$output .= '</div>';
		$output .= '<div>';
		$layout_option_values = array(
			'default'   => __( 'Default', 'avanam' ),
			'normal'    => __( 'Normal', 'avanam' ),
			'narrow'    => __( 'Narrow', 'avanam' ),
			'fullwidth' => __( 'Fullwidth', 'avanam' ),
			'left'      => __( 'Sidebar Left', 'avanam' ),
			'right'     => __( 'Sidebar Right', 'avanam' ),
		);
		$layout_select_value = get_post_meta( $post->ID, '_bst_post_layout', true );
		$output .= '<select style="width:100%; box-sizing: border-box;" name="_bst_post_layout">';
		foreach ( $layout_option_values as $key => $value ) {
			if ( $key == $layout_select_value ) {
				$output .= '<option value="' . esc_attr( $key ) . '" selected>' . esc_attr( $value ) . '</option>';
			} else {
				$output .= '<option value="' . esc_attr( $key ) . '">' . esc_attr( $value ) . '</option>';
			}
		}
		$output .= '</select>';
		$output .= '</div>';
		$output .= '<div class="clearfixit" style="padding: 5px 0; clear:both;"></div>';
		$output .= '</div>';
		$output .= '<div class="base_classic_meta_boxes" style="padding: 10px 0 0;">';
		$output .= '<div style="padding-bottom:10px;">';
		$output .= '<label for="_bst_post_content_style" style="font-weight: 600;">' . esc_html__( 'Content Style', 'avanam' ) . '</label>';
		$output .= '</div>';
		$output .= '<div>';
		$content_option_values = array(
			'default' => __( 'Default', 'avanam' ),
			'boxed'   => __( 'Boxed', 'avanam' ),
			'unboxed' => __( 'Unboxed', 'avanam' ),
		);
		$content_select_value = get_post_meta( $post->ID, '_bst_post_content_style', true );
		$output .= '<select style="width:100%; box-sizing: border-box;" name="_bst_post_content_style">';
		foreach ( $content_option_values as $key => $value ) {
			if ( $key == $content_select_value ) {
				$output .= '<option value="' . esc_attr( $key ) . '" selected>' . esc_attr( $value ) . '</option>';
			} else {
				$output .= '<option value="' . esc_attr( $key ) . '">' . esc_attr( $value ) . '</option>';
			}
		}
		$output .= '</select>';
		$output .= '</div>';
		$output .= '<div class="clearfixit" style="padding: 5px 0; clear:both;"></div>';
		$output .= '</div>';

		$output .= '<div class="base_classic_meta_boxes" style="padding: 10px 0 0;">';
		$output .= '<div style="padding-bottom:10px;">';
		$output .= '<label for="_bst_post_vertical_padding" style="font-weight: 600;">' . esc_html__( 'Content Vertical Padding', 'avanam' ) . '</label>';
		$output .= '</div>';
		$output .= '<div>';
		$padding_option_values = array(
			'default' => __( 'Default', 'avanam' ),
			'show'    => __( 'Enable', 'avanam' ),
			'hide'    => __( 'Disable', 'avanam' ),
			'top'     => __( 'Top Only', 'avanam' ),
			'bottom'  => __( 'Bottom Only', 'avanam' ),
		);
		$padding_select_value = get_post_meta( $post->ID, '_bst_post_vertical_padding', true );
		$output .= '<select style="width:100%; box-sizing: border-box;" name="_bst_post_vertical_padding">';
		foreach ( $padding_option_values as $key => $value ) {
			if ( $key == $padding_select_value ) {
				$output .= '<option value="' . esc_attr( $key ) . '" selected>' . esc_attr( $value ) . '</option>';
			} else {
				$output .= '<option value="' . esc_attr( $key ) . '">' . esc_attr( $value ) . '</option>';
			}
		}
		$output .= '</select>';
		$output .= '</div>';
		$output .= '<div class="clearfixit" style="padding: 5px 0; clear:both;"></div>';
		$output .= '</div>';


		$output .= '</div>';
		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	/**
	 * Renders the meta box.
	 *
	 * @param object $post the post object.
	 */
	public function render_metabox( $post ) {
		// Add nonce for security and authentication.
		wp_nonce_field( 'base_theme_classic_meta_nonce_action', 'base_theme_classic_meta_nonce' );
		$title_position = 'normal';
		$option_title_position = webapp()->option( get_post_type() . '_title_layout' );
		if ( 'above' === $option_title_position || 'normal' === $option_title_position ) {
			$title_position = $option_title_position;
		}
		$output = '<div class="base_classic_meta_boxes">';

		$output .= '<div class="base_classic_meta_boxes" style="padding: 10px 0 0;">';
		$output .= '<div style="padding-bottom:10px;">';
		$output .= '<label for="_bst_post_transparent" style="font-weight: 600;">' . esc_html__( 'Transparent Header', 'avanam' ) . '</label>';
		$output .= '</div>';
		$output .= '<div>';
		$trans_option_values = array(
			'default' => __( 'Default', 'avanam' ),
			'enable'  => __( 'Enable', 'avanam' ),
			'disable' => __( 'Disable', 'avanam' ),
		);
		$trans_select_value = get_post_meta( $post->ID, '_bst_post_transparent', true );
		$output .= '<select style="width:100%; box-sizing: border-box;" name="_bst_post_transparent">';
		foreach ( $trans_option_values as $key => $value ) {
			if ( $key == $trans_select_value ) {
				$output .= '<option value="' . esc_attr( $key ) . '" selected>' . esc_attr( $value ) . '</option>';
			} else {
				$output .= '<option value="' . esc_attr( $key ) . '">' . esc_attr( $value ) . '</option>';
			}
		}
		$output .= '</select>';
		$output .= '</div>';
		$output .= '<div class="clearfixit" style="padding: 5px 0; clear:both;"></div>';
		$output .= '</div>';
		$output .= '<div class="base_classic_meta_boxes" style="padding: 10px 0 0;">';
		$output .= '<div style="padding-bottom:10px;">';
		$output .= '<label for="_bst_post_title" style="font-weight: 600;">' . esc_html__( 'Display Title', 'avanam' ) . '</label>';
		$output .= '</div>';
		$output .= '<div>';
		$option_values = array(
			'default'       => __( 'Default', 'avanam' ),
			$title_position => __( 'Enable', 'avanam' ),
			'hide'          => __( 'Disable', 'avanam' ),
		);
		$select_value = get_post_meta( $post->ID, '_bst_post_title', true );
		$output .= '<select style="width:100%; box-sizing: border-box;" name="_bst_post_title">';
		foreach ( $option_values as $key => $value ) {
			if ( $key == $select_value ) {
				$output .= '<option value="' . esc_attr( $key ) . '" selected>' . esc_attr( $value ) . '</option>';
			} else {
				$output .= '<option value="' . esc_attr( $key ) . '">' . esc_attr( $value ) . '</option>';
			}
		}
		$output .= '</select>';
		$output .= '</div>';
		$output .= '<div class="clearfixit" style="padding: 5px 0; clear:both;"></div>';
		$output .= '</div>';

		$output .= '<div class="base_classic_meta_boxes" style="padding: 10px 0 0;">';
		$output .= '<div style="padding-bottom:10px;">';
		$output .= '<label for="_bst_post_layout" style="font-weight: 600;">' . esc_html__( 'Layout', 'avanam' ) . '</label>';
		$output .= '</div>';
		$output .= '<div>';
		$layout_option_values = array(
			'default'   => __( 'Default', 'avanam' ),
			'normal'    => __( 'Normal', 'avanam' ),
			'narrow'    => __( 'Narrow', 'avanam' ),
			'fullwidth' => __( 'Fullwidth', 'avanam' ),
			'left'      => __( 'Sidebar Left', 'avanam' ),
			'right'     => __( 'Sidebar Right', 'avanam' ),
		);
		$select_value = get_post_meta( $post->ID, '_bst_post_layout', true );
		$output .= '<select style="width:100%; box-sizing: border-box;" name="_bst_post_layout">';
		foreach ( $layout_option_values as $key => $value ) {
			if ( $key == $select_value ) {
				$output .= '<option value="' . esc_attr( $key ) . '" selected>' . esc_attr( $value ) . '</option>';
			} else {
				$output .= '<option value="' . esc_attr( $key ) . '">' . esc_attr( $value ) . '</option>';
			}
		}
		$output .= '</select>';
		$output .= '</div>';
		$output .= '<div class="clearfixit" style="padding: 5px 0; clear:both;"></div>';
		$output .= '</div>';

		$output .= '<div class="base_classic_meta_boxes" style="padding: 10px 0 0;">';
		$output .= '<div style="padding-bottom:10px;">';
		$output .= '<label for="_bst_post_content_style" style="font-weight: 600;">' . esc_html__( 'Content Style', 'avanam' ) . '</label>';
		$output .= '</div>';
		$output .= '<div>';
		$content_option_values = array(
			'default' => __( 'Default', 'avanam' ),
			'boxed'   => __( 'Boxed', 'avanam' ),
			'unboxed' => __( 'Unboxed', 'avanam' ),
		);
		$content_select_value = get_post_meta( $post->ID, '_bst_post_content_style', true );
		$output .= '<select style="width:100%; box-sizing: border-box;" name="_bst_post_content_style">';
		foreach ( $content_option_values as $key => $value ) {
			if ( $key == $content_select_value ) {
				$output .= '<option value="' . esc_attr( $key ) . '" selected>' . esc_attr( $value ) . '</option>';
			} else {
				$output .= '<option value="' . esc_attr( $key ) . '">' . esc_attr( $value ) . '</option>';
			}
		}
		$output .= '</select>';
		$output .= '</div>';
		$output .= '<div class="clearfixit" style="padding: 5px 0; clear:both;"></div>';
		$output .= '</div>';

		$output .= '<div class="base_classic_meta_boxes" style="padding: 10px 0 0;">';
		$output .= '<div style="padding-bottom:10px;">';
		$output .= '<label for="_bst_post_vertical_padding" style="font-weight: 600;">' . esc_html__( 'Content Vertical Padding', 'avanam' ) . '</label>';
		$output .= '</div>';
		$output .= '<div>';
		$padding_option_values = array(
			'default' => __( 'Default', 'avanam' ),
			'show'    => __( 'Enable', 'avanam' ),
			'hide'    => __( 'Disable', 'avanam' ),
			'top'     => __( 'Top Only', 'avanam' ),
			'bottom'  => __( 'Bottom Only', 'avanam' ),
		);
		$padding_select_value = get_post_meta( $post->ID, '_bst_post_vertical_padding', true );
		$output .= '<select style="width:100%; box-sizing: border-box;" name="_bst_post_vertical_padding">';
		foreach ( $padding_option_values as $key => $value ) {
			if ( $key == $padding_select_value ) {
				$output .= '<option value="' . esc_attr( $key ) . '" selected>' . esc_attr( $value ) . '</option>';
			} else {
				$output .= '<option value="' . esc_attr( $key ) . '">' . esc_attr( $value ) . '</option>';
			}
		}
		$output .= '</select>';
		$output .= '</div>';
		$output .= '<div class="clearfixit" style="padding: 5px 0; clear:both;"></div>';
		$output .= '</div>';

		$output .= '</div>';
		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	/**
	 * Register Post Meta options
	 *
	 * @param array  $args the post type args.
	 * @param string $post_type the post type.
	 */
	public function add_needed_custom_fields_support( $args, $post_type ) {
		if ( is_array( $args ) && isset( $args['public'] ) && $args['public'] && isset( $args['supports'] ) && is_array( $args['supports'] ) && ! in_array( 'custom-fields', $args['supports'], true ) ) {
			$args['supports'][] = 'custom-fields';
		}

		return $args;
	}
	/**
	 * Register Post Meta options
	 */
	public function register_meta() {
		register_post_meta(
			'', // Pass an empty string to register the meta key across all existing post types.
			'_bst_post_transparent',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'auth_callback' => '__return_true',
			)
		);
		register_post_meta(
			'', // Pass an empty string to register the meta key across all existing post types.
			'_bst_post_title',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'auth_callback' => '__return_true',
			)
		);
		register_post_meta(
			'', // Pass an empty string to register the meta key across all existing post types.
			'_bst_post_layout',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'auth_callback' => '__return_true',
			)
		);
		register_post_meta(
			'', // Pass an empty string to register the meta key across all existing post types.
			'_bst_post_sidebar_id',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'auth_callback' => '__return_true',
			)
		);
		register_post_meta(
			'', // Pass an empty string to register the meta key across all existing post types.
			'_bst_post_content_style',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'auth_callback' => '__return_true',
			)
		);
		register_post_meta(
			'', // Pass an empty string to register the meta key across all existing post types.
			'_bst_post_vertical_padding',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'auth_callback' => '__return_true',
			)
		);
		register_post_meta(
			'', // Pass an empty string to register the meta key across all existing post types.
			'_bst_post_feature',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'auth_callback' => '__return_true',
			)
		);
		register_post_meta(
			'', // Pass an empty string to register the meta key across all existing post types.
			'_bst_post_feature_position',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'auth_callback' => '__return_true',
			)
		);
		register_post_meta(
			'', // Pass an empty string to register the meta key across all existing post types.
			'_bst_post_header',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'boolean',
				'auth_callback' => '__return_true',
			)
		);
		register_post_meta(
			'', // Pass an empty string to register the meta key across all existing post types.
			'_bst_post_footer',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'boolean',
				'auth_callback' => '__return_true',
			)
		);
	}
	/**
	 * Enqueue Script for Meta options
	 */
	public function script_enqueue() {
		global $pagenow;
		if ( $pagenow === 'widgets.php' ) {
			return;
		}
		if ( is_customize_preview() ) {
			return;
		}
		$post_type = get_post_type();
		$post_type_object = get_post_type_object( get_post_type() );
		if ( is_object( $post_type_object ) ) {
			$post_type_name = $post_type_object->labels->singular_name;
		} else {
			$post_type_name = $post_type;
		}
		$ignore_type       = webapp()->get_public_post_types_to_ignore();
		if ( in_array( $post_type, $ignore_type, true ) ) {
			return;
		}
		$boxed            = 'boxed';
		$layout           = 'normal';
		$title            = 'normal';
		$sidebar          = 'none';
		$vpadding         = 'show';
		$feature          = 'hide';
		$feature_position = 'above';
		$title_position   = 'normal';

		$option_layout = webapp()->option( $post_type . '_layout' );
		if ( 'left' === $option_layout || 'right' === $option_layout ) {
			$sidebar = $option_layout;
		}
		$option_title = webapp()->option( $post_type . '_title' );
		if ( false === $option_title ) {
			$title = 'hide';
		} else {
			$option_title_layout = webapp()->option( $post_type . '_title_layout' );
			if ( 'above' === $option_title_layout || 'normal' === $option_title_layout ) {
				$title = $option_title_layout;
			}
		}
		$option_title_position = webapp()->option( $post_type . '_title_layout' );
		if ( 'above' === $option_title_position || 'normal' === $option_title_position ) {
			$title_position = $option_title_position;
		}
		$option_nav = webapp()->option( $post_type . '_navigation' );
		if ( $option_nav ) {
			$navigation = 'show';
		}
		$option_boxed = webapp()->option( $post_type . '_content_style' );
		if ( 'unboxed' === $option_boxed || 'boxed' === $option_boxed ) {
			$boxed = $option_boxed;
		}
		$option_feature = webapp()->option( $post_type . '_feature' );
		if ( $option_feature ) {
			$feature = 'show';
		}
		$option_feature_position = webapp()->option( $post_type . '_feature_postition' );
		if ( 'above' === $option_feature_position || 'below' === $option_feature_position || 'behind' === $option_feature_position ) {
			$feature_position = $option_feature_position;
		}
		$option_layout = webapp()->option( $post_type . '_layout' );
		if ( 'narrow' === $option_layout || 'fullwidth' === $option_layout ) {
			$layout = $option_layout;
		} elseif ( 'left' === $option_layout || 'right' === $option_layout ) {
			$layout = 'narrow';
		}
		$option_vpadding = webapp()->option( $post_type . '_vertical_padding' );
		if ( $option_vpadding ) {
			$vpadding = $option_vpadding;
		}
		$path = get_template_directory_uri() . '/inc/meta/react/';
		wp_enqueue_style( 'base-meta', $path . 'build/meta-controls.css', false, AVANAM_VERSION );
		wp_enqueue_script( 'base-meta' );
		wp_localize_script(
			'base-meta',
			'baseMetaParams',
			array(
				'post_type'        => $post_type,
				'post_type_name'   => $post_type_name,
				'layout'           => $layout,
				'boxed'            => $boxed,
				'title'            => $title,
				'title_position'   => $title_position,
				'sidebar'          => $sidebar,
				'vpadding'         => $vpadding,
				'supports_feature' => post_type_supports( $post_type, 'thumbnail' ),
				'feature'          => $feature,
				'feature_position' => $feature_position,
				'sidebars'         => $this->get_sidebar_options(),
			)
		);
	}
	/**
	 * Get all Sidebar Options
	 */
	public function get_sidebar_options() {
		$sidebars = array(
			array( 'value' => 'default', 'label' => __( 'Default', 'avanam' ) )
		);
		$nonsidebars = array(
			'header1',
			'header2',
			'footer1',
			'footer2',
			'footer3',
			'footer4',
			'footer5',
			'footer6',
		);
		foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) {
			if ( ! in_array( $sidebar['id'], $nonsidebars, true ) ) {
				$sidebars[] = array( 'value' => $sidebar['id'], 'label' => $sidebar['name'] );
			}
		}
		return $sidebars;
	}
	/**
	 * Get the asset file produced by wp scripts.
	 *
	 * @param string $filepath the file path.
	 * @return array
	 */
	public function get_asset_file( $filepath ) {
		$asset_path = get_template_directory() . $filepath . '.asset.php';

		return file_exists( $asset_path )
			? include $asset_path
			: array(
				'dependencies' => array( 'wp-plugins', 'wp-edit-post', 'wp-element' ),
				'version'      => AVANAM_VERSION,
			);
	}
	/**
	 * Register Script for Meta options
	 */
	public function plugin_register() {
		$path  = get_template_directory_uri() . '/assets/js/admin/meta.js';
		$asset = $this->get_asset_file( '/assets/js/admin/meta' );
		wp_register_script(
			'base-meta',
			$path,
			$asset['dependencies'],
			$asset['version']
		);
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'base-meta', 'avanam' );
		}
	}
}
Theme_Meta::get_instance();
