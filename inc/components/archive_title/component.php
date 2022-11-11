<?php
/**
 * Base\Archive_Title\Component class
 *
 * @package Base
 */

namespace Base\Archive_Title;

use Base\Component_Interface;
use Base\Templating_Component_Interface;
use function add_action;
use function apply_filters;
use function Base\webapp;
use function get_template_part;

/**
 * Class for adding custom title area support.
 *
 * Exposes template tags:
 * * `webapp()->render_archive_title()`
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'archive_title';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_filter( 'get_the_archive_title', array( $this, 'filter_archive_title' ) );
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
			'render_archive_title' => array( $this, 'render_archive_title' ),
		);
	}
	/**
	 * Update the archives to a better naming.
	 *
	 * @param string $title the name of the archive.
	 */
	public function filter_archive_title( $title ) {
		$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
		if ( is_home() && is_front_page() ) {
			$title = get_bloginfo( 'name' );
		} elseif ( is_category() ) {
			$title = single_cat_title( '', false );
		} elseif ( is_tag() ) {
			$title = single_tag_title( '', false );
		} elseif ( is_author() ) {
			/* translators: 1: Author Name */
			$title = sprintf( esc_html__( 'Author: %s', 'avanam' ), get_the_author() );
		} elseif ( is_day() ) {
			/* translators: 1: Day */
			$title = sprintf( esc_html__( 'Day: %s', 'avanam' ), get_the_date() );
		} elseif ( is_month() ) {
			/* translators: 1: Month */
			$title = sprintf( esc_html__( 'Month: %s', 'avanam' ), get_the_date( 'F Y' ) );
		} elseif ( is_year() ) {
			/* translators: 1: Year */
			$title = sprintf( esc_html__( 'Year: %s', 'avanam' ), get_the_date( 'Y' ) );
		} elseif ( class_exists( 'woocommerce' ) && is_shop() ) {
			$shop_page_id = wc_get_page_id( 'shop' );
			$title        = get_the_title( $shop_page_id );
		} elseif ( is_tax( array( 'product_cat', 'product_tag' ) ) ) {
			$title = single_term_title( '', false );
		} elseif ( $term ) {
			$title = $term->name;
		} elseif ( function_exists( 'is_bbpress' ) ) {
			if ( is_bbpress() ) {
				if ( bbp_is_forum_archive() ) {
					$title = bbp_get_forum_archive_title();
				} else {
					$title = bbp_title();
				}
			}
		} elseif ( function_exists( 'tribe_is_month' ) && ( tribe_is_month() || tribe_is_past() || tribe_is_upcoming() || tribe_is_day() ) ) {
			$title = tribe_get_event_label_plural();
		} elseif ( function_exists( 'tribe_is_photo' ) && ( tribe_is_map() || tribe_is_photo() || tribe_is_week() ) ) {
			$title = tribe_get_event_label_plural();
		} elseif ( is_post_type_archive( 'course' ) && function_exists( 'llms_get_page_id' ) ) {
			$title = get_the_title( llms_get_page_id( 'courses' ) );
		} elseif ( is_post_type_archive( 'llms_membership' ) && function_exists( 'llms_get_page_id' ) ) {
			$title = get_the_title( llms_get_page_id( 'memberships' ) );
		} elseif ( is_post_type_archive( 'ht_kb' ) ) {
			$title = get_the_title();
		} elseif ( is_post_type_archive() ) {
			$title = post_type_archive_title( '', false );
		}
		return $title;
	}
	/**
	 * Adds support to render header columns.
	 *
	 * @param string $archive_type the name of the row.
	 * @param string $area the name of the area.
	 */
	public function render_archive_title( $archive_type = 'post_archive', $area = 'normal' ) {
		$elements = webapp()->option( $archive_type . '_title_elements' );
		if ( isset( $elements ) && is_array( $elements ) && ! empty( $elements ) ) {
			foreach ( $elements as $item ) {
				if ( webapp()->sub_option( $archive_type . '_title_element_' . $item, 'enabled' ) ) {
					$template = apply_filters( 'base_title_elements_template_path', 'template-parts/archive-title/' . $item, $item, $area );
					get_template_part( $template );
				}
			}
		} else {
			get_template_part( 'template-parts/archive-title/title' );
		}
	}
}
