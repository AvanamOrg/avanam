<?php
/**
 * Base\BuddyBoss\Component class
 *
 * @package Base
 */

namespace Base\BuddyBoss;

use Base\Component_Interface;
use function Base\webapp;
use function add_action;
use function add_theme_support;
use function have_posts;
use function the_post;
use function is_search;
use function bp_core_get_directory_page_id;
use function bp_current_component;

/**
 * Class for adding BuddyBoss plugin support.
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'buddyboss';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_filter( 'base_post_layout', array( $this, 'filter_layout_for_component_pages' ) );
	}
	/**
	 * Filters the layout array to check the pages which are component pages for buddyboss
	 *
	 * @param string $layout the entry container class.
	 */
	public function filter_layout_for_component_pages( $layout ) {
		if ( is_page() && bp_current_component() ) {
			$component_id = bp_core_get_directory_page_id();
			if ( $component_id ) {
				// Layout.
				$postlayout = get_post_meta( $component_id, '_bst_post_layout', true );
				if ( isset( $postlayout ) && ( 'narrow' === $postlayout || 'fullwidth' === $postlayout ) ) {
					$layout['layout'] = $postlayout;
				}
				// Sidebar ID.
				$postsidebar    = get_post_meta( $component_id, '_bst_post_sidebar_id', true );
				if ( isset( $postsidebar ) && ! empty( $postsidebar ) && 'defualt' !== $postsidebar && 'default' !== $postsidebar ) {
					$layout['sidebar_id'] = $postsidebar;
				}
				// Boxed Style.
				$postboxed      = get_post_meta( $component_id, '_bst_post_content_style', true );
				if ( isset( $postboxed ) && ( 'unboxed' === $postboxed || 'boxed' === $postboxed ) ) {
					$layout['boxed'] = $postboxed;
				}
				// Post Feature.
				$postfeature = get_post_meta( $component_id, '_bst_post_feature', true );
				if ( isset( $postfeature ) && ( 'show' === $postfeature || 'hide' === $postfeature ) ) {
					$layout['feature'] = $postfeature;
				}
				// Post Feature position.
				$postf_position = get_post_meta( $component_id, '_bst_post_feature_position', true );
				if ( isset( $postf_position ) && ( 'above' === $postf_position || 'behind' === $postf_position || 'below' === $postf_position ) ) {
					$layout['feature_position'] = $postf_position;
				}
				// Post title.
				$posttitle = get_post_meta( $component_id, '_bst_post_title', true );
				if ( isset( $posttitle ) && ( 'above' === $posttitle || 'normal' === $posttitle || 'hide' === $posttitle ) ) {
					$layout['title'] = $posttitle;
				}
				// Post transparent.
				$posttrans = get_post_meta( $component_id, '_bst_post_transparent', true );
				if ( isset( $posttrans ) && ( 'enable' === $posttrans || 'disable' === $posttrans ) ) {
					$layout['transparent'] = $posttrans;
				}
				// Post Vertical Padding.
				$postvpadding = get_post_meta( $component_id, '_bst_post_vertical_padding', true );
				if ( isset( $postvpadding ) && ( 'show' === $postvpadding || 'hide' === $postvpadding || 'top' === $postvpadding || 'bottom' === $postvpadding ) ) {
					$layout['vpadding'] = $postvpadding;
				}
				// header.
				$postheader = get_post_meta( $component_id, '_bst_post_header', true );
				if ( isset( $postheader ) && true == $postheader ) {
					$layout['header'] = 'disable';
				}
				// Footer.
				$postfooter = get_post_meta( $component_id, '_bst_post_footer', true );
				if ( isset( $postfooter ) && true == $postfooter ) {
					$layout['footer'] = 'disable';
				}
			}
		}
		return $layout;
	}
}
