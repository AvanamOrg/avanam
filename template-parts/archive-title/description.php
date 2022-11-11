<?php
/**
 * Template part for displaying the archive description
 *
 * @package Base
 */

namespace Base;

if ( apply_filters( 'base_show_archive_description', ( is_tax() || is_category() || is_tag() || ( is_archive() && ! is_search() && ! is_post_type_archive( 'product' ) ) ) ) ) {
	the_archive_description( '<div class="archive-description">', '</div>' );
}
