<?php
/**
 * Base\Nav_Menus\Component class
 *
 * @package Base
 */

namespace Base\Nav_Menus;

use Base\Component_Interface;
use Base\Templating_Component_Interface;
use function Base\webapp;
use WP_Post;
use WP_Query;
use function add_action;
use function add_filter;
use function register_nav_menus;
use function has_nav_menu;
use function wp_nav_menu;

/**
 * Class for adding collapse option to navigation widget.
 */
class Nav_Widget_Settings {
	/**
	 * Default settings.
	 *
	 * @var array;
	 */
	protected $defaults = array(
		'collapse' => false,
	);
	/**
	 * Default widgets.
	 *
	 * @var array;
	 */
	protected $widgets = array(
		'nav_menu',
	);
	/**
	 * Construct.
	 *
	 * @var array;
	 */
	public function __construct() {
		// Hook in all the right places.
		add_action( 'in_widget_form', array( $this, 'add_settings' ), 10, 3 );
		add_filter( 'widget_update_callback', array( $this, 'save_settings' ), 10, 4 );
		add_filter( 'widget_nav_menu_args', array( $this, 'frontend_settings' ), 10, 4 );
	}

	/**
	 * Adds the custom settings to all widgets' forms.
	 *
	 * @param WP_Widget $widget   An instance of a WP_Widget derived subclass.
	 * @param mixed     $return   Return null if new fields are added.
	 * @param array     $instance An array of the widget's settings.
	 */
	public function add_settings( $widget, $return, $instance ) {
		if ( ! $this->is_supported( $widget ) ) {
			return null;
		}

		// Make sure $instance contains at least our default values.
		$instance = wp_parse_args( $instance, $this->defaults );
		?>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $widget->get_field_id( 'collapse' ) ); ?>" name="<?php echo esc_attr( $widget->get_field_name( 'collapse' ) ); ?>"<?php checked( $instance['collapse'] ); ?> />
			<label for="<?php echo esc_attr( $widget->get_field_id( 'collapse' ) ); ?>"><?php esc_html_e( 'Collapse sub menu items', 'avanam' ); ?></label>
		</p>
		<?php
	}

	/**
	 * Saves the custom settings.
	 *
	 * @param array     $instance     The current widget instance's settings.
	 * @param array     $new_instance Array of new widget settings.
	 * @param array     $old_instance Array of old widget settings.
	 * @param WP_Widget $widget       The current widget instance.
	 *
	 * @return array The widget instance's settings to get saved.
	 */
	public function save_settings( $instance, $new_instance, $old_instance, $widget ) {
		if ( ! $this->is_supported( $widget ) ) {
			return $instance;
		}

		// Make sure $instance contains at least our default values.
		$instance = wp_parse_args( $instance, $this->defaults );

		// Now check that a value is actually present, and assign it sanitized.
		if ( isset( $new_instance['collapse'] ) ) {
			$instance['collapse'] = ! empty( $new_instance['collapse'] ) ? 1 : 0;
		}

		return $instance;
	}

	/**
	 * Filters the arguments for the Navigation Menu widget.
	 *
	 * @since 4.2.0
	 * @since 4.4.0 Added the `$instance` parameter.
	 *
	 * @param array   $nav_menu_args {
	 *     An array of arguments passed to wp_nav_menu() to retrieve a navigation menu.
	 *
	 *     @type callable|bool $fallback_cb Callback to fire if the menu doesn't exist. Default empty.
	 *     @type mixed         $menu        Menu ID, slug, or name.
	 * }
	 * @param WP_Term $nav_menu      Nav menu object for the current menu.
	 * @param array   $args          Display arguments for the current widget.
	 * @param array   $instance      Array of settings for the current widget.
	 */
	public function frontend_settings( $nav_menu_args, $nav_menu, $args, $instance ) {
		if ( isset( $instance['collapse'] ) && $instance['collapse'] ) {
			$nav_menu_args['show_toggles']    = true;
			$nav_menu_args['container_class'] = 'collapse-sub-navigation';
			$nav_menu_args['menu_class']      = 'menu has-collapse-sub-nav';
			if ( ! isset( $nav_menu_args['menu_id'] ) && isset( $args['widget_id'] ) ) {
				$nav_menu_args['menu_id'] = 'menu-' . $args['widget_id'];
			}
		}

		return $nav_menu_args;
	}
	/**
	 * Checks to make sure this is only for nav widget.
	 *
	 * @param WP_Widget $widget       The current widget instance.
	 *
	 * @return bool if the right widget.
	 */
	protected function is_supported( $widget ) {
		if ( in_array( $widget->id_base, $this->widgets, true ) ) {
			return true;
		}
		return false;
	}
}

new Nav_Widget_Settings();
