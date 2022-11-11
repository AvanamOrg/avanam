<?php
/**
 * Template part for displaying a post's title
 *
 * @package Base
 */

namespace Base;

$slug              = ( is_search() ? 'search' : get_post_type() );
$title_element = webapp()->option( $slug . '_archive_element_title', array(
	'enabled' => true,
) );
if ( isset( $title_element ) && is_array( $title_element ) && true === $title_element['enabled'] ) {
	if ( is_search() || is_archive() || is_home() ) {
		the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
	} else {
		the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );
	}
}
