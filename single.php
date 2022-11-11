<?php
/**
 * The main single item template file.
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
 * Hook for everything, makes for better elementor theming support.
 */
do_action( 'base_single' );

get_footer();
