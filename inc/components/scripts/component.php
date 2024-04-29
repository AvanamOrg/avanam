<?php
/**
 * Base\Scripts\Component class
 *
 * @package Base
 */

namespace Base\Scripts;

use Base\Component_Interface;
use function Base\webapp;
use WP_Post;
use function add_action;
use function add_filter;
use function wp_enqueue_script;
use function get_theme_file_uri;
use function get_theme_file_path;
use function wp_script_add_data;
use function wp_localize_script;

/**
 * Class for adding scripts to the front end.
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'scripts';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', array( $this, 'action_enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'ie_11_support_scripts' ), 60 );
	}
	/**
	 * Add some very basic support for IE11
	 */
	public function ie_11_support_scripts() {
		if ( apply_filters( 'base_add_ie11_support', false ) || webapp()->option( 'ie11_basic_support' ) ) {
			wp_enqueue_style( 'base-ie11', get_theme_file_uri( '/assets/css/ie.min.css' ), array(), AVANAM_VERSION );
			wp_enqueue_script(
				'base-css-vars-poly',
				get_theme_file_uri( '/assets/js/css-vars-ponyfill.min.js' ),
				array(),
				AVANAM_VERSION,
				true
			);
			wp_script_add_data( 'base-css-vars-poly', 'async', true );
			wp_script_add_data( 'base-css-vars-poly', 'precache', true );
			wp_enqueue_script(
				'base-ie11',
				get_theme_file_uri( '/assets/js/ie.min.js' ),
				array(),
				AVANAM_VERSION,
				true
			);
			wp_script_add_data( 'base-ie11', 'async', true );
			wp_script_add_data( 'base-ie11', 'precache', true );
		}
	}
	/**
	 * Enqueues a script that improves navigation menu accessibility as well as sticky header etc.
	 */
	public function action_enqueue_scripts() {

		// If the AMP plugin is active, return early.
		if ( webapp()->is_amp() ) {
			return;
		}

		$breakpoint = 1024;
		if ( webapp()->sub_option( 'header_mobile_switch', 'size' ) ) {
			$breakpoint = webapp()->sub_option( 'header_mobile_switch', 'size' );
		}
		// Enqueue the slide script.
		wp_register_script(
			'bst-splide',
			get_theme_file_uri( '/assets/js/splide.min.js' ),
			array(),
			AVANAM_VERSION,
			true
		);
		wp_script_add_data( 'bst-splide', 'async', true );
		wp_script_add_data( 'bst-splide', 'precache', true );
		// Enqueue the slide script.
		wp_register_script(
			'base-slide-init',
			get_theme_file_uri( '/assets/js/splide-init.min.js' ),
			array( 'bst-splide', 'base-navigation' ),
			AVANAM_VERSION,
			true
		);
		wp_script_add_data( 'base-slide-init', 'async', true );
		wp_script_add_data( 'base-slide-init', 'precache', true );
		wp_localize_script(
			'base-slide-init',
			'baseSlideConfig',
			array(
				'of'    => __( 'of', 'avanam' ),
				'to'    => __( 'to', 'avanam' ),
				'slide' => __( 'Slide', 'avanam' ),
				'next'  => __( 'Next', 'avanam' ),
				'prev'  => __( 'Previous', 'avanam' ),
			)
		);
		if ( webapp()->option( 'lightbox' ) ) {
			// Enqueue the lightbox script.
			wp_enqueue_script(
				'base-simplelightbox',
				get_theme_file_uri( '/assets/js/simplelightbox.min.js' ),
				array(),
				AVANAM_VERSION,
				true
			);
			wp_script_add_data( 'base-simplelightbox', 'async', true );
			wp_script_add_data( 'base-simplelightbox', 'precache', true );
			// Enqueue the slide script.
			wp_enqueue_script(
				'base-lightbox-init',
				get_theme_file_uri( '/assets/js/lightbox-init.min.js' ),
				array( 'base-simplelightbox' ),
				AVANAM_VERSION,
				true
			);
			wp_script_add_data( 'base-lightbox-init', 'async', true );
			wp_script_add_data( 'base-lightbox-init', 'precache', true );
		}
		// Main js file.
		$file = 'navigation.min.js';
		// Lets make it possile to load a lighter file if things are not being used.
		if ( 'no' === webapp()->option( 'header_sticky' ) && 'no' === webapp()->option( 'mobile_header_sticky' ) && ! webapp()->option( 'enable_scroll_to_id' ) && ! webapp()->option( 'scroll_up' ) ) {
			$file = 'navigation-lite.min.js';
		}
		wp_enqueue_script(
			'base-navigation',
			get_theme_file_uri( '/assets/js/' . $file ),
			array(),
			AVANAM_VERSION,
			true
		);
		wp_script_add_data( 'base-navigation', 'async', true );
		wp_script_add_data( 'base-navigation', 'precache', true );
		wp_localize_script(
			'base-navigation',
			'baseConfig',
			array(
				'screenReader' => array(
					'expand'     => __( 'Child menu', 'avanam' ),
					'expandOf'   => __( 'Child menu of', 'avanam' ),
					'collapse'   => __( 'Child menu', 'avanam' ),
					'collapseOf' => __( 'Child menu of', 'avanam' ),
				),
				'breakPoints' => array(
					'desktop' => esc_attr( $breakpoint ),
					'tablet' => 768,
				),
				'scrollOffset' => apply_filters( 'base_scroll_to_id_additional_offset', 0 ),
			)
		);
	}
}
