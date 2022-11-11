<?php
/**
 * Calls in content using theme hooks.
 *
 * @package Base
 */

namespace Base;

use function Base\webapp;
use function add_action;

defined( 'ABSPATH' ) || exit;

/**
 * Base Header.
 *
 * @see Base\header_markup();
 */
add_action( 'base_header', 'Base\header_markup' );

/**
 * Base Header Rows
 *
 * @see Base\top_header();
 * @see Base\main_header();
 * @see Base\bottom_header();
 */
add_action( 'base_top_header', 'Base\top_header' );
add_action( 'base_main_header', 'Base\main_header' );
add_action( 'base_bottom_header', 'Base\bottom_header' );

/**
 * Base Header Columns
 *
 * @see Base\header_column();
 */
add_action( 'base_render_header_column', 'Base\header_column', 10, 2 );

/**
 * Base Mobile Header
 *
 * @see Base\mobile_header();
 */
add_action( 'base_mobile_header', 'Base\mobile_header' );

/**
 * Base Mobile Header Rows
 *
 * @see Base\mobile_top_header();
 * @see Base\mobile_main_header();
 * @see Base\mobile_bottom_header();
 */
add_action( 'base_mobile_top_header', 'Base\mobile_top_header' );
add_action( 'base_mobile_main_header', 'Base\mobile_main_header' );
add_action( 'base_mobile_bottom_header', 'Base\mobile_bottom_header' );

/**
 * Base Mobile Header Columns
 *
 * @see Base\mobile_header_column();
 */
add_action( 'base_render_mobile_header_column', 'Base\mobile_header_column', 10, 2 );

/**
 * Desktop Header Elements.
 *
 * @see Base\site_branding();
 * @see Base\primary_navigation();
 * @see Base\secondary_navigation();
 * @see Base\header_html();
 * @see Base\header_button();
 * @see Base\header_cart();
 * @see Base\header_social();
 * @see Base\header_search();
 */
add_action( 'base_site_branding', 'Base\site_branding' );
add_action( 'base_primary_navigation', 'Base\primary_navigation' );
add_action( 'base_secondary_navigation', 'Base\secondary_navigation' );
add_action( 'base_header_html', 'Base\header_html' );
add_action( 'base_header_button', 'Base\header_button' );
add_action( 'base_header_cart', 'Base\header_cart' );
add_action( 'base_header_social', 'Base\header_social' );
add_action( 'base_header_search', 'Base\header_search' );
/**
 * Mobile Header Elements.
 *
 * @see Base\mobile_site_branding();
 * @see Base\navigation_popup_toggle();
 * @see Base\mobile_navigation();
 * @see Base\mobile_html();
 * @see Base\mobile_button();
 * @see Base\mobile_cart();
 * @see Base\mobile_social();
 * @see Base\primary_navigation();
 */
add_action( 'base_mobile_site_branding', 'Base\mobile_site_branding' );
add_action( 'base_navigation_popup_toggle', 'Base\navigation_popup_toggle' );
add_action( 'base_mobile_navigation', 'Base\mobile_navigation' );
add_action( 'base_mobile_html', 'Base\mobile_html' );
add_action( 'base_mobile_button', 'Base\mobile_button' );
add_action( 'base_mobile_cart', 'Base\mobile_cart' );
add_action( 'base_mobile_social', 'Base\mobile_social' );

/**
 * Hero Title
 *
 * @see Base\hero_title();
 */
add_action( 'base_hero_header', 'Base\hero_title' );

/**
 * Page Title area
 *
 * @see Base\base_entry_header();
 */
add_action( 'base_entry_hero', 'Base\base_entry_header', 10, 2 );
add_action( 'base_entry_header', 'Base\base_entry_header', 10, 2 );

/**
 * Archive Title area
 *
 * @see Base\base_entry_archive_header();
 */
add_action( 'base_entry_archive_hero', 'Base\base_entry_archive_header', 10, 2 );
add_action( 'base_entry_archive_header', 'Base\base_entry_archive_header', 10, 2 );

/**
 * Singular Content
 *
 * @see Base\single_markup();
 */
add_action( 'base_single', 'Base\single_markup' );

/**
 * Singular Inner Content
 *
 * @see Base\single_content();
 */
add_action( 'base_single_content', 'Base\single_content' );

/**
 * 404 Content
 *
 * @see Base\get_404_content();
 */
add_action( 'base_404_content', 'Base\get_404_content' );

/**
 * Comments List
 *
 * @see Base\comments_list();
 */
add_action( 'base_comments', 'Base\comments_list' );

/**
 * Comment Form
 *
 * @see Base\comments_form();
 */
function check_comments_form_order() {
	$priority = ( webapp()->option( 'comment_form_before_list' ) ? 5 : 15 );
	add_action( 'base_comments', 'Base\comments_form', $priority );
}
add_action( 'base_comments', 'Base\check_comments_form_order', 1 );
/**
 * Archive Content
 *
 * @see Base\archive_markup();
 */
add_action( 'base_archive', 'Base\archive_markup' );

/**
 * Archive Entry Content.
 *
 * @see Base\loop_entry();
 */
add_action( 'base_loop_entry', 'Base\loop_entry' );

/**
 * Archive Entry thumbnail.
 *
 * @see Base\loop_entry_thumbnail();
 */
add_action( 'base_loop_entry_thumbnail', 'Base\loop_entry_thumbnail' );

/**
 * Archive Entry header.
 *
 * @see Base\loop_entry_header();
 */
add_action( 'base_loop_entry_content', 'Base\loop_entry_header', 10 );
/**
 * Archive Entry Summary.
 *
 * @see Base\loop_entry_summary();
 */
add_action( 'base_loop_entry_content', 'Base\loop_entry_summary', 20 );
/**
 * Archive Entry Footer.
 *
 * @see Base\loop_entry_footer();
 */
add_action( 'base_loop_entry_content', 'Base\loop_entry_footer', 30 );

/**
 * Archive Entry Taxonomies.
 *
 * @see Base\loop_entry_taxonomies();
 */
add_action( 'base_loop_entry_header', 'Base\loop_entry_taxonomies', 10 );
/**
 * Archive Entry Title.
 *
 * @see Base\loop_entry_title();
 */
add_action( 'base_loop_entry_header', 'Base\loop_entry_title', 20 );
/**
 * Archive Entry Meta.
 *
 * @see Base\loop_entry_meta();
 */
add_action( 'base_loop_entry_header', 'Base\loop_entry_meta', 30 );

/**
 * Main Call for Base footer
 *
 * @see Base\footer_markup();
 */
add_action( 'base_footer', 'Base\footer_markup' );

/**
 * Footer Top Row
 *
 * @see Base\top_footer();
 */
add_action( 'base_top_footer', 'Base\top_footer' );

/**
 * Footer Middle Row
 *
 * @see Base\middle_footer()
 */
add_action( 'base_middle_footer', 'Base\middle_footer' );

/**
 * Footer Bottom Row
 *
 * @see Base\bottom_footer()
 */
add_action( 'base_bottom_footer', 'Base\bottom_footer' );

/**
 * Footer Column
 *
 * @see Base\footer_column()
 */
add_action( 'base_render_footer_column', 'Base\footer_column', 10, 2 );


/**
 * Footer Elements
 *
 * @see Base\footer_html();
 * @see Base\footer_navigation()
 * @see Base\footer_social()
 */
add_action( 'base_footer_html', 'Base\footer_html' );
add_action( 'base_footer_navigation', 'Base\footer_navigation' );
add_action( 'base_footer_social', 'Base\footer_social' );

/**
 * WP Footer.
 *
 * @see Base\scroll_up();
 */
add_action( 'wp_footer', 'Base\scroll_up' );
