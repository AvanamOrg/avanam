<?php
/**
 * Base\Zoom_Recipe_Card\Component class
 *
 * @package Base
 */

namespace Base\Zoom_Recipe_Card;

use Base\Component_Interface;
use function Base\webapp;
use function add_action;
use function have_posts;
use function the_post;
use function is_search;
use function get_template_part;
use function get_post_type;

/**
 * Class for adding Woocommerce plugin support.
 */
class Component implements Component_Interface {
	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'zoom_recipe_card';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', array( $this, 'zoom_recipe_card_styles' ), 60 );
		add_action( 'after_setup_theme', array( $this, 'action_add_editor_styles' ) );
	}
	/**
	 * Enqueues WordPress theme styles for the editor.
	 */
	public function action_add_editor_styles() {
		// Enqueue block editor stylesheet.
		add_editor_style( 'assets/css/editor/zoom-recipe-editor-styles.min.css' );
	}
	/**
	 * Add some css styles for zoom_recipe_card
	 */
	public function zoom_recipe_card_styles() {
		wp_enqueue_style( 'base-zoom-recipe-card', get_theme_file_uri( '/assets/css/zoom-recipe-card.min.css' ), array(), AVANAM_VERSION );
	}
}
