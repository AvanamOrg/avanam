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
 * Class for managing navigation menus.
 *
 * Exposes template tags:
 * * `webapp()->is_primary_nav_menu_active()`
 * * `webapp()->display_primary_nav_menu( array $args = [] )`
 * * `webapp()->display_fallback_menu( array $args = [] )`
 * * `webapp()->is_mobile_nav_menu_active( array $args = [] )`
 * * `webapp()->display_mobile_nav_menu( array $args = [] )`
 */
class Component implements Component_Interface, Templating_Component_Interface {

	const PRIMARY_NAV_MENU_SLUG   = 'primary';
	const MOBILE_NAV_MENU_SLUG    = 'mobile';
	const SECONDARY_NAV_MENU_SLUG = 'secondary';
	const FOOTER_NAV_MENU_SLUG    = 'footer';

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'nav_menus';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'after_setup_theme', array( $this, 'action_register_nav_menus' ) );
		add_filter( 'nav_menu_item_title', array( $this, 'filter_primary_nav_menu_dropdown_symbol' ), 10, 4 );
		add_filter( 'walker_nav_menu_start_el', array( $this, 'filter_mobile_nav_menu_dropdown_symbol' ), 10, 4 );
		require_once get_template_directory() . '/inc/components/nav_menus/nav-widget-settings.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
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
			'is_primary_nav_menu_active'   => array( $this, 'is_primary_nav_menu_active' ),
			'display_primary_nav_menu'     => array( $this, 'display_primary_nav_menu' ),
			'is_secondary_nav_menu_active' => array( $this, 'is_secondary_nav_menu_active' ),
			'display_secondary_nav_menu'   => array( $this, 'display_secondary_nav_menu' ),
			'is_footer_nav_menu_active'    => array( $this, 'is_footer_nav_menu_active' ),
			'display_footer_nav_menu'      => array( $this, 'display_footer_nav_menu' ),
			'display_fallback_menu'        => array( $this, 'display_fallback_menu' ),
			'is_mobile_nav_menu_active'    => array( $this, 'is_mobile_nav_menu_active' ),
			'display_mobile_nav_menu'      => array( $this, 'display_mobile_nav_menu' ),
		);
	}

	/**
	 * Registers the navigation menus.
	 */
	public function action_register_nav_menus() {
		register_nav_menus(
			array(
				static::PRIMARY_NAV_MENU_SLUG   => esc_html__( 'Primary', 'avanam' ),
				static::SECONDARY_NAV_MENU_SLUG => esc_html__( 'Secondary', 'avanam' ),
				static::MOBILE_NAV_MENU_SLUG    => esc_html__( 'Mobile', 'avanam' ),
				static::FOOTER_NAV_MENU_SLUG    => esc_html__( 'Footer', 'avanam' ),
			)
		);
	}

	/**
	 * Adds a dropdown symbol to nav menu items with children.
	 *
	 * @param string   $title The menu item's title.
	 * @param object   $item  The current menu item usually a post object.
	 * @param stdClass $args  An object of wp_nav_menu arguments.
	 * @param int      $depth Depth of menu item. Used for padding.
	 */
	public function filter_primary_nav_menu_dropdown_symbol( $title, $item, $args, $depth ) {
		// // Only for our primary and secondary menu location.
		// if ( empty( $args->theme_location ) || ( static::PRIMARY_NAV_MENU_SLUG !== $args->theme_location && static::SECONDARY_NAV_MENU_SLUG !== $args->theme_location ) ) {
		// 	return $title;
		// }
		// // This can still get called because menu location isn't always correct.
		// if ( ! empty( $args->menu_id ) && 'mobile-menu' === $args->menu_id ) {
		// 	return $title;
		// }
		if ( ! isset( $args->sub_arrows ) || empty( $args->sub_arrows ) ) {
			return $title;
		}

		// Add the dropdown for items that have children.
		if ( ! empty( $item->classes ) && in_array( 'menu-item-has-children', $item->classes ) ) {
			$title = '<span class="nav-drop-title-wrap">' . $title . '<span class="dropdown-nav-toggle">' . webapp()->get_icon( 'arrow-down' ) . '</span></span>';
		}
		//aria-label="' . esc_attr__( 'Expand child menu', 'avanam' ) . '"

		return $title;
	}

	/**
	 * Adds a dropdown symbol to nav menu items with children.
	 *
	 * @param string $item_output The menu item's starting HTML output.
	 * @param object $item        Menu item data object.
	 * @param int    $depth       Depth of menu item. Used for padding.
	 * @param object $args        An object of wp_nav_menu.
	 * @return string Modified nav menu HTML.
	 */
	public function filter_mobile_nav_menu_dropdown_symbol( $item_output, $item, $depth, $args ) {
		// Only for our Mobile menu location.
		if ( ! isset( $args->show_toggles ) || empty( $args->show_toggles ) ) {
			return $item_output;
		}

		// Add the dropdown for items that have children.
		if ( ! empty( $item->classes ) && in_array( 'menu-item-has-children', $item->classes ) ) {
			if ( webapp()->is_amp() ) {
				return $item_output;
			}
			$menu_id = ( isset( $args->menu_id ) && ! empty( $args->menu_id ) ? '#' . $args->menu_id : '.menu' );
			$toggle_target_string = $menu_id . ' .menu-item-' . $item->ID . ' > .sub-menu';
			$output = '<div class="drawer-nav-drop-wrap">' . $item_output . '<button class="drawer-sub-toggle" data-toggle-duration="10" data-toggle-target="' . esc_attr( $toggle_target_string ) . '" aria-expanded="false"><span class="screen-reader-text">' . esc_html__( 'Toggle child menu', 'avanam' ) . '</span>' . webapp()->get_icon( 'arrow-down', '', false, false ) . '</button></div>';
			return $output;
		}

		return $item_output;
	}

	/**
	 * Checks whether the primary navigation menu is active.
	 *
	 * @return bool True if the primary navigation menu is active, false otherwise.
	 */
	public function is_primary_nav_menu_active() : bool {
		return (bool) has_nav_menu( static::PRIMARY_NAV_MENU_SLUG );
	}

	/**
	 * Checks whether the secondary navigation menu is active.
	 *
	 * @return bool True if the secondary navigation menu is active, false otherwise.
	 */
	public function is_secondary_nav_menu_active() : bool {
		return (bool) has_nav_menu( static::SECONDARY_NAV_MENU_SLUG );
	}

	/**
	 * Checks whether the footer navigation menu is active.
	 *
	 * @return bool True if the footer navigation menu is active, false otherwise.
	 */
	public function is_footer_nav_menu_active() : bool {
		return (bool) has_nav_menu( static::FOOTER_NAV_MENU_SLUG );
	}

	/**
	 * Checks whether the mobile navigation menu is active.
	 *
	 * @return bool True if the mobile navigation menu is active, false otherwise.
	 */
	public function is_mobile_nav_menu_active() : bool {
		return (bool) has_nav_menu( static::MOBILE_NAV_MENU_SLUG );
	}

	/**
	 * Displays the fallback page navigation menu.
	 *
	 * @param array $args Optional. Array of arguments. See wp page menu documentation for a list of supported.
	 */
	public function display_fallback_menu( array $args = array() ) {
		$latest   = new WP_Query(
			array(
				'post_type'      => 'page',
				'orderby'        => 'menu_order title',
				'order'          => 'ASC',
				'posts_per_page' => 5,
			)
		);
		$page_ids = wp_list_pluck( $latest->posts, 'ID' );
		$page_ids = implode( ',', $page_ids );

		$fallback_args = array(
			'depth'      => -1,
			'include'    => $page_ids,
			'show_home'  => false,
			'before'     => '',
			'after'      => '',
			'menu_id'    => 'primary-menu',
			'menu_class' => 'menu',
			'container'  => 'ul',
		);
		add_filter( 'wp_page_menu', array( $this, 'change_page_menu_classes' ), 10, 2 );
		wp_page_menu( $fallback_args );
		remove_filter( 'wp_page_menu', array( $this, 'change_page_menu_classes' ), 10, 2 );
	}
	/**
	 * Displays the primary navigation menu.
	 *
	 * @param array $args Optional. Array of arguments. See wp nav menu documentation for a list of supported arguments.
	 */
	public function change_page_menu_classes( $menu, $args ) {
		$menu = str_replace( 'page_item', 'menu-item', $menu );
		return $menu;
	}
	/**
	 * Displays the primary navigation menu.
	 *
	 * @param array $args Optional. Array of arguments. See wp nav menu documentation for a list of supported arguments.
	 */
	public function display_mobile_nav_menu( array $args = array() ) {
		if ( ! isset( $args['container'] ) ) {
			$args['container'] = 'ul';
		}
		if ( ! isset( $args['addon_support'] ) ) {
			$args['addon_support'] = true;
		}
		if ( ! isset( $args['mega_support'] ) && apply_filters( 'base_mobile_allow_mega_support', true ) ) {
			$args['mega_support'] = true;
		}
		$args['show_toggles']   = ( webapp()->option( 'mobile_navigation_collapse' ) ? true : false );
		$args['theme_location'] = static::MOBILE_NAV_MENU_SLUG;

		wp_nav_menu( $args );
	}

	/**
	 * Displays the primary navigation menu.
	 *
	 * @param array $args Optional. Array of arguments. See wp nav menu documentation for a list of supported arguments.
	 */
	public function display_primary_nav_menu( array $args = array() ) {
		if ( ! isset( $args['container'] ) ) {
			$args['container'] = 'ul';
		}
		if ( ! isset( $args['sub_arrows'] ) ) {
			$args['sub_arrows'] = true;
		}
		if ( ! isset( $args['mega_support'] ) ) {
			$args['mega_support'] = true;
		}
		if ( ! isset( $args['addon_support'] ) ) {
			$args['addon_support'] = true;
		}
		$args['theme_location'] = static::PRIMARY_NAV_MENU_SLUG;
		wp_nav_menu( $args );
	}

	/**
	 * Displays the Secondary navigation menu.
	 *
	 * @param array $args Optional. Array of arguments. See wp nav menu documentation for a list of supported arguments.
	 */
	public function display_secondary_nav_menu( array $args = array() ) {
		if ( ! isset( $args['container'] ) ) {
			$args['container'] = 'ul';
		}
		if ( ! isset( $args['sub_arrows'] ) ) {
			$args['sub_arrows'] = true;
		}
		if ( ! isset( $args['mega_support'] ) ) {
			$args['mega_support'] = true;
		}
		if ( ! isset( $args['addon_support'] ) ) {
			$args['addon_support'] = true;
		}
		$args['theme_location'] = static::SECONDARY_NAV_MENU_SLUG;
		wp_nav_menu( $args );
	}

	/**
	 * Displays the footer navigation menu.
	 *
	 * @param array $args Optional. Array of arguments. See wp nav menu documentation for a list of supported arguments.
	 */
	public function display_footer_nav_menu( array $args = array() ) {
		if ( ! isset( $args['container'] ) ) {
			$args['container'] = 'ul';
		}
		if ( ! isset( $args['depth'] ) ) {
			$args['depth'] = 1;
		}
		if ( ! isset( $args['addon_support'] ) ) {
			$args['addon_support'] = true;
		}
		$args['theme_location'] = static::FOOTER_NAV_MENU_SLUG;
		wp_nav_menu( $args );
	}
}
