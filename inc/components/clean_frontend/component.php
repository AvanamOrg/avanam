<?php
/**
 * Base\Clean_Frontend\Component class
 *
 * @package Base
 */

namespace Base\Clean_Frontend;

use Base\Component_Interface;
use function add_action;
use function add_filter;

/**
 * Class for adding custom functions to clean up the front end.
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'clean_frontend';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_filter( 'excerpt_more', array( $this, 'excerpt_more' ) );
	}

	/**
	 * Removes strange box around ... in excerpts.
	 *
	 * @param string $more the excerpt more text.
	 */
	public function excerpt_more( $more ) {
		if ( is_admin() ) {
			return $more;
		}
		return '...';
	}
}
