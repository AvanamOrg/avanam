<?php
/**
 * Template part for displaying a post's title
 *
 * @package Base
 */

namespace Base;

$html_tag = 'span';
if ( is_singular( 'product' ) ) {
	$title_element = webapp()->option( 'product_content_element_title' );
	if ( isset( $title_element ) && is_array( $title_element ) && false === $title_element['enabled'] ) {
		$html_tag = 'h1';
	}
}
the_title( '<' . $html_tag . ' class="extra-title">', '</' . $html_tag . '>' );
