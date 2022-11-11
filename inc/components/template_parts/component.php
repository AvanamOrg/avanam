<?php
/**
 * Base\Template_Parts\Component class
 *
 * @package Base
 */

namespace Base\Template_Parts;

use Base\Component_Interface;
use Base\Templating_Component_Interface;
use function Base\webapp;
use function add_action;
use function add_filter;
use function do_action;
use function is_active_sidebar;
use function dynamic_sidebar;

/**
 * Class for managing template parts.
 *
 * Exposes template tags:
 * * `webapp()->get_template()`
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'template_parts';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
	}

	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `webapp()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() : array {
		return array(
			'get_template' => array( $this, 'get_template' ),
		);
	}

	/**
	 * Get other templates assing attributes and including the file.
	 *
	 * @param string $slug The slug name for the generic template.
	 * @param string $name The name of the specialised template.
	 * @param array  $args          Arguments. (default: array).
	 */
	public static function get_template( $slug, $name = null, $args = array() ) {

		/**
		 * Pass custom variables to the template file.
		 */
		foreach ( (array) $args as $key => $value ) {
			set_query_var( $key, $value );
		}

		return get_template_part( $slug, $name );
	}
}
