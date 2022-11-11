<?php
/**
 * Base\Restrict_Content_Pro\Component class
 *
 * @package Base
 */

namespace Base\Restrict_Content_Pro;

use Base\Component_Interface;
use function Base\webapp;
use function add_action;
use function have_posts;
use function the_post;
use function apply_filters;
use function get_template_part;
use function get_post_type;


/**
 * Class for adding Restrict Content Pro plugin support.
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'restrict_content_pro';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', array( $this, 'add_styles' ), 60 );
	}
	/**
	 * Add some css styles for Restrict Content Pro
	 */
	public function add_styles() {
		wp_enqueue_style( 'base-rcp', get_theme_file_uri( '/assets/css/rcp.min.css' ), array(), AVANAM_VERSION );
	}
}
