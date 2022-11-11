<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Base
 */

namespace Base;

get_header();

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

webapp()->print_styles( 'base-content' );
/**
 * Hook for everything, makes for better elementor theming support.
 */
do_action( 'base_single' );

get_footer();
