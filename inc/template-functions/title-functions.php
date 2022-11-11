<?php
/**
 * Calls in content using theme hooks.
 *
 * @package Base
 */

namespace Base;

use function Base\webapp;
use function get_template_part;

defined( 'ABSPATH' ) || exit;
/**
 * Hero Title
 */
function hero_title() {
	if ( webapp()->show_hero_title() ) {
		if ( is_singular( get_post_type() ) ) {
			get_template_part( 'template-parts/content/entry_hero' );
		} else {
			get_template_part( 'template-parts/content/archive_hero' );
		}
	}
}
/**
 * Page Title area
 *
 * @param string $item_type the single post type.
 * @param string $area the title area.
 */
function base_entry_header( $item_type = 'post', $area = 'normal' ) {
	webapp()->render_title( $item_type, $area );
}

/**
 * Archive Title area
 *
 * @param string $item_type the single post type.
 * @param string $area the title area.
 */
function base_entry_archive_header( $item_type = 'post_archive', $area = 'normal' ) {
	webapp()->render_archive_title( $item_type, $area );
}
