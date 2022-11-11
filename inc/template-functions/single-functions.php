<?php
/**
 * Calls in content using theme hooks.
 *
 * @package Base
 */

namespace Base;

use function get_template_part;
use function get_the_categories;
use function comment_form;

defined( 'ABSPATH' ) || exit;

/**
 * Single Content
 */
function single_markup() {
	get_template_part( 'template-parts/content/single', get_post_type() );
}

/**
 * Single Inner content.
 */
function single_content() {
	get_template_part( 'template-parts/content/single-entry', get_post_type() );
}

/**
 * Get the related posts args.
 *
 * @param number $post_id the post id.
 * @return array query args.
 */
function get_related_posts_args( $post_id ) {
	if ( apply_filters( 'base_related_posts_use_tags', true ) ) {
		// Get categories.
		$categories = get_the_terms( $post_id, 'category' );
		if ( empty( $categories ) || is_wp_error( $categories ) ) {
			$categories = array();
		}
		$category_list = wp_list_pluck( $categories, 'slug' );
		// Get Tags.
		$tags     = get_the_terms( $post_id, 'post_tag' );
		if ( empty( $tags ) || is_wp_error( $tags ) ) {
			$tags = array();
		}
		$tag_list = wp_list_pluck( $tags, 'slug' );
		$related_args = array(
			'post_type'              => 'post',
			'posts_per_page'         => 6,
			'no_found_rows'          => true,
			'post_status'            => 'publish',
			// 'update_post_meta_cache' => false,
			// 'update_post_term_cache' => false,
			'post__not_in'           => array( $post_id ),
			'orderby'                => 'rand',
			'tax_query'              => array(
				'relation' => 'OR',
				array(
					'taxonomy' => 'category',
					'field'    => 'slug',
					'terms'    => $category_list,
				),
				array(
					'taxonomy' => 'post_tag',
					'field'    => 'slug',
					'terms'    => $tag_list,
				),
			),
		);
	} else {
		$categories = get_the_terms( $post_id, 'category' );
		if ( empty( $categories ) || is_wp_error( $categories ) ) {
			$categories = array();
		}
		$category_list = wp_list_pluck( $categories, 'term_id' );
		$related_args = array(
			'post_type'              => 'post',
			'posts_per_page'         => 6,
			'no_found_rows'          => true,
			'post_status'            => 'publish',
			// 'update_post_meta_cache' => false,
			// 'update_post_term_cache' => false,
			'post__not_in'           => array( $post_id ),
			'orderby'                => 'rand',
			'category__in'           => $category_list,

		);
	}
	return apply_filters( 'base_related_posts_args', $related_args );
}

/**
 * Get the related posts args.
 *
 * @return array column args.
 */
function get_related_posts_columns() {
	if ( webapp()->option( 'post_related_columns' ) ) {
		if ( webapp()->option( 'post_related_columns' ) == 2 ) {
			$cols = array(
				'xxl' => 2,
				'xl'  => 2,
				'md'  => 2,
				'sm'  => 2,
				'xs'  => 2,
				'ss'  => 1,
			);
		} else if ( webapp()->option( 'post_related_columns' ) == 4 ) {
			$cols = array(
				'xxl' => 4,
				'xl'  => 4,
				'md'  => 4,
				'sm'  => 3,
				'xs'  => 2,
				'ss'  => 2,
			);
		} else {
			$cols = array(
				'xxl' => 3,
				'xl'  => 3,
				'md'  => 3,
				'sm'  => 2,
				'xs'  => 2,
				'ss'  => 1,
			);
		}
	} else if ( webapp()->has_sidebar() ) {
		$cols = array(
			'xxl' => 2,
			'xl'  => 2,
			'md'  => 2,
			'sm'  => 2,
			'xs'  => 2,
			'ss'  => 1,
		);
	} else {
		$cols = array(
			'xxl' => 3,
			'xl'  => 3,
			'md'  => 3,
			'sm'  => 2,
			'xs'  => 2,
			'ss'  => 1,
		);
	}
	return apply_filters( 'base_related_posts_carousel_columns', $cols );
}

/**
 * Comment List
 */
function comments_list() {
	get_template_part( 'template-parts/content/comments-list' );
}
/**
 * Comment Form
 */
function comments_form() {
	comment_form();
}
/**
 * 404 Content.
 */
function get_404_content() {
	get_template_part( 'template-parts/content/error', '404' );
}

