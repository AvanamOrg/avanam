<?php
/**
 * The main archive template file
 *
 * @package Base
 */

namespace Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

webapp()->print_styles( 'base-content' );
/**
 * Hook for main archive content.
 */
do_action( 'base_archive' );

get_footer();
