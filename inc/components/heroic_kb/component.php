<?php
/**
 * Base\Heroic_Kb\Component class
 *
 * @package Base
 */

namespace Base\Heroic_Kb;

use Base\Component_Interface;
use function Base\webapp;
use function add_action;
use function add_filter;
use function get_template_part;
use function locate_template;

/**
 * Class for integrating with the block Heroic_Kb.
 *
 * @link https://wordpress.org/gutenberg/handbook/extensibility/theme-support/
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'heroic_kb';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		// Heroic Knowledge Base.
		//add_filter( 'hkb_locate_template', array( $this, 'output_edited_search' ) );
		add_filter( 'hkb_show_knowledgebase_search', array( $this, 'override_search_location' ) );
		add_filter( 'hkb_show_knowledgebase_breadcrumbs', array( $this, 'override_search_location' ) );
		add_action( 'base_entry_archive_hero', array( $this, 'ht_knowledge_base_breadcrumb_in_title' ), 5 );
		add_action( 'base_entry_archive_header', array( $this, 'ht_knowledge_base_breadcrumb_in_title' ), 5 );
		//add_action( 'base_entry_header', array( $this, 'ht_knowledge_base_breadcrumb_in_title' ), 5 );
		add_action( 'base_entry_archive_hero', array( $this, 'ht_knowledge_base_search_in_title' ), 20 );
		add_action( 'base_entry_archive_header', array( $this, 'ht_knowledge_base_search_in_title' ), 20 );
		add_action( 'base_entry_header', array( $this, 'ht_knowledge_base_search_in_title' ), 20 );
	}
	/**
	 * Check to see if string ends with somthing.
	 *
	 * @param string $string the string.
	 * @param string $test the test.
	 */
	public function endswith( $string, $test ) {
		$strlen  = strlen( $string );
		$testlen = strlen( $test );
		if ( $testlen > $strlen ) {
			return false;
		}
		return substr_compare( $string, $test, $strlen - $testlen, $testlen ) === 0;
	}
	/**
	 * Changes Heroic Knowledge Base Search Button.
	 *
	 * @param string $template the template.
	 */
	public function output_edited_search( $template ) {
		if ( $this->endswith( $template, 'hkb-searchbox.php' ) ) {
			$template = locate_template( 'template-parts/archive-title/hkb-searchbox' );
		}
		return $template;
	}
	/**
	 * Changes Heroic Knowledge Base Search Button.
	 *
	 * @param bool $show_search whether to show search.
	 */
	public function override_search_location( $show_search ) {
		return false;
	}
	/**
	 * Adds Support for Heroic Knowledge Base Search in Title Area.
	 */
	public function ht_knowledge_base_search_in_title() {
		if ( ( is_tax( 'ht_kb_category' ) || is_tax( 'ht_kb_tag' ) || is_post_type_archive( 'ht_kb' ) || ( is_search() && array_key_exists( 'ht-kb-search', $_REQUEST ) ) ) ) {
			get_template_part( 'template-parts/archive-title/hkb-searchbox' );
		}
	}
	/**
	 * Adds Support for Heroic Knowledge Base Breadcrumb in Title Area.
	 */
	public function ht_knowledge_base_breadcrumb_in_title() {
		if ( ( is_tax( 'ht_kb_category' ) || is_tax( 'ht_kb_tag' ) || is_post_type_archive( 'ht_kb' ) || ( is_search() && array_key_exists( 'ht-kb-search', $_REQUEST ) ) ) ) {
			webapp()->print_breadcrumb();
		}
	}
}
