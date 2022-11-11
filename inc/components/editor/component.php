<?php
/**
 * Base\Editor\Component class
 *
 * @package Base
 */

namespace Base\Editor;

use Base\Component_Interface;
use function Base\webapp;
use function add_action;
use function add_filter;
use function add_theme_support;

/**
 * Class for integrating with the block editor.
 *
 * @link https://wordpress.org/gutenberg/handbook/extensibility/theme-support/
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'editor';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'after_setup_theme', array( $this, 'action_add_editor_support' ) );
		add_filter( 'admin_body_class', array( $this, 'filter_admin_body_class' ) );
		remove_action( 'admin_head-post.php', 'base_blocks_admin_editor_width', 100 );
		remove_action( 'admin_head-post-new.php', 'base_blocks_admin_editor_width', 100 );
		add_filter( 'base_blocks_editor_width', array( $this, 'base_blocks_admin_editor_width_filter' ), 100 );
		add_action( 'after_setup_theme', array( $this, 'google_font_add_editor_styles' ) );
	}
	/**
	 * Registers an editor stylesheet for the current theme.
	 */
	public function google_font_add_editor_styles() {
		if ( webapp()->sub_option( 'base_font', 'google' ) ) {
			$font        = webapp()->option( 'base_font' );
			if ( $font['family'] ) {
				$font_string = $font['family'] . ':' . ( $font['variant'] ? $font['variant'] : '400' ) . ',700';
				if ( webapp()->sub_option( 'heading_font', 'google' ) ) {
					$heading_font = webapp()->option( 'heading_font' );
					if ( $heading_font['family'] ) {
						$font_string .= '%7C' . $heading_font['family'] . ':400,700';
					}
				}
				$font_url    = str_replace( ',', '%2C', '//fonts.googleapis.com/css?family=' . $font_string );
				add_editor_style( $font_url );
			}
		} elseif ( webapp()->sub_option( 'heading_font', 'google' ) ) {
			$font        = webapp()->option( 'heading_font' );
			if ( $font['family'] ) {
				$font_string = $font['family'] . ':400,700';
				$font_url    = str_replace( ',', '%2C', '//fonts.googleapis.com/css?family=' . $font_string );
				add_editor_style( $font_url );
			}
		}
	}
	/**
	 * Adds filter for Theme Editor Width.
	 *
	 * @param bool $enable the editor width enable.
	 */
	public function base_blocks_admin_editor_width_filter( $enable ) {
		return false;
	}
	/**
	 * Adds filter for admin body class to add in layout information.
	 *
	 * @param string $classes the admin classes.
	 */
	public function filter_admin_body_class( $classes ) {
		$screen = get_current_screen();
		if ( 'post' == $screen->base ) {
			global $post;
			$post_type  = get_post_type();
			$post_layout = array(
				'layout'           => 'normal',
				'boxed'            => 'boxed',
				'feature'          => 'hide',
				'comments'         => 'hide',
				'navigation'       => 'hide',
				'title'            => 'normal',
				'sidebar'          => 'none',
				'padding'         => 'show',
			);
			$post_layout = apply_filters( 'base_post_layout', $post_layout );

			$postlayout   = get_post_meta( $post->ID, '_bst_post_layout', true );
			$postboxed    = get_post_meta( $post->ID, '_bst_post_content_style', true );
			$postfeature  = get_post_meta( $post->ID, '_bst_post_feature', true );
			$vpadding     = get_post_meta( $post->ID, '_bst_post_vertical_padding', true );
			$postnav      = get_post_meta( $post->ID, '_bst_post_navigation', true );
			$posttitle    = get_post_meta( $post->ID, '_bst_post_title', true );
			$postsidebar  = get_post_meta( $post->ID, '_bst_post_layout', true );
			if ( isset( $postlayout ) && ( 'left' === $postsidebar || 'right' === $postsidebar ) ) {
				$post_layout['sidebar'] = $postlayout;
			} elseif ( ( isset( $postlayout ) && 'default' === $postlayout ) || empty( $postlayout ) ) {
				$option_layout = webapp()->option( $post_type . '_layout' );
				if ( 'left' === $option_layout || 'right' === $option_layout ) {
					$post_layout['sidebar'] = $option_layout;
				}
			}
			if ( isset( $posttitle ) && ( 'above' === $posttitle || 'normal' === $posttitle || 'hide' === $posttitle ) ) {
				$post_layout['title'] = $posttitle;
			} else {
				$option_title = webapp()->option( $post_type . '_title' );
				if ( false === $option_title ) {
					$post_layout['title'] = 'hide';
				} else {
					$option_title_layout = webapp()->option( $post_type . '_title_layout' );
					if ( 'above' === $option_title_layout || 'normal' === $option_title_layout ) {
						$post_layout['title'] = $option_title_layout;
					}
				}
			}
			if ( isset( $postnav ) && ( 'show' === $postnav || 'hide' === $postnav ) ) {
				$post_layout['navigation'] = $postnav;
			} else {
				$option_nav = webapp()->option( $post_type . '_navigation' );
				if ( $option_nav ) {
					$post_layout['navigation'] = 'show';
				}
			}
			if ( isset( $vpadding ) && ( 'show' === $vpadding || 'hide' === $vpadding ) ) {
				$post_layout['padding'] = $vpadding;
			} else {
				$option_padding = webapp()->option( $post_type . '_vertical_padding' );
				if ( $option_padding ) {
					$post_layout['padding'] = 'show';
				}
			}
			if ( isset( $postboxed ) && ( 'unboxed' === $postboxed || 'boxed' === $postboxed ) ) {
				$post_layout['boxed'] = $postboxed;
			} else {
				$option_boxed = webapp()->option( $post_type . '_content_style' );
				if ( 'unboxed' === $option_boxed || 'boxed' === $option_boxed ) {
					$post_layout['boxed'] = $option_boxed;
				}
			}
			if ( isset( $postfeature ) && ( 'show' === $postfeature || 'hide' === $postfeature ) ) {
				$post_layout['feature'] = $postfeature;
			} else {
				$option_feature = webapp()->option( $post_type . '_feature' );
				if ( $option_feature ) {
					$post_layout['feature'] = 'show';
				}
			}
			if ( isset( $postlayout ) && ( 'narrow' === $postlayout || 'fullwidth' === $postlayout ) ) {
				$post_layout['layout'] = $postlayout;
			} else if ( isset( $postlayout ) && ( 'left' === $postlayout || 'right' === $postlayout ) ) {
				$post_layout['layout'] = 'narrow';
			} elseif ( ( isset( $postlayout ) && 'default' === $postlayout ) || empty( $postlayout ) ) {
				$option_layout = webapp()->option( $post_type . '_layout' );
				if ( 'narrow' === $option_layout || 'fullwidth' === $option_layout ) {
					$post_layout['layout'] = $option_layout;
				} else if ( 'left' === $option_layout || 'right' === $option_layout ) {
					$post_layout['layout'] = 'narrow';
				}
			}
			$classes .= ' post-content-width-' . esc_attr( $post_layout['layout'] ) . ' admin-color-pcw-' . esc_attr( $post_layout['layout'] ) . ' post-content-style-' . esc_attr( $post_layout['boxed'] ) . ' admin-color-pcs-' . esc_attr( $post_layout['boxed'] ) . ' admin-color-post-type-' . esc_attr( $post_type ) . ' post-content-vertical-padding-' . esc_attr( $post_layout['padding'] ) . ' admin-color-pcvp-' . esc_attr( $post_layout['padding'] ) . ' post-content-title-' . esc_attr( $post_layout['title'] ) . ' admin-color-pct-' . esc_attr( $post_layout['title'] ) . '  post-content-sidebar-' . esc_attr( $post_layout['sidebar'] ) . ' ';
		}
		return $classes;
	}
	/**
	 * Adds support for various editor features.
	 */
	public function action_add_editor_support() {
		// Add support for post thumbnails.
		add_theme_support( 'post-thumbnails' );

		// Add support for editor styles.
		add_theme_support( 'editor-styles' );

		// Add support for default block styles.
		// Don't add this "add_theme_support( 'wp-block-styles' );" because theme provides it own styling.
		// add_theme_support( 'wp-block-styles' );
		// Add support for wide-aligned images.
		add_theme_support( 'align-wide' );
		add_theme_support( 'custom-spacing' );
		if ( apply_filters( 'base-theme-block-templates-support', true ) ) {
			add_theme_support( 'block-templates' );
		}
		/*
		 * Add support custom font sizes.
		 * MOVED TO JSON
		 */
		add_theme_support(
			'editor-font-sizes',
			array(
				array(
					'name'      => __( 'Small', 'avanam' ),
					'shortName' => __( 'S', 'avanam' ),
					'size'      => 14,
					'slug'      => 'small',
				),
				array(
					'name'      => __( 'Medium', 'avanam' ),
					'shortName' => __( 'M', 'avanam' ),
					'size'      => 24,
					'slug'      => 'medium',
				),
				array(
					'name'      => __( 'Large', 'avanam' ),
					'shortName' => __( 'L', 'avanam' ),
					'size'      => 32,
					'slug'      => 'large',
				),
				array(
					'name'      => __( 'Larger', 'avanam' ),
					'shortName' => __( 'XL', 'avanam' ),
					'size'      => 40,
					'slug'      => 'larger',
				),
			)
		);
	}
}
