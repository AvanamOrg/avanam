<?php
/**
 * The `webapp()` function.
 *
 * @package Base
 */

namespace Base;

use Base\Template_Tags;
use Base\Theme;
use function get_template_directory;

/**
 * Provides access to all available template tags of the theme.
 *
 * When called for the first time, the function will initialize the theme.
 *
 * @return Template_Tags Template tags instance exposing template tag methods.
 */
function webapp() : Template_Tags {
	static $theme = null;

	if ( null === $theme ) {
		$theme = Theme::instance();
	}

	return $theme->template_tags();
}
// Load the CSS class.
require get_template_directory() . '/inc/class-base-css.php';
// Load the Local Font class.
require get_template_directory() . '/inc/class-local-gfonts.php';

// Load the Customizer class.
require get_template_directory() . '/inc/customizer/class-theme-customizer.php';

// Load Settings Page Class.
require get_template_directory() . '/inc/dashboard/class-theme-dashboard.php';

// Load the Meta class.
require get_template_directory() . '/inc/meta/class-theme-meta.php';

// Load the template functions.
require get_template_directory() . '/inc/template-functions/header-functions.php';
require get_template_directory() . '/inc/template-functions/title-functions.php';
require get_template_directory() . '/inc/template-functions/single-functions.php';
require get_template_directory() . '/inc/template-functions/footer-functions.php';
require get_template_directory() . '/inc/template-functions/archive-functions.php';

// Load the template hooks.
require get_template_directory() . '/inc/template-hooks.php';

// Example Block Style
if ( function_exists( 'register_block_style' ) ) {
    register_block_style(
        'core/quote',
        array(
            'name'         => 'blue-quote',
            'label'        => __( 'Blue Quote', 'avanam' ),
            'is_default'   => true,
            'inline_style' => '.wp-block-quote.is-style-blue-quote { color: blue; }',
        )
    );
}
// Example Block Pattern
if ( function_exists( 'register_block_pattern' ) ) {
    register_block_pattern(
        'avanam/example',
        array(
            'title'      => __( 'Example Pattern', 'avanam' ),
            'categories' => array( 'header' ), // Assigning the pattern the "header" area.
            'blockTypes' => array( 'core/template-part/header' ),
            'content'    => 'Content of example block pattern',
        )
    );
}