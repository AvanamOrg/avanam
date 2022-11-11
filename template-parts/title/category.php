<?php
/**
 * Template part for displaying a post's category terms
 *
 * @package Base
 */

namespace Base;

use WPSEO_Primary_Term;

global $post;
$main_term = false;
if ( class_exists( 'WPSEO_Primary_Term' ) ) {
	$wpseo_term = new WPSEO_Primary_Term( 'product_cat', $post->ID );
	$wpseo_term = $wpseo_term->get_primary_term();
	$wpseo_term = get_term( $wpseo_term );
	if ( is_wp_error( $wpseo_term ) ) {
		$main_term = false;
	} else {
		$main_term = $wpseo_term;
	}
} elseif ( class_exists( 'RankMath' ) ) {
	$wpseo_term = get_post_meta( $post->ID, 'rank_math_primary_product_cat', true );
	if ( $wpseo_term ) {
		$wpseo_term = get_term( $wpseo_term );
		if ( is_wp_error( $wpseo_term ) ) {
			$main_term = false;
		} else {
			$main_term = $wpseo_term;
		}
	} else {
		$main_term = false;
	}
}
if ( false === $main_term ) {
	$main_term = '';
	$terms     = wp_get_post_terms(
		$post->ID,
		'product_cat',
		array(
			'orderby' => 'parent',
			'order'   => 'DESC',
		)
	);
	if ( $terms && ! is_wp_error( $terms ) ) {
		if ( is_array( $terms ) ) {
			$main_term = $terms[0];
		}
	}
}
if ( $main_term ) {
	$term_title = $main_term->name;
	echo '<div class="entry-taxonomies">';
	echo '<a href="' . esc_attr( get_term_link( $main_term->slug, 'product_cat' ) ) . '" class="product-above-category single-category">';
	echo esc_html( $term_title );
	echo '</a>';
	echo '</div>';
}
